<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument;
use App\Models\Doc\SuratPerintahTugasPenangkapanDocument\SuratPerintahTugasPenangkapanDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPerintahTugasPenangkapanDocumentController extends Controller
{
    /**
     * Tampilkan form pembuatan Dokumen Surat Perintah Tugas Penangkapan (S-10.1.2)
     */
    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?: $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        // Jika sudah ada dokumen, arahkan ke edit
        $existingDoc = SuratPerintahTugasPenangkapanDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.surat-perintah-tugas-penangkapan-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Cari apakah sudah ada Surat Perintah Penangkapan (SP.Kap / S-10.1.1)
        $existingSpKap = SuratPerintahPenangkapanDocument::where('accident_id', $accidentId)->latest()->first();
        // Cari apakah sudah ada Surat Perintah Penyidikan (SP.Sidik)
        $sprindik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->latest()->first();

        // Cari apakah sudah ada tersangka di kasus ini
        $availableSuspects = $this->getAvailableSuspects($accidentId);
        $firstSuspect = !empty($availableSuspects) ? $availableSuspects[0] : null;

        // Kopstuk standar
        $poldaName = $accident->polres->polda->name ?? 'SUMATERA UTARA';
        $polresName = $accident->polres->name ?? 'SIMALUNGUN';
        $polresAddress = $accident->polres->address ?? 'Jl. Jon Horailam Saragih No 110 Pamatang Raya';

        $kopstuk1 = 'KEPOLISIAN NEGARA REPUBLIK INDONESIA';
        $kopstuk2 = 'DAERAH ' . strtoupper($poldaName);
        $kopstuk3 = strtoupper($polresName);
        $kopstuk4 = $polresAddress;

        // Format nomor surat otomatis
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $currentMonth = (int) date('m');
        $currentYear = date('Y');
        $romanMonth = $romanMonths[$currentMonth] ?? 'XI';
        $defaultNomorSurat = "SP.Gas/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";

        // Default Pertimbangan
        $defaultPertimbangan = 'bahwa untuk kepentingan penyidikan diperlukan tindakan hukum berupa penangkapan terhadap Tersangka, berdasarkan pertimbangan situasi dan kondisi wilayah serta keselamatan dan keamanan penyidik/ Penyidik pembantu diperlukan bantuan dari petugas lain yang tidak termasuk dalam surat perintah tugas penyidikan, maka perlu dikeluarkan surat perintah tugas.';

        // Default Dasar Hukum
        $defaultDasarKuhap = 'Pasal 5 ayat (2), Pasal 7 ayat (1) huruf d, Pasal 11, Pasal 16, Pasal 18 ayat (1) dan Pasal 19 ayat (2) Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana';
        $defaultDasarPolri = 'Pasal 14 ayat (1) huruf g pasal 16 ayat (1) Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia';
        $defaultDasarUuPidana = 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        // Pre-fill dari SP.Kap jika ada
        $defaultDasarSpKapNomor = $existingSpKap ? $existingSpKap->nomor_surat : "SP.Kap/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";
        $defaultDasarSpKapTanggal = $existingSpKap && $existingSpKap->tanggal_surat ? Carbon::parse($existingSpKap->tanggal_surat)->format('Y-m-d') : date('Y-m-d');
        $defaultTersangkaNama = $existingSpKap ? $existingSpKap->tersangka_nama : ($firstSuspect['nama'] ?? '-');
        $defaultTersangkaId = $existingSpKap ? $existingSpKap->tersangka_id : ($firstSuspect['source_id'] ?? null);

        // Pre-fill SP.Sidik
        if ($existingSpKap && !empty($existingSpKap->dasar_sp_sidik_nomor)) {
            $defaultSpSidikNomor = $existingSpKap->dasar_sp_sidik_nomor;
            $defaultSpSidikTanggal = $existingSpKap->dasar_sp_sidik_tanggal ? Carbon::parse($existingSpKap->dasar_sp_sidik_tanggal)->format('Y-m-d') : date('Y-m-d');
        } elseif ($sprindik && !empty($sprindik->nomor_surat)) {
            $defaultSpSidikNomor = $sprindik->nomor_surat;
            $defaultSpSidikTanggal = $sprindik->tanggal_surat ? Carbon::parse($sprindik->tanggal_surat)->format('Y-m-d') : date('Y-m-d');
        } else {
            $defaultSpSidikNomor = "SP.Sidik/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";
            $defaultSpSidikTanggal = date('Y-m-d');
        }

        // Petugas polres untuk dropdown penerima & personil bantuan
        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        // Pejabat Penandatangan (Atasan Penyidik)
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
            $authorizedSignatories = AuthorizedSignatory::with(['rank', 'user'])
                ->where('police_id', $policeId)
                ->where('is_active', true)
                ->get();
        }

        // Default kota
        $defaultKota = strtoupper($accident->polres->city ?? ($accident->polres->name ?? 'RAYA'));
        $defaultKota = preg_replace('/^(POLRES|POLRESTA|POLRESTABES|KEPOLISIAN RESOR)\s+/i', '', $defaultKota);

        return view('docs.surat-perintah-tugas-penangkapan-document.create', compact(
            'accident',
            'accidentId',
            'existingSpKap',
            'availableSuspects',
            'firstSuspect',
            'kopstuk1',
            'kopstuk2',
            'kopstuk3',
            'kopstuk4',
            'defaultNomorSurat',
            'defaultPertimbangan',
            'defaultDasarKuhap',
            'defaultDasarPolri',
            'defaultDasarUuPidana',
            'defaultDasarSpKapNomor',
            'defaultDasarSpKapTanggal',
            'defaultTersangkaNama',
            'defaultTersangkaId',
            'defaultSpSidikNomor',
            'defaultSpSidikTanggal',
            'officers',
            'authorizedSignatories',
            'defaultKota'
        ));
    }

    /**
     * Simpan Dokumen Surat Perintah Tugas Penangkapan baru
     */
    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');

        $request->validate([
            'accident_id' => 'required|uuid',
            'kopstuk_1' => 'nullable|string',
            'kopstuk_2' => 'nullable|string',
            'kopstuk_3' => 'nullable|string',
            'kopstuk_4' => 'nullable|string',
            'nomor_surat' => 'nullable|string',
            'tanggal_surat' => 'nullable|date',
            'dikeluarkan_di' => 'nullable|string',
            'pertimbangan' => 'required|string',
            'dasar_kuhap' => 'required|string',
            'dasar_polri' => 'required|string',
            'dasar_uu_pidana' => 'required|string',
            'dasar_lp_nomor' => 'required|string',
            'dasar_lp_tanggal' => 'required|date',
            'dasar_sp_sidik_nomor' => 'required|string',
            'dasar_sp_sidik_tanggal' => 'required|date',
            'dasar_sp_kap_nomor' => 'required|string',
            'dasar_sp_kap_tanggal' => 'required|date',
            'tersangka_nama' => 'required|string',
            'officers' => 'required|array|min:1',
            'officers.*.nama' => 'required|string',
            'officers.*.pangkat' => 'required|string',
            'officers.*.nrp' => 'required|string',
            'officers.*.jabatan' => 'required|string',
            'untuk_sp_kap_nomor' => 'required|string',
            'untuk_sp_kap_tanggal' => 'required|date',
            'berlaku_sampai' => 'required|string',
            'penerima_nama' => 'required|string',
            'penerima_pangkat' => 'required|string',
            'penerima_nrp' => 'required|string',
            'penerima_jabatan' => 'required|string',
            'atasan_nama' => 'required|string',
            'atasan_pangkat' => 'required|string',
            'atasan_nrp' => 'required|string',
            'atasan_jabatan' => 'required|string',
        ]);

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);
        $poldaName = $accident->polres->polda->name ?? 'SUMATERA UTARA';
        $polresName = $accident->polres->name ?? 'SIMALUNGUN';
        $polresAddress = $accident->polres->address ?? 'Jl. Jon Horailam Saragih No 110 Pamatang Raya';

        $doc = new SuratPerintahTugasPenangkapanDocument();
        $doc->accident_id = $accidentId;
        $doc->document_category_id = '0303';
        $doc->status_id = '2';

        // Kopstuk & Surat
        $doc->kopstuk_1 = $request->input('kopstuk_1') ?: 'KEPOLISIAN NEGARA REPUBLIK INDONESIA';
        $doc->kopstuk_2 = $request->input('kopstuk_2') ?: ('DAERAH ' . strtoupper($poldaName));
        $doc->kopstuk_3 = $request->input('kopstuk_3') ?: strtoupper($polresName);
        $doc->kopstuk_4 = $request->input('kopstuk_4') ?: $polresAddress;

        if ($request->filled('nomor_surat')) {
            $doc->nomor_surat = $request->input('nomor_surat');
        } else {
            $romanMonths = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
            ];
            $currentMonth = (int) date('m');
            $currentYear = date('Y');
            $romanMonth = $romanMonths[$currentMonth] ?? 'XI';
            $doc->nomor_surat = "SP.Gas/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";
        }

        $doc->tanggal_surat = $request->input('tanggal_surat') ?: date('Y-m-d');

        $defaultKota = strtoupper($accident->polres->city ?? ($accident->polres->name ?? 'RAYA'));
        $defaultKota = preg_replace('/^(POLRES|POLRESTA|POLRESTABES|KEPOLISIAN RESOR)\s+/i', '', $defaultKota);
        $doc->dikeluarkan_di = $request->input('dikeluarkan_di') ?: $defaultKota;

        // Pertimbangan & Dasar
        $doc->pertimbangan = $request->input('pertimbangan');
        $doc->dasar_kuhap = $request->input('dasar_kuhap');
        $doc->dasar_polri = $request->input('dasar_polri');
        $doc->dasar_uu_pidana = $request->input('dasar_uu_pidana');
        $doc->dasar_lp_nomor = $request->input('dasar_lp_nomor');
        $doc->dasar_lp_tanggal = $request->input('dasar_lp_tanggal');
        $doc->dasar_sp_sidik_nomor = $request->input('dasar_sp_sidik_nomor');
        $doc->dasar_sp_sidik_tanggal = $request->input('dasar_sp_sidik_tanggal');
        $doc->dasar_sp_kap_nomor = $request->input('dasar_sp_kap_nomor');
        $doc->dasar_sp_kap_tanggal = $request->input('dasar_sp_kap_tanggal');

        // Tersangka
        $doc->tersangka_id = $request->input('tersangka_id');
        $doc->tersangka_nama = $request->input('tersangka_nama');

        // Petugas Pelaksana (Kepada)
        $doc->officers = array_values($request->input('officers', []));

        // Ketentuan Untuk
        $doc->untuk_sp_kap_nomor = $request->input('untuk_sp_kap_nomor');
        $doc->untuk_sp_kap_tanggal = $request->input('untuk_sp_kap_tanggal');
        $doc->berlaku_mulai = $request->input('tanggal_surat');
        $doc->berlaku_sampai = $request->input('berlaku_sampai');

        // Penandatangan
        $doc->penerima_nama = $request->input('penerima_nama');
        $doc->penerima_pangkat = $request->input('penerima_pangkat');
        $doc->penerima_nrp = $request->input('penerima_nrp');
        $doc->penerima_jabatan = $request->input('penerima_jabatan', 'Penyidik Pembantu');

        $doc->atasan_nama = $request->input('atasan_nama');
        $doc->atasan_pangkat = $request->input('atasan_pangkat');
        $doc->atasan_nrp = $request->input('atasan_nrp');
        $doc->atasan_jabatan = $request->input('atasan_jabatan', 'ATASAN PENYIDIK');

        $doc->created_by_user_id = Auth::id();
        $doc->updated_by_user_id = Auth::id();
        $doc->save();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Tugas Penangkapan (SP.Gas) berhasil disimpan.');
    }

    /**
     * Tampilkan form edit Dokumen Surat Perintah Tugas Penangkapan
     */
    public function edit($id, Request $request)
    {
        $doc = SuratPerintahTugasPenangkapanDocument::findOrFail($id);
        $accidentId = $doc->accident_id;
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

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
            $authorizedSignatories = AuthorizedSignatory::with(['rank', 'user'])
                ->where('police_id', $policeId)
                ->where('is_active', true)
                ->get();
        }

        return view('docs.surat-perintah-tugas-penangkapan-document.edit', compact(
            'doc',
            'accident',
            'accidentId',
            'officers',
            'authorizedSignatories'
        ));
    }

    /**
     * Update Dokumen Surat Perintah Tugas Penangkapan
     */
    public function update($id, Request $request)
    {
        $doc = SuratPerintahTugasPenangkapanDocument::findOrFail($id);
        $accidentId = $doc->accident_id;

        $request->validate([
            'kopstuk_1' => 'nullable|string',
            'kopstuk_2' => 'nullable|string',
            'kopstuk_3' => 'nullable|string',
            'kopstuk_4' => 'nullable|string',
            'nomor_surat' => 'nullable|string',
            'tanggal_surat' => 'nullable|date',
            'dikeluarkan_di' => 'nullable|string',
            'pertimbangan' => 'required|string',
            'dasar_kuhap' => 'required|string',
            'dasar_polri' => 'required|string',
            'dasar_uu_pidana' => 'required|string',
            'dasar_lp_nomor' => 'required|string',
            'dasar_lp_tanggal' => 'required|date',
            'dasar_sp_sidik_nomor' => 'required|string',
            'dasar_sp_sidik_tanggal' => 'required|date',
            'dasar_sp_kap_nomor' => 'required|string',
            'dasar_sp_kap_tanggal' => 'required|date',
            'tersangka_nama' => 'required|string',
            'officers' => 'required|array|min:1',
            'officers.*.nama' => 'required|string',
            'officers.*.pangkat' => 'required|string',
            'officers.*.nrp' => 'required|string',
            'officers.*.jabatan' => 'required|string',
            'untuk_sp_kap_nomor' => 'required|string',
            'untuk_sp_kap_tanggal' => 'required|date',
            'berlaku_sampai' => 'required|string',
            'penerima_nama' => 'required|string',
            'penerima_pangkat' => 'required|string',
            'penerima_nrp' => 'required|string',
            'penerima_jabatan' => 'required|string',
            'atasan_nama' => 'required|string',
            'atasan_pangkat' => 'required|string',
            'atasan_nrp' => 'required|string',
            'atasan_jabatan' => 'required|string',
        ]);

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);
        $poldaName = $accident->polres->polda->name ?? 'SUMATERA UTARA';
        $polresName = $accident->polres->name ?? 'SIMALUNGUN';
        $polresAddress = $accident->polres->address ?? 'Jl. Jon Horailam Saragih No 110 Pamatang Raya';

        // Kopstuk & Surat
        $doc->kopstuk_1 = $request->input('kopstuk_1') ?: ($doc->kopstuk_1 ?: 'KEPOLISIAN NEGARA REPUBLIK INDONESIA');
        $doc->kopstuk_2 = $request->input('kopstuk_2') ?: ($doc->kopstuk_2 ?: ('DAERAH ' . strtoupper($poldaName)));
        $doc->kopstuk_3 = $request->input('kopstuk_3') ?: ($doc->kopstuk_3 ?: strtoupper($polresName));
        $doc->kopstuk_4 = $request->input('kopstuk_4') ?: ($doc->kopstuk_4 ?: $polresAddress);
        $doc->nomor_surat = $request->input('nomor_surat') ?: $doc->nomor_surat;
        $doc->tanggal_surat = $request->input('tanggal_surat') ?: ($doc->tanggal_surat ?: date('Y-m-d'));
        $defaultKota = strtoupper($accident->polres->city ?? ($accident->polres->name ?? 'RAYA'));
        $defaultKota = preg_replace('/^(POLRES|POLRESTA|POLRESTABES|KEPOLISIAN RESOR)\s+/i', '', $defaultKota);
        $doc->dikeluarkan_di = $request->input('dikeluarkan_di') ?: ($doc->dikeluarkan_di ?: $defaultKota);

        // Pertimbangan & Dasar
        $doc->pertimbangan = $request->input('pertimbangan');
        $doc->dasar_kuhap = $request->input('dasar_kuhap');
        $doc->dasar_polri = $request->input('dasar_polri');
        $doc->dasar_uu_pidana = $request->input('dasar_uu_pidana');
        $doc->dasar_lp_nomor = $request->input('dasar_lp_nomor');
        $doc->dasar_lp_tanggal = $request->input('dasar_lp_tanggal');
        $doc->dasar_sp_sidik_nomor = $request->input('dasar_sp_sidik_nomor');
        $doc->dasar_sp_sidik_tanggal = $request->input('dasar_sp_sidik_tanggal');
        $doc->dasar_sp_kap_nomor = $request->input('dasar_sp_kap_nomor');
        $doc->dasar_sp_kap_tanggal = $request->input('dasar_sp_kap_tanggal');

        // Tersangka
        $doc->tersangka_id = $request->input('tersangka_id');
        $doc->tersangka_nama = $request->input('tersangka_nama');

        // Petugas Pelaksana (Kepada)
        $doc->officers = array_values($request->input('officers', []));

        // Ketentuan Untuk
        $doc->untuk_sp_kap_nomor = $request->input('untuk_sp_kap_nomor');
        $doc->untuk_sp_kap_tanggal = $request->input('untuk_sp_kap_tanggal');
        $doc->berlaku_mulai = $request->input('tanggal_surat');
        $doc->berlaku_sampai = $request->input('berlaku_sampai');

        // Penandatangan
        $doc->penerima_nama = $request->input('penerima_nama');
        $doc->penerima_pangkat = $request->input('penerima_pangkat');
        $doc->penerima_nrp = $request->input('penerima_nrp');
        $doc->penerima_jabatan = $request->input('penerima_jabatan', 'Penyidik Pembantu');

        $doc->atasan_nama = $request->input('atasan_nama');
        $doc->atasan_pangkat = $request->input('atasan_pangkat');
        $doc->atasan_nrp = $request->input('atasan_nrp');
        $doc->atasan_jabatan = $request->input('atasan_jabatan', 'ATASAN PENYIDIK');

        $doc->updated_by_user_id = Auth::id();
        $doc->save();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Tugas Penangkapan (SP.Gas) berhasil diperbarui.');
    }

    /**
     * Download dokumen Word .docx
     */
    public function download($id, Request $request)
    {
        $doc = SuratPerintahTugasPenangkapanDocument::with('accident.polres.polda')->findOrFail($id);
        $templatePath = public_path('word-template/surat_perintah_tugas_penangkapan.docx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template Word tidak ditemukan.');
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);

            // Sanitasi teks agar aman dan tidak memicu link Word
            $sanitizeNoUrl = function ($str) {
                if (empty($str)) {
                    return '-';
                }
                return str_replace(['http://', 'https://', 'www.'], '', (string) $str);
            };

            // 1. Kopstuk
            $templateProcessor->setValue('kopstuk_1', $doc->kopstuk_1 ?: 'KEPOLISIAN NEGARA REPUBLIK INDONESIA');
            $templateProcessor->setValue('kopstuk_2', $doc->kopstuk_2 ?: 'DAERAH SUMATERA UTARA');
            $templateProcessor->setValue('kopstuk_3', $doc->kopstuk_3 ?: 'RESOR SIMALUNGUN');
            $templateProcessor->setValue('kopstuk_4', $doc->kopstuk_4 ?: 'Jl. Jon Horailam Saragih No 110 Pamatang Raya');

            // 2. Nomor Surat
            $cleanNomor = $sanitizeNoUrl($doc->nomor_surat ?: '-');
            $cleanNomor = preg_replace('/^SP\.Gas\//i', '', $cleanNomor);
            $templateProcessor->setValue('nomor_sp_gas', $cleanNomor);

            // 3. Pertimbangan
            $templateProcessor->setValue('pertimbangan', $doc->pertimbangan ?: '-');

            // 4. Dasar Hukum
            $templateProcessor->setValue('dasar_kuhap', $doc->dasar_kuhap ?: '-');
            $templateProcessor->setValue('dasar_polri', $doc->dasar_polri ?: '-');
            $templateProcessor->setValue('dasar_uu_pidana', $doc->dasar_uu_pidana ?: '-');
            $templateProcessor->setValue('dasar_lp_nomor', $sanitizeNoUrl($doc->dasar_lp_nomor ?: '-'));
            $templateProcessor->setValue('dasar_lp_tanggal', $this->formatIndonesianDate($doc->dasar_lp_tanggal));
            $templateProcessor->setValue('dasar_sp_sidik_nomor', $sanitizeNoUrl($doc->dasar_sp_sidik_nomor ?: '-'));
            $templateProcessor->setValue('dasar_sp_sidik_tanggal', $this->formatIndonesianDate($doc->dasar_sp_sidik_tanggal));
            $templateProcessor->setValue('dasar_sp_kap_nomor', $sanitizeNoUrl($doc->dasar_sp_kap_nomor ?: '-'));
            $templateProcessor->setValue('dasar_sp_kap_tanggal', $this->formatIndonesianDate($doc->dasar_sp_kap_tanggal));

            // 5. Kepada (Petugas Pelaksana via cloneRow)
            $officers = $doc->officers;
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

            // 6. Untuk
            $templateProcessor->setValue('untuk_sp_kap_nomor', $sanitizeNoUrl($doc->untuk_sp_kap_nomor ?: ($doc->dasar_sp_kap_nomor ?: '-')));
            $templateProcessor->setValue('untuk_sp_kap_tanggal', $this->formatIndonesianDate($doc->untuk_sp_kap_tanggal ?: $doc->dasar_sp_kap_tanggal));
            $templateProcessor->setValue('berlaku_sampai', $doc->berlaku_sampai ?: '-');

            // 7. Lokasi & Tanggal
            $templateProcessor->setValue('dikeluarkan_di', strtoupper($doc->dikeluarkan_di ?: 'RAYA'));
            $templateProcessor->setValue('tanggal_surat', $this->formatIndonesianDate($doc->tanggal_surat));

            // 8. Tanda Tangan
            $templateProcessor->setValue('penerima_nama', strtoupper($doc->penerima_nama ?: '-'));
            $penerimaPangkatNrp = trim(($doc->penerima_pangkat ?: '') . (!empty($doc->penerima_nrp) && $doc->penerima_nrp !== '-' ? ' NRP ' . $doc->penerima_nrp : ''));
            $templateProcessor->setValue('penerima_pangkat_nrp', $penerimaPangkatNrp ?: '-');

            $templateProcessor->setValue('atasan_nama', strtoupper($doc->atasan_nama ?: '-'));
            $atasanPangkatNrp = trim(($doc->atasan_pangkat ?: '') . (!empty($doc->atasan_nrp) && $doc->atasan_nrp !== '-' ? ' NRP ' . $doc->atasan_nrp : ''));
            $templateProcessor->setValue('atasan_pangkat_nrp', $atasanPangkatNrp ?: '-');

            // Simpan file ke storage
            $cleanNomorFile = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $doc->nomor_surat ?: 'SP_Gas_Penangkapan');
            $filename = "Surat_Perintah_Tugas_Penangkapan_{$cleanNomorFile}_" . date('Ymd_His') . ".docx";
            $outputDir = storage_path('app/public');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $outputPath = $outputDir . DIRECTORY_SEPARATOR . $filename;

            $templateProcessor->saveAs($outputPath);

            return response()->download($outputPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh Surat Perintah Tugas Penangkapan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Dokumen Surat Perintah Tugas Penangkapan
     */
    public function delete($id, Request $request)
    {
        try {
            $doc = SuratPerintahTugasPenangkapanDocument::findOrFail($id);
            $accidentId = $doc->accident_id;
            $doc->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Tugas Penangkapan berhasil dihapus.');
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
     * Helper membersihkan string jabatan
     */
    protected function cleanOfficerPositionString($position, $default = 'Penyidik')
    {
        if (is_array($position)) {
            return $position['name'] ?? $default;
        }
        if (is_object($position)) {
            return $position->name ?? $default;
        }
        $str = trim((string) $position);
        if ($str === '') {
            return $default;
        }
        if (str_starts_with($str, '{')) {
            $decoded = json_decode($str, true);
            if (is_array($decoded) && isset($decoded['name'])) {
                return $decoded['name'];
            }
        }
        return $str;
    }

    /**
     * Helper list old & new polres ids
     */
    protected function getOldNewPolresIds($policeId)
    {
        $ids = [$policeId];
        try {
            $mapping = DB::table('polices')->where('id', $policeId)->first();
            if ($mapping && !empty($mapping->old_id)) {
                $ids[] = $mapping->old_id;
            }
            if ($mapping && !empty($mapping->code)) {
                $ids[] = $mapping->code;
            }
        } catch (\Throwable $e) {}
        return array_unique(array_filter($ids));
    }

    /**
     * Helper suspek dari berbagai tabel
     */
    protected function getAvailableSuspects($accidentId)
    {
        $consolidated = [];
        $seenNames = [];

        // 1. Dari penetapan tersangka
        try {
            $penetapan = DB::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->first();

            if ($penetapan && !empty($penetapan->tersangka_nama)) {
                $nameClean = trim($penetapan->tersangka_nama);
                $seenNames[strtolower($nameClean)] = true;
                $consolidated[] = [
                    'source' => 'penetapan_tersangka',
                    'source_id' => (string) ($penetapan->id ?? ''),
                    'nama' => $nameClean,
                    'no_identitas' => trim($penetapan->tersangka_nomor_identitas ?? '') ?: '-',
                    'kewarganegaraan' => trim($penetapan->tersangka_kewarganegaraan ?? '') ?: 'Indonesia',
                    'jenis_kelamin' => trim($penetapan->tersangka_jenis_kelamin ?? ''),
                    'tempat_lahir' => trim($penetapan->tersangka_tempat_lahir ?? ''),
                    'tanggal_lahir' => $penetapan->tersangka_tanggal_lahir ?? null,
                    'pekerjaan' => trim($penetapan->tersangka_pekerjaan ?? '') ?: '-',
                    'alamat' => trim($penetapan->tersangka_alamat ?? '') ?: '-',
                ];
            }
        } catch (\Throwable $e) {}

        // 2. Dari daftar_tersangka
        try {
            $dtSuspects = DB::table('daftar_tersangka')
                ->where('accident_id', $accidentId)
                ->get();

            foreach ($dtSuspects as $ds) {
                $nameClean = trim($ds->name ?? ($ds->nama ?? ''));
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $consolidated[] = [
                        'source' => 'daftar_tersangka',
                        'source_id' => (string) ($ds->id ?? ''),
                        'nama' => $nameClean,
                        'no_identitas' => trim($ds->identity_number ?? ($ds->no_identitas ?? ($ds->identity_no ?? ''))) ?: '-',
                        'kewarganegaraan' => !empty($ds->citizenship) ? trim($ds->citizenship) : 'Indonesia',
                        'jenis_kelamin' => trim($ds->gender ?? ''),
                        'tempat_lahir' => trim($ds->birthplace ?? ($ds->tempat_lahir ?? ($ds->city ?? ''))),
                        'tanggal_lahir' => $ds->birthdate ?? ($ds->tanggal_lahir ?? null),
                        'pekerjaan' => trim($ds->job ?? ($ds->pekerjaan ?? '-')),
                        'alamat' => trim($ds->address ?? ($ds->alamat ?? '-')),
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $consolidated;
    }
}

