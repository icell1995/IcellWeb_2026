<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPerintahPenangkapanDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Tampilkan form pembuatan Surat Perintah Penangkapan (S-10.1.1)
     */
    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?: $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        // Jika sudah ada dokumen, arahkan ke edit
        $existingDoc = SuratPerintahPenangkapanDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.surat-perintah-penangkapan-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Ambil pejabat penandatangan aktif
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

        // Petugas Polres untuk pelaksana perintah
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

        // Cari dokumen rujukan terkait
        $sprindik = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->latest()->first();
        $springas = SuratPerintahTugasDocument::where('accident_id', $accidentId)->latest()->first();
        $sTapTersangka = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)->latest()->first();

        // Tersangka yang tersedia
        $availableSuspects = $this->getConsolidatedSuspects($accidentId);
        $firstSuspect = !empty($availableSuspects) ? $availableSuspects[0] : null;

        // Kopstuk
        $poldaName = $accident->polres->polda->name ?? 'KEPOLISIAN DAERAH';
        $resorName = (in_array($accident->polres->id ?? '', ['1114']))
            ? 'DIREKTORAT LALU LINTAS'
            : 'KEPOLISIAN RESOR ' . strtoupper($accident->polres->full_name ?? $accident->polres->name ?? '');
        $resorAddress = ucwords(strtolower(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_district ?? '')));

        // Format tanggal hari ini
        $now = Carbon::now();
        $todayFormatted = $now->format('Y-m-d');
        $validUntilFormatted = $now->copy()->addDay()->format('Y-m-d');

        // Hari dalam bahasa Indonesia
        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $namaHariIni = $hariMap[$now->format('l')] ?? $now->format('l');

        // No SP.Kap default
        $monthRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $curMonthRomawi = $monthRomawi[(int) $now->format('m')] ?? 'X';
        $defaultNomorSpKap = "SP.Kap/01/{$curMonthRomawi}/" . $now->format('Y') . "/Satlantas";

        // Default Officers (minimal 2 petugas jika ada)
        $defaultOfficers = [];
        if ($officers->count() >= 2) {
            $defaultOfficers[] = [
                'nama' => $officers[0]->full_name ?? ($officers[0]->name ?? ''),
                'pangkat' => $officers[0]->rank->name ?? ($officers[0]->rank_name ?? '-'),
                'nrp' => $officers[0]->nrp ?? '-',
                'jabatan' => $this->getOfficerPositionName($officers[0], 'Penyidik'),
            ];
            $defaultOfficers[] = [
                'nama' => $officers[1]->full_name ?? ($officers[1]->name ?? ''),
                'pangkat' => $officers[1]->rank->name ?? ($officers[1]->rank_name ?? '-'),
                'nrp' => $officers[1]->nrp ?? '-',
                'jabatan' => $this->getOfficerPositionName($officers[1], 'Penyidik Pembantu'),
            ];
        } else {
            $defaultOfficers[] = [
                'nama' => '',
                'pangkat' => '',
                'nrp' => '',
                'jabatan' => 'Penyidik',
            ];
        }

        return view('docs.surat-perintah-penangkapan-document.create', compact(
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'sprindik',
            'springas',
            'sTapTersangka',
            'availableSuspects',
            'firstSuspect',
            'poldaName',
            'resorName',
            'resorAddress',
            'todayFormatted',
            'validUntilFormatted',
            'namaHariIni',
            'defaultNomorSpKap',
            'defaultOfficers'
        ));
    }

    /**
     * Simpan data Surat Perintah Penangkapan ke database
     */
    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.')->withInput();
        }

        DB::beginTransaction();
        try {
            // Ambil petugas repeater
            $officersInput = $request->input('officers', []);
            $cleanedOfficers = [];
            if (is_array($officersInput)) {
                foreach ($officersInput as $off) {
                    if (!empty($off['nama'])) {
                        $cleanedOfficers[] = [
                            'nama' => trim($off['nama']),
                            'pangkat' => trim($off['pangkat'] ?? ''),
                            'nrp' => trim($off['nrp'] ?? ''),
                            'jabatan' => $this->cleanOfficerPositionString($off['jabatan'] ?? '', 'Penyidik'),
                        ];
                    }
                }
            }

            // Masa berlaku
            $berlakuSampai = $request->input('berlaku_sampai');
            if ($request->input('berlaku_sampai_date')) {
                $berlakuSampai = Carbon::parse($request->input('berlaku_sampai_date'))->locale('id')->translatedFormat('d F Y');
            }

            $doc = SuratPerintahPenangkapanDocument::create([
                'accident_id' => $accidentId,
                'document_category_id' => '0301',
                'status_id' => '2',

                'kopstuk_1' => $request->kopstuk_1,
                'kopstuk_2' => $request->kopstuk_2,
                'kopstuk_3' => $request->kopstuk_3,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,

                'pertimbangan' => $request->pertimbangan,

                'dasar_kuhap' => $request->dasar_kuhap,
                'dasar_polri' => $request->dasar_polri,
                'dasar_uu_pidana' => $request->dasar_uu_pidana,
                'dasar_lp_nomor' => $request->dasar_lp_nomor,
                'dasar_lp_tanggal' => $request->dasar_lp_tanggal,
                'dasar_sp_sidik_nomor' => $request->dasar_sp_sidik_nomor,
                'dasar_sp_sidik_tanggal' => $request->dasar_sp_sidik_tanggal,
                'dasar_sp_gas_nomor' => $request->dasar_sp_gas_nomor,
                'dasar_sp_gas_tanggal' => $request->dasar_sp_gas_tanggal,
                'dasar_sp_tap_nomor' => $request->dasar_sp_tap_nomor,
                'dasar_sp_tap_tanggal' => $request->dasar_sp_tap_tanggal,
                'dasar_sp_tap_nama' => $request->dasar_sp_tap_nama,

                'officers' => $cleanedOfficers,

                'tersangka_id' => $request->tersangka_id ?: null,
                'tersangka_nama' => $request->tersangka_nama,
                'tersangka_nik' => $request->tersangka_nik,
                'tersangka_kewarganegaraan' => $request->tersangka_kewarganegaraan ?: 'Indonesia',
                'tersangka_jenis_kelamin' => $request->tersangka_jenis_kelamin,
                'tersangka_tempat_lahir' => $request->tersangka_tempat_lahir,
                'tersangka_tanggal_lahir' => $request->tersangka_tanggal_lahir,
                'tersangka_pekerjaan' => $request->tersangka_pekerjaan,
                'tersangka_agama' => $request->tersangka_agama,
                'tersangka_alamat' => $request->tersangka_alamat,

                'uraian_tindak_pidana' => $request->uraian_tindak_pidana,
                'pasal_pidana' => $request->pasal_pidana,
                'uu_pidana_nomor' => $request->uu_pidana_nomor,
                'uu_pidana_tahun' => $request->uu_pidana_tahun,
                'uu_pidana_tentang' => $request->uu_pidana_tentang,
                'tkp' => $request->tkp,
                'waktu_kejadian' => $request->waktu_kejadian,

                'perintah_penggeledahan' => $request->perintah_penggeledahan,
                'berlaku_mulai' => $request->berlaku_mulai,
                'berlaku_sampai' => $berlakuSampai,
                'perintah_laporan' => $request->perintah_laporan,

                'penerima_nama' => $request->penerima_nama,
                'penerima_pangkat' => $request->penerima_pangkat,
                'penerima_nrp' => $request->penerima_nrp,
                'penerima_jabatan' => $this->cleanOfficerPositionString($request->penerima_jabatan, 'Penyidik Pembantu'),

                'atasan_nama' => $request->atasan_nama,
                'atasan_pangkat' => $request->atasan_pangkat,
                'atasan_nrp' => $request->atasan_nrp,
                'atasan_jabatan' => $this->cleanOfficerPositionString($request->atasan_jabatan, 'ATASAN PENYIDIK'),

                'penyerahan_hari' => $request->penyerahan_hari,
                'penyerahan_tanggal' => $request->penyerahan_tanggal,
                'yang_menerima_nama' => $request->yang_menerima_nama,
                'yang_menyerahkan_nama' => $request->yang_menyerahkan_nama,
                'yang_menyerahkan_pangkat' => $request->yang_menyerahkan_pangkat,
                'yang_menyerahkan_nrp' => $request->yang_menyerahkan_nrp,

                'created_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            if ($request->input('action') === 'save_and_download') {
                return redirect()->route('doc.surat-perintah-penangkapan-document.download', [
                    'id' => $doc->id,
                    'accident_id' => $accidentId,
                ]);
            }

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Penangkapan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Surat Perintah Penangkapan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form edit Surat Perintah Penangkapan
     */
    public function edit($id, Request $request)
    {
        $document = SuratPerintahPenangkapanDocument::findOrFail($id);
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

        $availableSuspects = $this->getConsolidatedSuspects($accidentId);

        return view('docs.surat-perintah-penangkapan-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'availableSuspects'
        ));
    }

    /**
     * Update data Surat Perintah Penangkapan
     */
    public function update(Request $request, $id)
    {
        $document = SuratPerintahPenangkapanDocument::findOrFail($id);
        $accidentId = $document->accident_id;

        DB::beginTransaction();
        try {
            $officersInput = $request->input('officers', []);
            $cleanedOfficers = [];
            if (is_array($officersInput)) {
                foreach ($officersInput as $off) {
                    if (!empty($off['nama'])) {
                        $cleanedOfficers[] = [
                            'nama' => trim($off['nama']),
                            'pangkat' => trim($off['pangkat'] ?? ''),
                            'nrp' => trim($off['nrp'] ?? ''),
                            'jabatan' => $this->cleanOfficerPositionString($off['jabatan'] ?? '', 'Penyidik'),
                        ];
                    }
                }
            }

            $berlakuSampai = $request->input('berlaku_sampai');
            if ($request->input('berlaku_sampai_date')) {
                $berlakuSampai = Carbon::parse($request->input('berlaku_sampai_date'))->locale('id')->translatedFormat('d F Y');
            }

            $document->update([
                'kopstuk_1' => $request->kopstuk_1,
                'kopstuk_2' => $request->kopstuk_2,
                'kopstuk_3' => $request->kopstuk_3,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,

                'pertimbangan' => $request->pertimbangan,

                'dasar_kuhap' => $request->dasar_kuhap,
                'dasar_polri' => $request->dasar_polri,
                'dasar_uu_pidana' => $request->dasar_uu_pidana,
                'dasar_lp_nomor' => $request->dasar_lp_nomor,
                'dasar_lp_tanggal' => $request->dasar_lp_tanggal,
                'dasar_sp_sidik_nomor' => $request->dasar_sp_sidik_nomor,
                'dasar_sp_sidik_tanggal' => $request->dasar_sp_sidik_tanggal,
                'dasar_sp_gas_nomor' => $request->dasar_sp_gas_nomor,
                'dasar_sp_gas_tanggal' => $request->dasar_sp_gas_tanggal,
                'dasar_sp_tap_nomor' => $request->dasar_sp_tap_nomor,
                'dasar_sp_tap_tanggal' => $request->dasar_sp_tap_tanggal,
                'dasar_sp_tap_nama' => $request->dasar_sp_tap_nama,

                'officers' => $cleanedOfficers,

                'tersangka_id' => $request->tersangka_id ?: null,
                'tersangka_nama' => $request->tersangka_nama,
                'tersangka_nik' => $request->tersangka_nik,
                'tersangka_kewarganegaraan' => $request->tersangka_kewarganegaraan ?: 'Indonesia',
                'tersangka_jenis_kelamin' => $request->tersangka_jenis_kelamin,
                'tersangka_tempat_lahir' => $request->tersangka_tempat_lahir,
                'tersangka_tanggal_lahir' => $request->tersangka_tanggal_lahir,
                'tersangka_pekerjaan' => $request->tersangka_pekerjaan,
                'tersangka_agama' => $request->tersangka_agama,
                'tersangka_alamat' => $request->tersangka_alamat,

                'uraian_tindak_pidana' => $request->uraian_tindak_pidana,
                'pasal_pidana' => $request->pasal_pidana,
                'uu_pidana_nomor' => $request->uu_pidana_nomor,
                'uu_pidana_tahun' => $request->uu_pidana_tahun,
                'uu_pidana_tentang' => $request->uu_pidana_tentang,
                'tkp' => $request->tkp,
                'waktu_kejadian' => $request->waktu_kejadian,

                'perintah_penggeledahan' => $request->perintah_penggeledahan,
                'berlaku_mulai' => $request->berlaku_mulai,
                'berlaku_sampai' => $berlakuSampai,
                'perintah_laporan' => $request->perintah_laporan,

                'penerima_nama' => $request->penerima_nama,
                'penerima_pangkat' => $request->penerima_pangkat,
                'penerima_nrp' => $request->penerima_nrp,
                'penerima_jabatan' => $this->cleanOfficerPositionString($request->penerima_jabatan, 'Penyidik Pembantu'),

                'atasan_nama' => $request->atasan_nama,
                'atasan_pangkat' => $request->atasan_pangkat,
                'atasan_nrp' => $request->atasan_nrp,
                'atasan_jabatan' => $this->cleanOfficerPositionString($request->atasan_jabatan, 'ATASAN PENYIDIK'),

                'penyerahan_hari' => $request->penyerahan_hari,
                'penyerahan_tanggal' => $request->penyerahan_tanggal,
                'yang_menerima_nama' => $request->yang_menerima_nama,
                'yang_menyerahkan_nama' => $request->yang_menyerahkan_nama,
                'yang_menyerahkan_pangkat' => $request->yang_menyerahkan_pangkat,
                'yang_menyerahkan_nrp' => $request->yang_menyerahkan_nrp,

                'updated_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            if ($request->input('action') === 'save_and_download') {
                return redirect()->route('doc.surat-perintah-penangkapan-document.download', [
                    'id' => $document->id,
                    'accident_id' => $accidentId,
                ]);
            }

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Penangkapan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Surat Perintah Penangkapan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Surat Perintah Penangkapan (S-10.1.1)
     */
    public function download($id, Request $request)
    {
        try {
            $document = SuratPerintahPenangkapanDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/surat_perintah_penangkapan.docx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word surat_perintah_penangkapan.docx tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templateFile);

            $sanitizeNoUrl = function($str) {
                if (!$str || !is_string($str)) return $str;
                return str_replace('/', "/\u{200B}", $str);
            };

            // 1. Kop Surat
            $templateProcessor->setValue('kopstuk_1', $document->kopstuk_1 ?: ($accident->polres->polda->name ?? 'KEPOLISIAN DAERAH'));
            $templateProcessor->setValue('kopstuk_2', $document->kopstuk_2 ?: 'DIREKTORAT LALU LINTAS');
            $templateProcessor->setValue('kopstuk_3', $document->kopstuk_3 ?: ($accident->polres->address ?? '-'));

            // 2. Nomor Dokumen & Tempat/Tanggal
            $nomorSurat = $document->nomor_surat ?: '-';
            // Bersihkan jika nomor surat mengandung prefix SP.Kap/ agar tidak dobel
            $cleanNomorSpKap = preg_replace('/^SP\.Kap\/?/i', '', $nomorSurat);
            $templateProcessor->setValue('nomor_sp_kap', $sanitizeNoUrl($cleanNomorSpKap));

            $templateProcessor->setValue('dikeluarkan_di', $document->dikeluarkan_di ?: ($accident->polres->polres_district ?? 'Tempat'));
            $templateProcessor->setValue('tanggal_surat', $this->formatIndonesianDate($document->tanggal_surat));

            // 3. Pertimbangan
            $templateProcessor->setValue('pertimbangan', $document->pertimbangan ?: 'bahwa untuk kepentingan penyidikan, terhadap Tersangka yang dikuatirkan melarikan diri atau merusak barang bukti atau menghilangkan barang bukti harus dilakukan penangkapan, maka perlu dikeluarkan surat perintah.');

            // 4. Dasar Hukum
            $templateProcessor->setValue('dasar_kuhap', $document->dasar_kuhap ?: 'Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d, Pasal 16, Pasal 17, Pasal 18, Pasal 19 dan Pasal 37 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana');
            $templateProcessor->setValue('dasar_polri', $document->dasar_polri ?: 'Pasal 14 ayat (1) huruf g pasal 16 ayat (1) Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia');
            $templateProcessor->setValue('dasar_uu_pidana', $document->dasar_uu_pidana ?: 'Pasal 310 Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan');

            $templateProcessor->setValue('dasar_lp_nomor', $sanitizeNoUrl($document->dasar_lp_nomor ?: ($accident->no_lp ?: '-')));
            $templateProcessor->setValue('dasar_lp_tanggal', $this->formatIndonesianDate($document->dasar_lp_tanggal ?: $accident->lp_date));

            $templateProcessor->setValue('dasar_sp_sidik_nomor', $sanitizeNoUrl($document->dasar_sp_sidik_nomor ?: '-'));
            $templateProcessor->setValue('dasar_sp_sidik_tanggal', $this->formatIndonesianDate($document->dasar_sp_sidik_tanggal));

            $templateProcessor->setValue('dasar_sp_gas_nomor', $sanitizeNoUrl($document->dasar_sp_gas_nomor ?: '-'));
            $templateProcessor->setValue('dasar_sp_gas_tanggal', $this->formatIndonesianDate($document->dasar_sp_gas_tanggal));

            $templateProcessor->setValue('dasar_sp_tap_nomor', $sanitizeNoUrl($document->dasar_sp_tap_nomor ?: '-'));
            $templateProcessor->setValue('dasar_sp_tap_tanggal', $this->formatIndonesianDate($document->dasar_sp_tap_tanggal));
            $templateProcessor->setValue('dasar_sp_tap_nama', strtoupper($document->dasar_sp_tap_nama ?: ($document->tersangka_nama ?: '-')));

            // 5. Kepada (Petugas Pelaksana via cloneRow)
            $officers = $document->officers;
            $blockOfficers = [];
            if (!empty($officers) && is_array($officers)) {
                $no = 1;
                foreach ($officers as $off) {
                    $nama = strtoupper($off['nama'] ?? '');
                    $pangkat = $off['pangkat'] ?? '';
                    $nrp = !empty($off['nrp']) && $off['nrp'] !== '-' ? 'NRP ' . $off['nrp'] : '';
                    $jabatan = $this->cleanOfficerPositionString($off['jabatan'] ?? '', 'Penyidik');

                    $header = trim("{$pangkat} {$nama} {$nrp}");
                    $blockOfficers[] = [
                        'kepada_label' => $no === 1 ? 'Kepada' : '',
                        'kepada_colon' => $no === 1 ? ':' : '',
                        'officer_no' => (string) $no,
                        'officer_header' => $header,
                        'officer_position' => $jabatan ?: 'Penyidik',
                    ];
                    $no++;
                }
            }
            if (empty($blockOfficers)) {
                $blockOfficers[] = [
                    'kepada_label' => 'Kepada',
                    'kepada_colon' => ':',
                    'officer_no' => '1',
                    'officer_header' => '-',
                    'officer_position' => 'Penyidik',
                ];
            }
            $templateProcessor->cloneRowAndSetValues('officer_no', $blockOfficers);

            // 6. Untuk: Identitas Tersangka
            $templateProcessor->setValue('tersangka_nama', strtoupper($document->tersangka_nama ?: '-'));
            $templateProcessor->setValue('tersangka_nik', $document->tersangka_nik ?: '-');
            $templateProcessor->setValue('tersangka_kewarganegaraan', $document->tersangka_kewarganegaraan ?: 'Indonesia');
            $templateProcessor->setValue('tersangka_jenis_kelamin', $document->tersangka_jenis_kelamin ?: '-');

            $tglLahirFormatted = $this->formatIndonesianDate($document->tersangka_tanggal_lahir);
            $ttl = ($document->tersangka_tempat_lahir ? $document->tersangka_tempat_lahir . ', ' : '') . $tglLahirFormatted;
            $templateProcessor->setValue('tersangka_ttl', $ttl ?: '-');

            $templateProcessor->setValue('tersangka_pekerjaan', $document->tersangka_pekerjaan ?: '-');
            $templateProcessor->setValue('tersangka_agama', $document->tersangka_agama ?: '-');
            $templateProcessor->setValue('tersangka_alamat', $document->tersangka_alamat ?: '-');

            // Uraian Pidana & TKP
            $templateProcessor->setValue('uraian_tindak_pidana', $document->uraian_tindak_pidana ?: 'kejahatan lalu lintas yang mengakibatkan kecelakaan');
            $templateProcessor->setValue('pasal_pidana', $document->pasal_pidana ?: '310 ayat (4)');
            $templateProcessor->setValue('uu_pidana_nomor', $document->uu_pidana_nomor ?: '22');
            $templateProcessor->setValue('uu_pidana_tahun', $document->uu_pidana_tahun ?: '2009');
            $templateProcessor->setValue('uu_pidana_tentang', $document->uu_pidana_tentang ?: 'Lalu Lintas dan Angkutan Jalan');
            $templateProcessor->setValue('tkp', $document->tkp ?: ($accident->address_accident_1 ?: '-'));

            $waktuKejadian = $document->waktu_kejadian;
            $templateProcessor->setValue('hari_kejadian', $waktuKejadian ?: '-');
            $templateProcessor->setValue('tanggal_kejadian', $this->formatIndonesianDate($accident->date_accident));
            $templateProcessor->setValue('kurun_waktu', '-');

            // Ketentuan Perintah
            $templateProcessor->setValue('perintah_penggeledahan', $document->perintah_penggeledahan ?: 'melakukan penggeledahan badan/pakaian Tersangka');
            $templateProcessor->setValue('berlaku_sampai', $document->berlaku_sampai ?: '-');
            $templateProcessor->setValue('perintah_laporan', $document->perintah_laporan ?: 'setelah melaksanakan perintah ini dengan saksama dan penuh tanggung jawab agar segera melaporkan kepada Atasan Penyidik serta membuat Berita Acara Penangkapan serta Berita Acara Penggeledahan Badan/Pakaian.');

            // 7. Tanda Tangan Perintah
            $templateProcessor->setValue('penerima_nama', strtoupper($document->penerima_nama ?: '-'));
            $penerimaPangkatNrp = trim(($document->penerima_pangkat ?: '') . (!empty($document->penerima_nrp) && $document->penerima_nrp !== '-' ? ' NRP ' . $document->penerima_nrp : ''));
            $templateProcessor->setValue('penerima_pangkat_nrp', $penerimaPangkatNrp ?: '-');

            $templateProcessor->setValue('atasan_nama', strtoupper($document->atasan_nama ?: '-'));
            $atasanPangkatNrp = trim(($document->atasan_pangkat ?: '') . (!empty($document->atasan_nrp) && $document->atasan_nrp !== '-' ? ' NRP ' . $document->atasan_nrp : ''));
            $templateProcessor->setValue('atasan_pangkat_nrp', $atasanPangkatNrp ?: '-');

            // 8. Lembar Tanda Terima / Penyerahan (Bawah)
            $templateProcessor->setValue('penyerahan_hari', $document->penyerahan_hari ?: '-');
            $templateProcessor->setValue('penyerahan_tanggal', $this->formatIndonesianDate($document->penyerahan_tanggal ?: $document->tanggal_surat));
            $templateProcessor->setValue('yang_menerima_nama', $document->yang_menerima_nama ?: ($document->tersangka_nama ?: '..............................'));

            $templateProcessor->setValue('yang_menyerahkan_nama', strtoupper($document->yang_menyerahkan_nama ?: ($document->penerima_nama ?: '-')));
            $penyerahPangkatNrp = trim(($document->yang_menyerahkan_pangkat ?: $document->penerima_pangkat ?: '') . (!empty($document->yang_menyerahkan_nrp) && $document->yang_menyerahkan_nrp !== '-' ? ' NRP ' . $document->yang_menyerahkan_nrp : ''));
            $templateProcessor->setValue('yang_menyerahkan_pangkat_nrp', $penyerahPangkatNrp ?: '-');

            // Generate Output File
            $cleanNomorFile = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document->nomor_surat ?: 'SP_Kap');
            $filename = "Surat_Perintah_Penangkapan_{$cleanNomorFile}_" . date('Ymd_His') . ".docx";
            $outputDir = storage_path('app/public');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $outputPath = $outputDir . DIRECTORY_SEPARATOR . $filename;

            $templateProcessor->saveAs($outputPath);

            return response()->download($outputPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh Surat Perintah Penangkapan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data Surat Perintah Penangkapan
     */
    public function delete($id, Request $request)
    {
        try {
            $document = SuratPerintahPenangkapanDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Penangkapan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Helper formatting tanggal berbahasa Indonesia
     */
    protected function formatIndonesianDate($date)
    {
        if (empty($date)) {
            return '-';
        }
        try {
            return Carbon::parse($date)->locale('id')->translatedFormat('d F Y');
        } catch (\Exception $e) {
            return (string) $date;
        }
    }

    /**
     * Helper menyusun format daftar petugas pelaksana (Kepada)
     */
    protected function formatOfficersList($officers)
    {
        if (empty($officers) || !is_array($officers)) {
            return "1.   -\n     -";
        }

        $lines = [];
        $no = 1;
        foreach ($officers as $off) {
            $nama = strtoupper($off['nama'] ?? '');
            $pangkat = $off['pangkat'] ?? '';
            $nrp = !empty($off['nrp']) ? 'NRP ' . $off['nrp'] : '';
            $jabatan = $this->cleanOfficerPositionString($off['jabatan'] ?? '', 'Penyidik');

            $header = "{$no}.   " . trim("{$pangkat} {$nama} {$nrp}");
            $sub = "     " . ($jabatan ?: 'Penyidik');
            $lines[] = "{$header}\n{$sub}";
            $no++;
        }

        return implode("\n", $lines);
    }

    /**
     * Helper mengambil nama jabatan petugas secara aman (string, bukan objek/JSON)
     */
    public function getOfficerPositionName($officer, $fallback = 'Penyidik')
    {
        if (!$officer) {
            return $fallback;
        }
        $pos = $officer->position ?? null;
        return $this->cleanOfficerPositionString($pos, $fallback);
    }

    /**
     * Helper membersihkan string jabatan dari JSON atau [object Object]
     */
    public function cleanOfficerPositionString($pos, $fallback = 'Penyidik')
    {
        if (empty($pos)) {
            return $fallback;
        }
        if (is_object($pos)) {
            return $pos->name ?? $fallback;
        }
        if (is_array($pos)) {
            return $pos['name'] ?? $fallback;
        }
        if (is_string($pos)) {
            $trim = trim($pos);
            if (str_starts_with($trim, '{')) {
                $dec = json_decode($trim, true);
                if (is_array($dec) && !empty($dec['name'])) {
                    return $dec['name'];
                }
            }
            if ($trim === '[object Object]') {
                return $fallback;
            }
            return $trim ?: $fallback;
        }
        return $fallback;
    }

    /**
     * Helper mengonversi kode agama atau nama agama ke format standar teks
     */
    public function resolveReligionName($religionRaw)
    {
        if (empty($religionRaw)) {
            return 'Islam';
        }
        $trim = trim($religionRaw);
        $upper = strtoupper($trim);
        if ($upper === 'R0102' || $upper === '1') return 'Islam';
        if ($upper === 'R0103' || $upper === '2') return 'Kristen';
        if ($upper === 'R0104' || $upper === '3') return 'Katolik';
        if ($upper === 'R0105' || $upper === '4') return 'Hindu';
        if ($upper === 'R0106' || $upper === '5') return 'Buddha';
        if ($upper === 'R0107' || $upper === '6') return 'Khonghucu';
        if ($upper === 'R0101' || $upper === '0') return 'Lainnya';

        if (str_contains($upper, 'KATOLIK')) {
            return 'Katolik';
        }
        if (str_contains($upper, 'PROTESTAN') || str_contains($upper, 'KRISTEN')) {
            return 'Kristen';
        }
        if (str_contains($upper, 'ISLAM')) {
            return 'Islam';
        }
        if (str_contains($upper, 'HINDU')) {
            return 'Hindu';
        }
        if (str_contains($upper, 'BUD')) {
            return 'Buddha';
        }
        if (str_contains($upper, 'KONG') || str_contains($upper, 'KHONG')) {
            return 'Khonghucu';
        }
        return ucwords(strtolower($trim));
    }

    /**
     * Helper mengonversi kode gender ke format teks
     */
    public function resolveGenderName($genderRaw)
    {
        if (empty($genderRaw)) {
            return 'Laki-laki';
        }
        $upper = strtoupper(trim($genderRaw));
        if ($upper === 'G0103' || str_contains($upper, 'PEREMPUAN') || str_contains($upper, 'WANITA')) {
            return 'Perempuan';
        }
        return 'Laki-laki';
    }

    /**
     * Konsolidasi daftar tersangka dari perkara
     */
    protected function getConsolidatedSuspects($accidentId)
    {
        $consolidated = [];
        $seenNames = [];

        // 1. Dari model suspects (relasi utama perkara LP)
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
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $kewarganegaraan = !empty($s->nationality) ? trim($s->nationality) : (!empty($s->country_short_name) ? trim($s->country_short_name) : 'Indonesia');
                    $pekerjaan = trim($s->job_name ?? $s->occupation ?? '-');
                    $agama = $this->resolveReligionName($s->religion_name ?? ($s->religion_short_name ?? $s->religion_id));
                    $gender = $this->resolveGenderName($s->gender_name ?? ($s->gender_short_name ?? $s->gender_id));

                    $consolidated[] = [
                        'source' => 'suspects',
                        'source_id' => (string) ($s->id ?? ''),
                        'nama' => $nameClean,
                        'no_identitas' => trim($s->identity_number ?? '') ?: '-',
                        'kewarganegaraan' => $kewarganegaraan ?: 'Indonesia',
                        'jenis_kelamin' => $gender,
                        'tempat_lahir' => trim($s->birth_place ?? ''),
                        'tanggal_lahir' => $s->birth_date ?? null,
                        'pekerjaan' => ucwords(strtolower($pekerjaan)) ?: '-',
                        'agama' => $agama,
                        'alamat' => trim($s->address ?? '-') ?: '-',
                    ];
                }
            }
        } catch (\Throwable $e) {}

        // 2. Dari tabel public.daftar_tersangka
        try {
            $dtSuspects = DB::table('daftar_tersangka')
                ->leftJoin('ref as ref_gender', function ($join) {
                    $join->on('daftar_tersangka.gender', '=', 'ref_gender.id')->where('ref_gender.grp_id', '=', 'G01');
                })
                ->leftJoin('ref as ref_religion', function ($join) {
                    $join->on('daftar_tersangka.religion', '=', 'ref_religion.id')->where('ref_religion.grp_id', '=', 'R01');
                })
                ->where('daftar_tersangka.accident_id', $accidentId)
                ->select('daftar_tersangka.*', 'ref_gender.name as gender_name', 'ref_religion.name as religion_name')
                ->get();

            foreach ($dtSuspects as $ds) {
                $nameClean = trim($ds->name ?? ($ds->nama ?? ''));
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = $this->resolveGenderName($ds->gender_name ?? $ds->gender);
                    $agama = $this->resolveReligionName($ds->religion_name ?? $ds->religion);

                    $consolidated[] = [
                        'source' => 'daftar_tersangka',
                        'source_id' => (string) ($ds->id ?? ''),
                        'nama' => $nameClean,
                        'no_identitas' => trim($ds->identity_number ?? ($ds->no_identitas ?? ($ds->identity_no ?? ''))) ?: '-',
                        'kewarganegaraan' => !empty($ds->citizenship) ? trim($ds->citizenship) : (!empty($ds->citizen) ? trim($ds->citizen) : 'Indonesia'),
                        'jenis_kelamin' => $gender,
                        'tempat_lahir' => trim($ds->birthplace ?? ($ds->tempat_lahir ?? ($ds->city ?? ''))),
                        'tanggal_lahir' => $ds->birthdate ?? ($ds->tanggal_lahir ?? ($ds->birth_date ?? null)),
                        'pekerjaan' => trim($ds->job ?? ($ds->pekerjaan ?? '-')),
                        'agama' => $agama,
                        'alamat' => trim($ds->address ?? ($ds->alamat ?? '-')),
                    ];
                }
            }
        } catch (\Throwable $e) {}

        // 3. Dari involved_peoples (class = 'SUSPECT')
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
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $gender = $this->resolveGenderName($inv->gender_name ?? '');
                    $agama = $this->resolveReligionName($inv->religion_name ?? '');

                    $consolidated[] = [
                        'source' => 'involved_peoples',
                        'source_id' => (string) ($inv->id ?? ''),
                        'nama' => $nameClean,
                        'no_identitas' => trim($inv->identity_number ?? '') ?: '-',
                        'kewarganegaraan' => !empty($inv->nationality) ? trim($inv->nationality) : 'Indonesia',
                        'jenis_kelamin' => $gender,
                        'tempat_lahir' => trim($inv->birth_place ?? ''),
                        'tanggal_lahir' => $inv->birth_date ?? null,
                        'pekerjaan' => ucwords(strtolower(trim($inv->job_name ?? ''))) ?: '-',
                        'agama' => $agama,
                        'alamat' => trim($inv->address ?? '') ?: '-',
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $consolidated;
    }
}
