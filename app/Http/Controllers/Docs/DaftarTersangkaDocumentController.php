<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\DaftarTersangkaDocument\DaftarTersangkaDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class DaftarTersangkaDocumentController extends Controller
{
    use DocsOfficersTraits;
    /**
     * Tampilkan form pembuatan Daftar Tersangka (S-38.3)
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?: $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        // Jika sudah ada dokumen yang tersimpan, redirect langsung ke halaman edit
        $existingDoc = DaftarTersangkaDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.daftar-tersangka-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Ambil daftar pejabat penandatangan berwenang
        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->signatory()
                ->get();
        }

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->orderBy('first_name', 'asc')
                ->get();
        }

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

        // Konsolidasi tersangka dari perkara LP
        $availableSuspects = $this->getConsolidatedSuspects($accidentId);

        // Default list tersangka awal (ambil dari perkara jika ada, atau baris kosong)
        $initialTersangkaList = [];
        if (!empty($availableSuspects)) {
            $no = 1;
            foreach ($availableSuspects as $as) {
                $initialTersangkaList[] = [
                    'no' => $no++,
                    'nama' => $as['nama'],
                    'no_identitas' => $as['no_identitas'],
                    'kewarganegaraan' => $as['kewarganegaraan'],
                    'jenis_kelamin' => $as['jenis_kelamin'],
                    'pekerjaan' => $as['pekerjaan'],
                    'agama' => $as['agama'],
                    'alamat' => $as['alamat'],
                    'keterangan' => $as['keterangan'],
                    'suspect_id' => $as['source_id'],
                    'source' => $as['source'],
                ];
            }
        }

        // Ambil master referensi pekerjaan & agama
        $libJobs = DB::table('lib.jobs')->orderBy('name')->pluck('name')->toArray();
        if (empty($libJobs)) {
            $libJobs = ['PNS', 'Karyawan Swasta', 'Wiraswasta', 'Buruh', 'Pelajar', 'Mahasiswa', 'Petani', 'Pedagang', 'TNI', 'Polri', 'Supir'];
        }

        $libReligions = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

        return view('docs.daftar-tersangka-document.create', compact(
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'availableSuspects',
            'initialTersangkaList',
            'libJobs',
            'libReligions'
        ));
    }

    /**
     * Simpan data Daftar Tersangka baru
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $defaultNomorLp = $request->input('nomor_lp') ?: ($accident->no_lp ?? '-');
        $defaultLocation = $request->input('dikeluarkan_di') ?: ($accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat'));
        $defaultSatker = $request->input('satker_penyidik') ?: ($accident ? 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? '')) : 'Satuan Lalu Lintas');
        $defaultTanggalLp = $request->input('tanggal_lp') ?: ($accident && $accident->report_date ? Carbon::parse($accident->report_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));

        $request->merge([
            'nomor_lp' => $defaultNomorLp,
            'dikeluarkan_di' => $defaultLocation,
            'satker_penyidik' => $defaultSatker,
            'tanggal_lp' => $defaultTanggalLp,
        ]);

        $request->validate([
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required',
            'dikeluarkan_di' => 'required',
            'satker_penyidik' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'jabatan_penandatangan' => 'required',
            'tersangka' => 'required|array|min:1',
            'tersangka.*.nama' => 'required',
            'tersangka.*.no_identitas' => 'required',
            'tersangka.*.kewarganegaraan' => 'required',
            'tersangka.*.jenis_kelamin' => 'required',
            'tersangka.*.pekerjaan' => 'required',
            'tersangka.*.agama' => 'required',
            'tersangka.*.alamat' => 'required',
            'tersangka.*.keterangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'tersangka.required' => 'Data Tersangka wajib diisi minimal 1 data tersangka.',
            'tersangka.min' => 'Data Tersangka wajib diisi minimal 1 data tersangka.',
            'tersangka.*.nama.required' => 'Kolom Nama pada Data Tersangka wajib diisi.',
            'tersangka.*.no_identitas.required' => 'Kolom No. Identitas pada Data Tersangka wajib diisi.',
            'tersangka.*.kewarganegaraan.required' => 'Kolom Kewarganegaraan pada Data Tersangka wajib diisi.',
            'tersangka.*.jenis_kelamin.required' => 'Kolom Jenis Kelamin pada Data Tersangka wajib dipilih.',
            'tersangka.*.pekerjaan.required' => 'Kolom Pekerjaan pada Data Tersangka wajib diisi.',
            'tersangka.*.agama.required' => 'Kolom Agama pada Data Tersangka wajib dipilih.',
            'tersangka.*.alamat.required' => 'Kolom Alamat pada Data Tersangka wajib diisi.',
            'tersangka.*.keterangan.required' => 'Kolom Keterangan pada Data Tersangka wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Format List Tersangka
            $rawTersangkaList = $request->input('tersangka', []);
            $cleanedTersangkaList = [];
            $no = 1;

            if (is_array($rawTersangkaList)) {
                foreach ($rawTersangkaList as $t) {
                    $nama = trim($t['nama'] ?? '');
                    if ($nama !== '') {
                        $cleanedTersangkaList[] = [
                            'no' => $no++,
                            'nama' => $nama,
                            'no_identitas' => trim($t['no_identitas'] ?? '') ?: '-',
                            'kewarganegaraan' => trim($t['kewarganegaraan'] ?? '') ?: '-',
                            'jenis_kelamin' => trim($t['jenis_kelamin'] ?? '') ?: '-',
                            'pekerjaan' => trim($t['pekerjaan'] ?? '') ?: '-',
                            'agama' => trim($t['agama'] ?? '') ?: '-',
                            'alamat' => trim($t['alamat'] ?? '') ?: '-',
                            'keterangan' => trim($t['keterangan'] ?? '') ?: '-',
                            'suspect_id' => $t['suspect_id'] ?? null,
                            'source' => $t['source'] ?? null,
                        ];
                    }
                }
            }

            if (empty($cleanedTersangkaList)) {
                return redirect()->back()->with('error', 'Data Tersangka wajib diisi minimal 1 data tersangka.')->withInput();
            }

            $document = DaftarTersangkaDocument::create([
                'id' => (string) Str::uuid(),
                'accident_id' => $accidentId,
                'document_category_id' => '0804',
                'status_id' => '2',
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : null,

                'daftar_tersangka' => $cleanedTersangkaList,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => preg_replace('/[^0-9]/', '', (string)$request->nrp_penandatangan) ?: $request->nrp_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: 'Penyidik / Penyidik Pembantu',

                'created_by_user_id' => Auth::id() ?: 1,
                'ip_addresses' => ['created_ip' => $request->ip()],
                'timestamps' => ['created_at' => Carbon::now()->toIso8601String()],
            ]);

            DB::commit();

            // Two-way sync ke tabel public.daftar_tersangka (pasca-commit agar tidak memblokir)
            $this->syncSuspectsToDaftarTersangka($accidentId, $cleanedTersangkaList);

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Tersangka (S-38.3) berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat Daftar Tersangka: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form edit Daftar Tersangka
     *
     * @param string $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $document = DaftarTersangkaDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->signatory()
                ->get();
        }

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->orderBy('first_name', 'asc')
                ->get();
        }

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

        $savedTersangkaList = $document->daftar_tersangka ?: [];

        // Konsolidasi tersangka perkara LP
        $availableSuspects = $this->getConsolidatedSuspects($accidentId);

        // Ambil master referensi pekerjaan & agama
        $libJobs = DB::table('lib.jobs')->orderBy('name')->pluck('name')->toArray();
        if (empty($libJobs)) {
            $libJobs = ['PNS', 'Karyawan Swasta', 'Wiraswasta', 'Buruh', 'Pelajar', 'Mahasiswa', 'Petani', 'Pedagang', 'TNI', 'Polri', 'Supir'];
        }

        $libReligions = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

        $currentSignatoryName = trim($document->nama_penandatangan ?? '');

        return view('docs.daftar-tersangka-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'savedTersangkaList',
            'availableSuspects',
            'libJobs',
            'libReligions',
            'currentSignatoryName'
        ));
    }

    /**
     * Perbarui data Daftar Tersangka
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $document = DaftarTersangkaDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        $defaultNomorLp = $request->input('nomor_lp') ?: ($document->nomor_lp ?: ($accident->no_lp ?? '-'));
        $defaultLocation = $request->input('dikeluarkan_di') ?: ($document->dikeluarkan_di ?: ($accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat')));
        $defaultSatker = $request->input('satker_penyidik') ?: ($document->satker_penyidik ?: ($accident ? 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? '')) : 'Satuan Lalu Lintas'));
        $defaultTanggalLp = $request->input('tanggal_lp') ?: ($document->tanggal_lp ?: ($accident && $accident->report_date ? Carbon::parse($accident->report_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d')));

        $request->merge([
            'nomor_lp' => $defaultNomorLp,
            'dikeluarkan_di' => $defaultLocation,
            'satker_penyidik' => $defaultSatker,
            'tanggal_lp' => $defaultTanggalLp,
        ]);

        $request->validate([
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required',
            'dikeluarkan_di' => 'required',
            'satker_penyidik' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'jabatan_penandatangan' => 'required',
            'tersangka' => 'required|array|min:1',
            'tersangka.*.nama' => 'required',
            'tersangka.*.no_identitas' => 'required',
            'tersangka.*.kewarganegaraan' => 'required',
            'tersangka.*.jenis_kelamin' => 'required',
            'tersangka.*.pekerjaan' => 'required',
            'tersangka.*.agama' => 'required',
            'tersangka.*.alamat' => 'required',
            'tersangka.*.keterangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'tersangka.required' => 'Data Tersangka wajib diisi minimal 1 data tersangka.',
            'tersangka.min' => 'Data Tersangka wajib diisi minimal 1 data tersangka.',
            'tersangka.*.nama.required' => 'Kolom Nama pada Data Tersangka wajib diisi.',
            'tersangka.*.no_identitas.required' => 'Kolom No. Identitas pada Data Tersangka wajib diisi.',
            'tersangka.*.kewarganegaraan.required' => 'Kolom Kewarganegaraan pada Data Tersangka wajib diisi.',
            'tersangka.*.jenis_kelamin.required' => 'Kolom Jenis Kelamin pada Data Tersangka wajib dipilih.',
            'tersangka.*.pekerjaan.required' => 'Kolom Pekerjaan pada Data Tersangka wajib diisi.',
            'tersangka.*.agama.required' => 'Kolom Agama pada Data Tersangka wajib dipilih.',
            'tersangka.*.alamat.required' => 'Kolom Alamat pada Data Tersangka wajib diisi.',
            'tersangka.*.keterangan.required' => 'Kolom Keterangan pada Data Tersangka wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $rawTersangkaList = $request->input('tersangka', []);
            $cleanedTersangkaList = [];
            $no = 1;

            if (is_array($rawTersangkaList)) {
                foreach ($rawTersangkaList as $t) {
                    $nama = trim($t['nama'] ?? '');
                    if ($nama !== '') {
                        $cleanedTersangkaList[] = [
                            'no' => $no++,
                            'nama' => $nama,
                            'no_identitas' => trim($t['no_identitas'] ?? '') ?: '-',
                            'kewarganegaraan' => trim($t['kewarganegaraan'] ?? '') ?: '-',
                            'jenis_kelamin' => trim($t['jenis_kelamin'] ?? '') ?: '-',
                            'pekerjaan' => trim($t['pekerjaan'] ?? '') ?: '-',
                            'agama' => trim($t['agama'] ?? '') ?: '-',
                            'alamat' => trim($t['alamat'] ?? '') ?: '-',
                            'keterangan' => trim($t['keterangan'] ?? '') ?: '-',
                            'suspect_id' => $t['suspect_id'] ?? null,
                            'source' => $t['source'] ?? null,
                        ];
                    }
                }
            }

            if (empty($cleanedTersangkaList)) {
                return redirect()->back()->with('error', 'Data Tersangka wajib diisi minimal 1 data tersangka.')->withInput();
            }

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : $document->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : $document->tanggal_lp,

                'daftar_tersangka' => $cleanedTersangkaList,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => preg_replace('/[^0-9]/', '', (string)$request->nrp_penandatangan) ?: $request->nrp_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: $document->jabatan_penandatangan,

                'updated_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            // Two-way sync ke tabel public.daftar_tersangka
            $this->syncSuspectsToDaftarTersangka($accidentId, $cleanedTersangkaList);

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Tersangka (S-38.3) berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Daftar Tersangka: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Daftar Tersangka (S-38.3)
     *
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id, Request $request = null)
    {
        try {
            $document = DaftarTersangkaDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/daftar_tersangka.docx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word daftar_tersangka.docx tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templateFile);

            // 1. Kop Surat & Lokasi
            $poldaFullName = $accident->polres->polda->name ?? ($accident->polres->polda->full_name ?? '');
            $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114']))
                ? 'DIREKTORAT LALU LINTAS'
                : 'RESOR ' . strtoupper($accident->polres->full_name ?? $accident->polres->name ?? '');
            $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_district ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')));

            $documentLocation = $document->dikeluarkan_di ?: 'Tempat';
            $docDateFormatted = $document->tanggal_surat
                ? Carbon::parse($document->tanggal_surat)->locale('id')->translatedFormat('d F Y')
                : Carbon::now()->locale('id')->translatedFormat('d F Y');

            $esc = function ($val) {
                return htmlspecialchars((string) ($val ?? ''), ENT_XML1, 'UTF-8');
            };

            $templateProcessor->setValue('daerahPoliceFullName', $esc(strtoupper($poldaFullName)));
            $templateProcessor->setValue('resorPoliceFullName', $esc($resorPoliceFullName));
            $templateProcessor->setValue('resorPoliceAddress', $esc($resorPoliceAddress));
            $templateProcessor->setValue('documentLocation', $esc($documentLocation));
            $templateProcessor->setValue('documentDate', $esc($docDateFormatted));

            // 2. Format 9 Kolom Tersangka (Tabel S-38.3)
            $rawTersangka = $document->daftar_tersangka ?: [];
            $blockTersangka = [];
            $i = 1;

            if (is_array($rawTersangka)) {
                foreach ($rawTersangka as $t) {
                    $blockTersangka[] = [
                        'tersangka_no' => (string) ($i++),
                        'tersangka_nama' => $esc($t['nama'] ?? '-'),
                        'tersangka_identitas' => $esc($t['no_identitas'] ?? '-'),
                        'tersangka_warga_negara' => $esc($t['kewarganegaraan'] ?? 'WNI'),
                        'tersangka_gender' => $esc($t['jenis_kelamin'] ?? '-'),
                        'tersangka_pekerjaan' => $esc($t['pekerjaan'] ?? '-'),
                        'tersangka_agama' => $esc($t['agama'] ?? '-'),
                        'tersangka_alamat' => $esc($t['alamat'] ?? '-'),
                        'tersangka_keterangan' => $esc($t['keterangan'] ?? '-'),
                    ];
                }
            }

            if (empty($blockTersangka)) {
                $blockTersangka[] = [
                    'tersangka_no' => '1',
                    'tersangka_nama' => '-',
                    'tersangka_identitas' => '-',
                    'tersangka_warga_negara' => '-',
                    'tersangka_gender' => '-',
                    'tersangka_pekerjaan' => '-',
                    'tersangka_agama' => '-',
                    'tersangka_alamat' => '-',
                    'tersangka_keterangan' => '-',
                ];
            }

            $templateProcessor->cloneRowAndSetValues('tersangka_no', $blockTersangka);

            // 3. Pejabat Penandatangan (Penyidik / Penyidik Pembantu)
            $sigHead = $document->jabatan_penandatangan ?: 'Penyidik / Penyidik Pembantu';
            $templateProcessor->setValue('signatoryHeadText', $esc($sigHead));
            $templateProcessor->setValue('signatoryName', $esc($document->nama_penandatangan ?: '-'));
            $templateProcessor->setValue('signatoryRankName', $esc(strtoupper($document->pangkat_penandatangan ?: '-')));

            $cleanNrp = preg_replace('/[^0-9]/', '', (string)($document->nrp_penandatangan ?? ''));
            $templateProcessor->setValue('signatoryRegisterNumber', $esc($cleanNrp ?: '-'));

            // Simpan file temporary dan download
            $safeDocNo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $document->nomor_surat ?: 'Daftar_Tersangka');
            $fileName = 'Daftar_Tersangka_' . $safeDocNo . '_' . Carbon::now()->format('YmdHis') . '.docx';
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir . '/' . $fileName;

            $templateProcessor->saveAs($tempPath);

            return response()->download($tempPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh dokumen Daftar Tersangka: ' . $e->getMessage());
        }
    }

    /**
     * Hapus dokumen Daftar Tersangka
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request = null)
    {
        try {
            $document = DaftarTersangkaDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Tersangka berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk mengambil ID polres lama/baru
     */
    protected function getOldNewPolresIds($policeId)
    {
        $ids = [$policeId];
        try {
            $oldNew = DB::table('polres_old_new')->where('old_id', $policeId)->orWhere('new_id', $policeId)->first();
            if ($oldNew) {
                $ids = array_unique(array_filter([$oldNew->old_id, $oldNew->new_id]));
            }
        } catch (\Throwable $e) {
            // Ignore error
        }
        return $ids;
    }

    /**
     * Mengonsolidasikan data Tersangka perkara dari berbagai sumber tabel
     * (suspects, daftar_tersangka, dan involved_peoples)
     *
     * @param string $accidentId
     * @return array
     */
    protected function getConsolidatedSuspects($accidentId)
    {
        $consolidated = [];
        $seenNames = [];

        // 1. Dari tabel public.suspects (Tersangka resmi perkara)
        try {
            $suspects = DB::table('suspects')
                ->leftJoin('lib.genders as lg', 'suspects.gender_id', '=', 'lg.id')
                ->leftJoin('lib.religions as lr', 'suspects.religion_id', '=', 'lr.id')
                ->leftJoin('lib.jobs as lj', 'suspects.job_id', '=', 'lj.id')
                ->where('suspects.accident_id', $accidentId)
                ->select(
                    'suspects.*',
                    'lg.name as gender_name',
                    'lr.name as religion_name',
                    'lj.name as job_name'
                )
                ->get();

            foreach ($suspects as $s) {
                $nameClean = trim($s->name ?? '');
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && $nameKey !== 'tidak diketahui' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = 'Laki-laki';
                    $rawGender = strtoupper($s->gender_name ?? $s->gender_short_name ?? '');
                    if (str_contains($rawGender, 'PEREMPUAN') || str_contains($rawGender, 'WANITA')) {
                        $gender = 'Perempuan';
                    }

                    $kewarganegaraan = !empty($s->nationality) ? trim($s->nationality) : (!empty($s->country_short_name) ? trim($s->country_short_name) : 'WNI');
                    $pekerjaan = trim($s->job_name ?? $s->occupation ?? '');
                    $agama = trim($s->religion_name ?? $s->religion_short_name ?? '');

                    $consolidated[] = [
                        'source' => 'suspects',
                        'source_id' => (string) ($s->id ?? ''),
                        'source_label' => 'Tersangka Perkara (LP)',
                        'nama' => $nameClean,
                        'no_identitas' => trim($s->identity_number ?? '') ?: '-',
                        'kewarganegaraan' => $kewarganegaraan ?: 'WNI',
                        'jenis_kelamin' => $gender,
                        'pekerjaan' => ucwords(strtolower($pekerjaan)) ?: '-',
                        'agama' => ucwords(strtolower($agama)) ?: 'Islam',
                        'alamat' => trim($s->address ?? '') ?: '-',
                        'keterangan' => 'Tersangka Perkara',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        // 2. Dari tabel public.daftar_tersangka (Tersangka Penyidikan)
        try {
            $legacyTersangka = DB::table('daftar_tersangka')
                ->leftJoin('ref as ref_gender', function ($join) {
                    $join->on('daftar_tersangka.gender', '=', 'ref_gender.id')->where('ref_gender.grp_id', '=', 'G01');
                })
                ->leftJoin('ref as ref_religion', function ($join) {
                    $join->on('daftar_tersangka.religion', '=', 'ref_religion.id')->where('ref_religion.grp_id', '=', 'R01');
                })
                ->where('daftar_tersangka.accident_id', $accidentId)
                ->select('daftar_tersangka.*', 'ref_gender.name as gender_name', 'ref_religion.name as religion_name')
                ->get();

            foreach ($legacyTersangka as $lt) {
                $nameClean = trim($lt->name ?? '');
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = 'Laki-laki';
                    $rawGender = strtoupper($lt->gender_name ?? $lt->gender ?? '');
                    if (str_contains($rawGender, 'PEREMPUAN') || str_contains($rawGender, 'WANITA')) {
                        $gender = 'Perempuan';
                    }

                    $consolidated[] = [
                        'source' => 'daftar_tersangka',
                        'source_id' => (string) ($lt->id ?? ''),
                        'source_label' => 'Tersangka Terdata',
                        'nama' => $nameClean,
                        'no_identitas' => trim($lt->identity_no ?? '') ?: '-',
                        'kewarganegaraan' => !empty($lt->citizen) ? trim($lt->citizen) : 'WNI',
                        'jenis_kelamin' => $gender,
                        'pekerjaan' => ucwords(strtolower(trim($lt->job ?? ''))) ?: '-',
                        'agama' => ucwords(strtolower(trim($lt->religion_name ?? $lt->religion ?? ''))) ?: 'Islam',
                        'alamat' => trim($lt->address ?? '') ?: '-',
                        'keterangan' => 'Tersangka',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        // 3. Dari tabel public.involved_peoples berkategori tersangka
        try {
            $involved = DB::table('involved_peoples')
                ->leftJoin('lib.genders as lg', 'involved_peoples.gender_id', '=', 'lg.id')
                ->leftJoin('lib.religions as lr', 'involved_peoples.religion_id', '=', 'lr.id')
                ->leftJoin('lib.jobs as lj', 'involved_peoples.job_id', '=', 'lj.id')
                ->where('involved_peoples.accident_id', $accidentId)
                ->where('involved_peoples.class', '=', 'SUSPECT')
                ->select(
                    'involved_peoples.*',
                    'lg.name as gender_name',
                    'lr.name as religion_name',
                    'lj.name as job_name'
                )
                ->get();

            foreach ($involved as $inv) {
                $nameClean = trim($inv->name ?? '');
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && $nameKey !== 'tidak diketahui' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = 'Laki-laki';
                    $rawGender = strtoupper($inv->gender_name ?? '');
                    if (str_contains($rawGender, 'PEREMPUAN') || str_contains($rawGender, 'WANITA')) {
                        $gender = 'Perempuan';
                    }

                    $consolidated[] = [
                        'source' => 'involved_peoples',
                        'source_id' => (string) ($inv->id ?? ''),
                        'source_label' => 'Pihak Terlibat (Tersangka)',
                        'nama' => $nameClean,
                        'no_identitas' => trim($inv->identity_number ?? '') ?: '-',
                        'kewarganegaraan' => !empty($inv->nationality) ? trim($inv->nationality) : 'WNI',
                        'jenis_kelamin' => $gender,
                        'pekerjaan' => ucwords(strtolower(trim($inv->job_name ?? ''))) ?: '-',
                        'agama' => ucwords(strtolower(trim($inv->religion_name ?? ''))) ?: 'Islam',
                        'alamat' => trim($inv->address ?? '') ?: '-',
                        'keterangan' => 'Tersangka Terlibat',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        return $consolidated;
    }

    /**
     * Lakukan sinkronisasi dua arah ke tabel public.daftar_tersangka
     * agar data tersangka baru yang diinput pada form dapat digunakan oleh dokumen Mindik lain.
     */
    protected function syncSuspectsToDaftarTersangka($accidentId, array $tersangkaList)
    {
        if (empty($tersangkaList) || empty($accidentId)) {
            return;
        }

        foreach ($tersangkaList as $tItem) {
            $tName = trim($tItem['nama'] ?? '');
            if ($tName === '') {
                continue;
            }

            try {
                $exists = DB::table('daftar_tersangka')
                    ->where('accident_id', $accidentId)
                    ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($tName)])
                    ->exists();

                if (!$exists) {
                    $genderText = strtoupper(trim($tItem['jenis_kelamin'] ?? ''));
                    $genderCode = ($genderText === 'PEREMPUAN' || $genderText === 'WANITA') ? 'G0102' : 'G0101';

                    $relText = strtoupper(trim($tItem['agama'] ?? ''));
                    $relCode = 'R0102'; // default Islam
                    if (str_contains($relText, 'KATOLIK')) {
                        $relCode = 'R0104';
                    } elseif (str_contains($relText, 'KRISTEN') || str_contains($relText, 'PROTESTAN')) {
                        $relCode = 'R0103';
                    } elseif (str_contains($relText, 'HINDU')) {
                        $relCode = 'R0105';
                    } elseif (str_contains($relText, 'BUDDHA') || str_contains($relText, 'BUDHA')) {
                        $relCode = 'R0106';
                    } elseif (str_contains($relText, 'KONGHUCU')) {
                        $relCode = 'R0101';
                    }

                    \App\Models\DaftarTersangka::create([
                        'accident_id' => $accidentId,
                        'name' => $tName,
                        'gender' => $genderCode,
                        'city' => '-',
                        'birth_date' => '1990-01-01',
                        'religion' => $relCode,
                        'job' => trim($tItem['pekerjaan'] ?? '') ?: '-',
                        'education' => '-',
                        'phone' => '-',
                        'citizen' => trim($tItem['kewarganegaraan'] ?? '') ?: 'WNI',
                        'address' => trim($tItem['alamat'] ?? '') ?: '-',
                        'identity_no' => trim($tItem['no_identitas'] ?? '') ?: '-',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal sync tersangka ke public.daftar_tersangka: ' . $e->getMessage());
            }
        }
    }
}
