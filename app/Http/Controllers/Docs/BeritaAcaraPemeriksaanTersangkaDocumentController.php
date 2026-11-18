<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\BeritaAcaraPemeriksaanTersangkaDocument\BeritaAcaraPemeriksaanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Officer;
use App\Models\Suspect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class BeritaAcaraPemeriksaanTersangkaDocumentController extends Controller
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

    private function cleanAttributeString($val, $fallback = '')
    {
        if (empty($val)) return $fallback;
        if (is_array($val)) return $val['name'] ?? $fallback;
        if (is_object($val)) return $val->name ?? $fallback;
        if (is_string($val) && str_starts_with(trim($val), '{')) {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['name'])) {
                return $decoded['name'];
            }
        }
        return (string)$val;
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
            $suspects = Suspect::with(['job', 'religion', 'gender'])->where('accident_id', $accidentId)->get();
        } catch (\Exception $e) {}

        $sprindik = null;
        try {
            $sprindik = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                ->orderByDesc('created_at')
                ->first();
        } catch (\Exception $e) {}

        return compact('accident', 'officers', 'suspects', 'sprindik');
    }

    public function create(Request $request)
    {
        $accidentId = $request->accident_id;
        $data = $this->getMasterData($accidentId);

        $defaultDocNumber = 'BA-TSK/' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT) . '/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas';

        return view('docs.berita-acara-pemeriksaan-tersangka-document.create', array_merge($data, [
            'accidentId' => $accidentId,
            'defaultDocNumber' => $defaultDocNumber,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nama_tersangka' => 'required',
        ]);

        $accidentId = $request->accident_id;
        $documentDate = $request->document_date ? Carbon::parse($request->document_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $docData = $request->except(['_token', 'accident_id', 'document_number', 'document_date']);
        if (isset($docData['pekerjaan_tersangka'])) {
            $docData['pekerjaan_tersangka'] = $this->cleanAttributeString($docData['pekerjaan_tersangka'], 'Swasta');
        }
        if (isset($docData['agama_tersangka'])) {
            $docData['agama_tersangka'] = $this->cleanAttributeString($docData['agama_tersangka'], 'Islam');
        }
        if (isset($docData['jenis_kelamin_tersangka'])) {
            $g = $this->cleanAttributeString($docData['jenis_kelamin_tersangka'], 'Laki-laki');
            $docData['jenis_kelamin_tersangka'] = stripos($g, 'perempuan') !== false ? 'Perempuan' : 'Laki-laki';
        }
        if (isset($docData['pekerjaan_1']) && str_contains($docData['pekerjaan_1'], '{"id"')) {
            $docData['pekerjaan_1'] = 'Bekerja sebagai ' . ($docData['pekerjaan_tersangka'] ?? 'Swasta') . ' sampai sekarang';
        }

        BeritaAcaraPemeriksaanTersangkaDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->document_number ?? '-',
                'document_date' => $documentDate,
                'data' => $docData,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Tersangka berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = BeritaAcaraPemeriksaanTersangkaDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.berita-acara-pemeriksaan-tersangka-document.edit', array_merge($data, [
            'document' => $savedDoc,
            'savedData' => $savedData,
            'accidentId' => $accidentId,
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nama_tersangka' => 'required',
        ]);

        $accidentId = $request->accident_id;
        $documentDate = $request->document_date ? Carbon::parse($request->document_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $docData = $request->except(['_token', '_method', 'accident_id', 'document_number', 'document_date']);
        if (isset($docData['pekerjaan_tersangka'])) {
            $docData['pekerjaan_tersangka'] = $this->cleanAttributeString($docData['pekerjaan_tersangka'], 'Swasta');
        }
        if (isset($docData['agama_tersangka'])) {
            $docData['agama_tersangka'] = $this->cleanAttributeString($docData['agama_tersangka'], 'Islam');
        }
        if (isset($docData['jenis_kelamin_tersangka'])) {
            $g = $this->cleanAttributeString($docData['jenis_kelamin_tersangka'], 'Laki-laki');
            $docData['jenis_kelamin_tersangka'] = stripos($g, 'perempuan') !== false ? 'Perempuan' : 'Laki-laki';
        }
        if (isset($docData['pekerjaan_1']) && str_contains($docData['pekerjaan_1'], '{"id"')) {
            $docData['pekerjaan_1'] = 'Bekerja sebagai ' . ($docData['pekerjaan_tersangka'] ?? 'Swasta') . ' sampai sekarang';
        }

        BeritaAcaraPemeriksaanTersangkaDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->document_number ?? '-',
                'document_date' => $documentDate,
                'data' => $docData,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Tersangka berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->accident_id ?? $request->query('accident_id');
        $doc = BeritaAcaraPemeriksaanTersangkaDocument::where('accident_id', $accidentId)->first();
        if ($doc) {
            $doc->delete();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Tersangka berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        $accidentId = $request->accident_id ?? $request->query('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Fallback jika diunduh dari link list (query param)
        if (!$request->filled('nama_tersangka')) {
            $savedDoc = BeritaAcaraPemeriksaanTersangkaDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/berita_acara_pemeriksaan_tersangka.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template dokumen Berita Acara Pemeriksaan Tersangka tidak ditemukan.');
        }

        Settings::setOutputEscapingEnabled(true);
        $template = new TemplateProcessor($templatePath);

        // 1. Kopstuk
        $poldaName = strtoupper($accident->polres->polda->name ?? 'JAWA BARAT');
        $polresName = strtoupper($accident->polres->name ?? 'RESOR KOTA');
        $polresAddress = $accident->polres->address ?? 'Jalan Raya Kepolisian No. 1';

        $template->setValue('daerahPoliceFullName', $poldaName);
        $template->setValue('resorPoliceFullName', $polresName);
        $template->setValue('resorPoliceAddress', $polresAddress);

        // 2. Waktu Pemeriksaan
        $tglPeriksa = $request->tanggal_pemeriksaan ?? date('Y-m-d');
        $cTglPeriksa = Carbon::parse($tglPeriksa);
        $hariPemeriksaan = $request->hari_pemeriksaan ?? $this->getNamaHari($tglPeriksa);
        $jamPemeriksaan = $request->jam_pemeriksaan ?? '10.00';
        $zonaWaktu = $request->zona_waktu ?? 'WIB';

        $template->setValue('hari_pemeriksaan', $hariPemeriksaan);
        $template->setValue('tanggal_pemeriksaan_angka', $cTglPeriksa->day . ' (' . $this->terbilang($cTglPeriksa->day) . ')');
        $template->setValue('bulan_pemeriksaan', $cTglPeriksa->locale('id')->isoFormat('MMMM'));
        $template->setValue('tahun_pemeriksaan', $cTglPeriksa->year);
        $template->setValue('tahun_terbilang', $this->terbilang($cTglPeriksa->year));
        $template->setValue('jam_pemeriksaan', $jamPemeriksaan);
        $template->setValue('zona_waktu', $zonaWaktu);

        // 3. Penyidik 1 & 2
        $penyidik1Nama = strtoupper($request->penyidik_1_nama ?? 'PENYIDIK PEMERIKSA');
        $penyidik1Pangkat = strtoupper($request->penyidik_1_pangkat ?? 'AIPDA');
        $penyidik1Nrp = $request->penyidik_1_nrp ?? '00000000';
        $penyidik1Jabatan = $request->penyidik_1_jabatan ?? 'Penyidik Pembantu';

        $penyidik2Nama = strtoupper($request->penyidik_2_nama ?? $penyidik1Nama);
        $penyidik2Pangkat = strtoupper($request->penyidik_2_pangkat ?? $penyidik1Pangkat);
        $penyidik2Nrp = $request->penyidik_2_nrp ?? $penyidik1Nrp;
        $penyidik2Jabatan = $request->penyidik_2_jabatan ?? 'Penyidik Pembantu';

        $template->setValue('penyidik_1_nama', $penyidik1Nama);
        $template->setValue('penyidik_1_pangkat', $penyidik1Pangkat);
        $template->setValue('penyidik_1_nrp', $penyidik1Nrp);
        $template->setValue('penyidik_1_jabatan', $penyidik1Jabatan);

        $template->setValue('penyidik_2_nama', $penyidik2Nama);
        $template->setValue('penyidik_2_pangkat', $penyidik2Pangkat);
        $template->setValue('penyidik_2_nrp', $penyidik2Nrp);
        $template->setValue('penyidik_2_jabatan', $penyidik2Jabatan);

        // Dasar Penyidikan (SP.Sidik)
        $noSprindik = $request->no_sprindik ?? ('SP.Sidik/01/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas');
        $tglSprindik = $this->formatDateIndo($request->tanggal_sprindik ?? date('Y-m-d'));
        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tglSprindik);

        // 4. Identitas Tersangka
        $namaTersangka = strtoupper($request->nama_tersangka ?? 'TERSANGKA');
        $nikTersangka = $request->nik_tersangka ?? '-';
        $kewarganegaraan = $request->kewarganegaraan_tersangka ?? 'Indonesia';
        $rawGender = $this->cleanAttributeString($request->jenis_kelamin_tersangka, 'Laki-laki');
        $jenisKelamin = stripos($rawGender, 'perempuan') !== false ? 'Perempuan' : 'Laki-laki';
        $tempatLahir = $request->tempat_lahir_tersangka ?? '-';
        $tglLahir = $this->formatDateIndo($request->tanggal_lahir_tersangka ?? '1990-01-01');
        $pekerjaan = $this->cleanAttributeString($request->pekerjaan_tersangka, 'Swasta');
        $agama = $this->cleanAttributeString($request->agama_tersangka, 'Islam');
        $alamat = $request->alamat_tersangka ?? '-';

        $template->setValue('nama_tersangka', $namaTersangka);
        $template->setValue('nik_tersangka', $nikTersangka);
        $template->setValue('kewarganegaraan_tersangka', $kewarganegaraan);
        $template->setValue('jenis_kelamin_tersangka', $jenisKelamin);
        $template->setValue('tempat_lahir_tersangka', $tempatLahir);
        $template->setValue('tanggal_lahir_tersangka', $tglLahir);
        $template->setValue('pekerjaan_tersangka', $pekerjaan);
        $template->setValue('agama_tersangka', $agama);
        $template->setValue('alamat_tersangka', $alamat);

        // 5. Perkara & Dasar LP
        $tindakPidana = $request->tindak_pidana ?? 'Kecelakaan Lalu Lintas';
        $pasal = $request->pasal_pelanggaran ?? 'Pasal 310 ayat (4)';
        $uuNomorTahun = $request->uu_nomor_tahun ?? 'Nomor 22 Tahun 2009';
        $perihalUu = $request->perihal_uu ?? 'Lalu Lintas dan Angkutan Jalan';
        $tkp = $request->tkp ?? ($accident->accident_road ?? $accident->accident_address ?? '-');
        $hariKejadian = $request->hari_kejadian ?? $this->getNamaHari($accident->accident_date);
        $tglKejadian = $this->formatDateIndo($request->tanggal_kejadian ?? ($accident->accident_date ?? date('Y-m-d')));
        $waktuKejadian = $request->waktu_kejadian ?? ($accident->accident_time ?? '10.00');
        $namaPelapor = strtoupper($request->nama_pelapor ?? ($accident->reporter_name ?? 'PELAPOR / KORBAN'));
        $noLp = $request->no_lp ?? ($accident->no_lp ?? '-');
        $tglLp = $this->formatDateIndo($request->tanggal_lp ?? ($accident->accident_report_date ?? date('Y-m-d')));

        $template->setValue('tindak_pidana', $tindakPidana);
        $template->setValue('pasal_pelanggaran', $pasal);
        $template->setValue('uu_nomor_tahun', $uuNomorTahun);
        $template->setValue('perihal_uu', $perihalUu);
        $template->setValue('tkp', $tkp);
        $template->setValue('hari_kejadian', $hariKejadian);
        $template->setValue('tanggal_kejadian', $tglKejadian);
        $template->setValue('waktu_kejadian', $waktuKejadian);
        $template->setValue('nama_pelapor', $namaPelapor);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tglLp);

        // 6. Pertanyaan & Jawaban 1 s.d. 14
        $q1 = $request->pertanyaan_1 ?? 'Apakah pada saat ini saudara dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya?';
        $a1 = $request->jawaban_1 ?? 'Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya.';
        $template->setValue('pertanyaan_1', "1.   " . $q1);
        $template->setValue('jawaban_1', $a1);

        $q2 = $request->pertanyaan_2 ?? ("Pada saat ini saudara akan diperiksa sebagai Tersangka sehubungan dengan adanya dugaan terjadinya Tindak Pidana {$tindakPidana} sebagaimana dimaksud dalam {$pasal} UU {$uuNomorTahun} tentang {$perihalUu}, yang terjadi di {$tkp}, pada hari {$hariKejadian} tanggal {$tglKejadian}, dengan Pelapor atas nama {$namaPelapor}, sesuai dengan Laporan Polisi Nomor: {$noLp} tanggal {$tglLp}, apakah saudara mengerti penjelasan Penyidik tersebut?");
        $a2 = $request->jawaban_2 ?? ("Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Tersangka sehubungan dengan adanya dugaan terjadinya Tindak Pidana {$tindakPidana} sebagaimana dimaksud dalam {$pasal} UU {$uuNomorTahun} tentang {$perihalUu}, yang terjadi di {$tkp}, pada hari {$hariKejadian} tanggal {$tglKejadian}, dengan Pelapor atas nama {$namaPelapor}, sesuai dengan Laporan Polisi Nomor: {$noLp} tanggal {$tglLp}.");
        $template->setValue('pertanyaan_2', "2.   " . $q2);
        $template->setValue('jawaban_2', $a2);

        $q3 = $request->pertanyaan_3 ?? 'Sesuai dengan hak-hak saudara sebagai Tersangka yang mempunyai hak untuk didampingi oleh Pengacara atau Penasehat Hukum, apakah dalam pemeriksaan ini saudara akan didampingi oleh Pengacara atau Penasehat Hukum, dari mana dan apakah ada surat kuasanya?';
        $a3 = $request->jawaban_3 ?? 'Sesuai dengan hak-hak saya sebagai Tersangka yang mempunyai hak untuk didampingi oleh Pengacara atau Penasehat Hukum, dalam pemeriksaan ini saya tidak didampingi oleh kuasa hukum dan bersedia memberikan keterangan sendiri.';
        $template->setValue('pertanyaan_3', "3.   " . $q3);
        $template->setValue('jawaban_3', $a3);

        $q4 = $request->pertanyaan_4 ?? 'Walaupun pada saat pemeriksaan ini saudara tidak didampingi oleh Pengacara atau Penasehat Hukum, apakah pemeriksaan ini dapat dilanjutkan? jelaskan!';
        $a4 = $request->jawaban_4 ?? 'Walaupun pada saat pemeriksaan ini saya tidak didampingi oleh Pengacara atau Penasehat Hukum, pemeriksaan ini dapat dilanjutkan.';
        $template->setValue('pertanyaan_4', "4.   " . $q4);
        $template->setValue('jawaban_4', $a4);

        $q5 = $request->pertanyaan_5 ?? 'Apakah saudara pernah dihukum atau terlibat dalam suatu perkara Tindak Pidana, kalau pernah kapan, dalam perkara apa, dimana dan berapa lama saudara dihukum serta apakah ada putusannya?';
        $a5 = $request->jawaban_5 ?? 'Saya sebelumnya belum pernah dihukum atau terlibat dalam perkara tindak pidana lainnya.';
        $template->setValue('pertanyaan_5', "5.   " . $q5);
        $template->setValue('jawaban_5', $a5);

        $q6 = $request->pertanyaan_6 ?? 'Apakah saudara dapat menyerahkan putusan dari pengadilan sebagaimana jawaban saudara pada nomor 5 tersebut di atas?';
        $a6 = $request->jawaban_6 ?? 'Karena saya belum pernah dihukum, maka saya tidak ada putusan pengadilan yang diserahkan.';
        $template->setValue('pertanyaan_6', "6.   " . $q6);
        $template->setValue('jawaban_6', $a6);

        // Pertanyaan 7: Riwayat Hidup
        $q7 = $request->pertanyaan_7 ?? 'Agar saudara jelaskan riwayat hidup saudara secara singkat dan jelas baik mengenai pendidikan dan riwayat pekerjaan saudara sampai dengan saat sekarang ini?';
        $sd = $request->sd_tersangka ?? 'SD Negeri, lulus tahun 2002';
        $smp = $request->smp_tersangka ?? 'SMP Negeri, lulus tahun 2005';
        $sma = $request->sma_tersangka ?? 'SMA Negeri, lulus tahun 2008';
        $s1 = $request->s1_tersangka ?? '-';
        $s2 = $request->s2_tersangka ?? '-';
        $s3 = $request->s3_tersangka ?? '-';
        $pek1 = $request->pekerjaan_1 ?? ('Bekerja sebagai ' . $pekerjaan . ' sampai sekarang');
        if (str_contains($pek1, '{"id"')) {
            $pek1 = 'Bekerja sebagai ' . $pekerjaan . ' sampai sekarang';
        }
        $pek2 = $request->pekerjaan_2 ?? '-';
        $pek3 = $request->pekerjaan_3 ?? '-';

        $a7 = "Riwayat pendidikan dan riwayat pekerjaan saya adalah:\n" .
              "a. Riwayat Pendidikan:\n" .
              "   1) Pendidikan Formal:\n" .
              "      a) Sekolah Dasar di {$sd};\n" .
              "      b) Sekolah Menengah Pertama di {$smp};\n" .
              "      c) Sekolah Menengah Atas di {$sma};\n" .
              "      d) S1 di {$s1};\n" .
              "      e) S2 di {$s2};\n" .
              "      f) S3 di {$s3}.\n" .
              "b. Riwayat Pekerjaan:\n" .
              "   1) {$pek1};\n" .
              "   2) {$pek2};\n" .
              "   3) {$pek3}.";
        if ($request->filled('jawaban_7_custom')) {
            $a7 = $request->jawaban_7_custom;
        }
        $template->setValue('pertanyaan_7', "7.   " . $q7);
        $template->setValue('jawaban_7', $a7);

        // Pertanyaan 8: Hubungan Pelapor/Korban
        $q8 = $request->pertanyaan_8 ?? ("Apakah saudara mengenal dengan orang yang bernama {$namaPelapor} selaku Korban/Pelapor, jika kenal sejak kapan, dimana, dalam rangka apa dan apakah ada hubungan pekerjaan atau keluarga dengannya? Jelaskan!");
        $a8 = $request->jawaban_8 ?? ("Saya tidak mengenal dengan orang yang bernama {$namaPelapor} tersebut di atas dan tidak ada hubungan keluarga maupun pekerjaan dengannya.");
        $template->setValue('pertanyaan_8', "8.   " . $q8);
        $template->setValue('jawaban_8', $a8);

        // Pertanyaan 9: Hubungan Pihak Tersangka Lain / Saksi
        $q9 = $request->pertanyaan_9 ?? 'Apakah saudara mengenal dengan pihak saksi-saksi lainnya dalam perkara ini, jika kenal sejak kapan, dimana, dalam rangka apa dan apakah ada hubungan pekerjaan atau keluarga dengannya? Jelaskan!';
        $a9 = $request->jawaban_9 ?? 'Saya tidak mengenal dengan para saksi yang melihat kejadian tersebut di atas dan tidak ada hubungan keluarga maupun pekerjaan dengannya.';
        $template->setValue('pertanyaan_9', "9.   " . $q9);
        $template->setValue('jawaban_9', $a9);

        // Pertanyaan 10: 7 KAH (Kronologis Perkara Tersangka)
        $q10 = $request->pertanyaan_10 ?? 'Pertanyaan 7 KAH (Siapa, Apa, Dimana, Dengan apa, Mengapa, Bagaimana, Bilamana/Kapan): Jelaskan secara rinci kronologis kecelakaan lalu lintas yang saudara alami dari awal sebelum berangkat hingga terjadinya tabrakan!';
        $a10 = $request->jawaban_10 ?? 'Bahwa benar pada waktu kejadian tersebut di atas saya sedang mengemudikan kendaraan bermotor, kemudian saat melintas di TKP terjadi kecelakaan lalu lintas tersebut.';
        $template->setValue('pertanyaan_10', "10.   " . $q10);
        $template->setValue('jawaban_10', $a10);

        // Pertanyaan 11: Barang Bukti
        $q11 = $request->pertanyaan_11 ?? 'Apakah ada barang bukti yang akan saudara serahkan kepada Penyidik dalam pemeriksaan ini? Jelaskan!';
        $a11 = $request->jawaban_11 ?? 'Barang bukti yang saya serahkan kepada Penyidik berupa 1 (satu) unit kendaraan bermotor yang saya kemudikan beserta STNK dan Surat Izin Mengemudi (SIM).';
        $template->setValue('pertanyaan_11', "11.   " . $q11);
        $template->setValue('jawaban_11', $a11);

        // Pertanyaan 12: Saksi-saksi yang Mengetahui
        $q12 = $request->pertanyaan_12 ?? 'Siapa saja saksi-saksi yang mengetahui terkait dengan Tindak Pidana tersebut? jelaskan!';
        $a12 = $request->jawaban_12 ?? 'Saksi-saksi yang mengetahui kejadian kecelakaan tersebut adalah warga sekitar lokasi kejadian dan pengguna jalan yang berada di dekat tempat kejadian.';
        $template->setValue('pertanyaan_12', "12.   " . $q12);
        $template->setValue('jawaban_12', $a12);

        // Pertanyaan 13: Saksi / Ahli Meringankan (a de charge)
        $q13 = $request->pertanyaan_13 ?? 'Sebagaimana hak-hak Tersangka yaitu dapat mengajukan saksi atau ahli yang meringankan dalam perkara yang dipersangkakan, apakah saudara dalam perkara ini akan mengajukan saksi-saksi atau ahli yang meringankan?';
        $a13 = $request->jawaban_13 ?? 'Untuk sementara ini saya belum mengajukan saksi atau ahli yang meringankan (a de charge), dan apabila nantinya ada akan saya susulkan dan sampaikan kepada Penyidik.';
        $template->setValue('pertanyaan_13', "13.   " . $q13);
        $template->setValue('jawaban_13', $a13);

        // Pertanyaan 14: Keterangan Lanjutan Tersangka
        $q14 = $request->pertanyaan_14 ?? 'Apakah saat mengemudikan kendaraan saudara berada dalam pengaruh alkohol, obat-obatan terlarang atau dalam keadaan mengantuk?';
        $a14 = $request->jawaban_14 ?? 'Tidak, saya mengemudikan kendaraan dalam keadaan sadar dan tidak berada dalam pengaruh alkohol maupun obat-obatan.';
        $template->setValue('pertanyaan_14', "14.   " . $q14);
        $template->setValue('jawaban_14', $a14);

        // Dynamic Questions (Pertanyaan Tambahan)
        $pTambahan = $request->pertanyaan_kronologis ?? [];
        $jTambahan = $request->jawaban_kronologis ?? [];
        if (!empty($pTambahan) && is_array($pTambahan) && count($pTambahan) > 0) {
            $countDynamic = count($pTambahan);
            $template->cloneBlock('block_kronologis', $countDynamic, true, true);
            foreach ($pTambahan as $idx => $pText) {
                $numQ = 15 + $idx;
                $jText = $jTambahan[$idx] ?? '-';
                $template->setValue('no_kronologis#' . ($idx + 1), $numQ);
                $template->setValue('pertanyaan_kronologis#' . ($idx + 1), $pText);
                $template->setValue('jawaban_kronologis#' . ($idx + 1), $jText);
            }
            $startTutup = 15 + $countDynamic;
        } else {
            $template->deleteBlock('block_kronologis');
            $startTutup = 15;
        }

        // Tanda Tangan Jeda Tengah
        $namaKuasaHukum = $request->nama_kuasa_hukum ?? '';
        $ttdKuasaHukum = !empty($namaKuasaHukum) ? $namaKuasaHukum : '..........................';
        $template->setValue('nama_kuasa_hukum_ttd_1', $ttdKuasaHukum);
        $template->setValue('nama_tersangka_ttd_1', $namaTersangka);

        // Pertanyaan Penutup
        $template->setValue('no_tutup_1', $startTutup);
        $template->setValue('pertanyaan_tutup_1', 'Masih adakah keterangan lain yang ingin saudara tambahkan sehubungan dengan pemeriksaan sekarang ini?');
        $template->setValue('jawaban_tutup_1', $request->jawaban_penutup_1 ?? 'Tidak ada keterangan lain yang saya tambahkan.');

        $template->setValue('no_tutup_2', $startTutup + 1);
        $template->setValue('pertanyaan_tutup_2', 'Sudah benarkah keterangan yang telah saudara berikan dalam pemeriksaan tersebut di atas?');
        $template->setValue('jawaban_tutup_2', $request->jawaban_penutup_2 ?? 'Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan.');

        $template->setValue('no_tutup_3', $startTutup + 2);
        $template->setValue('pertanyaan_tutup_3', 'Apakah dalam pemeriksaan ini saudara merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain ?');
        $template->setValue('jawaban_tutup_3', $request->jawaban_penutup_3 ?? 'Selama dalam pemeriksaan ini saya tidak merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain.');

        // Tanda Tangan Akhir
        $template->setValue('nama_kuasa_hukum_ttd_2', $ttdKuasaHukum);
        $template->setValue('nama_tersangka_ttd_2', $namaTersangka);

        $tempatTtd = $request->tempat_tanda_tangan ?? ($accident->polres->polres_district ?? ($accident->polres->city ?? 'Kota'));
        $namaPemeriksa = strtoupper($request->nama_pemeriksa ?? $penyidik1Nama);
        $pangkatNrpPemeriksa = strtoupper($request->pangkat_nrp_pemeriksa ?? ($penyidik1Pangkat . ' NRP ' . $penyidik1Nrp));
        $jabatanPemeriksa = $request->jabatan_pemeriksa ?? $penyidik1Jabatan;

        $template->setValue('tempat_tanda_tangan', $tempatTtd);
        $template->setValue('jabatan_pemeriksa', $jabatanPemeriksa);
        $template->setValue('nama_pemeriksa', $namaPemeriksa);
        $template->setValue('pangkat_nrp_pemeriksa', $pangkatNrpPemeriksa);

        $filename = 'BAP_Tersangka_' . str_replace([' ', '/', '\\'], '_', $namaTersangka) . '_' . date('Ymd_His') . '.docx';
        $tempPath = storage_path('app/' . $filename);
        $template->saveAs($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
