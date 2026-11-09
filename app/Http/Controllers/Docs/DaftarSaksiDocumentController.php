<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\DaftarSaksiDocument\DaftarSaksiDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Witness;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class DaftarSaksiDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Tampilkan form pembuatan Daftar Saksi (S-38.1)
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
        $existingDoc = DaftarSaksiDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.daftar-saksi-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Ambil data pejabat penandatangan aktif
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

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

        // Ambil daftar petugas penyidik polres
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

        // ── Konsolidasi saksi perkara berdasarkan LP (witnesses, daftar_saksi, involved_peoples) ──
        $availableWitnesses = $this->getConsolidatedWitnesses($accidentId);
        $initialSaksiList = $availableWitnesses;

        // Ambil master referensi pekerjaan & agama
        $libJobs = DB::table('lib.jobs')->orderBy('name')->pluck('name')->toArray();
        if (empty($libJobs)) {
            $libJobs = ['PNS', 'Karyawan Swasta', 'Wiraswasta', 'Buruh', 'Pelajar', 'Mahasiswa', 'Petani', 'Pedagang', 'TNI', 'Polri', 'Supir'];
        }

        $libReligions = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

        $defaultLocation = $accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat');
        $defaultSatker = 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? ''));

        return view('docs.daftar-saksi-document.create', compact(
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'initialSaksiList',
            'availableWitnesses',
            'libJobs',
            'libReligions',
            'defaultLocation',
            'defaultSatker'
        ));
    }

    /**
     * Simpan data Daftar Saksi ke Database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->find($accidentId);

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
            'accident_id' => 'required',
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required',
            'dikeluarkan_di' => 'required',
            'satker_penyidik' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'jabatan_penandatangan' => 'required',
            'saksi' => 'required|array|min:1',
            'saksi.*.nama' => 'required',
            'saksi.*.no_identitas' => 'required',
            'saksi.*.kewarganegaraan' => 'required',
            'saksi.*.jenis_kelamin' => 'required',
            'saksi.*.pekerjaan' => 'required',
            'saksi.*.agama' => 'required',
            'saksi.*.alamat' => 'required',
            'saksi.*.keterangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'saksi.required' => 'Data Saksi wajib diisi minimal 1 data saksi.',
            'saksi.min' => 'Data Saksi wajib diisi minimal 1 data saksi.',
            'saksi.*.nama.required' => 'Kolom Nama pada Data Saksi wajib diisi.',
            'saksi.*.no_identitas.required' => 'Kolom No. Identitas pada Data Saksi wajib diisi.',
            'saksi.*.kewarganegaraan.required' => 'Kolom Kewarganegaraan pada Data Saksi wajib diisi.',
            'saksi.*.jenis_kelamin.required' => 'Kolom Jenis Kelamin pada Data Saksi wajib dipilih.',
            'saksi.*.pekerjaan.required' => 'Kolom Pekerjaan pada Data Saksi wajib diisi.',
            'saksi.*.agama.required' => 'Kolom Agama pada Data Saksi wajib dipilih.',
            'saksi.*.alamat.required' => 'Kolom Alamat pada Data Saksi wajib diisi.',
            'saksi.*.keterangan.required' => 'Kolom Keterangan pada Data Saksi wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Format List Saksi
            $rawSaksiList = $request->input('saksi', []);
            $cleanedSaksiList = [];
            $no = 1;

            if (is_array($rawSaksiList)) {
                foreach ($rawSaksiList as $s) {
                    $nama = trim($s['nama'] ?? '');
                    if ($nama !== '') {
                        $cleanedSaksiList[] = [
                            'no' => $no++,
                            'nama' => $nama,
                            'no_identitas' => trim($s['no_identitas'] ?? '') ?: '-',
                            'kewarganegaraan' => trim($s['kewarganegaraan'] ?? '') ?: '-',
                            'jenis_kelamin' => trim($s['jenis_kelamin'] ?? '') ?: '-',
                            'pekerjaan' => trim($s['pekerjaan'] ?? '') ?: '-',
                            'agama' => trim($s['agama'] ?? '') ?: '-',
                            'alamat' => trim($s['alamat'] ?? '') ?: '-',
                            'keterangan' => trim($s['keterangan'] ?? '') ?: '-',
                            'witness_id' => $s['witness_id'] ?? null,
                            'source' => $s['source'] ?? null,
                        ];
                    }
                }
            }

            if (empty($cleanedSaksiList)) {
                return redirect()->back()->with('error', 'Data Saksi wajib diisi minimal 1 data saksi.')->withInput();
            }

            $document = DaftarSaksiDocument::create([
                'id' => (string) Str::uuid(),
                'accident_id' => $accidentId,
                'document_category_id' => '0205',
                'status_id' => '2',
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : null,

                'daftar_saksi' => $cleanedSaksiList,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => preg_replace('/[^0-9]/', '', (string)$request->nrp_penandatangan) ?: $request->nrp_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: 'Penyidik / Penyidik Pembantu',

                'created_by_user_id' => Auth::id() ?: 1,
                'ip_addresses' => ['created_ip' => $request->ip()],
                'timestamps' => ['created_at' => Carbon::now()->toIso8601String()],
            ]);

            DB::commit();

            // Two-way sync ke tabel public.daftar_saksi (pasca-commit agar tidak memblokir simpan dokumen)
            $this->syncWitnessesToDaftarSaksi($accidentId, $cleanedSaksiList);

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Saksi (S-38.1) berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Daftar Saksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form pengubahan Daftar Saksi
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id, Request $request)
    {
        $document = DaftarSaksiDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

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

        $savedSaksiList = $document->daftar_saksi ?: [];

        // Konsolidasi saksi perkara LP untuk opsi penambahan saksi terdaftar
        $availableWitnesses = $this->getConsolidatedWitnesses($accidentId);

        // Ambil master referensi pekerjaan & agama
        $libJobs = DB::table('lib.jobs')->orderBy('name')->pluck('name')->toArray();
        if (empty($libJobs)) {
            $libJobs = ['PNS', 'Karyawan Swasta', 'Wiraswasta', 'Buruh', 'Pelajar', 'Mahasiswa', 'Petani', 'Pedagang', 'TNI', 'Polri', 'Supir'];
        }

        $libReligions = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

        $currentSignatoryName = trim($document->nama_penandatangan ?? '');

        return view('docs.daftar-saksi-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'savedSaksiList',
            'availableWitnesses',
            'libJobs',
            'libReligions',
            'currentSignatoryName'
        ));
    }

    /**
     * Perbarui data Daftar Saksi
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $document = DaftarSaksiDocument::findOrFail($id);
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
            'saksi' => 'required|array|min:1',
            'saksi.*.nama' => 'required',
            'saksi.*.no_identitas' => 'required',
            'saksi.*.kewarganegaraan' => 'required',
            'saksi.*.jenis_kelamin' => 'required',
            'saksi.*.pekerjaan' => 'required',
            'saksi.*.agama' => 'required',
            'saksi.*.alamat' => 'required',
            'saksi.*.keterangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'saksi.required' => 'Data Saksi wajib diisi minimal 1 data saksi.',
            'saksi.min' => 'Data Saksi wajib diisi minimal 1 data saksi.',
            'saksi.*.nama.required' => 'Kolom Nama pada Data Saksi wajib diisi.',
            'saksi.*.no_identitas.required' => 'Kolom No. Identitas pada Data Saksi wajib diisi.',
            'saksi.*.kewarganegaraan.required' => 'Kolom Kewarganegaraan pada Data Saksi wajib diisi.',
            'saksi.*.jenis_kelamin.required' => 'Kolom Jenis Kelamin pada Data Saksi wajib dipilih.',
            'saksi.*.pekerjaan.required' => 'Kolom Pekerjaan pada Data Saksi wajib diisi.',
            'saksi.*.agama.required' => 'Kolom Agama pada Data Saksi wajib dipilih.',
            'saksi.*.alamat.required' => 'Kolom Alamat pada Data Saksi wajib diisi.',
            'saksi.*.keterangan.required' => 'Kolom Keterangan pada Data Saksi wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $rawSaksiList = $request->input('saksi', []);
            $cleanedSaksiList = [];
            $no = 1;

            if (is_array($rawSaksiList)) {
                foreach ($rawSaksiList as $s) {
                    $nama = trim($s['nama'] ?? '');
                    if ($nama !== '') {
                        $cleanedSaksiList[] = [
                            'no' => $no++,
                            'nama' => $nama,
                            'no_identitas' => trim($s['no_identitas'] ?? '') ?: '-',
                            'kewarganegaraan' => trim($s['kewarganegaraan'] ?? '') ?: '-',
                            'jenis_kelamin' => trim($s['jenis_kelamin'] ?? '') ?: '-',
                            'pekerjaan' => trim($s['pekerjaan'] ?? '') ?: '-',
                            'agama' => trim($s['agama'] ?? '') ?: '-',
                            'alamat' => trim($s['alamat'] ?? '') ?: '-',
                            'keterangan' => trim($s['keterangan'] ?? '') ?: '-',
                            'witness_id' => $s['witness_id'] ?? null,
                            'source' => $s['source'] ?? null,
                        ];
                    }
                }
            }

            if (empty($cleanedSaksiList)) {
                return redirect()->back()->with('error', 'Data Saksi wajib diisi minimal 1 data saksi.')->withInput();
            }

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : $document->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : $document->tanggal_lp,

                'daftar_saksi' => $cleanedSaksiList,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => preg_replace('/[^0-9]/', '', (string)$request->nrp_penandatangan) ?: $request->nrp_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: $document->jabatan_penandatangan,

                'updated_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            // Two-way sync ke tabel public.daftar_saksi (pasca-commit)
            $this->syncWitnessesToDaftarSaksi($accidentId, $cleanedSaksiList);

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Saksi (S-38.1) berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Daftar Saksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Daftar Saksi (S-38.1)
     *
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id, Request $request = null)
    {
        try {
            $document = DaftarSaksiDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/daftar_saksi.docx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word daftar_saksi.docx tidak ditemukan.');
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

            // 2. Format 9 Kolom Saksi (Tabel S-38.1)
            $rawSaksi = $document->daftar_saksi ?: [];
            $blockSaksi = [];
            $i = 1;

            if (is_array($rawSaksi)) {
                foreach ($rawSaksi as $s) {
                    $blockSaksi[] = [
                        'saksi_no' => (string) ($i++),
                        'saksi_nama' => $esc($s['nama'] ?? '-'),
                        'saksi_identitas' => $esc($s['no_identitas'] ?? '-'),
                        'saksi_warga_negara' => $esc($s['kewarganegaraan'] ?? 'WNI'),
                        'saksi_gender' => $esc($s['jenis_kelamin'] ?? '-'),
                        'saksi_pekerjaan' => $esc($s['pekerjaan'] ?? '-'),
                        'saksi_agama' => $esc($s['agama'] ?? '-'),
                        'saksi_alamat' => $esc($s['alamat'] ?? '-'),
                        'saksi_keterangan' => $esc($s['keterangan'] ?? '-'),
                    ];
                }
            }

            if (empty($blockSaksi)) {
                $blockSaksi[] = [
                    'saksi_no' => '1',
                    'saksi_nama' => '-',
                    'saksi_identitas' => '-',
                    'saksi_warga_negara' => '-',
                    'saksi_gender' => '-',
                    'saksi_pekerjaan' => '-',
                    'saksi_agama' => '-',
                    'saksi_alamat' => '-',
                    'saksi_keterangan' => '-',
                ];
            }

            $templateProcessor->cloneRowAndSetValues('saksi_no', $blockSaksi);

            // 3. Pejabat Penandatangan (Penyidik / Penyidik Pembantu)
            $sigHead = $document->jabatan_penandatangan ?: 'Penyidik / Penyidik Pembantu';
            $templateProcessor->setValue('signatoryHeadText', $esc($sigHead));
            $templateProcessor->setValue('signatoryName', $esc($document->nama_penandatangan ?: '-'));
            $templateProcessor->setValue('signatoryRankName', $esc(strtoupper($document->pangkat_penandatangan ?: '-')));
            
            $cleanNrp = preg_replace('/[^0-9]/', '', (string)($document->nrp_penandatangan ?? ''));
            $templateProcessor->setValue('signatoryRegisterNumber', $esc($cleanNrp ?: '-'));

            // Simpan file temporary dan download
            $safeDocNo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document->nomor_surat ?: 'S_38_1');
            $filename = 'generate/' . Str::uuid() . '_Daftar_Saksi_' . $safeDocNo . '.docx';
            $fullPath = public_path($filename);
            $templateProcessor->saveAs($fullPath);

            return response()->download($fullPath, 'Daftar_Saksi_S38_1_' . $safeDocNo . '.docx')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh Daftar Saksi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus (Soft Delete) Dokumen Daftar Saksi
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        try {
            $document = DaftarSaksiDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Saksi (S-38.1) berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Dokumen Daftar Saksi: ' . $e->getMessage());
        }
    }

    /**
     * Konsolidasi daftar saksi unik pada perkara LP ini dari berbagai sumber (witnesses, daftar_saksi, involved_peoples)
     *
     * @param string $accidentId
     * @return array
     */
    private function getConsolidatedWitnesses($accidentId)
    {
        $consolidated = [];
        $seenNames = [];

        // 1. Dari tabel public.witnesses (IRSMS / DORS Olah TKP)
        try {
            $witnesses = DB::table('witnesses')
                ->leftJoin('lib.genders as lg', 'witnesses.gender_id', '=', 'lg.id')
                ->leftJoin('ref as rg', function ($join) {
                    $join->on('witnesses.gender_id', '=', 'rg.id')->where('rg.grp_id', '=', 'G01');
                })
                ->leftJoin('lib.religions as lr', 'witnesses.religion_id', '=', 'lr.id')
                ->leftJoin('ref as rr', function ($join) {
                    $join->on('witnesses.religion_id', '=', 'rr.id')->where('rr.grp_id', '=', 'R01');
                })
                ->leftJoin('lib.jobs as lj', 'witnesses.job_id', '=', 'lj.id')
                ->where('witnesses.accident_id', $accidentId)
                ->select(
                    'witnesses.*',
                    DB::raw("COALESCE(lg.name, rg.name) as gender_name"),
                    DB::raw("COALESCE(lr.name, rr.name) as religion_name"),
                    'lj.name as job_name'
                )
                ->get();

            foreach ($witnesses as $w) {
                $nameClean = trim($w->name ?? '');
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = 'Laki-laki';
                    $rawGender = strtoupper($w->gender_name ?? '');
                    if (str_contains($rawGender, 'PEREMPUAN') || str_contains($rawGender, 'WANITA')) {
                        $gender = 'Perempuan';
                    }

                    $consolidated[] = [
                        'source' => 'witnesses',
                        'source_id' => (string) ($w->id ?? ''),
                        'source_label' => 'Saksi TKP (IRSMS)',
                        'nama' => $nameClean,
                        'no_identitas' => trim($w->identity_number ?? '') ?: '-',
                        'kewarganegaraan' => !empty($w->nationality) ? trim($w->nationality) : 'WNI',
                        'jenis_kelamin' => $gender,
                        'pekerjaan' => ucwords(strtolower(trim($w->job_name ?? ''))) ?: '-',
                        'agama' => ucwords(strtolower(trim($w->religion_name ?? ''))) ?: 'Islam',
                        'alamat' => trim($w->address ?? '') ?: '-',
                        'keterangan' => trim($w->statement ?? '') ?: 'Saksi Fakta di TKP',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        // 2. Dari tabel public.daftar_saksi (Saksi Tambahan Penyidikan)
        try {
            $legacySaksi = DB::table('daftar_saksi')
                ->leftJoin('ref as ref_gender', function ($join) {
                    $join->on('daftar_saksi.gender', '=', 'ref_gender.id')->where('ref_gender.grp_id', '=', 'G01');
                })
                ->leftJoin('ref as ref_religion', function ($join) {
                    $join->on('daftar_saksi.religion', '=', 'ref_religion.id')->where('ref_religion.grp_id', '=', 'R01');
                })
                ->where('daftar_saksi.accident_id', $accidentId)
                ->select('daftar_saksi.*', 'ref_gender.name as gender_name', 'ref_religion.name as religion_name')
                ->get();

            foreach ($legacySaksi as $ls) {
                $nameClean = trim($ls->name ?? '');
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = 'Laki-laki';
                    $rawGender = strtoupper($ls->gender_name ?? $ls->gender ?? '');
                    if (str_contains($rawGender, 'PEREMPUAN') || str_contains($rawGender, 'WANITA')) {
                        $gender = 'Perempuan';
                    }

                    $consolidated[] = [
                        'source' => 'daftar_saksi',
                        'source_id' => (string) ($ls->id ?? ''),
                        'source_label' => 'Saksi Penyidikan',
                        'nama' => $nameClean,
                        'no_identitas' => '-',
                        'kewarganegaraan' => !empty($ls->citizen) ? trim($ls->citizen) : 'WNI',
                        'jenis_kelamin' => $gender,
                        'pekerjaan' => ucwords(strtolower(trim($ls->job ?? ''))) ?: '-',
                        'agama' => ucwords(strtolower(trim($ls->religion_name ?? $ls->religion ?? ''))) ?: 'Islam',
                        'alamat' => trim($ls->address ?? '') ?: '-',
                        'keterangan' => 'Saksi Fakta',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        // 3. Dari tabel public.involved_peoples (Orang terlibat di TKP non-tersangka)
        try {
            $involved = DB::table('involved_peoples')
                ->leftJoin('lib.genders as lg', 'involved_peoples.gender_id', '=', 'lg.id')
                ->leftJoin('lib.religions as lr', 'involved_peoples.religion_id', '=', 'lr.id')
                ->leftJoin('lib.jobs as lj', 'involved_peoples.job_id', '=', 'lj.id')
                ->where('involved_peoples.accident_id', $accidentId)
                ->where(function ($q) {
                    $q->whereNull('involved_peoples.class')
                      ->orWhere('involved_peoples.class', '!=', 'SUSPECT');
                })
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
                    $gender = '';
                    $rawGender = strtoupper($inv->gender_name ?? '');
                    if (str_contains($rawGender, 'PEREMPUAN') || str_contains($rawGender, 'WANITA')) {
                        $gender = 'Perempuan';
                    } elseif (str_contains($rawGender, 'LAKI') || str_contains($rawGender, 'PRIA')) {
                        $gender = 'Laki-laki';
                    }

                    $consolidated[] = [
                        'source' => 'involved_peoples',
                        'source_id' => (string) ($inv->id ?? ''),
                        'source_label' => 'Pihak Terlibat (' . ($inv->class ?: 'Saksi') . ')',
                        'nama' => $nameClean,
                        'no_identitas' => trim($inv->identity_number ?? '') ?: '',
                        'kewarganegaraan' => !empty($inv->nationality) ? trim($inv->nationality) : '',
                        'jenis_kelamin' => $gender,
                        'pekerjaan' => ucwords(strtolower(trim($inv->job_name ?? ''))) ?: '',
                        'agama' => ucwords(strtolower(trim($inv->religion_name ?? ''))) ?: '',
                        'alamat' => trim($inv->address ?? '') ?: '',
                        'keterangan' => 'Saksi / ' . ucwords(strtolower($inv->class ?: 'Terlibat')),
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        return $consolidated;
    }

    /**
     * Lakukan sinkronisasi dua arah ke tabel public.daftar_saksi
     * agar data saksi baru yang diinput pada form dapat digunakan oleh dokumen Mindik lain.
     */
    protected function syncWitnessesToDaftarSaksi($accidentId, array $saksiList)
    {
        if (empty($saksiList) || empty($accidentId)) {
            return;
        }

        foreach ($saksiList as $saksiItem) {
            $sName = trim($saksiItem['nama'] ?? '');
            if ($sName === '' || strtolower($sName) === 'saksi (belum diisi)') {
                continue;
            }

            try {
                $exists = DB::table('daftar_saksi')
                    ->where('accident_id', $accidentId)
                    ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($sName)])
                    ->exists();

                if (!$exists) {
                    $genderText = strtoupper(trim($saksiItem['jenis_kelamin'] ?? ''));
                    $genderCode = ($genderText === 'PEREMPUAN' || $genderText === 'WANITA') ? 'G0102' : 'G0101';

                    $relText = strtoupper(trim($saksiItem['agama'] ?? ''));
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

                    \App\Models\DaftarSaksi::create([
                        'accident_id' => $accidentId,
                        'name' => $sName,
                        'gender' => $genderCode,
                        'city' => '-',
                        'birth_date' => '1990-01-01',
                        'religion' => $relCode,
                        'job' => trim($saksiItem['pekerjaan'] ?? '') ?: '-',
                        'education' => '-',
                        'phone' => '-',
                        'citizen' => trim($saksiItem['kewarganegaraan'] ?? '') ?: 'WNI',
                        'address' => trim($saksiItem['alamat'] ?? '') ?: '-',
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal sync saksi ke public.daftar_saksi: ' . $e->getMessage());
            }
        }
    }
}
