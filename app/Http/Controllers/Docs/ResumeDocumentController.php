<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AuthorizedSignatory;
use App\Models\Doc\DaftarAhliDocument\DaftarAhliDocument;
use App\Models\Doc\DaftarSaksiDocument\DaftarSaksiDocument;
use App\Models\Doc\DaftarTersangkaDocument\DaftarTersangkaDocument;
use App\Models\Doc\ResumeDocument\ResumeDocument;
use App\Models\Officer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class ResumeDocumentController extends Controller
{
    /**
     * Tampilkan form pembuatan Dokumen Resume Berkas Perkara
     */
    public function create(Request $request)
    {
        $accidentId = $request->get('accident_id');
        if (empty($accidentId)) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        $accident = Accident::with([
            'polres.polda',
            'reportedPersons',
            'reportingPersons',
            'caseVehicle',
        ])->findOrFail($accidentId);

        // Jika dokumen sudah ada, arahkan ke edit
        $existingDoc = ResumeDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.resume-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ])->with('info', 'Dokumen Resume Berkas Perkara untuk perkara ini sudah ada. Anda dialihkan ke halaman edit.');
        }

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        // 1. Ambil Pejabat Penandatangan
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

        // 2. Auto-Relasi Dokumen Acuan (Bagian I. DASAR)
        $sprindikDoc = DB::table('doc.surat_perintah_penyidikan_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $springasDoc = DB::table('doc.surat_perintah_tugas_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $spdpDoc = DB::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $spTapDoc = DB::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        // 3. Auto-Relasi Saksi (Bagian III.I)
        $initialSaksi = [];
        $daftarSaksiDoc = DaftarSaksiDocument::where('accident_id', $accidentId)->first();
        if ($daftarSaksiDoc && ! empty($daftarSaksiDoc->daftar_saksi)) {
            foreach ($daftarSaksiDoc->daftar_saksi as $ds) {
                $nama = trim($ds['nama'] ?? '');
                if ($nama !== '') {
                    $initialSaksi[] = [
                        'nama' => $nama,
                        'keterangan' => [
                            'Menerangkan bahwa benar pada saat kejadian kecelakaan saksi berada di sekitar lokasi TKP;',
                            'Menerangkan mengetahui terjadinya tabrakan antara kendaraan yang terlibat kecelakaan;',
                            'Menerangkan kondisi cuaca dan situasi arus lalu lintas pada saat kejadian.',
                        ],
                    ];
                }
            }
        }
        if (empty($initialSaksi)) {
            $reportingPersons = $accident->reportingPersons;
            if ($reportingPersons->isNotEmpty()) {
                foreach ($reportingPersons as $rp) {
                    $initialSaksi[] = [
                        'nama' => $rp->name,
                        'keterangan' => [
                            'Menerangkan bahwa benar pada saat kejadian saksi melihat kecelakaan lalu lintas;',
                            'Menerangkan posisi dan arah laju kendaraan sebelum tabrakan terjadi.',
                        ],
                    ];
                }
            }
        }
        if (empty($initialSaksi)) {
            $initialSaksi = [
                [
                    'nama' => 'Saksi 1',
                    'keterangan' => [
                        'Menerangkan bahwa benar pada saat kejadian berada di sekitar lokasi TKP;',
                        'Menerangkan mendengar benturan keras tabrakan antar kendaraan.',
                    ],
                ],
            ];
        }

        // 4. Auto-Relasi Ahli (Bagian III.J)
        $initialAhli = [];
        $daftarAhliDoc = DaftarAhliDocument::where('accident_id', $accidentId)->first();
        if ($daftarAhliDoc && ! empty($daftarAhliDoc->daftar_ahli)) {
            foreach ($daftarAhliDoc->daftar_ahli as $da) {
                $nama = trim($da['nama'] ?? '');
                if ($nama !== '') {
                    $keahlian = trim($da['keahlian'] ?? 'Kedokteran Forensik / Uji Kelaikan Kendaraan');
                    $initialAhli[] = [
                        'nama' => $nama.' ('.$keahlian.')',
                        'keterangan' => [
                            'Menerangkan hasil visum et repertum / pemeriksaan teknis terhadap korban atau kendaraan terkait peristiwa kecelakaan;',
                            'Menerangkan kesimpulan penyebab luka atau kerusakan mekanis kendaraan.',
                        ],
                    ];
                }
            }
        }
        if (empty($initialAhli)) {
            $initialAhli = [
                [
                    'nama' => 'dr. AHLI FORENSIK / PEMERIKSA TEKNIS RANMOR',
                    'keterangan' => [
                        'Menerangkan hasil pemeriksaan medis visum et repertum terhadap korban kecelakaan;',
                        'Menerangkan kelaikan fungsi teknis pengereman dan kemudi kendaraan.',
                    ],
                ],
            ];
        }

        // 5. Auto-Relasi Tersangka (Bagian III.K)
        $initialTersangka = [];
        $daftarTersangkaDoc = DaftarTersangkaDocument::where('accident_id', $accidentId)->first();
        if ($daftarTersangkaDoc && ! empty($daftarTersangkaDoc->daftar_tersangka)) {
            foreach ($daftarTersangkaDoc->daftar_tersangka as $dt) {
                $nama = trim($dt['nama'] ?? '');
                if ($nama !== '') {
                    $initialTersangka[] = [
                        'nama' => $nama,
                        'keterangan' => [
                            'Menerangkan bahwa benar pada hari dan tanggal kejadian mengemudikan kendaraan bermotor;',
                            'Menerangkan kronologi saat kendaraan hilang kendali sehingga menabrak korban;',
                            'Menerangkan mengakui kelalaiannya dalam berkendara.',
                        ],
                    ];
                }
            }
        }
        if (empty($initialTersangka) && $spTapDoc && ! empty($spTapDoc->suspect_name)) {
            $initialTersangka[] = [
                'nama' => $spTapDoc->suspect_name,
                'keterangan' => [
                    'Menerangkan bahwa benar pada waktu kejadian mengemudikan kendaraan yang terlibat laka lantas;',
                    'Menerangkan bahwa dirinya lalai dan kurang hati-hati saat berkendara.',
                ],
            ];
        }
        if (empty($initialTersangka)) {
            $reportedPersons = $accident->reportedPersons;
            if ($reportedPersons->isNotEmpty()) {
                foreach ($reportedPersons as $rep) {
                    $initialTersangka[] = [
                        'nama' => $rep->name,
                        'keterangan' => [
                            'Menerangkan mengemudikan kendaraan saat terjadinya kecelakaan;',
                            'Menerangkan tidak konsentrasi saat melintas di TKP.',
                        ],
                    ];
                }
            }
        }
        if (empty($initialTersangka)) {
            $initialTersangka = [
                [
                    'nama' => 'Tersangka 1',
                    'keterangan' => [
                        'Menerangkan membenarkan kejadian kecelakaan lalu lintas tersebut;',
                        'Menerangkan mengakui kelalaian dalam berkendara.',
                    ],
                ],
            ];
        }

        // 6. Auto-Relasi Barang Bukti (Bagian III.S)
        $initialBarangBukti = [];
        $vehicles = $accident->caseVehicle;
        if ($vehicles && $vehicles->isNotEmpty()) {
            foreach ($vehicles as $v) {
                $owner = $v->owner_name ?: ($v->driver_name ?: 'Pengemudi / Tersangka');
                $initialBarangBukti[] = [
                    'disita_dari' => $owner,
                    'keterangan' => [
                        '1 (satu) unit Kendaraan '.($v->vehicle_brand ?: 'Ranmor').' No. Pol '.($v->plate_number ?: '-'),
                        '1 (satu) lembar STNK Kendaraan '.($v->vehicle_brand ?: 'Ranmor').' No. Pol '.($v->plate_number ?: '-'),
                        '1 (satu) lembar SIM Pengemudi atas nama '.$owner,
                    ],
                ];
            }
        }
        if (empty($initialBarangBukti)) {
            $initialBarangBukti = [
                [
                    'disita_dari' => 'Tersangka / Pemilik',
                    'keterangan' => [
                        '1 (satu) unit kendaraan bermotor yang terlibat kecelakaan;',
                        '1 (satu) lembar STNK dan 1 (satu) buah kunci kontak;',
                        '1 (satu) lembar Surat Izin Mengemudi (SIM).',
                    ],
                ],
            ];
        }

        // 7. Estimasi Waktu dan Hari Laka
        $accidentDate = $accident->accident_date ? Carbon::parse($accident->accident_date) : Carbon::now();
        $hariLaka = $accidentDate->locale('id')->isoFormat('dddd');
        $tglLaka = $accidentDate->format('Y-m-d');
        $tglLakaFormatted = $accidentDate->locale('id')->translatedFormat('d F Y');
        $jamLaka = $accident->accident_time ? 'sekitar pukul '.substr($accident->accident_time, 0, 5).' WIB' : 'sekitar pukul 10.00 WIB';

        // 8. Taksir Kerugian
        $nilaiTaksir = (float) ($accident->material_loss ?? 15000000);
        $terbilangTaksir = strtoupper($this->terbilang((int) $nilaiTaksir)).' RUPIAH';

        // 9. Satker & Kota
        $satker = 'SATUAN LALU LINTAS '.strtoupper($accident->polres->name ?? 'POLRESTA');
        $kota = strtoupper($accident->polres->polres_district ?? ($accident->polres->name ?? 'TEMPAT'));

        // 10. Tanggal-tanggal Dokumen Acuan yang diformat Y-m-d (agar valid untuk input type="date")
        $sprindikTanggal = $sprindikDoc && !empty($sprindikDoc->document_date)
            ? Carbon::parse($sprindikDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        $springasTanggal = $springasDoc && !empty($springasDoc->document_date)
            ? Carbon::parse($springasDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        $spdpTanggal = $spdpDoc && !empty($spdpDoc->document_date)
            ? Carbon::parse($spdpDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        $spTapTanggal = $spTapDoc && !empty($spTapDoc->document_date)
            ? Carbon::parse($spTapDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        return view('docs.resume-document.create', compact(
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'sprindikDoc',
            'springasDoc',
            'spdpDoc',
            'spTapDoc',
            'sprindikTanggal',
            'springasTanggal',
            'spdpTanggal',
            'spTapTanggal',
            'initialSaksi',
            'initialAhli',
            'initialTersangka',
            'initialBarangBukti',
            'hariLaka',
            'tglLaka',
            'tglLakaFormatted',
            'jamLaka',
            'nilaiTaksir',
            'terbilangTaksir',
            'satker',
            'kota'
        ));
    }

    /**
     * Simpan Dokumen Resume baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'accident_id' => 'required',
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'dikeluarkan_di' => 'required|string|max:255',
            'satker_penyidik' => 'required|string|max:255',
            'nomor_lp' => 'required|string|max:255',
            'tanggal_lp' => 'required|date',

            // I. Dasar
            'dasar.lp_nomor' => 'required|string',
            'dasar.lp_tanggal' => 'required|date',
            'dasar.sprindik_nomor' => 'required|string',
            'dasar.sprindik_tanggal' => 'required|date',
            'dasar.springas_nomor' => 'required|string',
            'dasar.springas_tanggal' => 'required|date',
            'dasar.spdp_nomor' => 'required|string',
            'dasar.spdp_tanggal' => 'required|date',
            'dasar.sp_tap_nomor' => 'required|string',
            'dasar.sp_tap_tanggal' => 'required|date',
            'dasar.sp_tap_tersangka' => 'required|string',
            'dasar.surat_jaksa_nomor' => 'required|string',
            'dasar.surat_jaksa_tanggal' => 'required|date',

            // II. Perkara
            'perkara.tindak_pidana' => 'required|string',
            'perkara.pasal' => 'required|string',
            'perkara.uu_nomor' => 'required|string',
            'perkara.uu_tahun' => 'required|string',
            'perkara.uu_tentang' => 'required|string',
            'perkara.tkp' => 'required|string',
            'perkara.hari' => 'required|string',
            'perkara.tanggal' => 'required|date',
            'perkara.kurun_waktu' => 'required|string',
            'perkara.pihak' => 'required|string',
            'perkara.nilai_taksir' => 'required|numeric',
            'perkara.terbilang' => 'required|string',

            // III. Fakta-Fakta Repeaters
            'keterangan_saksi' => 'required|array|min:1',
            'keterangan_saksi.*.nama' => 'required|string',
            'keterangan_tersangka' => 'required|array|min:1',
            'keterangan_tersangka.*.nama' => 'required|string',

            // IV & V
            'analisa_kasus' => 'required',
            'analisa_yuridis' => 'required',
            'kesimpulan' => 'required',

            // Dual Signatory
            'atasan_penyidik_nama' => 'required|string',
            'atasan_penyidik_pangkat' => 'required|string',
            'atasan_penyidik_nrp' => 'required|string',
            'pembuat_nama' => 'required|string',
            'pembuat_pangkat' => 'required|string',
            'pembuat_nrp' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $accidentId = $request->accident_id;

            // Bersihkan data array saksi, ahli, tersangka, barang bukti
            $saksiData = $this->cleanRepeater($request->input('keterangan_saksi', []));
            $ahliData = $this->cleanRepeater($request->input('keterangan_ahli', []));
            $tersangkaData = $this->cleanRepeater($request->input('keterangan_tersangka', []));
            $bbData = $this->cleanRepeaterBb($request->input('barang_bukti', []));

            $document = ResumeDocument::create([
                'accident_id' => $accidentId,
                'document_category_id' => '0801',
                'status_id' => '2',

                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => Carbon::parse($request->tanggal_surat)->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => Carbon::parse($request->tanggal_lp)->format('Y-m-d'),

                'dasar' => $request->input('dasar', []),
                'perkara' => $request->input('perkara', []),

                'fakta_tkp' => $request->input('fakta_tkp', []),
                'fakta_korban' => $request->input('fakta_korban', []),
                'fakta_pemanggilan' => $request->input('fakta_pemanggilan', []),
                'fakta_perintah_membawa' => $request->input('fakta_perintah_membawa', []),
                'fakta_penangkapan' => $request->input('fakta_penangkapan', []),
                'fakta_penahanan' => $request->input('fakta_penahanan', []),
                'fakta_penggeledahan' => $request->input('fakta_penggeledahan', []),
                'fakta_penyitaan' => $request->input('fakta_penyitaan', []),

                'keterangan_saksi' => $saksiData,
                'keterangan_ahli' => $ahliData,
                'keterangan_tersangka' => $tersangkaData,

                'fakta_konfrontasi' => $request->fakta_konfrontasi ?: 'Tidak dilaksanakan konfrontasi;',
                'fakta_rekonstruksi' => $request->fakta_rekonstruksi ?: 'Telah dilaksanakan rekonstruksi perkara pada TKP;',
                'fakta_dpo' => $request->fakta_dpo ?: 'Tidak ada daftar pencarian orang (Nihil);',
                'fakta_dpb' => $request->fakta_dpb ?: 'Tidak ada daftar pencarian barang (Nihil);',
                'fakta_red_notice' => $request->fakta_red_notice ?: 'Nihil;',
                'fakta_pencegahan' => $request->fakta_pencegahan ?: 'Nihil;',
                'fakta_penangkalan' => $request->fakta_penangkalan ?: 'Nihil;',

                'barang_bukti' => $bbData,
                'fakta_penghentian_penyidikan' => $request->input('fakta_penghentian_penyidikan', []),
                'fakta_penyidikan_lanjutan' => $request->input('fakta_penyidikan_lanjutan', []),

                'analisa_kasus' => $request->input('analisa_kasus', []),
                'analisa_yuridis' => $request->input('analisa_yuridis', []),
                'kesimpulan' => $request->input('kesimpulan', []),

                'atasan_penyidik_nama' => $request->atasan_penyidik_nama,
                'atasan_penyidik_pangkat' => $request->atasan_penyidik_pangkat,
                'atasan_penyidik_nrp' => preg_replace('/[^0-9]/', '', (string) $request->atasan_penyidik_nrp) ?: $request->atasan_penyidik_nrp,
                'atasan_penyidik_jabatan' => $request->atasan_penyidik_jabatan ?: 'ATASAN PENYIDIK Selaku Penyidik',

                'pembuat_nama' => $request->pembuat_nama,
                'pembuat_pangkat' => $request->pembuat_pangkat,
                'pembuat_nrp' => preg_replace('/[^0-9]/', '', (string) $request->pembuat_nrp) ?: $request->pembuat_nrp,
                'pembuat_jabatan' => $request->pembuat_jabatan ?: 'Penyidik / Penyidik Pembantu',

                'created_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Resume Berkas Perkara berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal menyimpan Dokumen Resume: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form edit Dokumen Resume
     */
    public function edit($id)
    {
        $document = ResumeDocument::findOrFail($id);
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

        $sprindikDoc = DB::table('doc.surat_perintah_penyidikan_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $springasDoc = DB::table('doc.surat_perintah_tugas_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $spdpDoc = DB::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $spTapDoc = DB::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $sprindikTanggal = $sprindikDoc && !empty($sprindikDoc->document_date)
            ? Carbon::parse($sprindikDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        $springasTanggal = $springasDoc && !empty($springasDoc->document_date)
            ? Carbon::parse($springasDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        $spdpTanggal = $spdpDoc && !empty($spdpDoc->document_date)
            ? Carbon::parse($spdpDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        $spTapTanggal = $spTapDoc && !empty($spTapDoc->document_date)
            ? Carbon::parse($spTapDoc->document_date)->format('Y-m-d')
            : date('Y-m-d');

        return view('docs.resume-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'sprindikDoc',
            'springasDoc',
            'spdpDoc',
            'spTapDoc',
            'sprindikTanggal',
            'springasTanggal',
            'spdpTanggal',
            'spTapTanggal'
        ));
    }

    /**
     * Perbarui Dokumen Resume
     */
    public function update(Request $request, $id)
    {
        $document = ResumeDocument::findOrFail($id);
        $accidentId = $document->accident_id;

        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'dikeluarkan_di' => 'required|string|max:255',
            'satker_penyidik' => 'required|string|max:255',
            'nomor_lp' => 'required|string|max:255',
            'tanggal_lp' => 'required|date',

            'dasar.lp_nomor' => 'required|string',
            'dasar.lp_tanggal' => 'required|date',
            'perkara.tindak_pidana' => 'required|string',
            'perkara.pasal' => 'required|string',
            'perkara.tkp' => 'required|string',

            'keterangan_saksi' => 'required|array|min:1',
            'keterangan_saksi.*.nama' => 'required|string',
            'keterangan_tersangka' => 'required|array|min:1',
            'keterangan_tersangka.*.nama' => 'required|string',

            'analisa_kasus' => 'required',
            'analisa_yuridis' => 'required',
            'kesimpulan' => 'required',

            'atasan_penyidik_nama' => 'required|string',
            'atasan_penyidik_pangkat' => 'required|string',
            'atasan_penyidik_nrp' => 'required|string',
            'pembuat_nama' => 'required|string',
            'pembuat_pangkat' => 'required|string',
            'pembuat_nrp' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $saksiData = $this->cleanRepeater($request->input('keterangan_saksi', []));
            $ahliData = $this->cleanRepeater($request->input('keterangan_ahli', []));
            $tersangkaData = $this->cleanRepeater($request->input('keterangan_tersangka', []));
            $bbData = $this->cleanRepeaterBb($request->input('barang_bukti', []));

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => Carbon::parse($request->tanggal_surat)->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'satker_penyidik' => $request->satker_penyidik,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => Carbon::parse($request->tanggal_lp)->format('Y-m-d'),

                'dasar' => $request->input('dasar', $document->dasar),
                'perkara' => $request->input('perkara', $document->perkara),

                'fakta_tkp' => $request->input('fakta_tkp', $document->fakta_tkp),
                'fakta_korban' => $request->input('fakta_korban', $document->fakta_korban),
                'fakta_pemanggilan' => $request->input('fakta_pemanggilan', $document->fakta_pemanggilan),
                'fakta_perintah_membawa' => $request->input('fakta_perintah_membawa', $document->fakta_perintah_membawa),
                'fakta_penangkapan' => $request->input('fakta_penangkapan', $document->fakta_penangkapan),
                'fakta_penahanan' => $request->input('fakta_penahanan', $document->fakta_penahanan),
                'fakta_penggeledahan' => $request->input('fakta_penggeledahan', $document->fakta_penggeledahan),
                'fakta_penyitaan' => $request->input('fakta_penyitaan', $document->fakta_penyitaan),

                'keterangan_saksi' => $saksiData,
                'keterangan_ahli' => $ahliData,
                'keterangan_tersangka' => $tersangkaData,

                'fakta_konfrontasi' => $request->fakta_konfrontasi ?: $document->fakta_konfrontasi,
                'fakta_rekonstruksi' => $request->fakta_rekonstruksi ?: $document->fakta_rekonstruksi,
                'fakta_dpo' => $request->fakta_dpo ?: $document->fakta_dpo,
                'fakta_dpb' => $request->fakta_dpb ?: $document->fakta_dpb,
                'fakta_red_notice' => $request->fakta_red_notice ?: $document->fakta_red_notice,
                'fakta_pencegahan' => $request->fakta_pencegahan ?: $document->fakta_pencegahan,
                'fakta_penangkalan' => $request->fakta_penangkalan ?: $document->fakta_penangkalan,

                'barang_bukti' => $bbData,
                'fakta_penghentian_penyidikan' => $request->input('fakta_penghentian_penyidikan', $document->fakta_penghentian_penyidikan),
                'fakta_penyidikan_lanjutan' => $request->input('fakta_penyidikan_lanjutan', $document->fakta_penyidikan_lanjutan),

                'analisa_kasus' => $request->input('analisa_kasus', $document->analisa_kasus),
                'analisa_yuridis' => $request->input('analisa_yuridis', $document->analisa_yuridis),
                'kesimpulan' => $request->input('kesimpulan', $document->kesimpulan),

                'atasan_penyidik_nama' => $request->atasan_penyidik_nama,
                'atasan_penyidik_pangkat' => $request->atasan_penyidik_pangkat,
                'atasan_penyidik_nrp' => preg_replace('/[^0-9]/', '', (string) $request->atasan_penyidik_nrp) ?: $request->atasan_penyidik_nrp,
                'atasan_penyidik_jabatan' => $request->atasan_penyidik_jabatan ?: $document->atasan_penyidik_jabatan,

                'pembuat_nama' => $request->pembuat_nama,
                'pembuat_pangkat' => $request->pembuat_pangkat,
                'pembuat_nrp' => preg_replace('/[^0-9]/', '', (string) $request->pembuat_nrp) ?: $request->pembuat_nrp,
                'pembuat_jabatan' => $request->pembuat_jabatan ?: $document->pembuat_jabatan,

                'updated_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Resume Berkas Perkara berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memperbarui Dokumen Resume: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Resume Berkas Perkara (S-1.1.3)
     */
    public function download($id)
    {
        try {
            $document = ResumeDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/resume_document.docx');
            if (! file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word resume_document.docx tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templateFile);

            $esc = function ($val) {
                return htmlspecialchars((string) ($val ?? ''), ENT_XML1, 'UTF-8');
            };

            $splitLines = function ($input) {
                if (is_string($input)) {
                    $input = explode("\n", $input);
                }
                $lines = [];
                if (is_array($input)) {
                    foreach ($input as $item) {
                        if (is_string($item)) {
                            foreach (explode("\n", $item) as $l) {
                                $trim = trim($l);
                                if ($trim !== '') {
                                    $lines[] = $trim;
                                }
                            }
                        }
                    }
                }

                return $lines;
            };

            // 1. Kop Surat & Lokasi
            $poldaFullName = $accident->polres->polda->name ?? ($accident->polres->polda->full_name ?? '');
            $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114']))
                ? 'DIREKTORAT LALU LINTAS'
                : 'RESOR '.strtoupper($accident->polres->full_name ?? $accident->polres->name ?? '');
            $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '').', '.($accident->polres->polres_district ?? '').', '.($accident->polres->polres_zipcode ?? '')));

            $documentLocation = $document->dikeluarkan_di ?: 'Tempat';
            $docDateFormatted = $document->tanggal_surat
                ? Carbon::parse($document->tanggal_surat)->locale('id')->translatedFormat('d F Y')
                : Carbon::now()->locale('id')->translatedFormat('d F Y');

            $templateProcessor->setValue('daerahPoliceFullName', $esc(strtoupper($poldaFullName)));
            $templateProcessor->setValue('resorPoliceFullName', $esc($resorPoliceFullName));
            $templateProcessor->setValue('resorPoliceAddress', $esc($resorPoliceAddress));
            $templateProcessor->setValue('documentLocation', $esc($documentLocation));
            $templateProcessor->setValue('documentDate', $esc($docDateFormatted));

            // 2. I. DASAR
            $dasar = $document->dasar ?: [];
            $templateProcessor->setValue('dasar_lp_nomor', $esc($dasar['lp_nomor'] ?? ($document->nomor_lp ?? '-')));
            $templateProcessor->setValue('dasar_lp_tanggal', $esc($this->formatDateStr($dasar['lp_tanggal'] ?? $document->tanggal_lp)));
            $templateProcessor->setValue('dasar_sprindik_nomor', $esc($dasar['sprindik_nomor'] ?? '-'));
            $templateProcessor->setValue('dasar_sprindik_tanggal', $esc($this->formatDateStr($dasar['sprindik_tanggal'] ?? null)));
            $templateProcessor->setValue('dasar_springas_nomor', $esc($dasar['springas_nomor'] ?? '-'));
            $templateProcessor->setValue('dasar_springas_tanggal', $esc($this->formatDateStr($dasar['springas_tanggal'] ?? null)));
            $templateProcessor->setValue('dasar_spdp_nomor', $esc($dasar['spdp_nomor'] ?? '-'));
            $templateProcessor->setValue('dasar_spdp_tanggal', $esc($this->formatDateStr($dasar['spdp_tanggal'] ?? null)));
            $templateProcessor->setValue('dasar_sp_tap_nomor', $esc($dasar['sp_tap_nomor'] ?? '-'));
            $templateProcessor->setValue('dasar_sp_tap_tanggal', $esc($this->formatDateStr($dasar['sp_tap_tanggal'] ?? null)));
            $templateProcessor->setValue('dasar_sp_tap_tersangka', $esc($dasar['sp_tap_tersangka'] ?? '-'));
            $templateProcessor->setValue('dasar_surat_jaksa_nomor', $esc($dasar['surat_jaksa_nomor'] ?? '-'));
            $templateProcessor->setValue('dasar_surat_jaksa_tanggal', $esc($this->formatDateStr($dasar['surat_jaksa_tanggal'] ?? null)));

            // 3. II. PERKARA
            $perkara = $document->perkara ?: [];
            $templateProcessor->setValue('perkara_tindak_pidana', $esc($perkara['tindak_pidana'] ?? 'Kecelakaan Lalu Lintas'));

            $rawPasal = trim($perkara['pasal'] ?? '310');
            $cleanPasal = preg_replace('/^pasal\s+/i', '', $rawPasal);
            $templateProcessor->setValue('perkara_pasal', $esc($cleanPasal ?: '310'));

            $templateProcessor->setValue('perkara_uu_nomor', $esc($perkara['uu_nomor'] ?? '22'));
            $templateProcessor->setValue('perkara_uu_tahun', $esc($perkara['uu_tahun'] ?? '2009'));
            $templateProcessor->setValue('perkara_uu_tentang', $esc($perkara['uu_tentang'] ?? 'Lalu Lintas dan Angkutan Jalan'));
            $templateProcessor->setValue('perkara_tkp', $esc($perkara['tkp'] ?? '-'));
            $templateProcessor->setValue('perkara_hari', $esc($perkara['hari'] ?? '-'));
            $templateProcessor->setValue('perkara_tanggal', $esc($this->formatDateStr($perkara['tanggal'] ?? null)));
            $templateProcessor->setValue('perkara_kurun_waktu', $esc($perkara['kurun_waktu'] ?? '-'));
            $templateProcessor->setValue('perkara_pihak', $esc($perkara['pihak'] ?? '-'));

            $kerugianList = $perkara['daftar_kerugian'] ?? [];
            $blockKerugian = [];
            $ch = 'A';
            if (is_array($kerugianList)) {
                foreach ($kerugianList as $k) {
                    $trimK = trim($k);
                    if ($trimK !== '') {
                        $cleanK = preg_replace('/^[A-Z0-9]+[\.\)]\s*/i', '', $trimK);
                        $blockKerugian[] = [
                            'kerugian_abjad' => $ch++,
                            'kerugian_uraian' => $esc($cleanK),
                        ];
                    }
                }
            }
            if (empty($blockKerugian)) {
                $blockKerugian[] = [
                    'kerugian_abjad' => 'A',
                    'kerugian_uraian' => '-',
                ];
            }
            $templateProcessor->cloneBlock('block_kerugian', count($blockKerugian), true, false, $blockKerugian);

            $templateProcessor->setValue('perkara_nilai_taksir', number_format((float) ($perkara['nilai_taksir'] ?? 0), 0, ',', '.'));
            $templateProcessor->setValue('perkara_terbilang', $esc($perkara['terbilang'] ?? '-'));

            // 4. III. FAKTA-FAKTA PENYIDIKAN (A s/d T Sesuai Format Resmi Mindik Perkap)

            // A.1 Tindakan Pertama di TKP
            $faktaTkp = $document->fakta_tkp ?: [];
            $tindakanList = $splitLines($faktaTkp['tindakan_pertama'] ?? []);
            if (empty($tindakanList)) {
                $tindakanList = [
                    'Mendatangi Tempat Kejadian Perkara (TKP) setelah menerima laporan kecelakaan;',
                    'Menolong korban kecelakaan dan membawa ke Rumah Sakit terdekat;',
                    'Mengamankan status quo TKP dan barang bukti kecelakaan.',
                ];
            }
            $tindakanXml = '';
            $ch = 'a';
            foreach ($tindakanList as $t) {
                if (trim($t) !== '') {
                    $cleanT = preg_replace('/^[a-z0-9]+[\.\)]\s*/i', '', trim($t));
                    $tindakanXml .= $this->buildXmlParagraph(($ch++).'.   '.$cleanT, 1580, 430);
                }
            }
            $this->replacePlaceholderXml($templateProcessor, 'fakta_tindakan_pertama_xml', $tindakanXml);

            // A.2 Pengolahan TKP
            $pengolahanList = $splitLines($faktaTkp['pengolahan_tkp'] ?? []);
            if (empty($pengolahanList)) {
                $pengolahanList = [
                    'Berita Acara Pemeriksaan di TKP;',
                    'Berita Acara Memasuki Rumah/Tempat Tertutup Di TKP;',
                    'Berita Acara Penemuan Dan Penyitaan Barang Bukti Di TKP;',
                    'Berita Acara Penemuan Dan Pengambilan Darah Di TKP;',
                    'Berita Acara Penemuan Dan Pengambilan Sperma Di TKP;',
                    'Berita Acara Penemuan Dan Pengambilan Sidik Jari Latent/Jejak Kaki/Jejak Ban Di TKP;',
                    'Kartu Pendapatan Jejak/Sidik Jari Di TKP;',
                    'Berita Acara Penemuan Dan Pengambilan Limbah Dan Lain-Lain Di TKP;',
                    'Berita Acara Pemotretan Di TKP (Termasuk Data Pemotretan Korban Dan Barang Bukti);',
                    'Gambar Sketsa Kasar TKP;',
                    'Sketsa TKP Umum/Khusus;',
                    'Data Pentahapan Penanganan TKP (Kegiatan Selama Di TKP).',
                ];
            }
            $pengolahanXml = '';
            $ch = 'a';
            foreach ($pengolahanList as $p) {
                if (trim($p) !== '') {
                    $cleanP = preg_replace('/^[a-z0-9]+[\.\)]\s*/i', '', trim($p));
                    $pengolahanXml .= $this->buildXmlParagraph(($ch++).'.   '.$cleanP, 1580, 430);
                }
            }
            $this->replacePlaceholderXml($templateProcessor, 'fakta_pengolahan_tkp_xml', $pengolahanXml);

            // B. Pemeriksaan Keadaan Korban
            $korbanList = $splitLines($document->fakta_korban ?? []);
            if (empty($korbanList)) {
                $korbanList = [
                    'Surat Permintaan Pemeriksaan Luka Kepada Kepala Rumah Sakit;',
                    'Surat Permintaan Pemeriksaan Mayat Kepada Kepala Rumah Sakit;',
                    'Surat Permintaan Pemeriksaan Korban Perkosaan/Perzinahan Kepada Kepala Rumah Sakit Atau Puskesmas;',
                    'Surat Permintaan Pemeriksaan Oleh Ahli;',
                    'Berita Acara Pemeriksaan Oleh Ahli/Visum et Repertum.',
                ];
            }
            $korbanXml = '';
            $no = 1;
            foreach ($korbanList as $k) {
                if (trim($k) !== '') {
                    $cleanK = preg_replace('/^[0-9]+[\.\)]\s*/i', '', trim($k));
                    $korbanXml .= $this->buildXmlParagraph(($no++).'.   '.$cleanK, 1150, 430);
                }
            }
            $this->replacePlaceholderXml($templateProcessor, 'fakta_korban_xml', $korbanXml);

            // C. Pemanggilan (1 & 2)
            $panggilan = $document->fakta_pemanggilan ?: [];
            if (!empty($panggilan['p1_text'])) {
                $p1Text = $panggilan['p1_text'];
            } else {
                $p1Nomor = $panggilan['p1_nomor'] ?? '.........';
                $p1Tanggal = $this->formatDateStr($panggilan['p1_tanggal'] ?? null);
                if ($p1Tanggal === '-') { $p1Tanggal = '....'; }
                $p1Nama = $panggilan['p1_nama'] ?? ($document->keterangan_tersangka[0]['nama'] ?? '.....');
                $p1Hadir = $this->formatDateStr($panggilan['p1_hadir'] ?? null);
                if ($p1Hadir === '-') { $p1Hadir = '.....'; }
                $p1Text = "Dengan Surat Panggilan Nomor: {$p1Nomor} tanggal {$p1Tanggal}, telah melakukan pemanggilan terhadap saksi/Tersangka atas nama {$p1Nama} untuk hadir pada tanggal {$p1Hadir};";
            }

            if (!empty($panggilan['p2_text'])) {
                $p2Text = $panggilan['p2_text'];
            } elseif (!empty($panggilan['p2_keterangan'])) {
                $p2Text = $panggilan['p2_keterangan'];
            } else {
                $p2Nomor = $panggilan['p2_nomor'] ?? '........';
                $p2Tanggal = $this->formatDateStr($panggilan['p2_tanggal'] ?? null);
                if ($p2Tanggal === '-') { $p2Tanggal = '....'; }
                $p2Nama = $panggilan['p2_nama'] ?? ($document->keterangan_tersangka[0]['nama'] ?? '.......');
                $p2Hadir = $this->formatDateStr($panggilan['p2_hadir'] ?? null);
                if ($p2Hadir === '-') { $p2Hadir = '.....'; }
                $p2Text = "Dengan Surat Panggilan Kedua Nomor: {$p2Nomor} tanggal {$p2Tanggal}, telah melakukan pemanggilan terhadap saksi/Tersangka atas nama {$p2Nama} untuk hadir pada tanggal {$p2Hadir};";
            }

            $templateProcessor->setValue('fakta_p1_text', $esc($p1Text));
            $templateProcessor->setValue('fakta_p2_text', $esc($p2Text));

            // D. Perintah Membawa (1, 2, 3)
            $membawa = $document->fakta_perintah_membawa ?: [];
            $spMembawa = $membawa['sp_membawa'] ?? 'Dengan Surat Perintah Membawa Tersangka/Saksi Nomor: ....... tanggal......, telah dibawa untuk dihadapkan kepada penyidik, seorang Tersangka/saksi atas nama .... pada tanggal ....';
            $baMembawa = $membawa['ba_membawa'] ?? 'Telah dibuatkan Berita acara Membawa Tersangka/Saksi atas nama .... tanggal ....';
            $suratKeluarga = $membawa['surat_keluarga'] ?? 'Telah dibuatkan dan dikirimkan Surat pemberitahuan kepada keluarga Nomor: B/...... tanggal ...... .';

            $cleanSuratKeluarga = preg_replace('/^[0-9]+[\.\)]\s*/i', '', trim($suratKeluarga));
            $suratKeluargaText = '3.   ' . $cleanSuratKeluarga;

            $templateProcessor->setValue('fakta_sp_membawa_text', $esc($spMembawa));
            $templateProcessor->setValue('fakta_ba_membawa_text', $esc($baMembawa));
            $templateProcessor->setValue('fakta_surat_keluarga_text', $esc($suratKeluargaText));

            // Helper renderer untuk section multi-poin (E, F, G, H, T)
            $renderSectionListXml = function ($inputLines, $defaultItems, $baseLeft = 1150, $hanging = 430) use ($splitLines) {
                $lines = $splitLines($inputLines);
                if (empty($lines)) {
                    $lines = $defaultItems;
                }
                $xml = '';
                $no = 1;
                foreach ($lines as $line) {
                    $trimLine = trim($line);
                    if ($trimLine === '') continue;

                    // Jika merupakan paragraf deskripsi tanpa nomor awalan
                    if (preg_match('/^(Telah|Dengan|Berdasarkan|Adapun)\b/i', $trimLine) && !preg_match('/^[0-9]+[\.\)]/i', $trimLine)) {
                        $xml .= $this->buildXmlParagraph($trimLine, $baseLeft, 0);
                    } else {
                        $cleanLine = preg_replace('/^[0-9]+[\.\)]\s*/i', '', $trimLine);
                        $xml .= $this->buildXmlParagraph(($no++).'.   '.$cleanLine, $baseLeft, $hanging);
                    }
                }
                return $xml;
            };

            // E. Penangkapan
            $defaultPenangkapan = [
                'Surat Perintah Tugas untuk melakukan penangkapan terhadap Tersangka;',
                'Surat Perintah Penangkapan telah dilakukan penangkapan terhadap Tersangka;',
                'Berita Acara Penangkapan;',
                'Berita Acara Penolakan Tanda Tangan Surat Perintah Penangkapan dan Berita Acara Penangkapan;',
                'Berita Acara Penolakan Tanda Tangan Atas Berita Acara Penolakan Tandatangan Surat Perintah Penangkapan Dan Berita Acara Penangkapan (oleh Penyidik);',
                'Berita Acara Penggeledahan Badan/Pakaian;',
                'Surat Pemberitahuan Penangkapan kepada keluarga Tersangka;',
                'Surat Perintah Membawa Dan Menghadapkan Tersangka kepada Penyidik;',
                'Berita Acara Membawa Dan Menghadapkan Tersangka kepada Penyidik;',
                'Surat Pemberitahuan Membawa Dan Menghadapkan kepada Keluarga Tersangka;',
                'Surat Perintah Pelepasan Tersangka;',
                'Berita Acara Pelepasan Tersangka;',
                'Surat Pemberitahuan Pelepasan Tersangka kepada keluarga Tersangka;',
                'Surat Perintah Penangkapan Atas Permintaan Satuan Lain/Kepolisian Anggota ICPO-Interpol;',
                'Berita Acara Penangkapan Atas Permintaan Satuan Lain/Kepolisian Anggota ICPO-Interpol;',
                'Surat Pemberitahuan Penangkapan Atas Permintaan Satuan Lain/Kepolisian Anggota ICPO-Interpol kepada keluarga Tersangka.',
            ];
            $this->replacePlaceholderXml($templateProcessor, 'fakta_penangkapan_xml', $renderSectionListXml($document->fakta_penangkapan, $defaultPenangkapan));

            // F. Penahanan
            $defaultPenahanan = [
                'Surat Perintah Penahanan;',
                'Berita Acara Penahanan;',
                'Berita Acara Penolakan Tandatangan Surat Perintah Penahanan Dan Berita Acara Penahanan;',
                'Berita Acara Penolakan Tandatangan Atas Berita Acara Penolakan Tandatangan Surat Perintah Penahanan Dan Berita Acara Penahanan (oleh Penyidik);',
                'Surat Pemberitahuan Penahanan kepada keluarga Tersangka;',
                'Surat Pemberitahuan Penangkapan dan Penahanan kepada keluarga Tersangka;',
                'Surat Permintaan Perpanjangan Penahanan kepada Jampidum/Jampidsus/Kepala Kejaksaan Tinggi/Negeri;',
                'Surat Ketetapan Perpanjangan Penahanan dari Jampidum/Jampidsus/Kepala Kejaksaan Tinggi/Negeri untuk memperpanjang penahanan terhadap Tersangka;',
                'Surat Perintah Perpanjangan Penahanan dari Jaksa;',
                'Berita Acara Perpanjangan Penahanan dari Jaksa;',
                'Surat Pemberitahuan Perpanjangan Penahanan dari JPU kepada keluarga Tersangka;',
                'Surat Permintaan Perpanjangan Penahanan kepada Ketua Pengadilan Negeri (1);',
                'Surat Ketetapan Perpanjangan Penahanan dari Ketua Pengadilan Negeri (1);',
                'Surat Perintah Perpanjangan Penahanan dari PN 1;',
                'Berita Acara Perpanjangan Penahanan Dari PN 1;',
                'Surat Pemberitahuan Perpanjangan Penahanan Dari PN1 Kepada Keluarga Tersangka;',
                'Surat Permintaan Perpanjangan Penahanan Kepada Ketua Pengadilan Negeri (2);',
                'Surat Ketetapan Perpanjangan Penahanan dari Ketua Pengadilan Negeri (2);',
                'Surat Perintah Perpanjangan Penahanan Dari PN 2;',
                'Berita Acara Perpanjangan Penahanan Dari PN 2;',
                'Surat Pemberitahuan Perpanjangan Penahanan Dari PN 2 Kepada Keluarga Tersangka;',
                'Surat Permohonan Penangguhan Penahanan Dari Tersangka/Keluarganya;',
                'Surat Perintah Penangguhan Penahanan;',
                'Berita Acara Penangguhan Penahanan;',
                'Surat Perintah Pencabutan Penangguhan Penahanan;',
                'Berita Acara Pencabutan Penangguhan Penahanan;',
                'Surat Permohonan Pengalihan Jenis Penahanan Dari Tersangka/Keluarganya;',
                'Surat Perintah Pengalihan Jenis Penahanan;',
                'Berita Acara Pengalihan Jenis Penahanan;',
                'Surat Pemberitahuan Pengalihan Jenis Penahanan Kepada Keluarga Tersangka;',
                'Surat Permohonan Pemindahan Tempat Penahanan Dari Tersangka/Keluarganya;',
                'Surat Perintah Pemindahan Tempat Penahanan;',
                'Berita Acara Pemindahan Tempat Penahanan;',
                'Surat Pemberitahuan Pemindahan Tempat Penahanan Kepada Keluarga Tersangka;',
                'Surat Permohonan Rawat Inap Tersangka Kepada Kepala Rumah Sakit;',
                'Surat Perintah Pembantaran Penahanan;',
                'Berita Acara Pembantaran Penahanan;',
                'Surat Pemberitahuan Pembantaran Penahanan kepada Keluarga Tersangka;',
                'Surat Pengambilan Tahanan Yang Telah Selesai Dirawat Inap Kepada Kepala Rumah Sakit;',
                'Surat Perintah Pencabutan Pembantaran Penahanan;',
                'Berita Acara Pencabutan Pembantaran Penahanan;',
                'Surat Perintah Penahanan Lanjutan;',
                'Berita Acara Penahanan Lanjutan;',
                'Surat Pemberitahuan Pencabutan Pembantaran dan Penahanan Lanjutan kepada Keluarga Tersangka;',
                'Surat Perintah Pengeluaran Tahanan;',
                'Berita Acara Pengeluaran Tahanan.',
            ];
            $this->replacePlaceholderXml($templateProcessor, 'fakta_penahanan_xml', $renderSectionListXml($document->fakta_penahanan, $defaultPenahanan));

            // G. Penggeledahan
            $defaultPenggeledahan = [
                'Surat Perintah Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Umum);',
                'Berita Acara Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Umum);',
                'Surat/Laporan Guna Memperoleh Persetujuan Penggeledahan Kepada Ketua PN;',
                'Surat/Laporan Guna Memperoleh Persetujuan Penggeledahan Dan Penyitaan Kepada Ketua PN;',
                'Surat Permintaan Izin/Izin Khusus Penggeledahan Kepada Ketua PN;',
                'Surat Permintaan Izin/Izin Khusus Penggeledahan Dan Penyitaan Kepada Ketua PN;',
                'Surat Perintah Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Setelah Mendapat Izin/Penetapan Dari Ketua PN);',
                'Berita Acara Penggeledahan Rumah Dan Tempat Tertutup Lainnya (Setelah Mendapat Izin/Penetapan Dari PN);',
                'Surat Perintah Penggeledahan Alat Angkutan Darat/Air/Udara;',
                'Berita Acara Penggeledahan Alat Angkutan Darat/Air/Udara.',
            ];
            $this->replacePlaceholderXml($templateProcessor, 'fakta_penggeledahan_xml', $renderSectionListXml($document->fakta_penggeledahan, $defaultPenggeledahan));

            // H. Penyitaan
            $defaultPenyitaan = [
                'Surat Perintah Penyitaan (Umum);',
                'Surat Tanda Penerimaan Barang Bukti;',
                'Berita Acara Penyitaan Barang Bukti (Umum);',
                'Surat/Laporan Guna Memperoleh Persetujuan Penyitaan Kepada Ketua PN;',
                'Surat Permintaan Izin/Izin Khusus Penyitaan;',
                'Surat Perintah Penyitaan (Setelah Mendapat Izin/Penetapan Dari PN);',
                'Surat Tanda Penerimaan Barang Bukti;',
                'Berita Acara Penyitaan (Setelah Mendapat Izin/Penetapan PN);',
                'Surat Perintah Pembungkusan Barang Bukti;',
                'Berita Acara Pembungkusan Barang Bukti;',
                'Surat Perintah Penyegelan Barang Bukti;',
                'Berita Acara Penyegelan Barang Bukti;',
                'Surat Perintah Penyegelan Dan Pembungkusan Barang Bukti;',
                'Berita Acara Penyegelan Dan Atau Pembungkusan Barang Bukti;',
                'Surat Perintah Pembukaan Dan Pembungkusan/ Penyegelan Kembali Barang Bukti;',
                'Berita Acara Pembukaan Dan Pembungkusan/ Penyegelan Kembali Barang Bukti;',
                'Surat Perintah Penitipan Barang Bukti;',
                'Berita Acara Penitipan Barang Bukti;',
                'Surat Perintah Titip/Titip Rawat Barang Bukti;',
                'Berita Acara Titip/Titip Rawat Barang Bukti;',
                'Surat Perintah Pengembalian Benda Sitaan/Barang Bukti;',
                'Berita Acara Pengembalian Barang Bukti;',
                'Surat Permintaan Penelitian/Pemeriksaan Benda Sitaan/Barang bukti Kepada Instansi Berwenang;',
                'Surat Pemberitahuan Dan Permintaan Persetujuan Lelang Barang Bukti Kepada Tersangka/Kuasanya;',
                'Surat Pernyataan Persetujuan Pelelangan Barang Bukti Dari Tersangka;',
                'Surat Permohonan Izin Pelelangan Barang Bukti Kepada Ketua PN;',
                'Surat Ketetapan Pelelangan Benda Sitaan/ Barang Bukti;',
                'Surat Perintah Pelelangan Benda Sitaan/Barang Bukti;',
                'Berita Acara Pelelangan Benda Sitaan/Barang Bukti;',
                'Surat Perintah Penyisihan Barang Bukti;',
                'Berita Acara Penyisihan Benda Sitaan/ Barang Bukti;',
                'Surat Permintaan Bantuan Pelelangan Benda Sitaan/Barang Bukti Kepada Kepala Kantor Lelang Negara;',
                'Berita Acara Penerimaan Hasil Lelang;',
                'Surat Permintaan Bantuan Pemeriksaan Benda Sitaan/Barang Bukti Kepada Instansi Berwenang Untuk Dimusnahkan;',
                'Surat Pemberitahuan Dan Permintaan Persetujuan Perampasan/Pemusnahan Barang Bukti Kepada Tersangka/Kuasanya;',
                'Surat Pernyataan Persetujuan Perampasan/Pemusnahan Barang Bukti Dari Tersangka;',
                'Surat Permohonan Izin Pemusnahan/Perampasan Benda Sitaan/Barang Bukti Yang Berbahaya Dan Terlarang/Dilarang Untuk Diedarkan Kepada Ketua PN;',
                'Surat Ketetapan Perampasan/Pemusnahan Benda Sitaan/Barang Bukti;',
                'Surat Perintah Perampasan/Pemusnahan Benda Sitaan/ Barang Bukti;',
                'Berita Acara Pemusnahan/Perampasan Benda Sitaan/Barang Bukti Yang Berbahaya Dan Terlarang/Dilarang Untuk Diedarkan;',
                'Surat Undangan Dalam Rangka Perampasan/Pemusnahan Benda Sitaan/Barang Bukti;',
                'Berita Acara Penyerahan Barang Bukti Ke Rubasan;',
                'Berita Acara Pengambilan Sampel Barang Bukti;',
                'Surat Kepada Ketua Pengadilan Negeri;',
                'Surat Perintah Pemeriksaan Surat;',
                'Berita Acara Pemeriksaan Surat;',
                'Surat Perintah Penyitaan Surat;',
            ];
            $this->replacePlaceholderXml($templateProcessor, 'fakta_penyitaan_xml', $renderSectionListXml($document->fakta_penyitaan, $defaultPenyitaan));

            // I. Keterangan Saksi-Saksi (Multi-level Hanging Indents)
            $saksiList = $document->keterangan_saksi ?: [];
            $saksiXml = '';
            $no = 1;
            foreach ($saksiList as $s) {
                $namaSaksi = trim($s['nama'] ?? '-');
                $saksiXml .= $this->buildXmlParagraph(($no++).'.   Nama        : '.$namaSaksi, 1150, 430);
                $saksiXml .= $this->buildXmlParagraph('Menerangkan :', 1150, 0);
                $ket = $s['keterangan'] ?? [];
                if (is_string($ket)) {
                    $ket = explode("\n", $ket);
                }
                $ch = 'a';
                if (is_array($ket)) {
                    foreach ($ket as $line) {
                        $trimLine = trim($line);
                        if ($trimLine !== '') {
                            $cleanLine = preg_replace('/^[a-z0-9]+[\.\)]\s*/i', '', $trimLine);
                            $saksiXml .= $this->buildXmlParagraph(($ch++).'.   '.$cleanLine, 1580, 430);
                        }
                    }
                }
            }
            $nextNo = $no;
            $saksiXml .= $this->buildXmlParagraph($nextNo.'.   saksi ke '.$nextNo.', '.($nextNo + 1).', dan seterusnya.', 1150, 430);
            $this->replacePlaceholderXml($templateProcessor, 'keterangan_saksi_xml', $saksiXml);

            // J. Keterangan Ahli
            $ahliList = $document->keterangan_ahli ?: [];
            $ahliXml = '';
            $no = 1;
            foreach ($ahliList as $a) {
                $namaAhli = trim($a['nama'] ?? '-');
                $ahliXml .= $this->buildXmlParagraph(($no++).'.   Nama        : '.$namaAhli, 1150, 430);
                $ahliXml .= $this->buildXmlParagraph('Menerangkan :', 1150, 0);
                $ket = $a['keterangan'] ?? [];
                if (is_string($ket)) {
                    $ket = explode("\n", $ket);
                }
                $ch = 'a';
                if (is_array($ket)) {
                    foreach ($ket as $line) {
                        $trimLine = trim($line);
                        if ($trimLine !== '') {
                            $cleanLine = preg_replace('/^[a-z0-9]+[\.\)]\s*/i', '', $trimLine);
                            $ahliXml .= $this->buildXmlParagraph(($ch++).'.   '.$cleanLine, 1580, 430);
                        }
                    }
                }
            }
            $nextNo = $no;
            $ahliXml .= $this->buildXmlParagraph($nextNo.'.   ahli ke '.$nextNo.', '.($nextNo + 1).', dan seterusnya.', 1150, 430);
            $this->replacePlaceholderXml($templateProcessor, 'keterangan_ahli_xml', $ahliXml);

            // K. Keterangan Tersangka
            $tskList = $document->keterangan_tersangka ?: [];
            $tskXml = '';
            $no = 1;
            foreach ($tskList as $t) {
                $namaTsk = trim($t['nama'] ?? '-');
                $tskXml .= $this->buildXmlParagraph(($no++).'.   Nama        : '.$namaTsk, 1150, 430);
                $tskXml .= $this->buildXmlParagraph('Menerangkan :', 1150, 0);
                $ket = $t['keterangan'] ?? [];
                if (is_string($ket)) {
                    $ket = explode("\n", $ket);
                }
                $ch = 'a';
                if (is_array($ket)) {
                    foreach ($ket as $line) {
                        $trimLine = trim($line);
                        if ($trimLine !== '') {
                            $cleanLine = preg_replace('/^[a-z0-9]+[\.\)]\s*/i', '', $trimLine);
                            $tskXml .= $this->buildXmlParagraph(($ch++).'.   '.$cleanLine, 1580, 430);
                        }
                    }
                }
            }
            $nextNo = $no;
            $tskXml .= $this->buildXmlParagraph($nextNo.'.   Tersangka ke '.$nextNo.', '.($nextNo + 1).', dan seterusnya.', 1150, 430);
            $this->replacePlaceholderXml($templateProcessor, 'keterangan_tersangka_xml', $tskXml);

            // Fakta Tambahan L s/d R
            $templateProcessor->setValue('fakta_konfrontasi', $esc($document->fakta_konfrontasi ?: 'Tidak dilaksanakan konfrontasi;'));
            $templateProcessor->setValue('fakta_rekonstruksi', $esc($document->fakta_rekonstruksi ?: 'Telah dilaksanakan rekonstruksi perkara pada TKP;'));
            $templateProcessor->setValue('fakta_dpo', $esc($document->fakta_dpo ?: 'Nihil;'));
            $templateProcessor->setValue('fakta_dpb', $esc($document->fakta_dpb ?: 'Nihil;'));
            $templateProcessor->setValue('fakta_red_notice', $esc($document->fakta_red_notice ?: 'Nihil;'));
            $templateProcessor->setValue('fakta_pencegahan', $esc($document->fakta_pencegahan ?: 'Nihil;'));
            $templateProcessor->setValue('fakta_penangkalan', $esc($document->fakta_penangkalan ?: 'Nihil;'));

            // S. Barang Bukti
            $bbList = $document->barang_bukti ?: [];
            $bbXml = '';
            $no = 1;
            foreach ($bbList as $b) {
                $disitaDari = trim($b['disita_dari'] ?? ($b['nama'] ?? '-'));
                $bbXml .= $this->buildXmlParagraph(($no++).'.   Disita dari : '.$disitaDari, 1150, 430);
                $bbXml .= $this->buildXmlParagraph('Menerangkan :', 1150, 0);
                $ket = $b['keterangan'] ?? [];
                if (is_string($ket)) {
                    $ket = explode("\n", $ket);
                }
                $ch = 'a';
                if (is_array($ket)) {
                    foreach ($ket as $line) {
                        $trimLine = trim($line);
                        if ($trimLine !== '') {
                            $cleanLine = preg_replace('/^[a-z0-9]+[\.\)]\s*/i', '', $trimLine);
                            $bbXml .= $this->buildXmlParagraph(($ch++).'.   '.$cleanLine, 1580, 430);
                        }
                    }
                }
            }
            $nextNo = $no;
            $bbXml .= $this->buildXmlParagraph($nextNo.'.   barang bukti ke '.$nextNo.', '.($nextNo + 1).', dan seterusnya.', 1150, 430);
            $this->replacePlaceholderXml($templateProcessor, 'barang_bukti_xml', $bbXml);

            // T. Penghentian Penyidikan
            $defaultPenghentian = [
                'Surat Perintah Penghentian Penyidikan;',
                'Surat Ketetapan Penghentian Penyidikan;',
                'Surat Pemberitahuan Penghentian Penyidikan Kepada Kejaksaan Dengan Tembusan Pelapor/Korban, Terlapor/Tersangka;',
                'Surat Pemberitahuan Penghentian Penyidikan Kepada Pelapor (SP2HP).',
            ];
            $this->replacePlaceholderXml($templateProcessor, 'fakta_penghentian_xml', $renderSectionListXml($document->fakta_penghentian_penyidikan, $defaultPenghentian));

            // 5. IV. PEMBAHASAN
            $kasus = $document->analisa_kasus ?: [];
            $kasusList = $splitLines($kasus);
            if (empty($kasusList)) {
                $kasusList = [
                    'Bahwa berdasarkan fakta-fakta penyidikan, keterangan saksi-saksi, dan olah TKP, telah terjadi kecelakaan lalu lintas;',
                    'Bahwa pengemudi kendaraan tidak berhati-hati dan melanggar batas kecepatan aman sehingga terjadi benturan.',
                ];
            }
            $kasusXml = '';
            $no = 1;
            foreach ($kasusList as $k) {
                $cleanK = preg_replace('/^[0-9]+[\.\)]\s*/i', '', trim($k));
                $kasusXml .= $this->buildXmlParagraph(($no++).'.   '.$cleanK, 1150, 430);
            }
            $this->replacePlaceholderXml($templateProcessor, 'analisa_kasus_xml', $kasusXml);

            $yuridis = $document->analisa_yuridis ?: [];
            $templateProcessor->setValue('analisa_tersangka_nama', $esc($yuridis['tersangka_nama'] ?? ($tskList[0]['nama'] ?? 'Tersangka')));
            $rawYuridisPasal = trim($yuridis['pasal'] ?? ($perkara['pasal'] ?? '310 KUHP'));
            $cleanYuridisPasal = preg_replace('/^pasal\s+/i', '', $rawYuridisPasal);
            $templateProcessor->setValue('analisa_pasal', $esc($cleanYuridisPasal ?: '310'));

            $unsurList = $splitLines($yuridis['unsur_unsur'] ?? []);
            if (empty($unsurList)) {
                $unsurList = [
                    'Setiap orang yang mengemudikan Kendaraan Bermotor: Unsur telah terpenuhi berdasarkan keterangan saksi dan Tersangka sendiri;',
                    'Karena kelalaiannya mengakibatkan Kecelakaan Lalu Lintas: Unsur telah terpenuhi berdasarkan olah TKP;',
                    'Mengakibatkan orang lain meninggal dunia: Unsur telah terpenuhi berdasarkan Visum et Repertum.',
                ];
            }
            $yuridisXml = '';
            $no = 1;
            foreach ($unsurList as $u) {
                $cleanU = preg_replace('/^[0-9]+[\.\)]\s*/i', '', trim($u));
                $yuridisXml .= $this->buildXmlParagraph(($no++).'.   '.$cleanU, 1150, 430);
            }
            $this->replacePlaceholderXml($templateProcessor, 'analisa_yuridis_xml', $yuridisXml);

            // 6. V. KESIMPULAN
            $kesimpulan = $document->kesimpulan ?: [];
            $kesimpulanList = $splitLines($kesimpulan);
            if (empty($kesimpulanList)) {
                $kesimpulanList = [
                    'Terhadap Tersangka patut diduga keras pada hari dan tanggal kejadian telah melakukan tindak pidana kelalaian dalam berkendara;',
                    'Oleh karena itu Tersangka dapat disangka telah melakukan Tindak Pidana sebagaimana dimaksud dalam pasal yang disangkakan.',
                ];
            }
            $kesimpulanXml = '';
            $no = 1;
            foreach ($kesimpulanList as $k) {
                $cleanK = preg_replace('/^[0-9A-Z]+[\.\)]\s*/i', '', trim($k));
                $kesimpulanXml .= $this->buildXmlParagraph(($no++).'.   '.$cleanK, 1150, 430);
            }
            $this->replacePlaceholderXml($templateProcessor, 'kesimpulan_xml', $kesimpulanXml);

            // 7. Dual Signatory
            $templateProcessor->setValue('atasanSignatoryName', $esc($document->atasan_penyidik_nama ?: '-'));
            $templateProcessor->setValue('atasanSignatoryRankName', $esc(strtoupper($document->atasan_penyidik_pangkat ?: '-')));
            $cleanNrpAtasan = preg_replace('/[^0-9]/', '', (string) $document->atasan_penyidik_nrp) ?: $document->atasan_penyidik_nrp;
            $templateProcessor->setValue('atasanSignatoryRegisterNumber', $esc($cleanNrpAtasan ?: '-'));

            $templateProcessor->setValue('pembuatSignatoryHeadText', $esc($document->pembuat_jabatan ?: 'Penyidik / Penyidik Pembantu'));
            $templateProcessor->setValue('pembuatSignatoryName', $esc($document->pembuat_nama ?: '-'));
            $templateProcessor->setValue('pembuatSignatoryRankName', $esc(strtoupper($document->pembuat_pangkat ?: '-')));
            $cleanNrpPembuat = preg_replace('/[^0-9]/', '', (string) $document->pembuat_nrp) ?: $document->pembuat_nrp;
            $templateProcessor->setValue('pembuatSignatoryRegisterNumber', $esc($cleanNrpPembuat ?: '-'));

            // Simpan dan Download
            $safeDocNo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $document->nomor_surat ?: 'Resume_Berkas_Perkara');
            $fileName = 'Resume_Berkas_Perkara_'.$safeDocNo.'_'.Carbon::now()->format('YmdHis').'.docx';
            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir.'/'.$fileName;

            $templateProcessor->saveAs($tempPath);

            return response()->download($tempPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh Dokumen Resume: '.$e->getMessage());
        }
    }

    /**
     * Hapus Dokumen Resume
     */
    public function delete($id)
    {
        try {
            $document = ResumeDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Resume Berkas Perkara berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: '.$e->getMessage());
        }
    }

    /**
     * Helper pembersih repeater
     */
    protected function cleanRepeater($items)
    {
        $res = [];
        if (! is_array($items)) {
            return $res;
        }

        foreach ($items as $item) {
            $nama = trim($item['nama'] ?? '');
            if ($nama !== '') {
                $rawKet = $item['keterangan'] ?? [];
                $cleanKet = [];
                if (is_array($rawKet)) {
                    foreach ($rawKet as $line) {
                        if (trim($line) !== '') {
                            $cleanKet[] = trim($line);
                        }
                    }
                } elseif (is_string($rawKet) && trim($rawKet) !== '') {
                    $cleanKet = explode("\n", trim($rawKet));
                }
                $res[] = [
                    'nama' => $nama,
                    'keterangan' => $cleanKet,
                ];
            }
        }

        return $res;
    }

    protected function cleanRepeaterBb($items)
    {
        $res = [];
        if (! is_array($items)) {
            return $res;
        }

        foreach ($items as $item) {
            $disita = trim($item['disita_dari'] ?? ($item['nama'] ?? ''));
            if ($disita !== '') {
                $rawKet = $item['keterangan'] ?? [];
                $cleanKet = [];
                if (is_array($rawKet)) {
                    foreach ($rawKet as $line) {
                        if (trim($line) !== '') {
                            $cleanKet[] = trim($line);
                        }
                    }
                } elseif (is_string($rawKet) && trim($rawKet) !== '') {
                    $cleanKet = explode("\n", trim($rawKet));
                }
                $res[] = [
                    'disita_dari' => $disita,
                    'keterangan' => $cleanKet,
                ];
            }
        }

        return $res;
    }

    protected function formatDateStr($date)
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

    protected function getOldNewPolresIds($policeId)
    {
        $ids = [$policeId];
        try {
            $oldNew = DB::table('polres_old_new')->where('old_id', $policeId)->orWhere('new_id', $policeId)->first();
            if ($oldNew) {
                if (! empty($oldNew->old_id)) {
                    $ids[] = $oldNew->old_id;
                }
                if (! empty($oldNew->new_id)) {
                    $ids[] = $oldNew->new_id;
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika tabel tidak ada
        }

        return array_unique($ids);
    }

    /**
     * Konversi bilangan ke kata-kata (Bahasa Indonesia)
     */
    private function terbilang($angka)
    {
        $angka = abs((float) $angka);
        $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' '.$baca[(int) $angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10).' belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10).' puluh'.$this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' seratus'.$this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100).' ratus'.$this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' seribu'.$this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000).' ribu'.$this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000).' juta'.$this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000).' milyar'.$this->terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000000).' trilyun'.$this->terbilang(fmod($angka, 1000000000000));
        }

        return trim($terbilang);
    }

    /**
     * Helper untuk membuat OpenXML paragraph dengan hanging indent dan line height presisi
     */
    private function buildXmlParagraph($text, $left = 1150, $hanging = 430, $bold = false)
    {
        $escText = htmlspecialchars((string) $text, ENT_XML1, 'UTF-8');
        $ind = ($hanging > 0) ? "<w:ind w:left=\"{$left}\" w:hanging=\"{$hanging}\"/>" : "<w:ind w:left=\"{$left}\"/>";
        $b = $bold ? "<w:b/>" : "";

        return "<w:p><w:pPr><w:jc w:val=\"both\"/>{$ind}<w:spacing w:after=\"35\" w:line=\"260\" w:lineRule=\"auto\"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii=\"Arial\" w:hAnsi=\"Arial\"/><w:sz w:val=\"20\"/>{$b}</w:rPr><w:t xml:space=\"preserve\">{$escText}</w:t></w:r></w:p>";
    }

    /**
     * Helper untuk mengganti placeholder ${searchKey} dengan blok OpenXML raw paragraphs
     */
    private function replacePlaceholderXml(TemplateProcessor $tp, $searchKey, $xmlReplacement)
    {
        $ref = new \ReflectionClass($tp);
        $prop = $ref->getProperty('tempDocumentMainPart');
        $prop->setAccessible(true);
        $content = $prop->getValue($tp);

        $pattern = '/<w:p\b[^>]*>(?:(?!<\/w:p>).)*?\\$\\{'.preg_quote($searchKey, '/').'\\}(?:(?!<\/w:p>).)*?<\/w:p>/s';
        $newContent = preg_replace($pattern, $xmlReplacement, $content, 1);
        $prop->setValue($tp, $newContent);
    }
}

