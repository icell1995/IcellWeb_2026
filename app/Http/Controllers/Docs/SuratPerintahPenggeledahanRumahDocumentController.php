<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratPerintahPenggeledahanRumahDocument\SuratPerintahPenggeledahanRumahDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPerintahPenggeledahanRumahDocumentController extends Controller
{
    /**
     * Tampilkan form pembuatan Dokumen Surat Perintah Penggeledahan Rumah (S-23.1.1.1)
     */
    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?: $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        // Jika sudah ada dokumen, arahkan ke edit
        $existingDoc = SuratPerintahPenggeledahanRumahDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.surat-perintah-penggeledahan-rumah-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Cari apakah sudah ada Surat Perintah Penyidikan (SP.Sidik)
        $sprindik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->latest()->first();
        // Cari apakah ada SP.Kap jika ada
        $existingSpKap = \App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument::where('accident_id', $accidentId)->latest()->first();

        // Cari tersangka
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
        $defaultNomorSurat = "SP.Dah/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";

        // Default Pertimbangan
        $defaultPertimbangan = 'bahwa untuk kepentingan penyidikan peristiwa Tindak Pidana, perlu dilakukan tindakan hukum berupa penggeledahan rumah dan atau tempat tertutup lainnya, maka perlu dikeluarkan surat perintah.';

        // Default Dasar Hukum
        $defaultDasarKuhap = 'Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d, Pasal 11, Pasal 16, Pasal 18, Pasal 32, Pasal 33, Pasal 34, Pasal 36, Pasal 125, Pasal 126 dan Pasal 127 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana';
        $defaultDasarPolri = 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia';
        $defaultDasarUuPidana = 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        // Pre-fill SP.Sidik
        if ($sprindik && !empty($sprindik->nomor_surat)) {
            $defaultSpSidikNomor = $sprindik->nomor_surat;
            $defaultSpSidikTanggal = $sprindik->tanggal_surat ? Carbon::parse($sprindik->tanggal_surat)->format('Y-m-d') : date('Y-m-d');
        } elseif ($existingSpKap && !empty($existingSpKap->dasar_sp_sidik_nomor)) {
            $defaultSpSidikNomor = $existingSpKap->dasar_sp_sidik_nomor;
            $defaultSpSidikTanggal = $existingSpKap->dasar_sp_sidik_tanggal ? Carbon::parse($existingSpKap->dasar_sp_sidik_tanggal)->format('Y-m-d') : date('Y-m-d');
        } else {
            $defaultSpSidikNomor = "SP.Sidik/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";
            $defaultSpSidikTanggal = date('Y-m-d');
        }

        // Data Tersangka & TKP
        $defaultTersangkaNama = $firstSuspect['nama'] ?? ($existingSpKap ? $existingSpKap->tersangka_nama : '-');
        $defaultTersangkaId = $firstSuspect['source_id'] ?? ($existingSpKap ? $existingSpKap->tersangka_id : null);
        $defaultLokasi = $firstSuspect['alamat'] ?? ($accident->address ?: 'Wilayah Hukum Polres Simalungun');

        // TKP & Waktu Kejadian
        $defaultTkp = $accident->address ?: ($accident->accident_address ?: 'Wilayah Hukum Polres Setempat');
        $defaultWaktuKejadian = '';
        if ($accident->accident_date) {
            $carbonDate = Carbon::parse($accident->accident_date)->locale('id');
            $hari = $carbonDate->translatedFormat('l');
            $tgl = $carbonDate->translatedFormat('d F Y');
            $jam = $accident->accident_time ? ' sekira pukul ' . Carbon::parse($accident->accident_time)->format('H:i') . ' WIB' : '';
            $defaultWaktuKejadian = "pada hari {$hari} tanggal {$tgl}{$jam}";
        } else {
            $defaultWaktuKejadian = "dalam kurun waktu tahun {$currentYear}";
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

        return view('docs.surat-perintah-penggeledahan-rumah-document.create', compact(
            'accident',
            'accidentId',
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
            'defaultSpSidikNomor',
            'defaultSpSidikTanggal',
            'defaultTersangkaNama',
            'defaultTersangkaId',
            'defaultLokasi',
            'defaultTkp',
            'defaultWaktuKejadian',
            'defaultKota',
            'officers',
            'authorizedSignatories'
        ));
    }

    /**
     * Simpan Dokumen Surat Perintah Penggeledahan Rumah baru
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
            'officers' => 'required|array|min:1',
            'officers.*.nama' => 'required|string',
            'officers.*.pangkat' => 'required|string',
            'officers.*.nrp' => 'required|string',
            'officers.*.jabatan' => 'required|string',
            'lokasi_penggeledahan' => 'required|string',
            'alasan_penggeledahan' => 'required|string',
            'uraian_tindak_pidana' => 'required|string',
            'pasal_pidana' => 'required|string',
            'uu_pidana_nomor' => 'required|string',
            'uu_pidana_tahun' => 'required|string',
            'uu_pidana_tentang' => 'required|string',
            'tkp' => 'required|string',
            'waktu_kejadian' => 'required|string',
            'tujuan_penggeledahan' => 'required|string',
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

        $doc = new SuratPerintahPenggeledahanRumahDocument();
        $doc->accident_id = $accidentId;
        $doc->document_category_id = '0401';
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
            $doc->nomor_surat = "SP.Dah/01/{$romanMonth}/RES.1.24/{$currentYear}/Lantas";
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

        // Petugas Pelaksana (Kepada)
        $doc->officers = array_values($request->input('officers', []));

        // Ketentuan Pelaksanaan Tugas (Untuk)
        $doc->lokasi_penggeledahan = $request->input('lokasi_penggeledahan');
        $doc->alasan_penggeledahan = $request->input('alasan_penggeledahan');
        $doc->tersangka_id = $request->input('tersangka_id');
        $doc->tersangka_nama = $request->input('tersangka_nama');
        $doc->uraian_tindak_pidana = $request->input('uraian_tindak_pidana');
        $doc->pasal_pidana = $request->input('pasal_pidana');
        $doc->uu_pidana_nomor = $request->input('uu_pidana_nomor');
        $doc->uu_pidana_tahun = $request->input('uu_pidana_tahun');
        $doc->uu_pidana_tentang = $request->input('uu_pidana_tentang');
        $doc->tkp = $request->input('tkp');
        $doc->waktu_kejadian = $request->input('waktu_kejadian');
        $doc->tujuan_penggeledahan = $request->input('tujuan_penggeledahan');
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
            ->with('success', 'Dokumen Surat Perintah Penggeledahan Rumah berhasil disimpan.');
    }

    /**
     * Tampilkan form edit Dokumen Surat Perintah Penggeledahan Rumah
     */
    public function edit($id, Request $request)
    {
        $doc = SuratPerintahPenggeledahanRumahDocument::findOrFail($id);
        $accidentId = $doc->accident_id;
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $availableSuspects = $this->getAvailableSuspects($accidentId);

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

        return view('docs.surat-perintah-penggeledahan-rumah-document.edit', compact(
            'doc',
            'accident',
            'accidentId',
            'availableSuspects',
            'officers',
            'authorizedSignatories'
        ));
    }

    /**
     * Update Dokumen Surat Perintah Penggeledahan Rumah
     */
    public function update($id, Request $request)
    {
        $doc = SuratPerintahPenggeledahanRumahDocument::findOrFail($id);
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
            'officers' => 'required|array|min:1',
            'officers.*.nama' => 'required|string',
            'officers.*.pangkat' => 'required|string',
            'officers.*.nrp' => 'required|string',
            'officers.*.jabatan' => 'required|string',
            'lokasi_penggeledahan' => 'required|string',
            'alasan_penggeledahan' => 'required|string',
            'uraian_tindak_pidana' => 'required|string',
            'pasal_pidana' => 'required|string',
            'uu_pidana_nomor' => 'required|string',
            'uu_pidana_tahun' => 'required|string',
            'uu_pidana_tentang' => 'required|string',
            'tkp' => 'required|string',
            'waktu_kejadian' => 'required|string',
            'tujuan_penggeledahan' => 'required|string',
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

        // Petugas Pelaksana (Kepada)
        $doc->officers = array_values($request->input('officers', []));

        // Ketentuan Pelaksanaan Tugas (Untuk)
        $doc->lokasi_penggeledahan = $request->input('lokasi_penggeledahan');
        $doc->alasan_penggeledahan = $request->input('alasan_penggeledahan');
        $doc->tersangka_id = $request->input('tersangka_id');
        $doc->tersangka_nama = $request->input('tersangka_nama');
        $doc->uraian_tindak_pidana = $request->input('uraian_tindak_pidana');
        $doc->pasal_pidana = $request->input('pasal_pidana');
        $doc->uu_pidana_nomor = $request->input('uu_pidana_nomor');
        $doc->uu_pidana_tahun = $request->input('uu_pidana_tahun');
        $doc->uu_pidana_tentang = $request->input('uu_pidana_tentang');
        $doc->tkp = $request->input('tkp');
        $doc->waktu_kejadian = $request->input('waktu_kejadian');
        $doc->tujuan_penggeledahan = $request->input('tujuan_penggeledahan');
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
            ->with('success', 'Dokumen Surat Perintah Penggeledahan Rumah berhasil diperbarui.');
    }

    /**
     * Download Dokumen Word Surat Perintah Penggeledahan Rumah
     */
    public function download($id, Request $request)
    {
        try {
            $doc = SuratPerintahPenggeledahanRumahDocument::findOrFail($id);
            $templatePath = public_path('word-template/surat_perintah_penggeledahan_rumah.docx');

            if (!file_exists($templatePath)) {
                return redirect()->back()->with('error', 'Template Word surat_perintah_penggeledahan_rumah.docx tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            $sanitizeNoUrl = function ($val) {
                if (empty($val)) return '-';
                return str_replace(['http://', 'https://', 'www.'], '', (string) $val);
            };

            // 1. Kopstuk
            $templateProcessor->setValue('kopstuk_1', strtoupper($doc->kopstuk_1 ?: 'KEPOLISIAN NEGARA REPUBLIK INDONESIA'));
            $templateProcessor->setValue('kopstuk_2', strtoupper($doc->kopstuk_2 ?: 'DAERAH SUMATERA UTARA'));
            $templateProcessor->setValue('kopstuk_3', strtoupper($doc->kopstuk_3 ?: 'RESOR SIMALUNGUN'));
            $templateProcessor->setValue('kopstuk_4', $doc->kopstuk_4 ?: 'Jl. Jon Horailam Saragih No 110 Pamatang Raya');

            // 2. Header
            $templateProcessor->setValue('nomor_surat', $sanitizeNoUrl($doc->nomor_surat ?: '-'));

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

            // 5. Kepada (Daftar Petugas)
            $officers = is_array($doc->officers) ? $doc->officers : json_decode($doc->officers, true);
            $blockOfficers = [];
            if (!empty($officers) && is_array($officers)) {
                $no = 1;
                foreach ($officers as $off) {
                    $nama = $off['nama'] ?? '-';
                    $pangkat = $off['pangkat'] ?? '';
                    $nrp = $off['nrp'] ?? '';
                    $jabatan = $off['jabatan'] ?? '';

                    $header = trim("{$pangkat} {$nama}" . (!empty($nrp) && $nrp !== '-' ? " NRP {$nrp}" : ""));
                    $blockOfficers[] = [
                        'kepada_label' => ($no === 1 ? 'Kepada' : ''),
                        'kepada_colon' => ($no === 1 ? ':' : ''),
                        'officer_no' => (string) $no,
                        'officer_header' => $header,
                        'officer_position' => $jabatan ?: 'Penyidik Pembantu',
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
                    'officer_position' => 'Penyidik Pembantu',
                ];
            }
            $templateProcessor->cloneRowAndSetValues('officer_no', $blockOfficers);

            // 6. Untuk
            $templateProcessor->setValue('lokasi_penggeledahan', $doc->lokasi_penggeledahan ?: '-');
            $templateProcessor->setValue('alasan_penggeledahan', $doc->alasan_penggeledahan ?: '-');
            $templateProcessor->setValue('uraian_tindak_pidana', $doc->uraian_tindak_pidana ?: '-');
            $templateProcessor->setValue('pasal_pidana', $doc->pasal_pidana ?: '-');
            $templateProcessor->setValue('uu_pidana_nomor', $doc->uu_pidana_nomor ?: '22');
            $templateProcessor->setValue('uu_pidana_tahun', $doc->uu_pidana_tahun ?: '2009');
            $templateProcessor->setValue('uu_pidana_tentang', $doc->uu_pidana_tentang ?: 'Lalu Lintas dan Angkutan Jalan');
            $templateProcessor->setValue('tkp', $doc->tkp ?: '-');
            $templateProcessor->setValue('waktu_kejadian', $doc->waktu_kejadian ?: '-');
            $templateProcessor->setValue('tujuan_penggeledahan', $doc->tujuan_penggeledahan ?: 'penggeledahan dilaksanakan untuk kepentingan pemeriksaan tempat kejadian perkara atau penyitaan dan atau penangkapan Tersangka atau pemeriksaan saksi');
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
            $cleanNomorFile = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $doc->nomor_surat ?: 'SP_Dah_Penggeledahan');
            $filename = "Surat_Perintah_Penggeledahan_Rumah_{$cleanNomorFile}_" . date('Ymd_His') . ".docx";
            $outputDir = storage_path('app/public');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $outputPath = $outputDir . DIRECTORY_SEPARATOR . $filename;

            $templateProcessor->saveAs($outputPath);

            return response()->download($outputPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh Surat Perintah Penggeledahan Rumah: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Dokumen Surat Perintah Penggeledahan Rumah
     */
    public function delete($id, Request $request)
    {
        try {
            $doc = SuratPerintahPenggeledahanRumahDocument::findOrFail($id);
            $accidentId = $doc->accident_id;
            $doc->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Penggeledahan Rumah berhasil dihapus.');
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
     * Ambil ID polres lama dan baru untuk compatibility
     */
    protected function getOldNewPolresIds($policeId)
    {
        $ids = [$policeId];
        try {
            $police = DB::table('polices')->where('id', $policeId)->first();
            if ($police && !empty($police->old_id)) {
                $ids[] = $police->old_id;
            }
        } catch (\Exception $e) {
            // ignore
        }
        return array_unique(array_filter($ids));
    }

    /**
     * Cari daftar tersangka yang tersedia di kasus ini
     */
    protected function getAvailableSuspects($accidentId)
    {
        $suspects = [];

        // 1. Dari tabel doc.daftar_tersangka_documents
        try {
            $dTersangka = DB::table('doc.daftar_tersangka_documents')
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->first();
            if ($dTersangka && !empty($dTersangka->tersangka_list)) {
                $list = json_decode($dTersangka->tersangka_list, true);
                if (is_array($list)) {
                    foreach ($list as $item) {
                        if (!empty($item['nama'])) {
                            $suspects[] = [
                                'nama' => $item['nama'],
                                'alamat' => $item['alamat'] ?? '-',
                                'source' => 'daftar_tersangka_documents',
                                'source_id' => $dTersangka->id,
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {}

        // 2. Dari tabel doc.surat_perintah_penangkapan_documents
        try {
            $spKap = DB::table('doc.surat_perintah_penangkapan_documents')
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->first();
            if ($spKap && !empty($spKap->tersangka_nama)) {
                $suspects[] = [
                    'nama' => $spKap->tersangka_nama,
                    'alamat' => $spKap->tersangka_tempat_tinggal ?? '-',
                    'source' => 'surat_perintah_penangkapan_documents',
                    'source_id' => $spKap->tersangka_id ?? $spKap->id,
                ];
            }
        } catch (\Exception $e) {}

        // 3. Dari tabel accident_suspects
        try {
            $accSuspects = DB::table('accident_suspects')
                ->where('accident_id', $accidentId)
                ->get();
            foreach ($accSuspects as $as) {
                $nama = $as->name ?? ($as->full_name ?? null);
                if ($nama) {
                    $suspects[] = [
                        'nama' => $nama,
                        'alamat' => $as->address ?? '-',
                        'source' => 'accident_suspects',
                        'source_id' => $as->id,
                    ];
                }
            }
        } catch (\Exception $e) {}

        // 4. Dari victims yang bertipe tersangka
        try {
            $victims = DB::table('victims')
                ->where('accident_id', $accidentId)
                ->where(function ($q) {
                    $q->where('victim_type', 'like', '%tersangka%')
                      ->orWhere('victim_type', 'like', '%pengemudi%')
                      ->orWhere('status', 'like', '%tersangka%');
                })
                ->get();
            foreach ($victims as $v) {
                $nama = $v->name ?? ($v->full_name ?? null);
                if ($nama) {
                    $suspects[] = [
                        'nama' => $nama,
                        'alamat' => $v->address ?? '-',
                        'source' => 'victims',
                        'source_id' => $v->id,
                    ];
                }
            }
        } catch (\Exception $e) {}

        // Deduplicate
        $unique = [];
        $result = [];
        foreach ($suspects as $s) {
            $key = strtolower(trim($s['nama']));
            if (!isset($unique[$key])) {
                $unique[$key] = true;
                $result[] = $s;
            }
        }

        return $result;
    }
}

