<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\DaftarAhliDocument\DaftarAhliDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class DaftarAhliDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Tampilkan form pembuatan Daftar Ahli (S-38.2)
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
        $existingDoc = DaftarAhliDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.daftar-ahli-document.edit', [
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

        // Preset Bidang Keahlian dan Master Agama
        $expertJobs = [
            'Dokter Spesialis Forensik / Kedokteran Kehakiman',
            'Ahli Rekonstruksi Kecelakaan Lalu Lintas (TAA Korlantas)',
            'Ahli Kelaikan Kendaraan Bermotor (Dinas Perhubungan)',
            'Ahli Mekanik & Komponen Otomotif (APM / Agen Pemegang Merk)',
            'Ahli Rekayasa Lalu Lintas & Geometrik Jalan (PUPR / BPTD)',
            'Ahli Toksikologi & Kimia Forensik (Puslabfor Polri)',
            'Ahli Digital Forensik & Rekaman CCTV (Puslabfor Polri)',
            'Ahli Hukum Pidana Lalu Lintas',
            'Ahli Psikologi Forensik',
            'Dokter Umum / Pemeriksa Rumah Sakit',
        ];

        $libReligions = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

        $defaultLocation = $accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat');
        $defaultSatker = 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? ''));

        return view('docs.daftar-ahli-document.create', compact(
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'expertJobs',
            'libReligions',
            'defaultLocation',
            'defaultSatker'
        ));
    }

    /**
     * Simpan data Daftar Ahli ke Database
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
            'ahli' => 'required|array|min:1',
            'ahli.*.nama' => 'required',
            'ahli.*.no_identitas' => 'required',
            'ahli.*.kewarganegaraan' => 'required',
            'ahli.*.jenis_kelamin' => 'required',
            'ahli.*.pekerjaan' => 'required',
            'ahli.*.agama' => 'required',
            'ahli.*.alamat' => 'required',
            'ahli.*.keterangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'ahli.required' => 'Data Ahli wajib diisi minimal 1 data ahli.',
            'ahli.min' => 'Data Ahli wajib diisi minimal 1 data ahli.',
            'ahli.*.nama.required' => 'Kolom Nama pada Data Ahli wajib diisi.',
            'ahli.*.no_identitas.required' => 'Kolom No. Identitas pada Data Ahli wajib diisi.',
            'ahli.*.kewarganegaraan.required' => 'Kolom Kewarganegaraan pada Data Ahli wajib diisi.',
            'ahli.*.jenis_kelamin.required' => 'Kolom Jenis Kelamin pada Data Ahli wajib dipilih.',
            'ahli.*.pekerjaan.required' => 'Kolom Pekerjaan pada Data Ahli wajib diisi.',
            'ahli.*.agama.required' => 'Kolom Agama pada Data Ahli wajib dipilih.',
            'ahli.*.alamat.required' => 'Kolom Alamat pada Data Ahli wajib diisi.',
            'ahli.*.keterangan.required' => 'Kolom Keterangan pada Data Ahli wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Format List Ahli
            $rawAhliList = $request->input('ahli', []);
            $cleanedAhliList = [];
            $no = 1;

            if (is_array($rawAhliList)) {
                foreach ($rawAhliList as $a) {
                    $nama = trim($a['nama'] ?? '');
                    if ($nama !== '') {
                        $cleanedAhliList[] = [
                            'no' => $no++,
                            'nama' => $nama,
                            'no_identitas' => trim($a['no_identitas'] ?? '') ?: '-',
                            'kewarganegaraan' => trim($a['kewarganegaraan'] ?? '') ?: '-',
                            'jenis_kelamin' => trim($a['jenis_kelamin'] ?? '') ?: '-',
                            'pekerjaan' => trim($a['pekerjaan'] ?? '') ?: '-',
                            'agama' => trim($a['agama'] ?? '') ?: '-',
                            'alamat' => trim($a['alamat'] ?? '') ?: '-',
                            'keterangan' => trim($a['keterangan'] ?? '') ?: 'Saksi Ahli',
                        ];
                    }
                }
            }

            if (empty($cleanedAhliList)) {
                return redirect()->back()->with('error', 'Data Ahli wajib diisi minimal 1 data ahli.')->withInput();
            }

            // Bersihkan NRP hanya deretan angka (tanpa _ADM)
            $cleanNrp = preg_replace('/[^0-9]/', '', (string)$request->nrp_penandatangan) ?: $request->nrp_penandatangan;

            $document = DaftarAhliDocument::create([
                'id' => (string) Str::uuid(),
                'accident_id' => $accidentId,
                'document_category_id' => '0217',
                'status_id' => '2',
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : null,

                'daftar_ahli' => $cleanedAhliList,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => $cleanNrp,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: 'Penyidik / Penyidik Pembantu',

                'created_by_user_id' => Auth::id() ?: 1,
                'ip_addresses' => ['created_ip' => $request->ip()],
                'timestamps' => ['created_at' => Carbon::now()->toIso8601String()],
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Ahli (S-38.2) berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Daftar Ahli: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form pengubahan Daftar Ahli
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id, Request $request)
    {
        $document = DaftarAhliDocument::findOrFail($id);
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

        $savedAhliList = $document->daftar_ahli ?: [];

        $expertJobs = [
            'Dokter Spesialis Forensik / Kedokteran Kehakiman',
            'Ahli Rekonstruksi Kecelakaan Lalu Lintas (TAA Korlantas)',
            'Ahli Kelaikan Kendaraan Bermotor (Dinas Perhubungan)',
            'Ahli Mekanik & Komponen Otomotif (APM / Agen Pemegang Merk)',
            'Ahli Rekayasa Lalu Lintas & Geometrik Jalan (PUPR / BPTD)',
            'Ahli Toksikologi & Kimia Forensik (Puslabfor Polri)',
            'Ahli Digital Forensik & Rekaman CCTV (Puslabfor Polri)',
            'Ahli Hukum Pidana Lalu Lintas',
            'Ahli Psikologi Forensik',
            'Dokter Umum / Pemeriksa Rumah Sakit',
        ];

        $libReligions = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $currentSignatoryName = trim($document->nama_penandatangan ?? '');

        return view('docs.daftar-ahli-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'savedAhliList',
            'expertJobs',
            'libReligions',
            'currentSignatoryName'
        ));
    }

    /**
     * Perbarui data Daftar Ahli
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $document = DaftarAhliDocument::findOrFail($id);
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
            'ahli' => 'required|array|min:1',
            'ahli.*.nama' => 'required',
            'ahli.*.no_identitas' => 'required',
            'ahli.*.kewarganegaraan' => 'required',
            'ahli.*.jenis_kelamin' => 'required',
            'ahli.*.pekerjaan' => 'required',
            'ahli.*.agama' => 'required',
            'ahli.*.alamat' => 'required',
            'ahli.*.keterangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
            'ahli.required' => 'Data Ahli wajib diisi minimal 1 data ahli.',
            'ahli.min' => 'Data Ahli wajib diisi minimal 1 data ahli.',
            'ahli.*.nama.required' => 'Kolom Nama pada Data Ahli wajib diisi.',
            'ahli.*.no_identitas.required' => 'Kolom No. Identitas pada Data Ahli wajib diisi.',
            'ahli.*.kewarganegaraan.required' => 'Kolom Kewarganegaraan pada Data Ahli wajib diisi.',
            'ahli.*.jenis_kelamin.required' => 'Kolom Jenis Kelamin pada Data Ahli wajib dipilih.',
            'ahli.*.pekerjaan.required' => 'Kolom Pekerjaan pada Data Ahli wajib diisi.',
            'ahli.*.agama.required' => 'Kolom Agama pada Data Ahli wajib dipilih.',
            'ahli.*.alamat.required' => 'Kolom Alamat pada Data Ahli wajib diisi.',
            'ahli.*.keterangan.required' => 'Kolom Keterangan pada Data Ahli wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $rawAhliList = $request->input('ahli', []);
            $cleanedAhliList = [];
            $no = 1;

            if (is_array($rawAhliList)) {
                foreach ($rawAhliList as $a) {
                    $nama = trim($a['nama'] ?? '');
                    if ($nama !== '') {
                        $cleanedAhliList[] = [
                            'no' => $no++,
                            'nama' => $nama,
                            'no_identitas' => trim($a['no_identitas'] ?? '') ?: '-',
                            'kewarganegaraan' => trim($a['kewarganegaraan'] ?? '') ?: '-',
                            'jenis_kelamin' => trim($a['jenis_kelamin'] ?? '') ?: '-',
                            'pekerjaan' => trim($a['pekerjaan'] ?? '') ?: '-',
                            'agama' => trim($a['agama'] ?? '') ?: '-',
                            'alamat' => trim($a['alamat'] ?? '') ?: '-',
                            'keterangan' => trim($a['keterangan'] ?? '') ?: 'Saksi Ahli',
                        ];
                    }
                }
            }

            if (empty($cleanedAhliList)) {
                return redirect()->back()->with('error', 'Data Ahli wajib diisi minimal 1 data ahli.')->withInput();
            }

            // Bersihkan NRP hanya deretan angka
            $cleanNrp = preg_replace('/[^0-9]/', '', (string)$request->nrp_penandatangan) ?: $request->nrp_penandatangan;

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : $document->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : $document->tanggal_lp,

                'daftar_ahli' => $cleanedAhliList,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => $cleanNrp,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: $document->jabatan_penandatangan,

                'updated_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Ahli (S-38.2) berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Daftar Ahli: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Daftar Ahli (S-38.2)
     *
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id, Request $request = null)
    {
        try {
            $document = DaftarAhliDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/daftar_ahli.docx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word daftar_ahli.docx tidak ditemukan.');
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

            // 2. Format 9 Kolom Ahli (Tabel S-38.2)
            $rawAhli = $document->daftar_ahli ?: [];
            $blockAhli = [];
            $i = 1;

            if (is_array($rawAhli)) {
                foreach ($rawAhli as $a) {
                    $blockAhli[] = [
                        'ahli_no' => (string) ($i++),
                        'ahli_nama' => $esc($a['nama'] ?? '-'),
                        'ahli_identitas' => $esc($a['no_identitas'] ?? '-'),
                        'ahli_warga_negara' => $esc($a['kewarganegaraan'] ?? 'WNI'),
                        'ahli_gender' => $esc($a['jenis_kelamin'] ?? '-'),
                        'ahli_pekerjaan' => $esc($a['pekerjaan'] ?? '-'),
                        'ahli_agama' => $esc($a['agama'] ?? '-'),
                        'ahli_alamat' => $esc($a['alamat'] ?? '-'),
                        'ahli_keterangan' => $esc($a['keterangan'] ?? '-'),
                    ];
                }
            }

            if (empty($blockAhli)) {
                $blockAhli[] = [
                    'ahli_no' => '1',
                    'ahli_nama' => '-',
                    'ahli_identitas' => '-',
                    'ahli_warga_negara' => '-',
                    'ahli_gender' => '-',
                    'ahli_pekerjaan' => '-',
                    'ahli_agama' => '-',
                    'ahli_alamat' => '-',
                    'ahli_keterangan' => '-',
                ];
            }

            $templateProcessor->cloneRowAndSetValues('ahli_no', $blockAhli);

            // 3. Pejabat Penandatangan (Penyidik / Penyidik Pembantu)
            $sigHead = $document->jabatan_penandatangan ?: 'Penyidik / Penyidik Pembantu';
            $templateProcessor->setValue('signatoryHeadText', $esc($sigHead));
            $templateProcessor->setValue('signatoryName', $esc($document->nama_penandatangan ?: '-'));
            $templateProcessor->setValue('signatoryRankName', $esc(strtoupper($document->pangkat_penandatangan ?: '-')));

            $cleanNrp = preg_replace('/[^0-9]/', '', (string)($document->nrp_penandatangan ?? ''));
            $templateProcessor->setValue('signatoryRegisterNumber', $esc($cleanNrp ?: '-'));

            // Simpan file temporary dan download
            $safeDocNo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document->nomor_surat ?: 'S_38_2');
            $filename = 'generate/' . Str::uuid() . '_Daftar_Ahli_' . $safeDocNo . '.docx';
            $fullPath = public_path($filename);
            $templateProcessor->saveAs($fullPath);

            return response()->download($fullPath, 'Daftar_Ahli_S38_2_' . $safeDocNo . '.docx')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh Daftar Ahli: ' . $e->getMessage());
        }
    }

    /**
     * Hapus (Soft Delete) Dokumen Daftar Ahli
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        try {
            $document = DaftarAhliDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Ahli (S-38.2) berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Dokumen Daftar Ahli: ' . $e->getMessage());
        }
    }
}

