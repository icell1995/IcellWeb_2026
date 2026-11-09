<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratDaftarPencarianOrangDocument\SuratDaftarPencarianOrangDocument;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratDaftarPencarianOrangDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Tampilkan form pembuatan Surat Daftar Pencarian Orang (DPO - S-5.6.1)
     */
    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?: $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        // Jika sudah ada dokumen, arahkan ke edit
        $existingDoc = SuratDaftarPencarianOrangDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.surat-daftar-pencarian-orang-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Pejabat penandatangan aktif
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

        // Petugas Polres untuk dropdown
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

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

        // Bulan romawi
        $monthRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $curMonthRomawi = $monthRomawi[(int) $now->format('m')] ?? 'XI';

        // Nomor DPO default
        $defaultNomorDpo = "DPO/01/{$curMonthRomawi}/RES.1.24/{$now->format('Y')}/Lantas";

        // Uraian tindak pidana & pasal dari accident
        $uraianTindakPidana = 'Kecelakaan Lalu Lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat dan/atau kerusakan kendaraan';
        $pasalPidana = 'Pasal 310 ayat (4) dan/atau ayat (3)';
        $uuPidanaNomor = '22';
        $uuPidanaTahun = '2009';
        $uuPidanaTentang = 'Lalu Lintas dan Angkutan Jalan';
        $tkp = $accident->address_accident_1 ?? 'Wilayah Hukum Polres Setempat';

        $waktuKejadian = '';
        if ($accident->accident_date) {
            try {
                $accDate = Carbon::parse($accident->accident_date);
                $hariAcc = $hariMap[$accDate->format('l')] ?? $accDate->format('l');
                $waktuKejadian = "{$hariAcc}, tanggal " . $this->formatIndonesianDate($accident->accident_date);
            } catch (\Throwable $e) {
                $waktuKejadian = (string) $accident->accident_date;
            }
        }
        if (empty($waktuKejadian)) {
            $waktuKejadian = "{$namaHariIni}, tanggal " . $this->formatIndonesianDate($now);
        }

        $noRegKejahatan = $accident->no_lp ?? '-';

        return view('docs.surat-daftar-pencarian-orang-document.create', [
            'accident' => $accident,
            'accidentId' => $accidentId,
            'poldaName' => $poldaName,
            'resorName' => $resorName,
            'resorAddress' => $resorAddress,
            'defaultNomorDpo' => $defaultNomorDpo,
            'todayFormatted' => $todayFormatted,
            'namaHariIni' => $namaHariIni,
            'availableSuspects' => $availableSuspects,
            'firstSuspect' => $firstSuspect,
            'authorizedSignatories' => $authorizedSignatories,
            'officers' => $officers,
            'uraianTindakPidana' => $uraianTindakPidana,
            'pasalPidana' => $pasalPidana,
            'uuPidanaNomor' => $uuPidanaNomor,
            'uuPidanaTahun' => $uuPidanaTahun,
            'uuPidanaTentang' => $uuPidanaTentang,
            'tkp' => $tkp,
            'waktuKejadian' => $waktuKejadian,
            'noRegKejahatan' => $noRegKejahatan,
        ]);
    }

    /**
     * Simpan Surat DPO baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'kopstuk_1' => 'required|string',
            'kopstuk_2' => 'required|string',
            'kopstuk_3' => 'required|string',
            'kopstuk_4' => 'required|string',
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'dikeluarkan_di' => 'required|string',
            'nomor_hp_penyidik' => 'required|string',
            'rujukan_instansi' => 'required|string',
            'rujukan_nomor' => 'required|string',
            'rujukan_tanggal' => 'required|date',
            'nama_tersangka' => 'required|string',
            'jenis_identitas' => 'required|string',
            'nomor_identitas' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'pekerjaan' => 'required|string',
            'alamat' => 'required|string',
            'kontak_tersangka' => 'required|string',
            'foto_tersangka' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'pernah_dihukum' => 'required|string',
            'hukuman_tempat' => 'required|string',
            'hukuman_lama' => 'required|string',
            'hukuman_keterangan' => 'required|string',
            'rumus_daktiloskopi' => 'required|string',
            'tinggi_badan' => 'required|string',
            'berat_badan' => 'required|string',
            'rambut' => 'required|string',
            'bentuk_tubuh' => 'required|string',
            'warna_kulit' => 'required|string',
            'mata' => 'required|string',
            'hidung' => 'required|string',
            'bibir' => 'required|string',
            'ciri_khusus_lainnya' => 'required|string',
            'uraian_tindak_pidana' => 'required|string',
            'pasal_pidana' => 'required|string',
            'uu_pidana_nomor' => 'required|string',
            'uu_pidana_tahun' => 'required|string',
            'uu_pidana_tentang' => 'required|string',
            'tkp' => 'required|string',
            'waktu_kejadian' => 'required|string',
            'no_reg_kejahatan' => 'required|string',
            'keterangan_lain_penting' => 'required|string',
            'distribusi_kepada' => 'required|string',
            'atasan_nama' => 'required|string',
            'atasan_pangkat' => 'required|string',
            'atasan_nrp' => 'required|string',
            'atasan_jabatan' => 'required|string',
        ]);

        $accidentId = $request->input('accident_id');
        $accident = Accident::findOrFail($accidentId);

        // Handle upload pasfoto 4x6
        $fotoPath = null;
        if ($request->hasFile('foto_tersangka')) {
            $file = $request->file('foto_tersangka');
            $filename = 'dpo_' . $accidentId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/dpo'), $filename);
            $fotoPath = 'documents/dpo/' . $filename;
        }

        $doc = new SuratDaftarPencarianOrangDocument();
        $doc->accident_id = $accidentId;
        $doc->document_category_id = '0302';
        $doc->status_id = '2';

        // Kopstuk & Surat
        $doc->kopstuk_1 = $request->input('kopstuk_1', 'KEPOLISIAN NEGARA REPUBLIK INDONESIA');
        $doc->kopstuk_2 = $request->input('kopstuk_2');
        $doc->kopstuk_3 = $request->input('kopstuk_3');
        $doc->kopstuk_4 = $request->input('kopstuk_4');
        $doc->nomor_surat = $request->input('nomor_surat');
        $doc->tanggal_surat = $request->input('tanggal_surat');
        $doc->dikeluarkan_di = $request->input('dikeluarkan_di');
        $doc->nomor_hp_penyidik = $request->input('nomor_hp_penyidik');

        // Rujukan Surat Permintaan
        $doc->rujukan_instansi = $request->input('rujukan_instansi');
        $doc->rujukan_nomor = $request->input('rujukan_nomor');
        $doc->rujukan_tanggal = $request->input('rujukan_tanggal');

        // Identitas Tersangka (1-8)
        $doc->tersangka_id = $request->input('tersangka_id');
        $doc->tersangka_nama = $request->input('nama_tersangka');
        $doc->tersangka_jenis_identitas = $request->input('jenis_identitas', 'KTP');
        $doc->tersangka_nomor_identitas = $request->input('nomor_identitas');
        $doc->tersangka_kewarganegaraan = $request->input('kewarganegaraan', 'Indonesia');
        $doc->tersangka_jenis_kelamin = $request->input('jenis_kelamin');
        $doc->tersangka_tempat_lahir = $request->input('tempat_lahir');
        $doc->tersangka_tanggal_lahir = $request->input('tanggal_lahir');
        $doc->tersangka_pekerjaan = $request->input('pekerjaan');
        $doc->tersangka_alamat = $request->input('alamat');
        $doc->tersangka_kontak = $request->input('kontak_tersangka');
        if ($fotoPath) {
            $doc->foto_tersangka_path = $fotoPath;
        }

        // Catatan Hukuman (9)
        $doc->pernah_dihukum = $request->input('pernah_dihukum', 'Tidak Pernah');
        $doc->hukuman_tempat = $request->input('hukuman_tempat');
        $doc->hukuman_lama = $request->input('hukuman_lama');
        $doc->hukuman_keterangan = $request->input('hukuman_keterangan');

        // Daktiloskopi (10)
        $doc->rumus_daktiloskopi = $request->input('rumus_daktiloskopi');

        // Ciri-Ciri Khusus Fisik (11)
        $doc->tinggi_badan = $request->input('tinggi_badan');
        $doc->berat_badan = $request->input('berat_badan');
        $doc->rambut = $request->input('rambut');
        $doc->bentuk_tubuh = $request->input('bentuk_tubuh');
        $doc->warna_kulit = $request->input('warna_kulit');
        $doc->mata = $request->input('mata');
        $doc->hidung = $request->input('hidung');
        $doc->bibir = $request->input('bibir');
        $doc->ciri_khusus_lainnya = $request->input('ciri_khusus_lainnya');

        // Uraian Kasus (12-15)
        $doc->uraian_tindak_pidana = $request->input('uraian_tindak_pidana');
        $doc->pasal_pidana = $request->input('pasal_pidana');
        $doc->uu_pidana_nomor = $request->input('uu_pidana_nomor', '22');
        $doc->uu_pidana_tahun = $request->input('uu_pidana_tahun', '2009');
        $doc->uu_pidana_tentang = $request->input('uu_pidana_tentang', 'Lalu Lintas dan Angkutan Jalan');
        $doc->tkp = $request->input('tkp');
        $doc->waktu_kejadian = $request->input('waktu_kejadian');
        $doc->no_reg_kejahatan = $request->input('no_reg_kejahatan');
        $doc->keterangan_lain_penting = $request->input('keterangan_lain_penting');

        // Distribusi / Kepada
        $doc->distribusi_kepada = $request->input('distribusi_kepada');

        // Atasan Penyidik
        $doc->atasan_nama = $request->input('atasan_nama');
        $doc->atasan_pangkat = $request->input('atasan_pangkat');
        $doc->atasan_nrp = $request->input('atasan_nrp');
        $doc->atasan_jabatan = $request->input('atasan_jabatan', 'ATASAN PENYIDIK');

        $doc->created_by_user_id = Auth::id();
        $doc->updated_by_user_id = Auth::id();
        $doc->save();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Daftar Pencarian Orang (DPO) berhasil disimpan.');
    }

    /**
     * Tampilkan form edit Dokumen DPO
     */
    public function edit($id, Request $request)
    {
        $doc = SuratDaftarPencarianOrangDocument::findOrFail($id);
        $accidentId = $doc->accident_id;
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

        return view('docs.surat-daftar-pencarian-orang-document.edit', [
            'doc' => $doc,
            'accident' => $accident,
            'accidentId' => $accidentId,
            'authorizedSignatories' => $authorizedSignatories,
            'officers' => $officers,
            'availableSuspects' => $availableSuspects,
        ]);
    }

    /**
     * Update Dokumen DPO
     */
    public function update(Request $request, $id)
    {
        $doc = SuratDaftarPencarianOrangDocument::findOrFail($id);
        $accidentId = $doc->accident_id;

        $request->validate([
            'kopstuk_1' => 'required|string',
            'kopstuk_2' => 'required|string',
            'kopstuk_3' => 'required|string',
            'kopstuk_4' => 'required|string',
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
            'dikeluarkan_di' => 'required|string',
            'nomor_hp_penyidik' => 'required|string',
            'rujukan_instansi' => 'required|string',
            'rujukan_nomor' => 'required|string',
            'rujukan_tanggal' => 'required|date',
            'nama_tersangka' => 'required|string',
            'jenis_identitas' => 'required|string',
            'nomor_identitas' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'pekerjaan' => 'required|string',
            'alamat' => 'required|string',
            'kontak_tersangka' => 'required|string',
            'foto_tersangka' => $doc->foto_tersangka_path ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
            'pernah_dihukum' => 'required|string',
            'hukuman_tempat' => 'required|string',
            'hukuman_lama' => 'required|string',
            'hukuman_keterangan' => 'required|string',
            'rumus_daktiloskopi' => 'required|string',
            'tinggi_badan' => 'required|string',
            'berat_badan' => 'required|string',
            'rambut' => 'required|string',
            'bentuk_tubuh' => 'required|string',
            'warna_kulit' => 'required|string',
            'mata' => 'required|string',
            'hidung' => 'required|string',
            'bibir' => 'required|string',
            'ciri_khusus_lainnya' => 'required|string',
            'uraian_tindak_pidana' => 'required|string',
            'pasal_pidana' => 'required|string',
            'uu_pidana_nomor' => 'required|string',
            'uu_pidana_tahun' => 'required|string',
            'uu_pidana_tentang' => 'required|string',
            'tkp' => 'required|string',
            'waktu_kejadian' => 'required|string',
            'no_reg_kejahatan' => 'required|string',
            'keterangan_lain_penting' => 'required|string',
            'distribusi_kepada' => 'required|string',
            'atasan_nama' => 'required|string',
            'atasan_pangkat' => 'required|string',
            'atasan_nrp' => 'required|string',
            'atasan_jabatan' => 'required|string',
        ]);

        // Handle upload pasfoto 4x6 baru
        if ($request->hasFile('foto_tersangka')) {
            $file = $request->file('foto_tersangka');
            $filename = 'dpo_' . $accidentId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/dpo'), $filename);
            $doc->foto_tersangka_path = 'documents/dpo/' . $filename;
        }

        // Kopstuk & Surat
        $doc->kopstuk_1 = $request->input('kopstuk_1', 'KEPOLISIAN NEGARA REPUBLIK INDONESIA');
        $doc->kopstuk_2 = $request->input('kopstuk_2');
        $doc->kopstuk_3 = $request->input('kopstuk_3');
        $doc->kopstuk_4 = $request->input('kopstuk_4');
        $doc->nomor_surat = $request->input('nomor_surat');
        $doc->tanggal_surat = $request->input('tanggal_surat');
        $doc->dikeluarkan_di = $request->input('dikeluarkan_di');
        $doc->nomor_hp_penyidik = $request->input('nomor_hp_penyidik');

        // Rujukan Surat Permintaan
        $doc->rujukan_instansi = $request->input('rujukan_instansi');
        $doc->rujukan_nomor = $request->input('rujukan_nomor');
        $doc->rujukan_tanggal = $request->input('rujukan_tanggal');

        // Identitas Tersangka (1-8)
        $doc->tersangka_id = $request->input('tersangka_id');
        $doc->tersangka_nama = $request->input('nama_tersangka');
        $doc->tersangka_jenis_identitas = $request->input('jenis_identitas', 'KTP');
        $doc->tersangka_nomor_identitas = $request->input('nomor_identitas');
        $doc->tersangka_kewarganegaraan = $request->input('kewarganegaraan', 'Indonesia');
        $doc->tersangka_jenis_kelamin = $request->input('jenis_kelamin');
        $doc->tersangka_tempat_lahir = $request->input('tempat_lahir');
        $doc->tersangka_tanggal_lahir = $request->input('tanggal_lahir');
        $doc->tersangka_pekerjaan = $request->input('pekerjaan');
        $doc->tersangka_alamat = $request->input('alamat');
        $doc->tersangka_kontak = $request->input('kontak_tersangka');

        // Catatan Hukuman (9)
        $doc->pernah_dihukum = $request->input('pernah_dihukum', 'Tidak Pernah');
        $doc->hukuman_tempat = $request->input('hukuman_tempat');
        $doc->hukuman_lama = $request->input('hukuman_lama');
        $doc->hukuman_keterangan = $request->input('hukuman_keterangan');

        // Daktiloskopi (10)
        $doc->rumus_daktiloskopi = $request->input('rumus_daktiloskopi');

        // Ciri-Ciri Khusus Fisik (11)
        $doc->tinggi_badan = $request->input('tinggi_badan');
        $doc->berat_badan = $request->input('berat_badan');
        $doc->rambut = $request->input('rambut');
        $doc->bentuk_tubuh = $request->input('bentuk_tubuh');
        $doc->warna_kulit = $request->input('warna_kulit');
        $doc->mata = $request->input('mata');
        $doc->hidung = $request->input('hidung');
        $doc->bibir = $request->input('bibir');
        $doc->ciri_khusus_lainnya = $request->input('ciri_khusus_lainnya');

        // Uraian Kasus (12-15)
        $doc->uraian_tindak_pidana = $request->input('uraian_tindak_pidana');
        $doc->pasal_pidana = $request->input('pasal_pidana');
        $doc->uu_pidana_nomor = $request->input('uu_pidana_nomor', '22');
        $doc->uu_pidana_tahun = $request->input('uu_pidana_tahun', '2009');
        $doc->uu_pidana_tentang = $request->input('uu_pidana_tentang', 'Lalu Lintas dan Angkutan Jalan');
        $doc->tkp = $request->input('tkp');
        $doc->waktu_kejadian = $request->input('waktu_kejadian');
        $doc->no_reg_kejahatan = $request->input('no_reg_kejahatan');
        $doc->keterangan_lain_penting = $request->input('keterangan_lain_penting');

        // Distribusi / Kepada
        $doc->distribusi_kepada = $request->input('distribusi_kepada');

        // Atasan Penyidik
        $doc->atasan_nama = $request->input('atasan_nama');
        $doc->atasan_pangkat = $request->input('atasan_pangkat');
        $doc->atasan_nrp = $request->input('atasan_nrp');
        $doc->atasan_jabatan = $request->input('atasan_jabatan', 'ATASAN PENYIDIK');

        $doc->updated_by_user_id = Auth::id();
        $doc->save();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Daftar Pencarian Orang (DPO) berhasil diperbarui.');
    }

    /**
     * Download dokumen Word .docx
     */
    public function download($id, Request $request)
    {
        $doc = SuratDaftarPencarianOrangDocument::with('accident.polres.polda')->findOrFail($id);
        $templatePath = public_path('word-template/daftar_pencarian_orang.docx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template Word tidak ditemukan.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Sanitize function to avoid auto-hyperlink in Word
        $cleanSlash = function ($str) {
            if (!$str) return '';
            return str_replace('/', "/\u{200B}", $str);
        };

        // Kopstuk
        $templateProcessor->setValue('kopstuk_1', $doc->kopstuk_1 ?: 'KEPOLISIAN NEGARA REPUBLIK INDONESIA');
        $templateProcessor->setValue('kopstuk_2', $doc->kopstuk_2 ?: '');
        $templateProcessor->setValue('kopstuk_3', $doc->kopstuk_3 ?: '');
        $templateProcessor->setValue('kopstuk_4', $doc->kopstuk_4 ?: '');

        // Nomor Surat & HP
        $templateProcessor->setValue('nomor_surat', $cleanSlash($doc->nomor_surat));
        $templateProcessor->setValue('nomor_hp_penyidik', $doc->nomor_hp_penyidik ?: '+6280000000000');

        // Pasfoto 4x6
        $hasPhoto = false;
        if ($doc->foto_tersangka_path && file_exists(public_path($doc->foto_tersangka_path))) {
            try {
                $templateProcessor->setImageValue('photo_tersangka', [
                    'path' => public_path($doc->foto_tersangka_path),
                    'width' => 115,
                    'height' => 160,
                    'ratio' => false,
                ]);
                $hasPhoto = true;
            } catch (\Throwable $e) {}
        }
        if (!$hasPhoto) {
            $templateProcessor->setValue('photo_tersangka', "Photo 4 X 6\nTersangka");
        }

        // Rujukan Surat Permintaan
        $rujukanLines = explode("\n", $doc->rujukan_instansi ?: '');
        $templateProcessor->setValue('rujukan_instansi_line1', trim($rujukanLines[0] ?? '.........................................................................'));
        $templateProcessor->setValue('rujukan_instansi_line2', trim($rujukanLines[1] ?? '.........................................................................'));
        $templateProcessor->setValue('rujukan_nomor', $cleanSlash($doc->rujukan_nomor) ?: '................................................................');
        $tglRujukan = $doc->rujukan_tanggal ? $this->formatIndonesianDate($doc->rujukan_tanggal) : '................................................................';
        $templateProcessor->setValue('rujukan_tanggal', $tglRujukan);

        // Identitas Tersangka (1-8)
        $templateProcessor->setValue('nama_tersangka', $doc->tersangka_nama ?: '.........................................................................');
        $noIdentitas = ($doc->tersangka_jenis_identitas ? $doc->tersangka_jenis_identitas . ': ' : '') . ($doc->tersangka_nomor_identitas ?: '.........................................................................');
        $templateProcessor->setValue('nomor_identitas', $cleanSlash($noIdentitas));
        $templateProcessor->setValue('kewarganegaraan', $doc->tersangka_kewarganegaraan ?: 'Indonesia');
        $templateProcessor->setValue('jenis_kelamin', $doc->tersangka_jenis_kelamin ?: '-');

        $ttl = trim(($doc->tersangka_tempat_lahir ?: '') . ($doc->tersangka_tanggal_lahir ? ', ' . $this->formatIndonesianDate($doc->tersangka_tanggal_lahir) : ''));
        $templateProcessor->setValue('tempat_tanggal_lahir', $ttl ?: '.........................................................................');
        $templateProcessor->setValue('pekerjaan', $doc->tersangka_pekerjaan ?: '.........................................................................');
        $templateProcessor->setValue('alamat', $doc->tersangka_alamat ?: '.........................................................................');
        $templateProcessor->setValue('kontak_tersangka', $doc->tersangka_kontak ?: '.........................................................................');

        // Catatan Hukuman (9)
        $statusHukuman = $doc->pernah_dihukum ?: 'Tidak Pernah';
        $templateProcessor->setValue('catatan_hukuman_status', "[{$statusHukuman}]");
        $templateProcessor->setValue('hukuman_tempat', $doc->hukuman_tempat ?: '.................................................................');
        $templateProcessor->setValue('hukuman_lama', $doc->hukuman_lama ?: '.........................................');
        $templateProcessor->setValue('hukuman_keterangan', $doc->hukuman_keterangan ?: '.................................................................');

        // Daktiloskopi (10)
        $templateProcessor->setValue('rumus_daktiloskopi', $doc->rumus_daktiloskopi ?: '.........................................................................');

        // Ciri-Ciri Khusus Fisik (11)
        $templateProcessor->setValue('tinggi_badan', $doc->tinggi_badan ?: '.........');
        $templateProcessor->setValue('berat_badan', $doc->berat_badan ?: '.........');
        $templateProcessor->setValue('rambut', $doc->rambut ?: '............................................................');
        $templateProcessor->setValue('bentuk_tubuh', $doc->bentuk_tubuh ?: '....................................................');
        $templateProcessor->setValue('warna_kulit', $doc->warna_kulit ?: '....................................................');
        $templateProcessor->setValue('mata', $doc->mata ?: '...............................................................');
        $templateProcessor->setValue('hidung', $doc->hidung ?: '.............................................................');
        $templateProcessor->setValue('bibir', $doc->bibir ?: '...............................................................');
        $templateProcessor->setValue('ciri_khusus_lainnya', $doc->ciri_khusus_lainnya ?: '........................................................');

        // Uraian Perkara (12-15)
        $templateProcessor->setValue('uraian_tindak_pidana', $doc->uraian_tindak_pidana ?: 'Kecelakaan Lalu Lintas');
        $templateProcessor->setValue('pasal_pidana', $cleanSlash($doc->pasal_pidana ?: 'Pasal 310 ayat (4)'));
        $templateProcessor->setValue('uu_pidana_nomor', $doc->uu_pidana_nomor ?: '22');
        $templateProcessor->setValue('uu_pidana_tahun', $doc->uu_pidana_tahun ?: '2009');
        $templateProcessor->setValue('uu_pidana_tentang', $doc->uu_pidana_tentang ?: 'Lalu Lintas dan Angkutan Jalan');
        $templateProcessor->setValue('tkp', $doc->tkp ?: 'TKP Setempat');

        // Parse waktu kejadian
        $hariKejadian = '-';
        $tglKejadian = '-';
        if ($doc->waktu_kejadian) {
            $tglKejadian = $doc->waktu_kejadian;
        } elseif ($doc->accident && $doc->accident->accident_date) {
            try {
                $accDate = Carbon::parse($doc->accident->accident_date);
                $hariMap = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                $hariKejadian = $hariMap[$accDate->format('l')] ?? $accDate->format('l');
                $tglKejadian = $this->formatIndonesianDate($doc->accident->accident_date);
            } catch (\Throwable $e) {}
        }
        $templateProcessor->setValue('hari_kejadian', $hariKejadian);
        $templateProcessor->setValue('tanggal_kejadian', $tglKejadian);

        $templateProcessor->setValue('no_reg_kejahatan', $cleanSlash($doc->no_reg_kejahatan ?: '-'));
        $templateProcessor->setValue('keterangan_lain_penting', $doc->keterangan_lain_penting ?: '.........................................................................');

        // Bottom Section
        $templateProcessor->setValue('kepada_3', $doc->distribusi_kepada ?: 'Kasatlantas / Penyidik Pembantu.');
        $templateProcessor->setValue('dikeluarkan_di', $doc->dikeluarkan_di ?: 'Muaradua');
        $templateProcessor->setValue('tanggal_surat_indo', $doc->tanggal_surat ? $this->formatIndonesianDate($doc->tanggal_surat) : date('d/m/Y'));
        $templateProcessor->setValue('atasan_nama', strtoupper($doc->atasan_nama ?: 'NAMA ATASAN PENYIDIK'));
        
        $pangkatNrp = trim(($doc->atasan_pangkat ?: '') . ($doc->atasan_nrp ? ' NRP ' . $doc->atasan_nrp : ''));
        $templateProcessor->setValue('atasan_pangkat_nrp', $pangkatNrp ?: 'PANGKAT NRP 00000000');

        // Output file
        $cleanFilename = 'DPO_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $doc->nomor_surat ?: 'Surat_DPO') . '.docx';
        $tempPath = storage_path('app/' . $cleanFilename);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, $cleanFilename)->deleteFileAfterSend(true);
    }

    /**
     * Hapus Dokumen DPO (Soft Delete)
     */
    public function delete($id, Request $request)
    {
        $doc = SuratDaftarPencarianOrangDocument::findOrFail($id);
        $accidentId = $doc->accident_id;
        $doc->deleted_by_user_id = Auth::id();
        $doc->save();
        $doc->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Daftar Pencarian Orang (DPO) berhasil dihapus.');
    }

    /**
     * Format tanggal Indonesia
     */
    private function formatIndonesianDate($date)
    {
        if (!$date) return '';
        try {
            $c = Carbon::parse($date);
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            return $c->format('d') . ' ' . ($months[(int) $c->format('m')] ?? $c->format('F')) . ' ' . $c->format('Y');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    /**
     * Ambil konsolidasi daftar tersangka dari kasus
     */
    private function getConsolidatedSuspects($accidentId)
    {
        $consolidated = [];
        $seenNames = [];

        // 1. Dari tabel suspects
        try {
            $suspects = DB::table('suspects')
                ->leftJoin('lib.religions as lr', 'suspects.religion_id', '=', 'lr.id')
                ->leftJoin('lib.genders as lg', 'suspects.gender_id', '=', 'lg.id')
                ->leftJoin('lib.jobs as lj', 'suspects.job_id', '=', 'lj.id')
                ->where('suspects.accident_id', $accidentId)
                ->select(
                    'suspects.*',
                    'lr.name as religion_name',
                    'lg.name as gender_name',
                    'lj.name as job_name'
                )
                ->get();

            foreach ($suspects as $s) {
                $nameClean = trim($s->name ?? '');
                $nameKey = strtolower($nameClean);
                if ($nameClean !== '' && !isset($seenNames[$nameKey])) {
                    $seenNames[$nameKey] = true;
                    $consolidated[] = [
                        'source' => 'suspects',
                        'source_id' => (string) ($s->id ?? ''),
                        'nama' => $nameClean,
                        'no_identitas' => trim($s->identity_number ?? '') ?: '-',
                        'kewarganegaraan' => trim($s->nationality ?? 'Indonesia') ?: 'Indonesia',
                        'jenis_kelamin' => $this->resolveGenderName($s->gender_name ?? $s->gender_id),
                        'tempat_lahir' => trim($s->birth_place ?? ''),
                        'tanggal_lahir' => $s->birth_date ?? null,
                        'pekerjaan' => ucwords(strtolower(trim($s->job_name ?? '-'))) ?: '-',
                        'agama' => $this->resolveReligionName($s->religion_name ?? $s->religion_id),
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
                    $consolidated[] = [
                        'source' => 'daftar_tersangka',
                        'source_id' => (string) ($ds->id ?? ''),
                        'nama' => $nameClean,
                        'no_identitas' => trim($ds->identity_number ?? ($ds->no_identitas ?? '')) ?: '-',
                        'kewarganegaraan' => trim($ds->citizenship ?? 'Indonesia') ?: 'Indonesia',
                        'jenis_kelamin' => $this->resolveGenderName($ds->gender_name ?? $ds->gender),
                        'tempat_lahir' => trim($ds->birthplace ?? ($ds->tempat_lahir ?? '')),
                        'tanggal_lahir' => $ds->birthdate ?? ($ds->tanggal_lahir ?? null),
                        'pekerjaan' => trim($ds->job ?? ($ds->pekerjaan ?? '-')),
                        'agama' => $this->resolveReligionName($ds->religion_name ?? $ds->religion),
                        'alamat' => trim($ds->address ?? ($ds->alamat ?? '-')),
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $consolidated;
    }

    private function resolveGenderName($val)
    {
        if (!$val) return 'Laki-laki';
        $v = strtolower(trim((string)$val));
        if (in_array($v, ['1', 'l', 'laki-laki', 'pria', 'male', 'm'])) return 'Laki-laki';
        if (in_array($v, ['2', 'p', 'perempuan', 'wanita', 'female', 'f'])) return 'Perempuan';
        return (string)$val;
    }

    private function resolveReligionName($val)
    {
        if (!$val) return 'Islam';
        $v = strtolower(trim((string)$val));
        if (in_array($v, ['1', 'islam'])) return 'Islam';
        if (in_array($v, ['2', 'kristen', 'protestan'])) return 'Kristen';
        if (in_array($v, ['3', 'katolik'])) return 'Katolik';
        if (in_array($v, ['4', 'hindu'])) return 'Hindu';
        if (in_array($v, ['5', 'buddha', 'budha'])) return 'Buddha';
        if (in_array($v, ['6', 'khonghucu', 'konghucu'])) return 'Khonghucu';
        return (string)$val;
    }
}

