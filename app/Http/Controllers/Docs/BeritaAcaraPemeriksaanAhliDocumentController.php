<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\BeritaAcaraPemeriksaanAhliDocument\BeritaAcaraPemeriksaanAhliDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPermintaanKeteranganAhliDocument\SuratPermintaanKeteranganAhliDocument;
use App\Models\Officer;
use App\Models\Suspect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class BeritaAcaraPemeriksaanAhliDocumentController extends Controller
{
    private function formatDateIndo($dateStr)
    {
        if (empty($dateStr)) return '-';
        try {
            $months = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $d = Carbon::parse($dateStr);
            return $d->day . ' ' . $months[$d->month] . ' ' . $d->year;
        } catch (\Exception $e) {
            return $dateStr;
        }
    }

    private function getNamaHari($dateStr)
    {
        if (empty($dateStr)) return 'Senin';
        try {
            $days = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            $dayEn = Carbon::parse($dateStr)->format('l');
            return $days[$dayEn] ?? 'Senin';
        } catch (\Exception $e) {
            return 'Senin';
        }
    }

    private function terbilang($angka)
    {
        $angka = abs((int)$angka);
        $baca = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $terbilang = $this->terbilang((int)($angka / 10)) . " puluh" . $this->terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " seratus" . $this->terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = $this->terbilang((int)($angka / 100)) . " ratus" . $this->terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " seribu" . $this->terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = $this->terbilang((int)($angka / 1000)) . " ribu" . $this->terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = $this->terbilang((int)($angka / 1000000)) . " juta" . $this->terbilang($angka % 1000000);
        }

        return trim($terbilang);
    }

    private function getMasterData($accidentId)
    {
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $officers = collect();
        try {
            $officers = Officer::with(['rank', 'position'])
                ->selectFullName()
                ->orderBy('first_name', 'asc')
                ->get();
        } catch (\Exception $e) {
            try {
                $officers = Officer::with(['rank', 'position'])->get();
            } catch (\Exception $e2) {}
        }

        $suspects = collect();
        try {
            $suspects = Suspect::where('accident_id', $accidentId)->get();
        } catch (\Exception $e) {}

        $sprindik = null;
        try {
            $sprindik = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                ->orderByDesc('created_at')
                ->first();
        } catch (\Exception $e) {}

        $permintaanAhliDoc = null;
        try {
            $permintaanAhliDoc = SuratPermintaanKeteranganAhliDocument::where('accident_id', $accidentId)
                ->orderByDesc('created_at')
                ->first();
        } catch (\Exception $e) {}

        return compact('accident', 'officers', 'suspects', 'sprindik', 'permintaanAhliDoc');
    }

    public function create(Request $request)
    {
        $accidentId = $request->accident_id;
        $data = $this->getMasterData($accidentId);

        $defaultDocNumber = 'BA-AHLI/' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT) . '/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas';

        return view('docs.berita-acara-pemeriksaan-ahli-document.create', array_merge($data, [
            'accidentId' => $accidentId,
            'defaultDocNumber' => $defaultDocNumber,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nama_ahli' => 'required',
        ]);

        $accidentId = $request->accident_id;
        $documentDate = $request->document_date ? Carbon::parse($request->document_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $docData = $request->except(['_token', 'accident_id', 'document_number', 'document_date']);

        BeritaAcaraPemeriksaanAhliDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->document_number ?? '-',
                'document_date' => $documentDate,
                'data' => $docData,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Ahli berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = BeritaAcaraPemeriksaanAhliDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.berita-acara-pemeriksaan-ahli-document.edit', array_merge($data, [
            'document' => $savedDoc,
            'savedData' => $savedData,
            'accidentId' => $accidentId,
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nama_ahli' => 'required',
        ]);

        $accidentId = $request->accident_id;
        $documentDate = $request->document_date ? Carbon::parse($request->document_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $docData = $request->except(['_token', '_method', 'accident_id', 'document_number', 'document_date']);

        BeritaAcaraPemeriksaanAhliDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->document_number ?? '-',
                'document_date' => $documentDate,
                'data' => $docData,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Ahli berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        BeritaAcaraPemeriksaanAhliDocument::where('accident_id', $accidentId)->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Ahli berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        Settings::setOutputEscapingEnabled(true);

        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Jika dipanggil via link tanpa payload form lengkap, muat data tersimpan di DB
        if (!$request->filled('nama_ahli')) {
            $savedDoc = BeritaAcaraPemeriksaanAhliDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/berita_acara_pemeriksaan_ahli.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template Word BAP Ahli tidak ditemukan.');
        }

        $template = new TemplateProcessor($templatePath);

        // Kopstuk
        $polres = $accident->polres;
        $polda = $polres->polda ?? null;

        $daerahPoliceFullName = strtoupper($polda->name ?? 'METRO JAYA');
        $resorPoliceFullName = strtoupper($polres->name ?? 'RESOR KOTA');
        $resorPoliceAddress = $polres->address ?? 'Jl. Jenderal Sudirman No. 55';

        // Waktu Pemeriksaan
        $tglPemeriksaanRaw = $request->input('tanggal_pemeriksaan', date('Y-m-d'));
        $dateObj = Carbon::parse($tglPemeriksaanRaw);
        $hariPemeriksaan = $request->input('hari_pemeriksaan', $this->getNamaHari($tglPemeriksaanRaw));
        $tanggalPemeriksaanAngka = $dateObj->day;
        $bulanList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $namaBulan = $bulanList[$dateObj->month] ?? 'Januari';
        $tahunPemeriksaan = $dateObj->year;
        $tahunTerbilang = $this->terbilang($tahunPemeriksaan);
        $jamPemeriksaan = $request->input('jam_pemeriksaan', '10.00');
        $zonaWaktu = $request->input('zona_waktu', 'WIB');

        // Penyidik 1 & 2
        $penyidik1Nama = strtoupper($request->input('penyidik_1_nama', 'PENYIDIK SATU'));
        $penyidik1Pangkat = $request->input('penyidik_1_pangkat', 'AIPDA');
        $penyidik1Nrp = $request->input('penyidik_1_nrp', '00000000');
        $penyidik1Jabatan = $request->input('penyidik_1_jabatan', 'Penyidik Pembantu');

        if ($request->filled('penyidik_1_officer_id')) {
            $off1 = Officer::with(['rank'])->selectFullName()->find($request->penyidik_1_officer_id);
            if ($off1) {
                $penyidik1Nama = strtoupper($off1->full_name ?? ($off1->first_name . ' ' . $off1->last_name));
                $penyidik1Pangkat = $off1->rank->name ?? $penyidik1Pangkat;
                $penyidik1Nrp = $off1->register_number ?? $penyidik1Nrp;
            }
        }

        $penyidik2Nama = strtoupper($request->input('penyidik_2_nama', 'PENYIDIK DUA'));
        $penyidik2Pangkat = $request->input('penyidik_2_pangkat', 'BRIPKA');
        $penyidik2Nrp = $request->input('penyidik_2_nrp', '00000000');
        $penyidik2Jabatan = $request->input('penyidik_2_jabatan', 'Penyidik Pembantu');

        if ($request->filled('penyidik_2_officer_id')) {
            $off2 = Officer::with(['rank'])->selectFullName()->find($request->penyidik_2_officer_id);
            if ($off2) {
                $penyidik2Nama = strtoupper($off2->full_name ?? ($off2->first_name . ' ' . $off2->last_name));
                $penyidik2Pangkat = $off2->rank->name ?? $penyidik2Pangkat;
                $penyidik2Nrp = $off2->register_number ?? $penyidik2Nrp;
            }
        }

        // Dasar Pemeriksaan
        $noSprindik = $request->input('no_sprindik', 'SP.Sidik/01/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas');
        $tanggalSprindik = $this->formatDateIndo($request->input('tanggal_sprindik', date('Y-m-d')));

        // Identitas Ahli
        $namaAhli = strtoupper($request->input('nama_ahli', '-'));
        $nikAhli = $request->input('nik_ahli', '-');
        $kewarganegaraanAhli = $request->input('kewarganegaraan_ahli', 'Indonesia');
        $jenisKelaminAhli = $request->input('jenis_kelamin_ahli', 'Laki-laki');
        $tempatLahirAhli = $request->input('tempat_lahir_ahli', '-');
        $tanggalLahirAhli = $this->formatDateIndo($request->input('tanggal_lahir_ahli', '1985-01-01'));
        $pekerjaanAhli = $request->input('pekerjaan_ahli', 'Dosen / Ahli');
        $agamaAhli = $request->input('agama_ahli', 'Islam');
        $alamatAhli = $request->input('alamat_ahli', '-');
        $bidangKeahlian = $request->input('bidang_keahlian', 'Lalu Lintas dan Angkutan Jalan');
        $instansiAhli = $request->input('instansi_ahli', 'Dinas Perhubungan');

        // Data Perkara
        $tindakPidana = $request->input('tindak_pidana', 'Kecelakaan Lalu Lintas');
        $pasalPelanggaran = $request->input('pasal_pelanggaran', 'Pasal 310 ayat (4)');
        $uuNomorTahun = $request->input('uu_nomor_tahun', '22 Tahun 2009');
        $perihalUu = $request->input('perihal_uu', 'Lalu Lintas dan Angkutan Jalan');
        $tkp = $request->input('tkp', $accident->accident_road ?? $accident->accident_address ?? '-');
        $hariKejadian = $request->input('hari_kejadian', $this->getNamaHari($accident->accident_date));
        $tanggalKejadian = $this->formatDateIndo($request->input('tanggal_kejadian', $accident->accident_date));
        $waktuKejadian = $request->input('waktu_kejadian', $accident->accident_time ?? '10.00');
        $namaTersangka = $request->input('nama_tersangka', '-');
        $noLp = $request->input('no_lp', $accident->accident_code ?? ($accident->no_lp ?? '-'));
        $tanggalLp = $this->formatDateIndo($request->input('tanggal_lp', $accident->accident_report_date ?? $accident->accident_date));
        $namaPelaporKorban = $request->input('nama_pelapor_korban', 'Pelapor / Korban');

        // Replace Kopstuk
        $template->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $template->setValue('resorPoliceFullName', $resorPoliceFullName);
        $template->setValue('resorPoliceAddress', $resorPoliceAddress);

        // Replace Pembukaan
        $template->setValue('hari_pemeriksaan', $hariPemeriksaan);
        $template->setValue('tanggal_pemeriksaan_angka', $tanggalPemeriksaanAngka);
        $template->setValue('bulan_pemeriksaan', $namaBulan);
        $template->setValue('tahun_pemeriksaan', $tahunPemeriksaan);
        $template->setValue('tahun_terbilang', $tahunTerbilang);
        $template->setValue('jam_pemeriksaan', $jamPemeriksaan);
        $template->setValue('zona_waktu', $zonaWaktu);

        $template->setValue('penyidik_1_nama', $penyidik1Nama);
        $template->setValue('penyidik_1_pangkat', $penyidik1Pangkat);
        $template->setValue('penyidik_1_nrp', $penyidik1Nrp);
        $template->setValue('penyidik_1_jabatan', $penyidik1Jabatan);

        $template->setValue('penyidik_2_nama', $penyidik2Nama);
        $template->setValue('penyidik_2_pangkat', $penyidik2Pangkat);
        $template->setValue('penyidik_2_nrp', $penyidik2Nrp);
        $template->setValue('penyidik_2_jabatan', $penyidik2Jabatan);

        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tanggalSprindik);

        $template->setValue('nama_ahli', $namaAhli);
        $template->setValue('nik_ahli', $nikAhli);
        $template->setValue('kewarganegaraan_ahli', $kewarganegaraanAhli);
        $template->setValue('jenis_kelamin_ahli', $jenisKelaminAhli);
        $template->setValue('tempat_lahir_ahli', $tempatLahirAhli);
        $template->setValue('tanggal_lahir_ahli', $tanggalLahirAhli);
        $template->setValue('pekerjaan_ahli', $pekerjaanAhli);
        $template->setValue('agama_ahli', $agamaAhli);
        $template->setValue('alamat_ahli', $alamatAhli);
        $template->setValue('bidang_keahlian', $bidangKeahlian);

        $template->setValue('tindak_pidana', $tindakPidana);
        $template->setValue('pasal_pelanggaran', $pasalPelanggaran);
        $template->setValue('uu_nomor_tahun', $uuNomorTahun);
        $template->setValue('perihal_uu', $perihalUu);
        $template->setValue('tkp', $tkp);
        $template->setValue('hari_kejadian', $hariKejadian);
        $template->setValue('tanggal_kejadian', $tanggalKejadian);
        $template->setValue('waktu_kejadian', $waktuKejadian);
        $template->setValue('nama_tersangka', $namaTersangka);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tanggalLp);

        // Pertanyaan 1 s.d. 16
        // Q1
        $pertanyaan1 = $request->input('pertanyaan_1', 'Apakah pada saat ini saudara Ahli dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya? -----------------------------------------------------------------------------');
        $jawaban1 = $request->input('jawaban_1', 'Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya. ---------------------------------------------------------------------------------------------');
        $template->setValue('pertanyaan_1', '1.   ' . $pertanyaan1);
        $template->setValue('jawaban_1', $jawaban1);

        // Q2
        $pertanyaan2 = $request->input('pertanyaan_2', 'Pada saat ini saudara akan diperiksa sebagai Ahli dibidang ' . $bidangKeahlian . ', sehubungan dengan adanya dugaan terjadinya Tindak Pidana ' . $tindakPidana . ' sebagaimana dimaksud dalam Pasal ' . $pasalPelanggaran . ' UU Nomor ' . $uuNomorTahun . ' tentang ' . $perihalUu . ', yang terjadi di ' . $tkp . ', pada hari ' . $hariKejadian . ' tanggal ' . $tanggalKejadian . ' sekira pukul ' . $waktuKejadian . ' ' . $zonaWaktu . ', dengan Tersangka atas nama ' . $namaTersangka . ', sesuai dengan Laporan Polisi Nomor: ' . $noLp . ' tanggal ' . $tanggalLp . ', apakah saudara Ahli mengerti penjelasan Penyidik tersebut? -----------------------------');
        $jawaban2 = $request->input('jawaban_2', 'Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Ahli dibidang ' . $bidangKeahlian . ', sehubungan dengan adanya dugaan terjadinya Tindak Pidana ' . $tindakPidana . ' sebagaimana dimaksud dalam Pasal ' . $pasalPelanggaran . ' UU Nomor ' . $uuNomorTahun . ' tentang ' . $perihalUu . ', yang terjadi di ' . $tkp . ', pada hari ' . $hariKejadian . ' tanggal ' . $tanggalKejadian . ' sekira pukul ' . $waktuKejadian . ' ' . $zonaWaktu . ', dengan Tersangka atas nama ' . $namaTersangka . ', sesuai dengan Laporan Polisi Nomor: ' . $noLp . ' tanggal ' . $tanggalLp . '. ---------------------------------------');
        $template->setValue('pertanyaan_2', '2.   ' . $pertanyaan2);
        $template->setValue('jawaban_2', $jawaban2);

        // Q3 (Sumpah KUHAP)
        $pertanyaan3 = $request->input('pertanyaan_3', 'Sesuai dengan ketentuan Pasal 120 ayat (2) KUHAP, maka sebelum Ahli memberikan keterangan mengenai perkara yang akan dilakukan pemeriksaan oleh Penyidik wajib mengangkat sumpah, sehubungan dengan hal tersebut apakah saudara ahli bersedia mengucapkan sumpah menurut agama yang saudara anut? -------------------------');
        $jawaban3 = $request->input('jawaban_3', 'Sebelum memberikan keterangan selaku ahli dibidang ' . $bidangKeahlian . ', saya bersedia mengangkat sumpah dihadapan Penyidik sesuai dengan agama saya yaitu agama ' . $agamaAhli . ' yang selanjutnya dituangkan dalam Berita Acara Sumpah. ---------------------------------');
        $template->setValue('pertanyaan_3', '3.   ' . $pertanyaan3);
        $template->setValue('jawaban_3', $jawaban3);

        // Q4 (Surat Penunjukan)
        $noSuratTugasAhli = $request->input('no_surat_penunjukan_ahli', 'B/123/X/2026/Dishub');
        $tglSuratTugasAhli = $this->formatDateIndo($request->input('tanggal_penunjukan_ahli', date('Y-m-d')));
        $pejabatPenunjuk = $request->input('pejabat_penunjuk_ahli', 'Kepala ' . $instansiAhli);
        $pertanyaan4 = $request->input('pertanyaan_4', 'Dalam pemeriksaan ini apakah saudara Ahli telah mendapatkan Surat Penunjukan dari Pimpinan Instansi saudara yaitu ' . $instansiAhli . ', jika mendapatkan berapa nomor suratnya dan ditanda tangani oleh siapa? jelaskan! ----------------');
        $jawaban4 = $request->input('jawaban_4', 'Dalam pemeriksaan ini saya telah mendapatkan Surat Penunjukan dari ' . $pejabatPenunjuk . ' dengan Nomor: ' . $noSuratTugasAhli . ' tanggal ' . $tglSuratTugasAhli . ' perihal Penunjukan Ahli dalam pemeriksaan Tersangka ' . $namaTersangka . ', berdasarkan adanya surat permintaan dari Penyidik. -----------------------------------------------------------------------------------------------------------------');
        $template->setValue('pertanyaan_4', '4.   ' . $pertanyaan4);
        $template->setValue('jawaban_4', $jawaban4);

        // Q5 (Riwayat Pendidikan & Pekerjaan)
        $pertanyaan5 = $request->input('pertanyaan_5', 'Agar saudara Ahli jelaskan riwayat hidup saudara secara singkat dan jelas baik mengenai pendidikan dan riwayat pekerjaan saudara sampai dengan saat sekarang ini? -------------------------------------------------------------------------------');
        $defaultJawaban5 = "Riwayat pendidikan dan riwayat pekerjaan saya adalah:\n   a. Riwayat Pendidikan:\n      1) Pendidikan Formal:\n         a) Sekolah Dasar di " . $request->input('sd_ahli', 'SD Negeri, lulus tahun 1997') . ";\n         b) SMP di " . $request->input('smp_ahli', 'SMP Negeri, lulus tahun 2000') . ";\n         c) SMA di " . $request->input('sma_ahli', 'SMA Negeri, lulus tahun 2003') . ";\n         d) S1 di Fakultas " . $request->input('s1_ahli', 'Teknik Mesin Universitas Indonesia, lulus tahun 2007') . ";\n         e) S2 di Fakultas " . $request->input('s2_ahli', 'Transportasi Darat ITB, lulus tahun 2011') . ";\n         f) S3 di Fakultas " . $request->input('s3_ahli', '-') . ".\n      2) Pendidikan Keahlian: " . $request->input('diklat_ahli', 'Sertifikasi Keahlian Investigasi Kecelakaan Transportasi') . "\n   b. Riwayat Pekerjaan:\n      " . $request->input('pekerjaan_riwayat_ahli', 'Bekerja pada ' . $instansiAhli . ' sebagai Ahli Teknis');
        $jawaban5 = $request->input('jawaban_5', $defaultJawaban5);
        $template->setValue('pertanyaan_5', '5.   ' . $pertanyaan5);
        $template->setValue('jawaban_5', $jawaban5);

        // Q6
        $pertanyaan6 = $request->input('pertanyaan_6', 'Apakah saudara Ahli mempunyai ilmu dan keahlian dibidang Tindak Pidana ' . $tindakPidana . '? jelaskan! --------------------');
        $jawaban6 = $request->input('jawaban_6', 'Ya, saya mempunyai ilmu dan keahlian dibidang ' . $bidangKeahlian . ', karena latar belakang pendidikan dan pekerjaan saya menekuni bidang tersebut yaitu sebagai ' . $pekerjaanAhli . '. -------------------------------------------------------------');
        $template->setValue('pertanyaan_6', '6.   ' . $pertanyaan6);
        $template->setValue('jawaban_6', $jawaban6);

        // Q7
        $pertanyaan7 = $request->input('pertanyaan_7', 'Agar saudara Ahli jelaskan sudah berapa kali diminta pendapatnya sebagai Ahli dibidang Tindak Pidana ' . $tindakPidana . ' baik di tingkat penyidikan maupun pada tingkat peradilan pidana! -----------------------------------------------');
        $jawaban7 = $request->input('jawaban_7', 'Bahwa saya sudah sering memberikan keterangan sebagai ahli, khususnya dibidang ' . $bidangKeahlian . ' baik di tingkat penyidikan maupun di persidangan pengadilan negeri. -------------------------------------------------------------');
        $template->setValue('pertanyaan_7', '7.   ' . $pertanyaan7);
        $template->setValue('jawaban_7', $jawaban7);

        // Q8
        $pertanyaan8 = $request->input('pertanyaan_8', 'Apa jabatan dan tugas serta tanggung jawab saudara Ahli di ' . $instansiAhli . ' dan dalam pelaksanaan tugas saudara Ahli bertanggung jawab kepada siapa? jelaskan! -----------------------------------------------------------------------------------');
        $jawaban8 = $request->input('jawaban_8', 'Jabatan saya saat ini adalah ' . $pekerjaanAhli . ' pada ' . $instansiAhli . '. Tanggung jawab saya adalah memberikan keterangan teknis dan saintifik mengenai ketentuan ' . $perihalUu . ' kepada pimpinan instansi dan penyidik yang berwenang. -----');
        $template->setValue('pertanyaan_8', '8.   ' . $pertanyaan8);
        $template->setValue('jawaban_8', $jawaban8);

        // Q9
        $pertanyaan9 = $request->input('pertanyaan_9', 'Agar saudara Ahli jelaskan apa tugas dan fungsi dari ' . $instansiAhli . ' tempat ahli bekerja? ---------------------------------');
        $jawaban9 = $request->input('jawaban_9', 'Berdasarkan regulasi yang berlaku, ' . $instansiAhli . ' mempunyai tugas dan fungsi menyelenggarakan urusan pemerintahan di bidang keselamatan dan manajemen sarana prasarana perhubungan darat. -------------------------------------------------');
        $template->setValue('pertanyaan_9', '9.   ' . $pertanyaan9);
        $template->setValue('jawaban_9', $jawaban9);

        // Q10
        $pertanyaan10 = $request->input('pertanyaan_10', 'Apakah saudara Ahli mengenal dengan orang yang bernama ' . $namaPelaporKorban . ' selaku Pelapor / Korban, jika kenal sejak kapan, dimana, dalam rangka apa dan apakah ada hubungan pekerjaan atau keluarga dengannya? Jelaskan! ---');
        $jawaban10 = $request->input('jawaban_10', 'Saya tidak mengenal dengan orang yang bernama ' . $namaPelaporKorban . ' selaku Pelapor / Korban dan tidak ada hubungan pekerjaan maupun hubungan keluarga dengannya. --------------------------------------------------------------------------------');
        $template->setValue('pertanyaan_10', '10.  ' . $pertanyaan10);
        $template->setValue('jawaban_10', $jawaban10);

        // Q11
        $pertanyaan11 = $request->input('pertanyaan_11', 'Apakah saudara Ahli mengenal dengan orang yang bernama ' . $namaTersangka . ' (pihak Tersangka), jika kenal sejak kapan, dimana, dalam rangka apa dan apakah ada hubungan pekerjaan atau keluarga dengannya? Jelaskan! ------------');
        $jawaban11 = $request->input('jawaban_11', 'Saya tidak mengenal dengan orang yang bernama ' . $namaTersangka . ' (pihak Tersangka) dan tidak ada hubungan keluarga maupun pekerjaan dengannya. ----------------------------------------------------------------------------------------------------');
        $template->setValue('pertanyaan_11', '11.  ' . $pertanyaan11);
        $template->setValue('jawaban_11', $jawaban11);

        // Q12
        $pertanyaan12 = $request->input('pertanyaan_12', 'Agar saudara Ahli jelaskan apa saja pola dasar dalam Tindak Pidana ' . $tindakPidana . ' yang dilakukan oleh para Tersangka! -------------------------------------------------------------------------------------------------------------------------------');
        $jawaban12 = $request->input('jawaban_12', "Pola dasar terjadinya Tindak Pidana Kecelakaan Lalu Lintas pada umumnya meliputi:\n   a. Faktor manusia: kelalaian, kurang konsentrasi, kecepatan melebihi batas, atau gagal mengantisipasi ruang lalu lintas;\n   b. Faktor kendaraan: kelaikan rem, kemudi, atau lampu penerangan;\n   c. Faktor jalan dan lingkungan: geometri jalan, visibilitas, dan kondisi permukaan jalan.");
        $template->setValue('pertanyaan_12', '12.  ' . $pertanyaan12);
        $template->setValue('jawaban_12', $jawaban12);

        // Q13
        $pertanyaan13 = $request->input('pertanyaan_13', 'Agar saudara Ahli jelaskan apa yang dimaksud dengan Tindak Pidana ' . $tindakPidana . ' dan diatur dimana ketentuan tersebut? -----------------------------------------------------------------------------------------------------------------------------------------');
        $jawaban13 = $request->input('jawaban_13', 'Kecelakaan Lalu Lintas adalah suatu peristiwa di jalan yang tidak diduga dan tidak disengaja melibatkan kendaraan dengan atau tanpa pengguna jalan lain yang mengakibatkan korban manusia dan/atau kerugian harta benda, diatur dalam UU No. 22 Tahun 2009.');
        $template->setValue('pertanyaan_13', '13.  ' . $pertanyaan13);
        $template->setValue('jawaban_13', $jawaban13);

        // Q14
        $pertanyaan14 = $request->input('pertanyaan_14', 'Agar saudara Ahli jelaskan unsur-unsur apa saja yang harus ada dalam setiap perbuatan atau Tindak Pidana ' . $tindakPidana . ' sebagaimana saudara Ahli jelaskan tersebut di atas? -----------------------------------------------------------');
        $jawaban14 = $request->input('jawaban_14', "Unsur-unsur yang harus terpenuhi dalam Pasal 310 UU No. 22 Tahun 2009 adalah:\n   a. Setiap orang yang mengemudikan kendaraan bermotor;\n   b. Karena kelalaiannya (kealpaannya);\n   c. Mengakibatkan kecelakaan lalu lintas dengan kerusakan benda, luka ringan/berat, atau meninggal dunia.");
        $template->setValue('pertanyaan_14', '14.  ' . $pertanyaan14);
        $template->setValue('jawaban_14', $jawaban14);

        // Q15
        $pertanyaan15 = $request->input('pertanyaan_15', 'Sesuai dengan keterangan saudara Ahli tersebut di atas, bagaimana seseorang dapat dikriteriakan melakukan Tindak Pidana ' . $tindakPidana . '? jelaskan! ----------------------------------------------------------------------------------------');
        $jawaban15 = $request->input('jawaban_15', 'Seseorang dikriteriakan melakukan tindak pidana tersebut apabila ada hubungan kausalitas (sebab-akibat) yang nyata antara kelalaian pengemudi dalam mengendalikan kendaraannya dengan timbulnya akibat kecelakaan dan kerugian korban.');
        $template->setValue('pertanyaan_15', '15.  ' . $pertanyaan15);
        $template->setValue('jawaban_15', $jawaban15);

        // Q16
        $pertanyaan16 = $request->input('pertanyaan_16', "Disampaikan kepada saudara Ahli kronologis perkara Tindak Pidana " . $tindakPidana . ", sebagaimana dimaksud dalam Pasal " . $pasalPelanggaran . ", yang terjadi di " . $tkp . " pada tanggal " . $tanggalKejadian . ", sesuai LP No: " . $noLp . ", dengan Tersangka " . $namaTersangka . ".\nDari kronologis tersebut, bagaimanakah pemenuhan unsur pasalnya menurut pandangan keahlian saudara? Jelaskan! --------------------");
        $jawaban16 = $request->input('jawaban_16', 'Berdasarkan analisis teknis dan yuridis dari kronologis yang dipaparkan, perbuatan Tersangka ' . $namaTersangka . ' telah memenuhi seluruh unsur rumusan Pasal ' . $pasalPelanggaran . ' UU No. ' . $uuNomorTahun . ', karena adanya kelalaian yang menjadi penyebab utama terjadinya kecelakaan tersebut.');
        $template->setValue('pertanyaan_16', '16.  ' . $pertanyaan16);
        $template->setValue('jawaban_16', $jawaban16);

        // Dynamic Additional Questions
        $rawPertanyaan = $request->input('pertanyaan_kronologis', []);
        $rawJawaban = $request->input('jawaban_kronologis', []);

        $blockKronologis = [];
        $currentNo = 17;

        if (is_array($rawPertanyaan) && count($rawPertanyaan) > 0) {
            foreach ($rawPertanyaan as $idx => $tanya) {
                if (!empty(trim($tanya))) {
                    $jawab = $rawJawaban[$idx] ?? '-';
                    $blockKronologis[] = [
                        'no_kronologis' => (string)$currentNo,
                        'pertanyaan_kronologis' => trim($tanya) . ' -------------------------------------------------------------',
                        'jawaban_kronologis' => trim($jawab) . ' ---------------------------------------------------------------------------------------------',
                    ];
                    $currentNo++;
                }
            }
        }

        $template->cloneBlock('block_kronologis', count($blockKronologis), true, false, $blockKronologis);

        // Pertanyaan Penutup (17, 18, 19 standar)
        $template->setValue('no_17', (string)$currentNo);
        $template->setValue('pertanyaan_17', $currentNo . '.  Masih adakah keterangan lain yang ingin saudara Ahli tambahkan sehubungan dengan pemeriksaan sekarang ini? ------------');
        $template->setValue('jawaban_17', $request->input('jawaban_penutup_1', 'Tidak ada keterangan lain yang saya tambahkan. ---------------------------------------------------------------------'));
        $currentNo++;

        $template->setValue('no_18', (string)$currentNo);
        $template->setValue('pertanyaan_18', $currentNo . '.  Sudah benarkah keterangan yang telah saudara Ahli berikan dalam pemeriksaan tersebut di atas? ---------------------------');
        $template->setValue('jawaban_18', $request->input('jawaban_penutup_2', 'Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan. -------------'));
        $currentNo++;

        $template->setValue('no_19', (string)$currentNo);
        $template->setValue('pertanyaan_19', $currentNo . '.  Apakah dalam pemeriksaan ini saudara Ahli merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain? ---------------------');
        $template->setValue('jawaban_19', $request->input('jawaban_penutup_3', 'Selama dalam pemeriksaan ini saya tidak merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain. ------------------'));

        // Penutup & Tanda Tangan
        $tempatTandaTangan = $request->input('tempat_tanda_tangan', $polres->city ?? 'Kota');
        $template->setValue('tempat_tanda_tangan', $tempatTandaTangan);

        $jabatanPemeriksa = $request->input('jabatan_pemeriksa', 'Penyidik Pembantu');
        $namaPemeriksa = strtoupper($request->input('nama_pemeriksa', $penyidik1Nama));
        $pangkatNrpPemeriksa = $request->input('pangkat_nrp_pemeriksa', $penyidik1Pangkat . ' NRP ' . $penyidik1Nrp);

        if ($request->filled('pemeriksa_officer_id')) {
            $offPem = Officer::with(['rank', 'position'])->selectFullName()->find($request->pemeriksa_officer_id);
            if ($offPem) {
                $jabatanPemeriksa = $offPem->position->name ?? $jabatanPemeriksa;
                $namaPemeriksa = strtoupper($offPem->full_name ?? ($offPem->first_name . ' ' . $offPem->last_name));
                $pangkatNrpPemeriksa = ($offPem->rank->name ?? 'PANGKAT') . ' NRP ' . ($offPem->register_number ?? '-');
            }
        }

        $template->setValue('jabatan_pemeriksa', $jabatanPemeriksa);
        $template->setValue('nama_pemeriksa', $namaPemeriksa);
        $template->setValue('pangkat_nrp_pemeriksa', $pangkatNrpPemeriksa);

        $cleanFilename = 'BAP_Ahli_' . str_replace(['/', ' '], '_', $namaAhli) . '_' . date('Ymd_His') . '.docx';
        $tempPath = storage_path('app/' . $cleanFilename);

        $template->saveAs($tempPath);

        return response()->download($tempPath, $cleanFilename)->deleteFileAfterSend(true);
    }
}
