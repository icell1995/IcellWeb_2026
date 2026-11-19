<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\BeritaAcaraPemeriksaanSaksiDocument\BeritaAcaraPemeriksaanSaksiDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Officer;
use App\Models\Suspect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class BeritaAcaraPemeriksaanSaksiDocumentController extends Controller
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

        $saksiList = collect();
        try {
            $saksiList = DB::table('daftar_saksi')
                ->where('accident_id', $accidentId)
                ->orderBy('created_at', 'asc')
                ->get();
        } catch (\Exception $e) {}

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

        return compact('accident', 'officers', 'saksiList', 'suspects', 'sprindik');
    }

    public function create(Request $request)
    {
        $accidentId = $request->accident_id;
        $data = $this->getMasterData($accidentId);

        $defaultDocNumber = 'BA/' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT) . '/' . date('m') . '/RES.0.0/' . date('Y') . '/Lantas';

        return view('docs.berita-acara-pemeriksaan-saksi-document.create', array_merge($data, [
            'accidentId' => $accidentId,
            'defaultDocNumber' => $defaultDocNumber,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nama_saksi' => 'required',
        ]);

        $accidentId = $request->accident_id;
        $documentDate = $request->document_date ? Carbon::parse($request->document_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $docData = $request->except(['_token', 'accident_id', 'document_number', 'document_date']);

        BeritaAcaraPemeriksaanSaksiDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->document_number ?? '-',
                'document_date' => $documentDate,
                'data' => $docData,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Saksi berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = BeritaAcaraPemeriksaanSaksiDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.berita-acara-pemeriksaan-saksi-document.edit', array_merge($data, [
            'document' => $savedDoc,
            'savedData' => $savedData,
            'accidentId' => $accidentId,
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nama_saksi' => 'required',
        ]);

        $accidentId = $request->accident_id;
        $documentDate = $request->document_date ? Carbon::parse($request->document_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $docData = $request->except(['_token', '_method', 'accident_id', 'document_number', 'document_date']);

        BeritaAcaraPemeriksaanSaksiDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->document_number ?? '-',
                'document_date' => $documentDate,
                'data' => $docData,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Saksi berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        BeritaAcaraPemeriksaanSaksiDocument::where('accident_id', $accidentId)->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Pemeriksaan Saksi berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        Settings::setOutputEscapingEnabled(true);

        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        if (!$request->filled('nama_saksi')) {
            $savedDoc = BeritaAcaraPemeriksaanSaksiDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/berita_acara_pemeriksaan_saksi.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template Word BAP Saksi tidak ditemukan.');
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
        $bulanPemeriksaan = $this->formatDateIndo($tglPemeriksaanRaw);
        // ambil hanya nama bulannya
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

        // Identitas Saksi
        $namaSaksi = strtoupper($request->input('nama_saksi', '-'));
        $nikSaksi = $request->input('nik_saksi', '-');
        $kewarganegaraanSaksi = $request->input('kewarganegaraan_saksi', 'Indonesia');
        $jenisKelaminSaksi = $request->input('jenis_kelamin_saksi', 'Laki-laki');
        $tempatLahirSaksi = $request->input('tempat_lahir_saksi', '-');
        $tanggalLahirSaksi = $this->formatDateIndo($request->input('tanggal_lahir_saksi', '1990-01-01'));
        $pekerjaanSaksi = $request->input('pekerjaan_saksi', 'Swasta');
        $agamaSaksi = $request->input('agama_saksi', 'Islam');
        $alamatSaksi = $request->input('alamat_saksi', '-');

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
        $noLp = $request->input('no_lp', $accident->accident_code ?? '-');
        $tanggalLp = $this->formatDateIndo($request->input('tanggal_lp', $accident->accident_report_date ?? $accident->accident_date));

        // Pertanyaan 1 - 5
        $pertanyaan1 = $request->input('pertanyaan_1', 'Apakah pada saat ini saudara dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya? -----------------------------------------------------------------------------');
        $jawaban1 = $request->input('jawaban_1', 'Ya, saya dalam keadaan sehat baik jasmani maupun rohani, bersedia diperiksa dan akan memberikan keterangan yang sebenarnya. ---------------------------------------------------------------------------------------------');

        $pertanyaan2 = $request->input('pertanyaan_2', 'Pada saat ini saudara akan diperiksa sebagai Saksi sehubungan dengan adanya dugaan terjadinya Tindak Pidana ' . $tindakPidana . ' sebagaimana dimaksud dalam ' . $pasalPelanggaran . ' UU Nomor ' . $uuNomorTahun . ' tentang ' . $perihalUu . ', yang terjadi di ' . $tkp . ', pada hari ' . $hariKejadian . ' tanggal ' . $tanggalKejadian . ' sekira pukul ' . $waktuKejadian . ' ' . $zonaWaktu . ', dengan Tersangka atas nama ' . $namaTersangka . ', sesuai dengan Laporan Polisi Nomor: ' . $noLp . ' tanggal ' . $tanggalLp . ', apakah saudara mengerti penjelasan Penyidik tersebut? -----------------------------');
        $jawaban2 = $request->input('jawaban_2', 'Ya, saya mengerti sesuai dengan penjelasan Penyidik bahwa saya akan diperiksa sebagai Saksi, sehubungan dengan adanya dugaan terjadinya Tindak Pidana ' . $tindakPidana . ' sebagaimana dimaksud dalam ' . $pasalPelanggaran . ' UU Nomor ' . $uuNomorTahun . ' tentang ' . $perihalUu . ', yang terjadi di ' . $tkp . ', pada hari ' . $hariKejadian . ' tanggal ' . $tanggalKejadian . ' sekira pukul ' . $waktuKejadian . ' ' . $zonaWaktu . ', dengan Tersangka atas nama ' . $namaTersangka . ', sesuai dengan Laporan Polisi Nomor: ' . $noLp . ' tanggal ' . $tanggalLp . '. ---------------------------------------');

        $pertanyaan3 = $request->input('pertanyaan_3', 'Agar saudara jelaskan riwayat hidup saudara secara singkat dan jelas baik mengenai pendidikan dan riwayat pekerjaan saudara sampai dengan saat sekarang ini? -------------------------------------------------------------------------------');
        $jawaban3 = $request->input('jawaban_3', "Riwayat pendidikan dan riwayat pekerjaan saya adalah:\n   a. Riwayat Pendidikan:\n      1) SD: " . $request->input('sd_saksi', '-') . "\n      2) SMP: " . $request->input('smp_saksi', '-') . "\n      3) SMA: " . $request->input('sma_saksi', '-') . "\n      4) Perguruan Tinggi: " . $request->input('pt_saksi', '-') . "\n   b. Riwayat Pekerjaan:\n      " . $request->input('pekerjaan_riwayat', $pekerjaanSaksi) . " ---------------------------------------------------");

        $pertanyaan4 = $request->input('pertanyaan_4', 'Apakah saudara mengenal dengan orang yang bernama ' . $request->input('nama_pelapor_korban', 'Pelapor/Korban') . ' selaku Pelapor / Korban, jika kenal sejak kapan, dimana, dalam rangka apa dan apakah ada hubungan pekerjaan atau keluarga dengannya? Jelaskan! --------------------');
        $jawaban4 = $request->input('jawaban_4', $request->input('jawaban_4_custom', 'Saya tidak mengenal dengan orang yang bernama ' . $request->input('nama_pelapor_korban', 'Pelapor/Korban') . ' selaku Pelapor / Korban dan tidak ada hubungan pekerjaan atau keluarga dengannya. ----------------------------------------------------------------------------------------------------'));

        $pertanyaan5 = $request->input('pertanyaan_5', 'Apakah saudara mengenal dengan orang yang bernama ' . $namaTersangka . ' (pihak Tersangka), jika kenal sejak kapan, dimana, dalam rangka apa dan apakah ada hubungan pekerjaan atau keluarga dengannya? Jelaskan! ----------------------------');
        $jawaban5 = $request->input('jawaban_5', $request->input('jawaban_5_custom', 'Saya tidak mengenal dengan orang yang bernama ' . $namaTersangka . ' dan tidak ada hubungan keluarga dengannya. ---------------------------------------------------------------------------------------------'));

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

        $template->setValue('nama_saksi', $namaSaksi);
        $template->setValue('nik_saksi', $nikSaksi);
        $template->setValue('kewarganegaraan_saksi', $kewarganegaraanSaksi);
        $template->setValue('jenis_kelamin_saksi', $jenisKelaminSaksi);
        $template->setValue('tempat_lahir_saksi', $tempatLahirSaksi);
        $template->setValue('tanggal_lahir_saksi', $tanggalLahirSaksi);
        $template->setValue('pekerjaan_saksi', $pekerjaanSaksi);
        $template->setValue('agama_saksi', $agamaSaksi);
        $template->setValue('alamat_saksi', $alamatSaksi);

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

        // Replace Q1 - Q5
        $template->setValue('pertanyaan_1', '1.   ' . $pertanyaan1);
        $template->setValue('jawaban_1', $jawaban1);

        $template->setValue('pertanyaan_2', '2.   ' . $pertanyaan2);
        $template->setValue('jawaban_2', $jawaban2);

        $template->setValue('pertanyaan_3', '3.   ' . $pertanyaan3);
        $template->setValue('jawaban_3', $jawaban3);

        $template->setValue('pertanyaan_4', '4.   ' . $pertanyaan4);
        $template->setValue('jawaban_4', $jawaban4);

        $template->setValue('pertanyaan_5', '5.   ' . $pertanyaan5);
        $template->setValue('jawaban_5', $jawaban5);

        // Dynamic 7 KAH Kronologis Q&A
        $rawPertanyaan = $request->input('pertanyaan_kronologis', []);
        $rawJawaban = $request->input('jawaban_kronologis', []);

        $blockKronologis = [];
        $currentNo = 6;

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

        if (empty($blockKronologis)) {
            // default sample question if none provided
            $blockKronologis[] = [
                'no_kronologis' => (string)$currentNo,
                'pertanyaan_kronologis' => 'Apakah saudara melihat langsung terjadinya kecelakaan lalu lintas tersebut, coba jelaskan secara singkat dan jelas kronologis kejadiannya? -------------------------------------------------------------',
                'jawaban_kronologis' => 'Ya, saya melihat langsung kejadian kecelakaan lalu lintas tersebut saat berada di sekitar TKP... ---------------------------------------------------------------------------------------------',
            ];
            $currentNo++;
        }

        $template->cloneBlock('block_kronologis', count($blockKronologis), true, false, $blockKronologis);

        // Jeda Tanda Tangan
        $sertakanJeda = $request->input('sertakan_jeda_tanda_tangan', '1');
        if ($sertakanJeda == '1') {
            $template->cloneBlock('block_jeda', 1, true, false, [['nama_saksi' => $namaSaksi]]);
        } else {
            $template->cloneBlock('block_jeda', 0, true, false);
        }

        // Pertanyaan Penutup (3 Pertanyaan)
        $template->setValue('no_tutup_1', (string)$currentNo);
        $template->setValue('pertanyaan_tutup_1', 'Masih adakah keterangan lain yang ingin saudara tambahkan sehubungan dengan pemeriksaan sekarang ini? ------------');
        $template->setValue('jawaban_tutup_1', $request->input('jawaban_penutup_1', 'Tidak ada keterangan lain yang saya tambahkan. ---------------------------------------------------------------------'));
        $currentNo++;

        $template->setValue('no_tutup_2', (string)$currentNo);
        $template->setValue('pertanyaan_tutup_2', 'Sudah benarkah keterangan yang telah saudara berikan dalam pemeriksaan tersebut di atas? ---------------------------');
        $template->setValue('jawaban_tutup_2', $request->input('jawaban_penutup_2', 'Semua keterangan yang telah saya berikan tersebut di atas sudah benar dan dapat dipertanggung jawabkan. -------------'));
        $currentNo++;

        $template->setValue('no_tutup_3', (string)$currentNo);
        $template->setValue('pertanyaan_tutup_3', 'Apakah dalam pemeriksaan ini saudara merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain? ---------------------');
        $template->setValue('jawaban_tutup_3', $request->input('jawaban_penutup_3', 'Selama dalam pemeriksaan ini saya tidak merasa dipaksa, ditekan atau dipengaruhi oleh pihak lain. ------------------'));

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

        $cleanFilename = 'BAP_Saksi_' . str_replace(['/', ' '], '_', $namaSaksi) . '_' . date('Ymd_His') . '.docx';
        $tempPath = storage_path('app/' . $cleanFilename);

        $template->saveAs($tempPath);

        return response()->download($tempPath, $cleanFilename)->deleteFileAfterSend(true);
    }
}
