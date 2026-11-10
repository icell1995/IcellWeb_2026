<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use ZipArchive;
use PhpOffice\PhpWord\TemplateProcessor;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\CaseVehicle;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Lib\Prosecutor;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument;
use App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Traits\DocsOfficersTraits;

class BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected DocService $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index()
    {
        $accidentId = request()->query('accident_id');
        if ($accidentId) {
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
        }
        return redirect()->back();
    }

    /**
     * Tampilan form pembuatan Berita Acara Serah Terima Tersangka Dan Barang Bukti
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // 1. Dokumen SPDP
        $spdpDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSpdp = $spdpDocument->nomor ?? $spdpDocument->document_number ?? null;
        $tanggalSpdp = $spdpDocument->tanggal ?? $spdpDocument->document_date ?? null;

        // 2. Dokumen Surat Perintah Penyidikan
        $sprindikDocument = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSprindik = $sprindikDocument->nomor ?? $sprindikDocument->document_number ?? null;
        $tanggalSprindik = $sprindikDocument->tanggal ?? $sprindikDocument->document_date ?? null;

        // 3. Dokumen Surat Perintah Penahanan
        $suratPerintahPenahanan = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorS17 = $suratPerintahPenahanan->nomor ?? $suratPerintahPenahanan->document_number ?? null;
        $tanggalS17 = $suratPerintahPenahanan->tanggal ?? $suratPerintahPenahanan->document_date ?? null;

        // 4. Tersangka Perkara
        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accidentId)->get();
        $firstSuspect = $suspects->first();
        $secondSuspect = $suspects->count() > 1 ? $suspects->get(1) : null;

        // 5. Petugas Internal
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();

        $today = date('Y-m-d');
        $hariIni = $this->getIndoDay($today);
        $jamIni = date('H.i');
        $tempatDefault = 'Kantor Kejaksaan Negeri ' . ($accident->polres->name ?? 'Pasuruan');

        // Barang Bukti Default dari Kendaraan
        $vehicles = CaseVehicle::where('accident_id', $accidentId)->get();
        $defaultBarangBukti = '';
        if ($vehicles && $vehicles->isNotEmpty()) {
            $bbLines = [];
            $n = 1;
            foreach ($vehicles as $v) {
                $type = $v->vehicle_type ?: 'Kendaraan Bermotor';
                $nopol = $v->plate_number ? 'No. Pol: ' . $v->plate_number : 'Tanpa Plat Nomor';
                $color = $v->vehicle_color ? ', Warna: ' . $v->vehicle_color : '';
                $nosin = $v->engine_number ? ', No. Mesin: ' . $v->engine_number : '';
                $noka = $v->frame_number ? ', No. Rangka: ' . $v->frame_number : '';
                $bbLines[] = $n++ . ". 1 (satu) unit " . $type . " " . $nopol . $color . $noka . $nosin . ";";
                if ($v->stnk_number) {
                    $bbLines[] = $n++ . ". 1 (satu) lembar STNK " . $nopol . " No: " . $v->stnk_number . ($v->owner_name ? " an. " . $v->owner_name : "") . ";";
                }
                if ($v->owner_license_number) {
                    $bbLines[] = $n++ . ". 1 (satu) buah SIM " . ($v->owner_license_class ?: 'C') . " No: " . $v->owner_license_number . ($v->owner_name ? " an. " . $v->owner_name : "") . ";";
                }
            }
            $defaultBarangBukti = implode("\n", $bbLines);
        }

        // Default Pasal & Tindak Pidana
        $defaultTindakPidana = 'Kecelakaan Lalu Lintas';
        $defaultPasal = '310 ayat (4)';
        $defaultUuNomor = '22';
        $defaultUuTahun = '2009';
        $defaultUuTentang = 'Lalu Lintas dan Angkutan Jalan';

        if ($sprindikDocument && $sprindikDocument->suratPerintahPenyidikanDocumentLaws && $sprindikDocument->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $tList = [];
            $pList = [];
            foreach ($sprindikDocument->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->crimeType) {
                    $tList[] = trim($law->crimeType->name);
                }
                if (!empty($law->constitution_chapter)) {
                    $pList[] = trim(str_ireplace('pasal', '', $law->constitution_chapter));
                }
            }
            if (!empty($tList)) {
                $defaultTindakPidana = implode(', ', array_unique($tList));
            }
            if (!empty($pList)) {
                $defaultPasal = implode(', ', array_unique($pList));
            }
        }

        $defaultTkp = $accident->address_detail ?: ($accident->location ?: ($accident->road_name ?: 'Wilayah Hukum Polres ' . ($accident->polres->name ?? 'Pasuruan')));
        $defaultWaktuKejadian = 'hari ' . ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : 'Senin')
            . ' tanggal ' . ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-')
            . ($accident->accident_time ? ' sekira pukul ' . Carbon::parse($accident->accident_time)->format('H.i') . ' WIB' : '');

        // Kop Default
        $defaultDaerahPolice = strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR');
        $defaultResorPolice = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN'));
        $defaultResorPoliceAddress = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        // Waktu & Tanggal Terbilang
        $dayDate = date('d');
        $monthName = Carbon::now()->locale('id')->translatedFormat('F');
        $yearDate = date('Y');
        $defaultDateText = $this->terbilangTanggal((int)$dayDate, $monthName, (int)$yearDate);

        // Nomor Berkas & Pengantar
        $defaultBerkasNumber = 'BP/' . ($sprindikDocument->nomor ?? ($accident->no_lp ?: '01'));
        $defaultBerkasDate = $sprindikDocument && $sprindikDocument->tanggal ? Carbon::parse($sprindikDocument->tanggal)->locale('id')->translatedFormat('d F Y') : date('d F Y');
        $defaultSuspectName = $firstSuspect ? $firstSuspect->name : '-';
        $defaultSuspect2Line = $secondSuspect
            ? ('2. Tersangka atas nama ' . $secondSuspect->name . ' sebagaimana terlampir dalam daftar tersangka pada berkas perkara Nomor: ' . $defaultBerkasNumber . ' tanggal ' . $defaultBerkasDate . ';')
            : '-';

        return view('docs.berita-acara-serah-terima-tersangka-dan-barang-bukti-document.create', [
            'accident'                  => $accident,
            'accidentId'                => $accidentId,
            'spdpDocument'              => $spdpDocument,
            'nomorSpdp'                 => $nomorSpdp,
            'tanggalSpdp'               => $tanggalSpdp,
            'sprindikDocument'          => $sprindikDocument,
            'nomorSprindik'             => $nomorSprindik,
            'tanggalSprindik'           => $tanggalSprindik,
            'suratPerintahPenahanan'    => $suratPerintahPenahanan,
            'nomorS17'                  => $nomorS17,
            'tanggalS17'                => $tanggalS17,
            'suspects'                  => $suspects,
            'firstSuspect'              => $firstSuspect,
            'secondSuspect'             => $secondSuspect,
            'officers'                  => $officers,
            'authorizedSignatories'     => $authorizedSignatories,
            'prosecutors'               => $prosecutors,
            'today'                     => $today,
            'hariIni'                   => $hariIni,
            'jamIni'                    => $jamIni,
            'tempatDefault'             => $tempatDefault,
            'defaultBarangBukti'        => $defaultBarangBukti,
            'defaultDaerahPolice'       => $defaultDaerahPolice,
            'defaultResorPolice'        => $defaultResorPolice,
            'defaultResorPoliceAddress' => $defaultResorPoliceAddress,
            'dayDate'                   => $dayDate,
            'monthName'                 => $monthName,
            'yearDate'                  => $yearDate,
            'defaultDateText'           => $defaultDateText,
            'defaultTindakPidana'       => $defaultTindakPidana,
            'defaultPasal'              => $defaultPasal,
            'defaultUuNomor'            => $defaultUuNomor,
            'defaultUuTahun'            => $defaultUuTahun,
            'defaultUuTentang'          => $defaultUuTentang,
            'defaultTkp'                => $defaultTkp,
            'defaultWaktuKejadian'      => $defaultWaktuKejadian,
            'defaultBerkasNumber'       => $defaultBerkasNumber,
            'defaultBerkasDate'         => $defaultBerkasDate,
            'defaultSuspectName'        => $defaultSuspectName,
            'defaultSuspect2Line'       => $defaultSuspect2Line,
            'accompanyingOfficerList'   => [
                ['name' => '-', 'rank_name' => '-', 'register_number' => '-', 'position_name' => '-'],
            ],
            'witnessList'               => [
                ['name' => '-', 'rank_nip' => '-', 'position_name' => '-', 'address' => '-'],
            ],
        ]);
    }

    /**
     * Menyimpan Berita Acara Serah Terima Tersangka Dan Barang Bukti
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'investigator_name'          => 'required|string|max:255',
            'prosecutor_officer_name'    => 'required|string|max:255',
        ], [
            'investigator_name.required'       => 'Nama Penyidik yang menyerahkan wajib diisi.',
            'prosecutor_officer_name.required' => 'Nama Jaksa Penuntut Umum yang menerima wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $docId = (string) Uuid::generate();

            $sprindikDoc = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            $sphDoc = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            // Pihak Pertama (Penyidik yang menyerahkan)
            $deliverer = null;
            if ($request->filled('deliverer_officer_id')) {
                $deliverer = Officer::with(['rank', 'position', 'police'])->find($request->deliverer_officer_id);
            }

            // Data Signatory
            $signatory = null;
            $signatoryName = null;
            $signatoryRank = null;
            $signatoryNrp = null;
            $signatoryPosition = null;
            if ($request->filled('signatory')) {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
                if ($signatory) {
                    $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
                    $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
                    $signatoryNrp = $signatory->register_number ?? '-';
                    $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');
                }
            }

            // Data Kejaksaan
            $prosecutor = Prosecutor::find($request->prosecutor_id);
            $namaKejaksaan = $request->prosecutor_institution_name ?: ($prosecutor ? $prosecutor->name : ($request->nama_kejaksaan ?? 'Kejaksaan Negeri'));

            // Tanggal Terbilang
            $dayDate = $request->document_day_date ?: date('d');
            $monthName = $request->document_month ?: Carbon::now()->locale('id')->translatedFormat('F');
            $yearDate = $request->document_year ?: date('Y');
            $dateText = $request->document_date_text ?: $this->terbilangTanggal((int)$dayDate, $monthName, (int)$yearDate);

            // Saksi-saksi dinamis
            $witnessList = $this->extractWitnessList($request);
            $w1 = $witnessList[0] ?? ['name' => '-', 'rank_nip' => '-', 'position_name' => '-', 'address' => '-'];
            $w2 = $witnessList[1] ?? null;

            // Anggota pendamping dinamis
            $accompanyingOfficerList = $this->extractAccompanyingOfficerList($request);
            $off1 = $accompanyingOfficerList[0] ?? ['name' => '-', 'rank_name' => '-', 'register_number' => '-', 'position_name' => '-'];
            $off2 = $accompanyingOfficerList[1] ?? null;

            $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::create([
                'id'                                    => $docId,
                'accident_id'                           => $accidentId,
                'surat_perintah_penyidikan_document_id' => $sprindikDoc->id ?? null,
                'surat_perintah_penahanan_document_id'  => $sphDoc->id ?? null,
                'prosecutor_id'                         => $request->prosecutor_id,
                'nomor'                                 => $request->delivery_letter_number ?: $request->nomor,
                'tanggal'                               => $request->tanggal ?: date('Y-m-d'),
                'hari'                                  => $request->document_day_name ?: $request->hari,
                'jam'                                   => $request->document_time ?: $request->jam,
                'tempat'                                => $request->document_location ?: $request->tempat,
                'nomor_spdp'                            => $request->nomor_spdp,
                'tanggal_spdp'                          => $request->tanggal_spdp,
                'nomor_sprindik'                        => $request->berkas_number ?: $request->nomor_sprindik,
                'tanggal_sprindik'                      => $request->tanggal_sprindik,
                'nomor_surat_perintah_penahanan'        => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan'      => $request->tanggal_surat_perintah_penahanan,
                'nomor_p21'                             => $request->p21_document_number ?: $request->nomor_p21,
                'tanggal_p21'                           => $request->tanggal_p21,
                'nama_kejaksaan'                        => $namaKejaksaan,
                'lokasi_kejaksaan'                      => $request->handover_place ?: $request->lokasi_kejaksaan,
                'pihak_kedua_nama'                      => $request->prosecutor_officer_name ?: $request->pihak_kedua_nama,
                'pihak_kedua_nip'                       => $request->prosecutor_officer_rank_nip ?: $request->pihak_kedua_nip,
                'pihak_kedua_pangkat'                   => $request->prosecutor_officer_rank_nip ?: $request->pihak_kedua_pangkat,
                'pihak_kedua_jabatan'                   => $request->prosecutor_officer_position_name ?: $request->pihak_kedua_jabatan,
                'pihak_kedua_instansi'                  => $namaKejaksaan,
                'daftar_barang_bukti'                   => $request->daftar_barang_bukti,
                'catatan'                               => $request->execution_description ?: $request->catatan,
                'signatory_id'                          => $request->signatory,
                'signatory_head_text'                   => $request->signatory_head_text,
                'signatory_position'                    => $signatoryPosition,
                'signatory_name'                        => $signatoryName,
                'signatory_rank'                        => $signatoryRank,
                'signatory_nrp'                         => $signatoryNrp,
                'document_number'                       => $request->delivery_letter_number ?: $request->nomor,
                'document_date'                         => $request->tanggal ?: date('Y-m-d'),
                'status_id'                             => '2',
                'document_category_id'                  => '0602',

                // 55 Kolom Template Word Persis (otomatis dari relasi No LP/accident jika tidak diisi)
                'daerah_police_full_name'               => $request->daerah_police_full_name ?: strtoupper($accident->polres->polda->name ?? 'SUMATERA UTARA'),
                'resor_police_full_name'                => $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))),
                'resor_police_address'                  => $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')),
                'document_day_name'                     => $request->document_day_name,
                'document_day_date'                     => $dayDate,
                'document_month'                        => $monthName,
                'document_year'                         => $yearDate,
                'document_date_text'                    => $dateText,
                'document_time'                         => $request->document_time,
                'document_location'                     => $request->document_location,

                'investigator_name'                     => $request->investigator_name,
                'investigator_rank_name'                => $request->investigator_rank_name,
                'investigator_register_number'          => $request->investigator_register_number,
                'investigator_position_name'            => $request->investigator_position_name,
                'investigator_role'                     => $request->investigator_role ?: 'PENYIDIK / PENYIDIK PEMBANTU',

                'officer1_name'                         => $off1['name'] ?: '-',
                'officer1_rank_name'                    => $off1['rank_name'] ?: '-',
                'officer1_register_number'              => $off1['register_number'] ?: '-',
                'officer1_position_name'                => $off1['position_name'] ?: '-',

                'officer2_name'                         => $off2 ? ($off2['name'] ?: '-') : null,
                'officer2_rank_name'                    => $off2 ? ($off2['rank_name'] ?: '-') : null,
                'officer2_register_number'              => $off2 ? ($off2['register_number'] ?: '-') : null,
                'officer2_position_name'                => $off2 ? ($off2['position_name'] ?: '-') : null,

                'prosecutor_institution_name'           => $namaKejaksaan,
                'p21_document_number'                   => $request->p21_document_number,
                'p21_document_date'                     => $request->p21_document_date,
                'deliverer_head_title'                  => $request->deliverer_head_title,
                'delivery_letter_number'                => $request->delivery_letter_number,
                'delivery_letter_date'                  => $request->delivery_letter_date,

                'suspect_name'                          => $request->suspect_name,
                'suspect2_line'                         => $request->suspect2_line ?: '-',
                'berkas_number'                         => $request->berkas_number,
                'berkas_date'                           => $request->berkas_date,

                'prosecutor_officer_name'               => $request->prosecutor_officer_name,
                'prosecutor_officer_rank_nip'           => $request->prosecutor_officer_rank_nip,
                'prosecutor_officer_position_name'      => $request->prosecutor_officer_position_name,

                'dugaan_tindak_pidana'                  => $request->dugaan_tindak_pidana,
                'pasal_diduga'                          => $request->pasal_diduga,
                'uu_nomor'                              => $request->uu_nomor ?: '22',
                'uu_tahun'                              => $request->uu_tahun ?: '2009',
                'uu_tentang'                            => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'                       => $request->tempat_kejadian,
                'waktu_kejadian'                        => $request->waktu_kejadian,

                'handover_day'                          => $request->handover_day,
                'handover_date'                         => $request->handover_date,
                'handover_place'                        => $request->handover_place,
                'execution_description'                 => $request->execution_description,

                'witness1_name'                         => $w1['name'] ?: '-',
                'witness1_rank_nip'                     => $w1['rank_nip'] ?: '-',
                'witness1_position_name'                => $w1['position_name'] ?: '-',
                'witness1_address'                      => $w1['address'] ?: '-',

                'witness2_name'                         => $w2 ? ($w2['name'] ?: '-') : null,
                'witness2_rank_nip'                     => $w2 ? ($w2['rank_nip'] ?: '-') : null,
                'witness2_position_name'                => $w2 ? ($w2['position_name'] ?: '-') : null,
                'witness2_address'                      => $w2 ? ($w2['address'] ?: '-') : null,

                'payload'                               => [
                    'witnesses'             => $witnessList,
                    'accompanying_officers' => $accompanyingOfficerList,
                ],

                'created_by_user_id'                    => Auth::id(),
                'updated_by_user_id'                    => Auth::id(),
            ]);

            // Sync Tersangka jika dipilih
            if (!empty($request->suspects)) {
                $syncData = [];
                foreach ($request->suspects as $suspectId) {
                    $syncData[$suspectId] = [
                        'id' => (string) Uuid::generate(),
                    ];
                }
                $document->suspects()->sync($syncData);
            }

            // Simpan Petugas Penyidik Yang Menyerahkan (DELIVERER)
            if ($deliverer) {
                BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $docId,
                    'officer_id'      => (string) $deliverer->id,
                    'first_name'      => $deliverer->first_name,
                    'last_name'       => $deliverer->last_name,
                    'first_title'     => $deliverer->first_title,
                    'last_title'      => $deliverer->last_title,
                    'register_number' => $deliverer->register_number,
                    'position_id'     => $deliverer->position_id,
                    'rank_id'         => $deliverer->rank_id,
                    'police_id'       => $deliverer->police_id,
                    'class'           => 'DELIVERER',
                    'order_number'    => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Berita Acara Serah Terima Tersangka Dan Barang Bukti berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan form edit Berita Acara Serah Terima Tersangka Dan Barang Bukti
     */
    public function edit($id)
    {
        $accidentId = request()->query('accident_id');
        $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::with([
            'suspects',
            'delivererOfficer',
            'signatory',
            'officers',
        ])->findOrFail($id);

        $accidentId = $document->accident_id ?? $accidentId;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();

        $selectedSuspectIds = $document->suspects->pluck('id')->toArray();
        $selectedDelivererId = $document->delivererOfficer->officer_id ?? null;
        $selectedSignatoryId = $document->signatory_id ?? ($document->signatory->officer_id ?? null);

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.berita-acara-serah-terima-tersangka-dan-barang-bukti-document.edit', array_merge([
            'document'               => $document,
            'accident'               => $accident,
            'accidentId'             => $accidentId,
            'suspects'               => $suspects,
            'selectedSuspectIds'     => $selectedSuspectIds,
            'officers'               => $officers,
            'selectedDelivererId'    => $selectedDelivererId,
            'authorizedSignatories'  => $authorizedSignatories,
            'selectedSignatoryId'    => $selectedSignatoryId,
            'prosecutors'            => $prosecutors,
        ], $data));
    }

    /**
     * Update Berita Acara Serah Terima Tersangka Dan Barang Bukti
     */
    public function update(Request $request, $id)
    {
        $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::findOrFail($id);
        $accidentId = $document->accident_id ?? $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'investigator_name'          => 'required|string|max:255',
            'prosecutor_officer_name'    => 'required|string|max:255',
        ], [
            'investigator_name.required'       => 'Nama Penyidik yang menyerahkan wajib diisi.',
            'prosecutor_officer_name.required' => 'Nama Jaksa Penuntut Umum yang menerima wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Pihak Pertama (Penyidik yang menyerahkan)
            $deliverer = null;
            if ($request->filled('deliverer_officer_id')) {
                $deliverer = Officer::with(['rank', 'position', 'police'])->find($request->deliverer_officer_id);
            }

            // Data Signatory
            $signatory = null;
            $signatoryName = $document->signatory_name;
            $signatoryRank = $document->signatory_rank;
            $signatoryNrp = $document->signatory_nrp;
            $signatoryPosition = $document->signatory_position;
            if ($request->filled('signatory')) {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
                if ($signatory) {
                    $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
                    $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
                    $signatoryNrp = $signatory->register_number ?? '-';
                    $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');
                }
            }

            // Data Kejaksaan
            $prosecutor = Prosecutor::find($request->prosecutor_id);
            $namaKejaksaan = $request->prosecutor_institution_name ?: ($prosecutor ? $prosecutor->name : ($request->nama_kejaksaan ?? $document->nama_kejaksaan));

            $dayDate = $request->document_day_date ?: ($document->document_day_date ?: date('d'));
            $monthName = $request->document_month ?: ($document->document_month ?: Carbon::now()->locale('id')->translatedFormat('F'));
            $yearDate = $request->document_year ?: ($document->document_year ?: date('Y'));
            $dateText = $request->document_date_text ?: $this->terbilangTanggal((int)$dayDate, $monthName, (int)$yearDate);

            // Saksi-saksi dinamis
            $witnessList = $this->extractWitnessList($request);
            $w1 = $witnessList[0] ?? ['name' => '-', 'rank_nip' => '-', 'position_name' => '-', 'address' => '-'];
            $w2 = $witnessList[1] ?? null;

            // Anggota pendamping dinamis
            $accompanyingOfficerList = $this->extractAccompanyingOfficerList($request);
            $off1 = $accompanyingOfficerList[0] ?? ['name' => '-', 'rank_name' => '-', 'register_number' => '-', 'position_name' => '-'];
            $off2 = $accompanyingOfficerList[1] ?? null;

            $existingPayload = $document->payload ?? [];
            if (!is_array($existingPayload)) {
                $existingPayload = json_decode($existingPayload, true) ?: [];
            }
            $existingPayload['witnesses'] = $witnessList;
            $existingPayload['accompanying_officers'] = $accompanyingOfficerList;

            $document->update([
                'prosecutor_id'                         => $request->prosecutor_id,
                'nomor'                                 => $request->delivery_letter_number ?: $request->nomor,
                'tanggal'                               => $request->tanggal ?: $document->tanggal,
                'hari'                                  => $request->document_day_name ?: $request->hari,
                'jam'                                   => $request->document_time ?: $request->jam,
                'tempat'                                => $request->document_location ?: $request->tempat,
                'nomor_spdp'                            => $request->nomor_spdp,
                'tanggal_spdp'                          => $request->tanggal_spdp,
                'nomor_sprindik'                        => $request->berkas_number ?: $request->nomor_sprindik,
                'tanggal_sprindik'                      => $request->tanggal_sprindik,
                'nomor_surat_perintah_penahanan'        => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan'      => $request->tanggal_surat_perintah_penahanan,
                'nomor_p21'                             => $request->p21_document_number ?: $request->nomor_p21,
                'tanggal_p21'                           => $request->tanggal_p21,
                'nama_kejaksaan'                        => $namaKejaksaan,
                'lokasi_kejaksaan'                      => $request->handover_place ?: $request->lokasi_kejaksaan,
                'pihak_kedua_nama'                      => $request->prosecutor_officer_name ?: $request->pihak_kedua_nama,
                'pihak_kedua_nip'                       => $request->prosecutor_officer_rank_nip ?: $request->pihak_kedua_nip,
                'pihak_kedua_pangkat'                   => $request->prosecutor_officer_rank_nip ?: $request->pihak_kedua_pangkat,
                'pihak_kedua_jabatan'                   => $request->prosecutor_officer_position_name ?: $request->pihak_kedua_jabatan,
                'pihak_kedua_instansi'                  => $namaKejaksaan,
                'daftar_barang_bukti'                   => $request->daftar_barang_bukti,
                'catatan'                               => $request->execution_description ?: $request->catatan,
                'signatory_id'                          => $request->signatory,
                'signatory_head_text'                   => $request->signatory_head_text,
                'signatory_position'                    => $signatoryPosition,
                'signatory_name'                        => $signatoryName,
                'signatory_rank'                        => $signatoryRank,
                'signatory_nrp'                         => $signatoryNrp,
                'document_number'                       => $request->delivery_letter_number ?: $request->nomor,
                'document_date'                         => $request->tanggal ?: $document->tanggal,

                // 55 Kolom Template Word Persis (otomatis dari relasi No LP/accident jika tidak diisi)
                'daerah_police_full_name'               => $request->daerah_police_full_name ?: strtoupper($accident->polres->polda->name ?? 'SUMATERA UTARA'),
                'resor_police_full_name'                => $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))),
                'resor_police_address'                  => $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')),
                'document_day_name'                     => $request->document_day_name,
                'document_day_date'                     => $dayDate,
                'document_month'                        => $monthName,
                'document_year'                         => $yearDate,
                'document_date_text'                    => $dateText,
                'document_time'                         => $request->document_time,
                'document_location'                     => $request->document_location,

                'investigator_name'                     => $request->investigator_name,
                'investigator_rank_name'                => $request->investigator_rank_name,
                'investigator_register_number'          => $request->investigator_register_number,
                'investigator_position_name'            => $request->investigator_position_name,
                'investigator_role'                     => $request->investigator_role ?: ($document->investigator_role ?: 'PENYIDIK / PENYIDIK PEMBANTU'),

                'officer1_name'                         => $off1['name'] ?: '-',
                'officer1_rank_name'                    => $off1['rank_name'] ?: '-',
                'officer1_register_number'              => $off1['register_number'] ?: '-',
                'officer1_position_name'                => $off1['position_name'] ?: '-',

                'officer2_name'                         => $off2 ? ($off2['name'] ?: '-') : null,
                'officer2_rank_name'                    => $off2 ? ($off2['rank_name'] ?: '-') : null,
                'officer2_register_number'              => $off2 ? ($off2['register_number'] ?: '-') : null,
                'officer2_position_name'                => $off2 ? ($off2['position_name'] ?: '-') : null,

                'prosecutor_institution_name'           => $namaKejaksaan,
                'p21_document_number'                   => $request->p21_document_number,
                'p21_document_date'                     => $request->p21_document_date,
                'deliverer_head_title'                  => $request->deliverer_head_title,
                'delivery_letter_number'                => $request->delivery_letter_number,
                'delivery_letter_date'                  => $request->delivery_letter_date,

                'suspect_name'                          => $request->suspect_name,
                'suspect2_line'                         => $request->suspect2_line ?: '-',
                'berkas_number'                         => $request->berkas_number,
                'berkas_date'                           => $request->berkas_date,

                'prosecutor_officer_name'               => $request->prosecutor_officer_name,
                'prosecutor_officer_rank_nip'           => $request->prosecutor_officer_rank_nip,
                'prosecutor_officer_position_name'      => $request->prosecutor_officer_position_name,

                'dugaan_tindak_pidana'                  => $request->dugaan_tindak_pidana,
                'pasal_diduga'                          => $request->pasal_diduga,
                'uu_nomor'                              => $request->uu_nomor ?: '22',
                'uu_tahun'                              => $request->uu_tahun ?: '2009',
                'uu_tentang'                            => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'                       => $request->tempat_kejadian,
                'waktu_kejadian'                        => $request->waktu_kejadian,

                'handover_day'                          => $request->handover_day,
                'handover_date'                         => $request->handover_date,
                'handover_place'                        => $request->handover_place,
                'execution_description'                 => $request->execution_description,

                'witness1_name'                         => $w1['name'] ?: '-',
                'witness1_rank_nip'                     => $w1['rank_nip'] ?: '-',
                'witness1_position_name'                => $w1['position_name'] ?: '-',
                'witness1_address'                      => $w1['address'] ?: '-',

                'witness2_name'                         => $w2 ? ($w2['name'] ?: '-') : null,
                'witness2_rank_nip'                     => $w2 ? ($w2['rank_nip'] ?: '-') : null,
                'witness2_position_name'                => $w2 ? ($w2['position_name'] ?: '-') : null,
                'witness2_address'                      => $w2 ? ($w2['address'] ?: '-') : null,

                'payload'                               => $existingPayload,

                'updated_by_user_id'                    => Auth::id(),
            ]);

            // Sync Tersangka
            if (!empty($request->suspects)) {
                $syncData = [];
                foreach ($request->suspects as $suspectId) {
                    $syncData[$suspectId] = [
                        'id' => (string) Uuid::generate(),
                    ];
                }
                $document->suspects()->sync($syncData);
            } else {
                $document->suspects()->detach();
            }

            // Hapus officer lama dan buat baru
            BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer::where('doc_id', $document->id)->delete();

            if ($deliverer) {
                BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $document->id,
                    'officer_id'      => (string) $deliverer->id,
                    'first_name'      => $deliverer->first_name,
                    'last_name'       => $deliverer->last_name,
                    'first_title'     => $deliverer->first_title,
                    'last_title'      => $deliverer->last_title,
                    'register_number' => $deliverer->register_number,
                    'position_id'     => $deliverer->position_id,
                    'rank_id'         => $deliverer->rank_id,
                    'police_id'       => $deliverer->police_id,
                    'class'           => 'DELIVERER',
                    'order_number'    => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Berita Acara Serah Terima Tersangka Dan Barang Bukti berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Preview / Lihat Dokumen Berita Acara Serah Terima
     */
    public function show($id)
    {
        $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects',
            'delivererOfficer',
            'signatory',
            'officers',
        ])->findOrFail($id);

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.berita-acara-serah-terima-tersangka-dan-barang-bukti-document.show', array_merge([
            'document' => $document,
            'accident' => $accident,
        ], $data));
    }

    /**
     * Hapus dokumen
     */
    public function delete($id)
    {
        $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Serah Terima Tersangka Dan Barang Bukti berhasil dihapus.');
    }

    /**
     * Unduh File Word Berita Acara Serah Terima Tersangka Dan Barang Bukti
     */
    public function download($id)
    {
        $result = $this->generatePopulatedWordFile($id);
        return response()->download($result['filename'])->deleteFileAfterSend(true);
    }

    /**
     * Siapkan seluruh data 55 variabel template Word Berita Acara Serah Terima
     */
    public function prepareDocumentData($document, $accident): array
    {
        // 1. Kop
        $daerahPoliceFullName = strtoupper($document->daerah_police_full_name ?: ($accident->polres->polda->name ?? 'JAWA TIMUR'));
        $resorPoliceFullName = $document->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN')));
        $resorPoliceAddress = $document->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        // 2. Waktu & Pembukaan
        $docDate = $document->tanggal ? Carbon::parse($document->tanggal) : ($document->document_date ? Carbon::parse($document->document_date) : Carbon::now());
        $documentDayName = $document->document_day_name ?: ($document->hari ?: $this->getIndoDay($docDate->format('Y-m-d')));
        $documentDayDate = $document->document_day_date ?: $docDate->format('d');
        $documentMonth = $document->document_month ?: $docDate->locale('id')->translatedFormat('F');
        $documentYear = $document->document_year ?: $docDate->format('Y');

        $documentDateText = $document->document_date_text ?: $this->terbilangTanggal((int)$documentDayDate, $documentMonth, (int)$documentYear);
        $documentTime = $document->document_time ?: ($document->jam ?: '10.00');
        $documentLocation = $document->document_location ?: ($document->tempat ?: ($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan')));

        // 3. Penyidik (Yang Menyerahkan)
        $investigatorName = $document->investigator_name;
        $investigatorRankName = $document->investigator_rank_name;
        $investigatorRegisterNumber = $document->investigator_register_number;
        $investigatorPositionName = $document->investigator_position_name;
        $investigatorRole = $document->investigator_role ?: 'PENYIDIK / PENYIDIK PEMBANTU';

        if (empty($investigatorName) && $document->relationLoaded('delivererOfficer') && $document->delivererOfficer) {
            $dev = $document->delivererOfficer;
            $investigatorName = $dev->full_name ?? PeopleNameHelper::getFullName($dev->first_title, $dev->first_name, $dev->last_name, $dev->last_title);
            $investigatorRankName = $dev->rank->name ?? ($dev->rank_id ?? '-');
            $investigatorRegisterNumber = $dev->register_number ?? '-';
            $investigatorPositionName = $dev->position->name ?? 'Penyidik Pembantu';
        }

        // 4. Pendamping 1 & 2 (Dinamis 1, 2, atau N)
        $payload = $document->payload;
        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        $accompanyingOfficerList = [];
        if (!empty($payload['accompanying_officers']) && is_array($payload['accompanying_officers'])) {
            $accompanyingOfficerList = $payload['accompanying_officers'];
        } else {
            if (!empty($document->officer1_name)) {
                $accompanyingOfficerList[] = [
                    'name'            => $document->officer1_name,
                    'rank_name'       => $document->officer1_rank_name ?: '-',
                    'register_number' => $document->officer1_register_number ?: '-',
                    'position_name'   => $document->officer1_position_name ?: '-',
                ];
            }
            if (!empty($document->officer2_name) && $document->officer2_name !== '-' && trim($document->officer2_name) !== '') {
                $accompanyingOfficerList[] = [
                    'name'            => $document->officer2_name,
                    'rank_name'       => $document->officer2_rank_name ?: '-',
                    'register_number' => $document->officer2_register_number ?: '-',
                    'position_name'   => $document->officer2_position_name ?: '-',
                ];
            }
        }

        if (empty($accompanyingOfficerList)) {
            $accompanyingOfficerList[] = [
                'name'            => $document->officer1_name ?: '-',
                'rank_name'       => $document->officer1_rank_name ?: '-',
                'register_number' => $document->officer1_register_number ?: '-',
                'position_name'   => $document->officer1_position_name ?: '-',
            ];
        }

        $off1 = $accompanyingOfficerList[0] ?? ['name' => '-', 'rank_name' => '-', 'register_number' => '-', 'position_name' => '-'];
        $off2 = $accompanyingOfficerList[1] ?? ['name' => '-', 'rank_name' => '-', 'register_number' => '-', 'position_name' => '-'];

        $officer1Name = $off1['name'] ?: '-';
        $officer1RankName = $off1['rank_name'] ?: '-';
        $officer1RegisterNumber = $off1['register_number'] ?: '-';
        $officer1PositionName = $off1['position_name'] ?: '-';

        $officer2Name = $off2['name'] ?: '-';
        $officer2RankName = $off2['rank_name'] ?: '-';
        $officer2RegisterNumber = $off2['register_number'] ?: '-';
        $officer2PositionName = $off2['position_name'] ?: '-';

        // 5. Rujukan Dasar
        $prosecutorInstitutionName = $document->prosecutor_institution_name ?: ($document->nama_kejaksaan ?: ('Kejaksaan Negeri ' . ($accident->polres->name ?? 'Pasuruan')));
        $p21DocumentNumber = $document->p21_document_number ?: ($document->nomor_p21 ?: '-');
        $p21DocumentDate = $document->p21_document_date ?: ($document->tanggal_p21 ? Carbon::parse($document->tanggal_p21)->locale('id')->translatedFormat('d F Y') : '-');
        $delivererHeadTitle = $document->deliverer_head_title ?: ('Kepala Satuan Lalu Lintas Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan'));
        $deliveryLetterNumber = $document->delivery_letter_number ?: ($document->nomor ?: '-');
        $deliveryLetterDate = $document->delivery_letter_date ?: ($document->tanggal ? Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : date('d F Y'));

        // 6. Tersangka & Berkas
        $suspect = $document->relationLoaded('suspects') && $document->suspects && $document->suspects->isNotEmpty() ? $document->suspects->first() : null;
        $suspectName = $document->suspect_name ?: ($suspect->name ?? 'Tersangka');
        $berkasNumber = $document->berkas_number ?: ($document->nomor_sprindik ?: ($accident->no_lp ?: '-'));
        $berkasDate = $document->berkas_date ?: ($document->tanggal_sprindik ? Carbon::parse($document->tanggal_sprindik)->locale('id')->translatedFormat('d F Y') : '-');
        $suspect2Line = $document->suspect2_line ?: '-';

        // 7. Penuntut Umum (Penerima)
        $prosecutorOfficerName = $document->prosecutor_officer_name ?: ($document->pihak_kedua_nama ?: '....................................');
        $prosecutorOfficerRankNip = $document->prosecutor_officer_rank_nip ?: ($document->pihak_kedua_nip ? ($document->pihak_kedua_pangkat . ' / NIP. ' . $document->pihak_kedua_nip) : '-');
        $prosecutorOfficerPositionName = $document->prosecutor_officer_position_name ?: ($document->pihak_kedua_jabatan ?: 'Jaksa Penuntut Umum');

        // 8. Perkara
        $dugaanTindakPidana = $document->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalDiduga = $document->pasal_diduga ?: '310 ayat (4)';
        $uuNomor = $document->uu_nomor ?: '22';
        $uuTahun = $document->uu_tahun ?: '2009';
        $uuTentang = $document->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan';
        $tempatKejadian = $document->tempat_kejadian ?: ($accident->tkp ?: ($accident->address_detail ?: ($accident->location ?: '-')));
        $waktuKejadian = $document->waktu_kejadian ?: (($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l, d F Y') : '') . ($accident->accident_time ? ' sekira pukul ' . Carbon::parse($accident->accident_time)->format('H.i') . ' WIB' : ''));

        // 9. Serah Terima Dilakukan Pada
        $handoverDay = $document->handover_day ?: $documentDayName;
        $handoverDate = $document->handover_date ?: ($docDate->locale('id')->translatedFormat('d F Y'));
        $handoverPlace = $document->handover_place ?: ($document->tempat ?: ('Kantor ' . $prosecutorInstitutionName));

        // 10. Saksi-saksi (Dinamis 1, 2, atau N)
        $witnessList = [];
        $payload = $document->payload;
        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }
        if (!empty($payload['witnesses']) && is_array($payload['witnesses'])) {
            $witnessList = $payload['witnesses'];
        } else {
            if (!empty($document->witness1_name)) {
                $witnessList[] = [
                    'name'          => $document->witness1_name,
                    'rank_nip'      => $document->witness1_rank_nip ?: '-',
                    'position_name' => $document->witness1_position_name ?: '-',
                    'address'       => $document->witness1_address ?: '-',
                ];
            }
            if (!empty($document->witness2_name) && $document->witness2_name !== '-' && trim($document->witness2_name) !== '') {
                $witnessList[] = [
                    'name'          => $document->witness2_name,
                    'rank_nip'      => $document->witness2_rank_nip ?: '-',
                    'position_name' => $document->witness2_position_name ?: '-',
                    'address'       => $document->witness2_address ?: '-',
                ];
            }
        }

        if (empty($witnessList)) {
            $witnessList[] = [
                'name'          => $document->witness1_name ?: '....................................',
                'rank_nip'      => $document->witness1_rank_nip ?: '-',
                'position_name' => $document->witness1_position_name ?: '-',
                'address'       => $document->witness1_address ?: '-',
            ];
        }

        $w1 = $witnessList[0] ?? ['name' => '-', 'rank_nip' => '-', 'position_name' => '-', 'address' => '-'];
        $w2 = $witnessList[1] ?? ['name' => '-', 'rank_nip' => '-', 'position_name' => '-', 'address' => '-'];

        $witness1Name = $w1['name'] ?: '....................................';
        $witness1RankNip = $w1['rank_nip'] ?: '-';
        $witness1PositionName = $w1['position_name'] ?: '-';
        $witness1Address = $w1['address'] ?: '-';

        $witness2Name = $w2['name'] ?: '....................................';
        $witness2RankNip = $w2['rank_nip'] ?: '-';
        $witness2PositionName = $w2['position_name'] ?: '-';
        $witness2Address = $w2['address'] ?: '-';

        // 11. Pelaksanaan
        $executionDescription = $document->execution_description ?: ($document->catatan ?: 'Tersangka dan barang bukti diserahkan dalam keadaan lengkap dan baik. Tersangka dalam kondisi sehat jasmani dan rohani.');

        $resultData = [
            'daerahPoliceFullName'          => $daerahPoliceFullName,
            'resorPoliceFullName'           => $resorPoliceFullName,
            'resorPoliceAddress'            => $resorPoliceAddress,
            'documentDayName'               => $documentDayName,
            'documentDayDate'               => $documentDayDate,
            'documentMonth'                 => $documentMonth,
            'documentYear'                  => $documentYear,
            'documentDateText'              => $documentDateText,
            'documentTime'                  => $documentTime,
            'documentLocation'              => $documentLocation,
            'investigatorName'              => $investigatorName ?: '....................................',
            'investigatorRankName'          => $investigatorRankName ?: '-',
            'investigatorRegisterNumber'    => $investigatorRegisterNumber ?: '-',
            'investigatorPositionName'      => $investigatorPositionName ?: 'Penyidik Pembantu',
            'investigatorRole'              => $investigatorRole,
            'officer1Name'                  => $officer1Name,
            'officer1RankName'              => $officer1RankName,
            'officer1RegisterNumber'        => $officer1RegisterNumber,
            'officer1PositionName'          => $officer1PositionName,
            'officer2Name'                  => $officer2Name,
            'officer2RankName'              => $officer2RankName,
            'officer2RegisterNumber'        => $officer2RegisterNumber,
            'officer2PositionName'          => $officer2PositionName,
            'prosecutorInstitutionName'     => $prosecutorInstitutionName,
            'p21DocumentNumber'             => $p21DocumentNumber,
            'p21DocumentDate'               => $p21DocumentDate,
            'suspectName'                   => $suspectName,
            'delivererHeadTitle'            => $delivererHeadTitle,
            'deliveryLetterNumber'          => $deliveryLetterNumber,
            'deliveryLetterDate'            => $deliveryLetterDate,
            'berkasNumber'                  => $berkasNumber,
            'berkasDate'                    => $berkasDate,
            'suspect2Line'                  => $suspect2Line,
            'prosecutorOfficerName'         => $prosecutorOfficerName,
            'prosecutorOfficerRankNip'      => $prosecutorOfficerRankNip,
            'prosecutorOfficerPositionName' => $prosecutorOfficerPositionName,
            'dugaanTindakPidana'            => $dugaanTindakPidana,
            'pasalDiduga'                   => $pasalDiduga,
            'uuNomor'                       => $uuNomor,
            'uuTahun'                       => $uuTahun,
            'uuTentang'                     => $uuTentang,
            'tempatKejadian'                => $tempatKejadian,
            'waktuKejadian'                 => $waktuKejadian,
            'handoverDay'                   => $handoverDay,
            'handoverDate'                  => $handoverDate,
            'handoverPlace'                 => $handoverPlace,
            'accompanyingOfficerList'       => $accompanyingOfficerList,
            'witnessList'                   => $witnessList,
            'witness1Name'                  => $witness1Name,
            'witness1RankNip'               => $witness1RankNip,
            'witness1PositionName'          => $witness1PositionName,
            'witness1Address'               => $witness1Address,
            'witness2Name'                  => $witness2Name,
            'witness2RankNip'               => $witness2RankNip,
            'witness2PositionName'          => $witness2PositionName,
            'witness2Address'               => $witness2Address,
            'executionDescription'          => $executionDescription,
        ];

        // Sediakan variabel dinamis officer1Name, officer2Name, officer3Name, dst.
        foreach ($accompanyingOfficerList as $idx => $off) {
            $k = $idx + 1;
            $resultData["officer{$k}Name"] = $off['name'] ?? '-';
            $resultData["officer{$k}RankName"] = $off['rank_name'] ?? '-';
            $resultData["officer{$k}RegisterNumber"] = $off['register_number'] ?? '-';
            $resultData["officer{$k}PositionName"] = $off['position_name'] ?? '-';
        }

        // Sediakan variabel dinamis witness1Name, witness2Name, witness3Name, dst.
        foreach ($witnessList as $idx => $w) {
            $k = $idx + 1;
            $resultData["witness{$k}Name"] = $w['name'] ?? '-';
            $resultData["witness{$k}RankNip"] = $w['rank_nip'] ?? '-';
            $resultData["witness{$k}PositionName"] = $w['position_name'] ?? '-';
            $resultData["witness{$k}Address"] = $w['address'] ?? '-';
        }

        return $resultData;
    }

    /**
     * Generate file Word dari template word-template/berita_acara_serah_terima_tersangka.docx
     */
    private function generatePopulatedWordFile($id): array
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects',
            'delivererOfficer',
            'signatory',
            'officers',
        ])->where('id', $id)->first();

        if (!$document) {
            $document = BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument::where('id', $id)->firstOrFail();
        }

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($accidentId ?? $document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        $templatePath = public_path('word-template/berita_acara_serah_terima_tersangka.docx');
        if (!file_exists($templatePath)) {
            $templatePath = public_path('word-template/berita_acara_Serah_Terima_Tersangka.docx');
        }
        if (!file_exists($templatePath)) {
            $templatePath = base_path('public/word-template/berita_acara_serah_terima_tersangka.docx');
        }

        $witnessCount = count($data['witnessList'] ?? []);
        if ($witnessCount === 0) {
            $witnessCount = 1;
        }

        $accompanyingCount = count($data['accompanyingOfficerList'] ?? []);
        if ($accompanyingCount === 0) {
            $accompanyingCount = 1;
        }

        // Kloning file template ke temporary path untuk dimodifikasi sesuai jumlah saksi & pendamping
        $tempTemplate = sys_get_temp_dir() . '/ba_template_' . uniqid() . '.docx';
        copy($templatePath, $tempTemplate);

        // Sesuaikan baris tabel template Word (pendamping & saksi)
        $this->adjustWordTemplate($tempTemplate, $witnessCount, $accompanyingCount);

        $templateProcessor = new TemplateProcessor($tempTemplate);

        // Set all variables
        $allVars = $templateProcessor->getVariables();
        foreach ($allVars as $var) {
            if (isset($data[$var])) {
                $templateProcessor->setValue($var, $data[$var]);
            }
        }

        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0777, true);
        }

        $cleanNomor = preg_replace('/[^A-Za-z0-9_\-]/', '_', $document->nomor ?: ($document->document_number ?: 'DRAFT'));
        $filename = public_path('generate/' . $document->id . ' - Berita Acara Serah Terima Tersangka - ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres')) . '.docx');
        $templateProcessor->saveAs($filename);

        if (file_exists($tempTemplate)) {
            @unlink($tempTemplate);
        }

        return [
            'document'    => $document,
            'accident'    => $accident,
            'filename'    => $filename,
            'clean_nomor' => $cleanNomor,
        ];
    }

    /**
     * Menyesuaikan baris tabel di template Word berdasarkan jumlah saksi dan pendamping
     */
    private function adjustWordTemplate(string $docxPath, int $witnessCount, int $accompanyingCount): void
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml) {
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadXML($xml);
                libxml_clear_errors();

                $tables = $dom->getElementsByTagName('tbl');
                if ($tables->length >= 5) {
                    // Table 0: Anggota Pendamping (Bersama-sama dengan)
                    $tbl0 = $tables->item(0);
                    $rows0 = [];
                    foreach ($tbl0->childNodes as $node) {
                        if ($node->nodeName === 'w:tr') {
                            $rows0[] = $node;
                        }
                    }

                    if ($accompanyingCount <= 1) {
                        // Hapus baris 3, 4, 5 (Petugas Pendamping 2)
                        for ($i = 3; $i < 6; $i++) {
                            if (isset($rows0[$i])) {
                                $tbl0->removeChild($rows0[$i]);
                            }
                        }
                    } elseif ($accompanyingCount > 2) {
                        // Kloning baris 3, 4, 5 untuk pendamping ke-3 dst
                        for ($k = 3; $k <= $accompanyingCount; $k++) {
                            for ($r = 3; $r < 6; $r++) {
                                if (isset($rows0[$r])) {
                                    $newRow = $rows0[$r]->cloneNode(true);
                                    foreach ($newRow->getElementsByTagName('t') as $tNode) {
                                        if ($tNode->nodeValue) {
                                            $tNode->nodeValue = str_replace('officer2', 'officer' . $k, $tNode->nodeValue);
                                            if ($r === 3 && str_contains($tNode->nodeValue, '2.')) {
                                                $tNode->nodeValue = str_replace('2.', $k . '.', $tNode->nodeValue);
                                            }
                                        }
                                    }
                                    $tbl0->appendChild($newRow);
                                }
                            }
                        }
                    }

                    // Table 2: Rincian Saksi, Table 4: Tanda Tangan Saksi
                    $tbl2 = $tables->item(2);
                    $tbl4 = $tables->item(4);

                    $rows2 = [];
                    foreach ($tbl2->childNodes as $node) {
                        if ($node->nodeName === 'w:tr') {
                            $rows2[] = $node;
                        }
                    }

                    $rows4 = [];
                    foreach ($tbl4->childNodes as $node) {
                        if ($node->nodeName === 'w:tr') {
                            $rows4[] = $node;
                        }
                    }

                    if ($witnessCount <= 1) {
                        // Hapus baris 4, 5, 6, 7 pada Tabel 2 (data Saksi 2)
                        for ($i = 4; $i < 8; $i++) {
                            if (isset($rows2[$i])) {
                                $tbl2->removeChild($rows2[$i]);
                            }
                        }
                        // Hapus baris 1 pada Tabel 4 (tanda tangan Saksi 2)
                        if (isset($rows4[1])) {
                            $tbl4->removeChild($rows4[1]);
                        }
                    } elseif ($witnessCount > 2) {
                        // Kloning baris 4-7 untuk saksi ke-3 dst pada Tabel 2
                        for ($k = 3; $k <= $witnessCount; $k++) {
                            for ($r = 4; $r < 8; $r++) {
                                if (isset($rows2[$r])) {
                                    $newRow = $rows2[$r]->cloneNode(true);
                                    foreach ($newRow->getElementsByTagName('t') as $tNode) {
                                        if ($tNode->nodeValue) {
                                            $tNode->nodeValue = str_replace('witness2', 'witness' . $k, $tNode->nodeValue);
                                            if ($r === 4 && str_contains($tNode->nodeValue, '2.')) {
                                                $tNode->nodeValue = str_replace('2.', $k . '.', $tNode->nodeValue);
                                            }
                                        }
                                    }
                                    $tbl2->appendChild($newRow);
                                }
                            }
                        }

                        // Kloning baris 1 pada Tabel 4 untuk tanda tangan saksi ke-3 dst
                        for ($k = 3; $k <= $witnessCount; $k++) {
                            if (isset($rows4[1])) {
                                $newRow = $rows4[1]->cloneNode(true);
                                foreach ($newRow->getElementsByTagName('t') as $tNode) {
                                    if ($tNode->nodeValue) {
                                        $tNode->nodeValue = str_replace('witness2', 'witness' . $k, $tNode->nodeValue);
                                        if (str_contains($tNode->nodeValue, '2.')) {
                                            $tNode->nodeValue = str_replace('2.', $k . '.', $tNode->nodeValue);
                                        }
                                    }
                                }
                                $tbl4->appendChild($newRow);
                            }
                        }
                    }

                    $zip->addFromString('word/document.xml', $dom->saveXML());
                }
            }
            $zip->close();
        }
    }

    /**
     * Ekstrak daftar anggota pendamping dari request (mendukung dinamis 1, 2, 3.. dst)
     */
    private function extractAccompanyingOfficerList(Request $request): array
    {
        $officerList = [];

        if ($request->has('accompanying_officers') && is_array($request->accompanying_officers)) {
            foreach ($request->accompanying_officers as $off) {
                $name = trim($off['name'] ?? '');
                $rankName = trim($off['rank_name'] ?? '');
                $nrp = trim($off['register_number'] ?? '');
                $position = trim($off['position_name'] ?? '');

                if ($name !== '' || $rankName !== '' || $nrp !== '' || $position !== '') {
                    $officerList[] = [
                        'name'            => $name !== '' ? $name : '-',
                        'rank_name'       => $rankName !== '' ? $rankName : '-',
                        'register_number' => $nrp !== '' ? $nrp : '-',
                        'position_name'   => $position !== '' ? $position : '-',
                    ];
                }
            }
        }

        // Fallback jika tidak menggunakan input array accompanying_officers
        if (empty($officerList)) {
            if ($request->filled('officer1_name')) {
                $officerList[] = [
                    'name'            => $request->officer1_name ?: '-',
                    'rank_name'       => $request->officer1_rank_name ?: '-',
                    'register_number' => $request->officer1_register_number ?: '-',
                    'position_name'   => $request->officer1_position_name ?: '-',
                ];
            }
            if ($request->filled('officer2_name') && $request->officer2_name !== '-' && trim($request->officer2_name) !== '') {
                $officerList[] = [
                    'name'            => $request->officer2_name,
                    'rank_name'       => $request->officer2_rank_name ?: '-',
                    'register_number' => $request->officer2_register_number ?: '-',
                    'position_name'   => $request->officer2_position_name ?: '-',
                ];
            }
        }

        // Minimal selalu ada 1 data pendamping
        if (empty($officerList)) {
            $officerList[] = [
                'name'            => '-',
                'rank_name'       => '-',
                'register_number' => '-',
                'position_name'   => '-',
            ];
        }

        return $officerList;
    }

    /**
     * Ekstrak daftar saksi dari request (mendukung dinamis 1, 2, 3.. dst)
     */
    private function extractWitnessList(Request $request): array
    {
        $witnessList = [];

        if ($request->has('witnesses') && is_array($request->witnesses)) {
            foreach ($request->witnesses as $w) {
                $name = trim($w['name'] ?? '');
                $rankNip = trim($w['rank_nip'] ?? '');
                $position = trim($w['position_name'] ?? '');
                $address = trim($w['address'] ?? '');

                // Abaikan jika semua field kosong
                if ($name !== '' || $rankNip !== '' || $position !== '' || $address !== '') {
                    $witnessList[] = [
                        'name'          => $name !== '' ? $name : '-',
                        'rank_nip'      => $rankNip !== '' ? $rankNip : '-',
                        'position_name' => $position !== '' ? $position : '-',
                        'address'       => $address !== '' ? $address : '-',
                    ];
                }
            }
        }

        // Fallback jika tidak menggunakan input array witnesses
        if (empty($witnessList)) {
            if ($request->filled('witness1_name')) {
                $witnessList[] = [
                    'name'          => $request->witness1_name ?: '-',
                    'rank_nip'      => $request->witness1_rank_nip ?: '-',
                    'position_name' => $request->witness1_position_name ?: '-',
                    'address'       => $request->witness1_address ?: '-',
                ];
            }
            if ($request->filled('witness2_name') && $request->witness2_name !== '-' && trim($request->witness2_name) !== '') {
                $witnessList[] = [
                    'name'          => $request->witness2_name,
                    'rank_nip'      => $request->witness2_rank_nip ?: '-',
                    'position_name' => $request->witness2_position_name ?: '-',
                    'address'       => $request->witness2_address ?: '-',
                ];
            }
        }

        // Minimal selalu ada 1 data saksi
        if (empty($witnessList)) {
            $witnessList[] = [
                'name'          => '-',
                'rank_nip'      => '-',
                'position_name' => '-',
                'address'       => '-',
            ];
        }

        return $witnessList;
    }

    /**
     * Helper Terbilang Bilangan
     */
    private function terbilang($angka): string
    {
        $angka = (int)$angka;
        $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return $this->terbilang((int)($angka / 10)) . ' puluh' . ($angka % 10 ? ' ' . $this->terbilang($angka % 10) : '');
        } elseif ($angka < 200) {
            return 'seratus' . ($angka - 100 ? ' ' . $this->terbilang($angka - 100) : '');
        } elseif ($angka < 1000) {
            return $this->terbilang((int)($angka / 100)) . ' ratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        } elseif ($angka < 2000) {
            return 'seribu' . ($angka - 1000 ? ' ' . $this->terbilang($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            return $this->terbilang((int)($angka / 1000)) . ' ribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        }
        return (string)$angka;
    }

    /**
     * Helper Terbilang Tanggal (contoh: lima bulan November tahun dua ribu dua puluh enam)
     */
    private function terbilangTanggal($day, $monthName, $year): string
    {
        $tglStr = trim($this->terbilang((int)$day));
        $tahunStr = trim($this->terbilang((int)$year));
        return $tglStr . ' bulan ' . $monthName . ' tahun ' . $tahunStr;
    }

    private function getIndoDay($date)
    {
        $days = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];
        $day = date('l', strtotime($date));
        return $days[$day] ?? $day;
    }
}
