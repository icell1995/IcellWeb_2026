<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPenunjukanPenasihatHukumDocument\SuratPenunjukanPenasihatHukumDocument;

use App\Traits\DocsOfficersTraits;

class SuratPenunjukanPenasihatHukumDocumentController extends Controller
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
     * Form pembuatan Surat Penunjukan Penasihat Hukum
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // 1. Dokumen Surat Perintah Penyidikan (Sprindik)
        $sprindikDocument = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // 2. Tersangka
        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accidentId)->get();
        $firstSuspect = $suspects->first();

        // 3. Petugas & Penandatangan
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

        // Nilai Default
        $defaultDaerahPolice = strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR');
        $defaultResorPolice = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN'));
        $defaultResorPoliceAddress = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));
        $defaultKota = $accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan');

        $defaultDocDate = Carbon::now()->locale('id')->translatedFormat('d F Y');
        $defaultDocNumber = 'B/' . ($sprindikDocument->nomor ?? ($accident->no_lp ?: '01')) . '/XI/RES.1.24/' . date('Y') . '/Satlantas';
        $defaultTargetLegalAid = 'Ketua Pos Bantuan Hukum (Posbakum) Pengadilan Negeri ' . ($accident->polres->name ?? 'Pasuruan');

        $defaultDasarKuhap = 'Pasal 54, Pasal 56 ayat (1) Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana';
        $defaultUuLain = 'Undang-Undang Nomor 16 Tahun 2011 tentang Bantuan Hukum';

        $defaultNoLp = $accident->no_lp ?: '-';
        $defaultTglLp = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : date('d F Y');

        $defaultNoSprindik = $sprindikDocument->nomor ?? ($sprindikDocument->document_number ?? '-');
        $defaultTglSprindik = $sprindikDocument && $sprindikDocument->tanggal ? Carbon::parse($sprindikDocument->tanggal)->locale('id')->translatedFormat('d F Y') : date('d F Y');

        // Tindak Pidana Default
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

        // Jadwal Riksa
        $nextDay = Carbon::now()->addDays(2);
        $defaultExamDay = $nextDay->locale('id')->translatedFormat('l');
        $defaultExamDate = $nextDay->locale('id')->translatedFormat('d F Y');
        $defaultExamOffice = 'Ruang Riksa Unit Gakkum Satlantas Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan');
        $defaultExamAddress = $defaultResorPoliceAddress;

        return view('docs.surat-penunjukan-penasihat-hukum-document.create', [
            'accident'                  => $accident,
            'accidentId'                => $accidentId,
            'sprindikDocument'          => $sprindikDocument,
            'suspects'                  => $suspects,
            'firstSuspect'              => $firstSuspect,
            'officers'                  => $officers,
            'authorizedSignatories'     => $authorizedSignatories,
            'defaultDaerahPolice'       => $defaultDaerahPolice,
            'defaultResorPolice'        => $defaultResorPolice,
            'defaultResorPoliceAddress' => $defaultResorPoliceAddress,
            'defaultKota'               => $defaultKota,
            'defaultDocDate'            => $defaultDocDate,
            'defaultDocNumber'          => $defaultDocNumber,
            'defaultTargetLegalAid'     => $defaultTargetLegalAid,
            'defaultDasarKuhap'         => $defaultDasarKuhap,
            'defaultUuLain'             => $defaultUuLain,
            'defaultNoLp'               => $defaultNoLp,
            'defaultTglLp'              => $defaultTglLp,
            'defaultNoSprindik'         => $defaultNoSprindik,
            'defaultTglSprindik'        => $defaultTglSprindik,
            'defaultTindakPidana'       => $defaultTindakPidana,
            'defaultPasal'              => $defaultPasal,
            'defaultUuNomor'            => $defaultUuNomor,
            'defaultUuTahun'            => $defaultUuTahun,
            'defaultUuTentang'          => $defaultUuTentang,
            'defaultTkp'                => $defaultTkp,
            'defaultWaktuKejadian'      => $defaultWaktuKejadian,
            'defaultExamDay'            => $defaultExamDay,
            'defaultExamDate'           => $defaultExamDate,
            'defaultExamOffice'         => $defaultExamOffice,
            'defaultExamAddress'        => $defaultExamAddress,
        ]);
    }

    /**
     * Simpan Surat Penunjukan Penasihat Hukum
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'document_number'        => 'required|string|max:255',
            'target_legal_aid_name'  => 'required|string|max:255',
            'suspect_name'           => 'required|string|max:255',
        ], [
            'document_number.required'       => 'Nomor Surat wajib diisi.',
            'target_legal_aid_name.required' => 'Nama Lembaga Bantuan Hukum / Posbakum tujuan wajib diisi.',
            'suspect_name.required'          => 'Nama Tersangka wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $docId = (string) Uuid::generate();

            // Signatory Data
            $signatoryName = $request->signatory_name;
            $signatoryRank = $request->signatory_rank_name;
            $signatoryNrp = $request->signatory_register_number;
            $signatoryPosition = $request->signatory_position_name ?: 'KASAT LANTAS';

            if ($request->filled('signatory_id')) {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory_id);
                if ($signatory) {
                    $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
                    $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
                    $signatoryNrp = $signatory->register_number ?? '-';
                    $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');
                }
            }

            // Contact Officer Data
            $contactName = $request->contact_officer_name;
            $contactPhone = $request->contact_officer_phone;
            $contactRole = $request->contact_officer_role ?: 'Penyidik Pembantu';
            if ($request->filled('contact_officer_id')) {
                $cOfficer = Officer::with(['rank', 'position'])->find($request->contact_officer_id);
                if ($cOfficer) {
                    $contactName = PeopleNameHelper::getFullName($cOfficer->first_title, $cOfficer->first_name, $cOfficer->last_name, $cOfficer->last_title);
                    if (empty($contactPhone) && !empty($cOfficer->phone_number)) {
                        $contactPhone = $cOfficer->phone_number;
                    }
                }
            }

            // Kop Polda & Polres otomatis dari relasi no LP / perkara
            $daerahPoliceFullName = $request->daerah_police_full_name ?: strtoupper($accident->polres->polda->name ?? 'SUMATERA UTARA');
            $resorPoliceFullName  = $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN')));
            $resorPoliceAddress   = $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

            SuratPenunjukanPenasihatHukumDocument::create([
                'id'                                        => $docId,
                'accident_id'                               => $accidentId,
                'status_id'                                 => '2',
                'document_category_id'                      => '0704',

                'daerah_police_full_name'                   => $daerahPoliceFullName,
                'resor_police_full_name'                    => $resorPoliceFullName,
                'resor_police_address'                      => $resorPoliceAddress,

                'document_number'                           => $request->document_number,
                'document_location'                         => $request->document_location,
                'document_date'                             => $request->document_date,
                'document_classification_name'              => $request->document_classification_name ?: 'Biasa',
                'appendix'                                  => $request->appendix ?: '-',
                'target_legal_aid_name'                     => $request->target_legal_aid_name,
                'target_legal_aid_location'                 => $request->target_legal_aid_location,

                'dasar_kuhap'                               => $request->dasar_kuhap,
                'undang_undang_lain'                        => $request->undang_undang_lain,
                'accident_number'                           => $request->accident_number,
                'accident_date'                             => $request->accident_date,
                'surat_perintah_penyidikan_document_number' => $request->surat_perintah_penyidikan_document_number,
                'surat_perintah_penyidikan_document_date'   => $request->surat_perintah_penyidikan_document_date,
                'surat_keterangan_tidak_mampu_instansi'     => $request->surat_keterangan_tidak_mampu_instansi ?: '-',
                'surat_keterangan_tidak_mampu_number'       => $request->surat_keterangan_tidak_mampu_number ?: '-',
                'surat_keterangan_tidak_mampu_date'         => $request->surat_keterangan_tidak_mampu_date ?: '-',
                'surat_keterangan_tidak_mampu_person_name'  => $request->surat_keterangan_tidak_mampu_person_name ?: ($request->suspect_name ?: '-'),

                'suspect_id'                                => !empty($request->suspect_id) ? $request->suspect_id : null,
                'suspect_name'                              => $request->suspect_name,
                'suspect_identity_number'                   => $request->suspect_identity_number ?: '-',
                'suspect_nationality'                       => $request->suspect_nationality ?: 'Indonesia',
                'suspect_gender_name'                       => $request->suspect_gender_name ?: '-',
                'suspect_birth_place'                       => $request->suspect_birth_place ?: '-',
                'suspect_birth_date'                        => $request->suspect_birth_date ?: '-',
                'suspect_job_name'                          => $request->suspect_job_name ?: '-',
                'suspect_religion_name'                     => $request->suspect_religion_name ?: '-',
                'suspect_full_address'                      => $request->suspect_full_address ?: '-',

                'dugaan_tindak_pidana'                      => $request->dugaan_tindak_pidana,
                'pasal_diduga'                              => $request->pasal_diduga,
                'uu_nomor'                                  => $request->uu_nomor ?: '22',
                'uu_tahun'                                  => $request->uu_tahun ?: '2009',
                'uu_tentang'                                => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'                           => $request->tempat_kejadian,
                'waktu_kejadian'                            => $request->waktu_kejadian,
                'kurun_waktu'                               => $request->kurun_waktu ?: '-',

                'examination_day'                           => $request->examination_day,
                'examination_date'                          => $request->examination_date,
                'examination_time'                          => $request->examination_time,
                'examination_office_name'                   => $request->examination_office_name,
                'examination_office_address'                => $request->examination_office_address,
                'contact_officer_id'                        => !empty($request->contact_officer_id) ? $request->contact_officer_id : null,
                'contact_officer_role'                      => $contactRole,
                'contact_officer_name'                      => $contactName ?: '-',
                'contact_officer_phone'                     => $contactPhone ?: '-',

                'carbon_copy_1'                             => $request->carbon_copy_1 ?: '-',
                'carbon_copy_2'                             => $request->carbon_copy_2 ?: '-',
                'carbon_copy_3'                             => $request->carbon_copy_3 ?: '-',

                'signatory_id'                              => !empty($request->signatory_id) ? $request->signatory_id : null,
                'signatory_head_text'                       => $request->signatory_head_text ?: ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN')),
                'signatory_position_name'                   => $signatoryPosition,
                'signatory_name'                            => $signatoryName ?: '-',
                'signatory_rank_name'                       => $signatoryRank ?: '-',
                'signatory_register_number'                 => $signatoryNrp ?: '-',

                'created_by_user_id'                        => Auth::id(),
                'updated_by_user_id'                        => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Penunjukan Penasihat Hukum berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Form Edit Surat Penunjukan Penasihat Hukum
     */
    public function edit($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPenunjukanPenasihatHukumDocument::with(['accident', 'suspect', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);

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

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.surat-penunjukan-penasihat-hukum-document.edit', array_merge([
            'document'              => $document,
            'accident'              => $accident,
            'accidentId'            => $accidentId,
            'suspects'              => $suspects,
            'officers'              => $officers,
            'authorizedSignatories' => $authorizedSignatories,
        ], $data));
    }

    /**
     * Update Surat Penunjukan Penasihat Hukum
     */
    public function update(Request $request, $id)
    {
        $document = SuratPenunjukanPenasihatHukumDocument::findOrFail($id);
        $accidentId = $document->accident_id ?? $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'document_number'        => 'required|string|max:255',
            'target_legal_aid_name'  => 'required|string|max:255',
            'suspect_name'           => 'required|string|max:255',
        ], [
            'document_number.required'       => 'Nomor Surat wajib diisi.',
            'target_legal_aid_name.required' => 'Nama Lembaga Bantuan Hukum / Posbakum tujuan wajib diisi.',
            'suspect_name.required'          => 'Nama Tersangka wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Signatory Data
            $signatoryName = $request->signatory_name ?: $document->signatory_name;
            $signatoryRank = $request->signatory_rank_name ?: $document->signatory_rank_name;
            $signatoryNrp = $request->signatory_register_number ?: $document->signatory_register_number;
            $signatoryPosition = $request->signatory_position_name ?: ($document->signatory_position_name ?: 'KASAT LANTAS');

            if ($request->filled('signatory_id')) {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory_id);
                if ($signatory) {
                    $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
                    $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
                    $signatoryNrp = $signatory->register_number ?? '-';
                    $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');
                }
            }

            // Contact Officer Data
            $contactName = $request->contact_officer_name ?: $document->contact_officer_name;
            $contactPhone = $request->contact_officer_phone ?: $document->contact_officer_phone;
            $contactRole = $request->contact_officer_role ?: ($document->contact_officer_role ?: 'Penyidik Pembantu');
            if ($request->filled('contact_officer_id')) {
                $cOfficer = Officer::with(['rank', 'position'])->find($request->contact_officer_id);
                if ($cOfficer) {
                    $contactName = PeopleNameHelper::getFullName($cOfficer->first_title, $cOfficer->first_name, $cOfficer->last_name, $cOfficer->last_title);
                    if (empty($contactPhone) && !empty($cOfficer->phone_number)) {
                        $contactPhone = $cOfficer->phone_number;
                    }
                }
            }

            // Kop Polda & Polres otomatis dari relasi no LP / perkara
            $daerahPoliceFullName = $request->daerah_police_full_name ?: ($document->daerah_police_full_name ?: strtoupper($accident->polres->polda->name ?? 'SUMATERA UTARA'));
            $resorPoliceFullName  = $request->resor_police_full_name ?: ($document->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))));
            $resorPoliceAddress   = $request->resor_police_address ?: ($document->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')));

            $document->update([
                'daerah_police_full_name'                   => $daerahPoliceFullName,
                'resor_police_full_name'                    => $resorPoliceFullName,
                'resor_police_address'                      => $resorPoliceAddress,

                'document_number'                           => $request->document_number,
                'document_location'                         => $request->document_location,
                'document_date'                             => $request->document_date,
                'document_classification_name'              => $request->document_classification_name ?: 'Biasa',
                'appendix'                                  => $request->appendix ?: '-',
                'target_legal_aid_name'                     => $request->target_legal_aid_name,
                'target_legal_aid_location'                 => $request->target_legal_aid_location,

                'dasar_kuhap'                               => $request->dasar_kuhap,
                'undang_undang_lain'                        => $request->undang_undang_lain,
                'accident_number'                           => $request->accident_number,
                'accident_date'                             => $request->accident_date,
                'surat_perintah_penyidikan_document_number' => $request->surat_perintah_penyidikan_document_number,
                'surat_perintah_penyidikan_document_date'   => $request->surat_perintah_penyidikan_document_date,
                'surat_keterangan_tidak_mampu_instansi'     => $request->surat_keterangan_tidak_mampu_instansi ?: '-',
                'surat_keterangan_tidak_mampu_number'       => $request->surat_keterangan_tidak_mampu_number ?: '-',
                'surat_keterangan_tidak_mampu_date'         => $request->surat_keterangan_tidak_mampu_date ?: '-',
                'surat_keterangan_tidak_mampu_person_name'  => $request->surat_keterangan_tidak_mampu_person_name ?: ($request->suspect_name ?: '-'),

                'suspect_id'                                => !empty($request->suspect_id) ? $request->suspect_id : null,
                'suspect_name'                              => $request->suspect_name,
                'suspect_identity_number'                   => $request->suspect_identity_number ?: '-',
                'suspect_nationality'                       => $request->suspect_nationality ?: 'Indonesia',
                'suspect_gender_name'                       => $request->suspect_gender_name ?: '-',
                'suspect_birth_place'                       => $request->suspect_birth_place ?: '-',
                'suspect_birth_date'                        => $request->suspect_birth_date ?: '-',
                'suspect_job_name'                          => $request->suspect_job_name ?: '-',
                'suspect_religion_name'                     => $request->suspect_religion_name ?: '-',
                'suspect_full_address'                      => $request->suspect_full_address ?: '-',

                'dugaan_tindak_pidana'                      => $request->dugaan_tindak_pidana,
                'pasal_diduga'                              => $request->pasal_diduga,
                'uu_nomor'                                  => $request->uu_nomor ?: '22',
                'uu_tahun'                                  => $request->uu_tahun ?: '2009',
                'uu_tentang'                                => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'                           => $request->tempat_kejadian,
                'waktu_kejadian'                            => $request->waktu_kejadian,
                'kurun_waktu'                               => $request->kurun_waktu ?: '-',

                'examination_day'                           => $request->examination_day,
                'examination_date'                          => $request->examination_date,
                'examination_time'                          => $request->examination_time,
                'examination_office_name'                   => $request->examination_office_name,
                'examination_office_address'                => $request->examination_office_address,
                'contact_officer_id'                        => !empty($request->contact_officer_id) ? $request->contact_officer_id : null,
                'contact_officer_role'                      => $contactRole,
                'contact_officer_name'                      => $contactName ?: '-',
                'contact_officer_phone'                     => $contactPhone ?: '-',

                'carbon_copy_1'                             => $request->carbon_copy_1 ?: '-',
                'carbon_copy_2'                             => $request->carbon_copy_2 ?: '-',
                'carbon_copy_3'                             => $request->carbon_copy_3 ?: '-',

                'signatory_id'                              => !empty($request->signatory_id) ? $request->signatory_id : null,
                'signatory_head_text'                       => $request->signatory_head_text ?: $document->signatory_head_text,
                'signatory_position_name'                   => $signatoryPosition,
                'signatory_name'                            => $signatoryName ?: '-',
                'signatory_rank_name'                       => $signatoryRank ?: '-',
                'signatory_register_number'                 => $signatoryNrp ?: '-',

                'updated_by_user_id'                        => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Penunjukan Penasihat Hukum berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Preview Tampilan Dokumen
     */
    public function show($id)
    {
        $document = SuratPenunjukanPenasihatHukumDocument::with(['accident', 'suspect', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.surat-penunjukan-penasihat-hukum-document.show', array_merge([
            'document' => $document,
            'accident' => $accident,
        ], $data));
    }

    /**
     * Hapus Dokumen
     */
    public function delete($id)
    {
        $document = SuratPenunjukanPenasihatHukumDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Penunjukan Penasihat Hukum berhasil dihapus.');
    }

    /**
     * Unduh File Word (.docx)
     */
    public function download($id)
    {
        $result = $this->generatePopulatedWordFile($id);
        return response()->download($result['filename'])->deleteFileAfterSend(true);
    }

    /**
     * Mempersiapkan ke-53 data variabel untuk template Word & HTML View
     */
    public function prepareDocumentData($document, $accident): array
    {
        $daerahPoliceFullName = strtoupper($document->daerah_police_full_name ?: ($accident->polres->polda->name ?? 'SUMATERA UTARA'));
        $resorPoliceFullName = $document->resor_police_full_name ?: ((in_array($accident->polres_id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN')));
        $resorPoliceAddress = $document->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        $documentLocation = $document->document_location ?: ($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan'));
        $documentDate = $document->document_date ?: Carbon::now()->locale('id')->translatedFormat('d F Y');
        $documentNumber = $document->document_number ?: '-';
        $documentClassificationName = $document->document_classification_name ?: 'Biasa';
        $appendix = $document->appendix ?: '-';
        $targetLegalAidName = $document->target_legal_aid_name ?: ('Ketua Pos Bantuan Hukum (Posbakum) Pengadilan Negeri ' . ($accident->polres->name ?? 'Pasuruan'));
        $targetLegalAidLocation = $document->target_legal_aid_location ?: $documentLocation;

        $dasarKuhap = $document->dasar_kuhap ?: 'Pasal 54, Pasal 56 ayat (1) Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana';
        $undangUndangLain = $document->undang_undang_lain ?: 'Undang-Undang Nomor 16 Tahun 2011 tentang Bantuan Hukum';

        $accidentNumber = $document->accident_number ?: ($accident->no_lp ?: '-');
        $accidentDate = $document->accident_date ?: ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-');

        $suratPerintahPenyidikanDocumentNumber = $document->surat_perintah_penyidikan_document_number ?: '-';
        $suratPerintahPenyidikanDocumentDate = $document->surat_perintah_penyidikan_document_date ?: '-';

        $suratKeteranganTidakMampuInstansi = $document->surat_keterangan_tidak_mampu_instansi ?: '-';
        $suratKeteranganTidakMampuNumber = $document->surat_keterangan_tidak_mampu_number ?: '-';
        $suratKeteranganTidakMampuDate = $document->surat_keterangan_tidak_mampu_date ?: '-';
        $suratKeteranganTidakMampuPersonName = $document->surat_keterangan_tidak_mampu_person_name ?: ($document->suspect_name ?: '-');

        $suspectName = $document->suspect_name ?: '-';
        $suspectIdentityNumber = $document->suspect_identity_number ?: '-';
        $suspectNationality = $document->suspect_nationality ?: 'Indonesia';
        $suspectGenderName = $document->suspect_gender_name ?: '-';
        $suspectBirthPlace = $document->suspect_birth_place ?: '-';
        $suspectBirthDate = $document->suspect_birth_date ?: '-';
        $suspectJobName = $document->suspect_job_name ?: '-';
        $suspectReligionName = $document->suspect_religion_name ?: '-';
        $suspectFullAddress = $document->suspect_full_address ?: '-';

        $dugaanTindakPidana = $document->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalDiduga = $document->pasal_diduga ?: '310 ayat (4)';
        $uuNomor = $document->uu_nomor ?: '22';
        $uuTahun = $document->uu_tahun ?: '2009';
        $uuTentang = $document->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan';
        $tempatKejadian = $document->tempat_kejadian ?: ($accident->address_detail ?: ($accident->location ?: '-'));
        $waktuKejadian = $document->waktu_kejadian ?: '-';
        $kurunWaktu = $document->kurun_waktu ?: '-';

        $examinationDay = $document->examination_day ?: 'Senin';
        $examinationDate = $document->examination_date ?: Carbon::now()->addDays(2)->locale('id')->translatedFormat('d F Y');
        $examinationTime = $document->examination_time ?: '09.00';
        $examinationOfficeName = $document->examination_office_name ?: ('Ruang Riksa Unit Gakkum Satlantas Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan'));
        $examinationOfficeAddress = $document->examination_office_address ?: $resorPoliceAddress;
        $contactOfficerRole = $document->contact_officer_role ?: 'Penyidik Pembantu';
        $contactOfficerName = $document->contact_officer_name ?: '-';
        $contactOfficerPhone = $document->contact_officer_phone ?: '-';

        $carbonCopy1 = $document->carbon_copy_1 ?: ('Kepala Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan'));
        $carbonCopy2 = $document->carbon_copy_2 ?: ('Pengawas Penyidikan (Wasidik) Polres ' . ($accident->polres->name ?? 'Pasuruan'));
        $carbonCopy3 = $document->carbon_copy_3 ?: 'Arsip';

        $signatoryHeadText = $document->signatory_head_text ?: ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN'));
        $signatoryPositionName = $document->signatory_position_name ?: 'KASAT LANTAS';
        $signatoryName = $document->signatory_name ?: '....................................';
        $signatoryRankName = $document->signatory_rank_name ?: '-';
        $signatoryRegisterNumber = $document->signatory_register_number ?: '-';

        return [
            'daerahPoliceFullName'                  => $daerahPoliceFullName,
            'resorPoliceFullName'                   => $resorPoliceFullName,
            'resorPoliceAddress'                    => $resorPoliceAddress,
            'documentLocation'                      => $documentLocation,
            'documentDate'                          => $documentDate,
            'documentNumber'                        => $documentNumber,
            'documentClassificationName'             => $documentClassificationName,
            'appendix'                              => $appendix,
            'targetLegalAidName'                    => $targetLegalAidName,
            'targetLegalAidLocation'                => $targetLegalAidLocation,
            'dasarKuhap'                            => $dasarKuhap,
            'undangUndangLain'                      => $undangUndangLain,
            'accidentNumber'                        => $accidentNumber,
            'accidentDate'                          => $accidentDate,
            'suratPerintahPenyidikanDocumentNumber' => $suratPerintahPenyidikanDocumentNumber,
            'suratPerintahPenyidikanDocumentDate'   => $suratPerintahPenyidikanDocumentDate,
            'suratKeteranganTidakMampuInstansi'     => $suratKeteranganTidakMampuInstansi,
            'suratKeteranganTidakMampuNumber'       => $suratKeteranganTidakMampuNumber,
            'suratKeteranganTidakMampuDate'         => $suratKeteranganTidakMampuDate,
            'suratKeteranganTidakMampuPersonName'   => $suratKeteranganTidakMampuPersonName,
            'suspectName'                           => $suspectName,
            'suspectIdentityNumber'                 => $suspectIdentityNumber,
            'suspectNationality'                    => $suspectNationality,
            'suspectGenderName'                     => $suspectGenderName,
            'suspectBirthPlace'                     => $suspectBirthPlace,
            'suspectBirthDate'                      => $suspectBirthDate,
            'suspectJobName'                        => $suspectJobName,
            'suspectReligionName'                   => $suspectReligionName,
            'suspectFullAddress'                    => $suspectFullAddress,
            'dugaanTindakPidana'                    => $dugaanTindakPidana,
            'pasalDiduga'                           => $pasalDiduga,
            'uuNomor'                               => $uuNomor,
            'uuTahun'                               => $uuTahun,
            'uuTentang'                             => $uuTentang,
            'tempatKejadian'                        => $tempatKejadian,
            'waktuKejadian'                         => $waktuKejadian,
            'kurunWaktu'                            => $kurunWaktu,
            'examinationDay'                        => $examinationDay,
            'examinationDate'                       => $examinationDate,
            'examinationTime'                       => $examinationTime,
            'examinationOfficeName'                 => $examinationOfficeName,
            'examinationOfficeAddress'              => $examinationOfficeAddress,
            'contactOfficerRole'                    => $contactOfficerRole,
            'contactOfficerName'                    => $contactOfficerName,
            'contactOfficerPhone'                   => $contactOfficerPhone,
            'carbon_copy_1'                         => $carbonCopy1,
            'carbon_copy_2'                         => $carbonCopy2,
            'carbon_copy_3'                         => $carbonCopy3,
            'signatoryHeadText'                     => $signatoryHeadText,
            'signatoryPositionName'                 => $signatoryPositionName,
            'signatoryName'                         => $signatoryName,
            'signatoryRankName'                     => $signatoryRankName,
            'signatoryRegisterNumber'               => $signatoryRegisterNumber,
        ];
    }

    /**
     * Generate file Word dengan template surat_penunjukan_penasehat_hukum.docx
     */
    public function generatePopulatedWordFile($id): array
    {
        $document = SuratPenunjukanPenasihatHukumDocument::with(['accident', 'suspect', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($document->accident_id);

        $templatePath = public_path('word-template/surat_penunjukan_penasehat_hukum.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('File template Word tidak ditemukan di: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);
        $data = $this->prepareDocumentData($document, $accident);

        foreach ($data as $key => $val) {
            $tp->setValue($key, (string) ($val ?? ''));
        }

        $cleanNomor = str_replace(['/', '\\', ' '], '_', $document->document_number ?: 'Draft');
        $fileName = 'Surat_Penunjukan_Penasihat_Hukum_' . $cleanNomor . '_' . date('Ymd_His') . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);

        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        $tp->saveAs($tempPath);

        return [
            'filename' => $tempPath,
            'basename' => $fileName,
        ];
    }
}

