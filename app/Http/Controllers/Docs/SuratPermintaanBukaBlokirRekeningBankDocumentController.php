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
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPermintaanBukaBlokirRekeningBankDocument\SuratPermintaanBukaBlokirRekeningBankDocument;

use App\Traits\DocsOfficersTraits;

class SuratPermintaanBukaBlokirRekeningBankDocumentController extends Controller
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
     * Form pembuatan Surat Permintaan Buka Blokir Rekening Bank
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // 1. Dokumen Sprindik
        $sprindikDocument = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // 2. Tersangka
        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accidentId)->get();
        $firstSuspect = $suspects->first();

        // 3. Petugas Internal & Penandatangan
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

        // Nilai Default Kop
        $defaultDaerahPolice = strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR');
        $defaultResorPolice = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN'));
        $defaultResorPoliceAddress = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));
        $defaultKota = $accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan');

        $defaultDocDate = Carbon::now()->locale('id')->translatedFormat('d F Y');
        $defaultDocNumber = 'B/' . ($sprindikDocument->nomor ?? ($accident->no_lp ?: '01')) . '/XI/RES.1.24/' . date('Y') . '/Satlantas';

        $defaultDasarKuhap = 'Pasal 5, Pasal 7 ayat (1) huruf d dan j, Pasal 11, Pasal 39, Pasal 40, Pasal 44 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana';
        $defaultUuLain = 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        $defaultNoLp = $accident->no_lp ?: '-';
        $defaultTglLp = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : date('d F Y');

        $defaultNoSprindik = $sprindikDocument->nomor ?? ($sprindikDocument->document_number ?? '-');
        $defaultTglSprindik = $sprindikDocument && $sprindikDocument->tanggal ? Carbon::parse($sprindikDocument->tanggal)->locale('id')->translatedFormat('d F Y') : date('d F Y');

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

        $defaultSatker = 'Satlantas Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan');
        $defaultCarbonCopy2 = 'Kapolda ' . ($accident->polres->polda->name ?? 'Jawa Timur');

        return view('docs.surat-permintaan-buka-blokir-rekening-bank-document.create', [
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
            'defaultSatker'             => $defaultSatker,
            'defaultCarbonCopy2'        => $defaultCarbonCopy2,
        ]);
    }

    /**
     * Simpan Surat Permintaan Buka Blokir Rekening Bank
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'document_number'        => 'required|string|max:255',
            'target_bank_name'       => 'required|string|max:255',
            'suspect_name'           => 'required|string|max:255',
            'account_number_1'       => 'required|string|max:255',
        ], [
            'document_number.required'  => 'Nomor Surat wajib diisi.',
            'target_bank_name.required' => 'Nama Bank Tujuan wajib diisi.',
            'suspect_name.required'     => 'Nama Tersangka wajib diisi.',
            'account_number_1.required' => 'Nomor rekening yang akan dibuka blokir wajib diisi.',
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

            SuratPermintaanBukaBlokirRekeningBankDocument::create([
                'id'                                        => $docId,
                'accident_id'                               => $accidentId,
                'status_id'                                 => '2',
                'document_category_id'                      => '0708',

                // Kop Polda & Polres otomatis dari relasi no LP / perkara
                'daerah_police_full_name'                   => $request->daerah_police_full_name ?: strtoupper($accident->polres->polda->name ?? 'SUMATERA UTARA'),
                'resor_police_full_name'                    => $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))),
                'resor_police_address'                      => $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')),

                'document_number'                           => $request->document_number,
                'document_location'                         => $request->document_location,
                'document_date'                             => $request->document_date,
                'document_classification_name'              => $request->document_classification_name ?: 'Biasa',
                'appendix'                                  => $request->appendix ?: '-',
                'target_bank_name'                          => $request->target_bank_name,
                'target_bank_location'                      => $request->target_bank_location,

                'dasar_kuhap'                               => $request->dasar_kuhap,
                'undang_undang_lain'                        => $request->undang_undang_lain,
                'accident_number'                           => $request->accident_number,
                'accident_date'                             => $request->accident_date,
                'surat_perintah_penyidikan_document_number' => $request->surat_perintah_penyidikan_document_number,
                'surat_perintah_penyidikan_document_date'   => $request->surat_perintah_penyidikan_document_date,
                'surat_pemblokiran_nomor'                   => $request->surat_pemblokiran_nomor ?: '-',
                'surat_pemblokiran_tanggal'                 => $request->surat_pemblokiran_tanggal ?: '-',
                'nama_pengadilan_negeri'                    => $request->nama_pengadilan_negeri ?: ($accident->polres->name ?? 'Pasuruan'),
                'penetapan_court_number'                    => $request->penetapan_court_number ?: '-',
                'penetapan_court_date'                      => $request->penetapan_court_date ?: '-',
                'surat_perintah_kepala_instansi'            => $request->surat_perintah_kepala_instansi ?: ('Kepala Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan')),
                'surat_perintah_kepala_nomor'               => $request->surat_perintah_kepala_nomor ?: '-',

                'target_bank_leader_title'                  => $request->target_bank_leader_title ?: 'Pemimpin Cabang',
                'satker_penyidik'                           => $request->satker_penyidik,
                'dugaan_tindak_pidana'                      => $request->dugaan_tindak_pidana,
                'pasal_diduga'                              => $request->pasal_diduga,
                'uu_nomor'                                  => $request->uu_nomor ?: '22',
                'uu_tahun'                                  => $request->uu_tahun ?: '2009',
                'uu_tentang'                                => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'                           => $request->tempat_kejadian,
                'waktu_kejadian'                            => $request->waktu_kejadian,

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

                'account_number_1'                          => $request->account_number_1,
                'account_holder_name_1'                     => $request->account_holder_name_1 ?: $request->suspect_name,
                'account_owner_type_1'                      => $request->account_owner_type_1 ?: 'Tersangka',
                'account_number_2_line'                     => $request->account_number_2_line ?: '-',
                'seizure_amount'                            => $request->seizure_amount ?: '0',
                'escrow_bank_name'                          => $request->escrow_bank_name ?: 'BRI',
                'escrow_account_number'                     => $request->escrow_account_number ?: '-',
                'escrow_account_holder_name'                => $request->escrow_account_holder_name ?: '-',
                'escrow_account_owner_type'                 => $request->escrow_account_owner_type ?: 'Rekening Penampungan Barang Bukti',

                'contact_officer_id'                        => !empty($request->contact_officer_id) ? $request->contact_officer_id : null,
                'contact_officer_role'                      => $contactRole,
                'contact_officer_name'                      => $contactName ?: '-',
                'contact_officer_phone'                     => $contactPhone ?: '-',

                'carbon_copy_2'                             => $request->carbon_copy_2 ?: ('Kapolda ' . ($accident->polres->polda->name ?? 'Jawa Timur')),

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
                ->with('success', 'Surat Permintaan Buka Blokir Rekening Bank berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Form Edit Surat Permintaan Buka Blokir Rekening Bank
     */
    public function edit($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPermintaanBukaBlokirRekeningBankDocument::with(['accident', 'suspect', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);

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

        return view('docs.surat-permintaan-buka-blokir-rekening-bank-document.edit', array_merge([
            'document'              => $document,
            'accident'              => $accident,
            'accidentId'            => $accidentId,
            'suspects'              => $suspects,
            'officers'              => $officers,
            'authorizedSignatories' => $authorizedSignatories,
        ], $data));
    }

    /**
     * Update Surat Permintaan Buka Blokir Rekening Bank
     */
    public function update(Request $request, $id)
    {
        $document = SuratPermintaanBukaBlokirRekeningBankDocument::findOrFail($id);
        $accidentId = $document->accident_id ?? $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'document_number'        => 'required|string|max:255',
            'target_bank_name'       => 'required|string|max:255',
            'suspect_name'           => 'required|string|max:255',
            'account_number_1'       => 'required|string|max:255',
        ], [
            'document_number.required'  => 'Nomor Surat wajib diisi.',
            'target_bank_name.required' => 'Nama Bank Tujuan wajib diisi.',
            'suspect_name.required'     => 'Nama Tersangka wajib diisi.',
            'account_number_1.required' => 'Nomor rekening yang akan dibuka blokir wajib diisi.',
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

            $document->update([
                // Kop Polda & Polres otomatis dari relasi no LP / perkara
                'daerah_police_full_name'                   => $request->daerah_police_full_name ?: strtoupper($accident->polres->polda->name ?? 'SUMATERA UTARA'),
                'resor_police_full_name'                    => $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))),
                'resor_police_address'                      => $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')),

                'document_number'                           => $request->document_number,
                'document_location'                         => $request->document_location,
                'document_date'                             => $request->document_date,
                'document_classification_name'              => $request->document_classification_name ?: 'Biasa',
                'appendix'                                  => $request->appendix ?: '-',
                'target_bank_name'                          => $request->target_bank_name,
                'target_bank_location'                      => $request->target_bank_location,

                'dasar_kuhap'                               => $request->dasar_kuhap,
                'undang_undang_lain'                        => $request->undang_undang_lain,
                'accident_number'                           => $request->accident_number,
                'accident_date'                             => $request->accident_date,
                'surat_perintah_penyidikan_document_number' => $request->surat_perintah_penyidikan_document_number,
                'surat_perintah_penyidikan_document_date'   => $request->surat_perintah_penyidikan_document_date,
                'surat_pemblokiran_nomor'                   => $request->surat_pemblokiran_nomor ?: '-',
                'surat_pemblokiran_tanggal'                 => $request->surat_pemblokiran_tanggal ?: '-',
                'nama_pengadilan_negeri'                    => $request->nama_pengadilan_negeri ?: ($accident->polres->name ?? 'Pasuruan'),
                'penetapan_court_number'                    => $request->penetapan_court_number ?: '-',
                'penetapan_court_date'                      => $request->penetapan_court_date ?: '-',
                'surat_perintah_kepala_instansi'            => $request->surat_perintah_kepala_instansi ?: ('Kepala Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan')),
                'surat_perintah_kepala_nomor'               => $request->surat_perintah_kepala_nomor ?: '-',

                'target_bank_leader_title'                  => $request->target_bank_leader_title ?: 'Pemimpin Cabang',
                'satker_penyidik'                           => $request->satker_penyidik,
                'dugaan_tindak_pidana'                      => $request->dugaan_tindak_pidana,
                'pasal_diduga'                              => $request->pasal_diduga,
                'uu_nomor'                                  => $request->uu_nomor ?: '22',
                'uu_tahun'                                  => $request->uu_tahun ?: '2009',
                'uu_tentang'                                => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'                           => $request->tempat_kejadian,
                'waktu_kejadian'                            => $request->waktu_kejadian,

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

                'account_number_1'                          => $request->account_number_1,
                'account_holder_name_1'                     => $request->account_holder_name_1 ?: $request->suspect_name,
                'account_owner_type_1'                      => $request->account_owner_type_1 ?: 'Tersangka',
                'account_number_2_line'                     => $request->account_number_2_line ?: '-',
                'seizure_amount'                            => $request->seizure_amount ?: '0',
                'escrow_bank_name'                          => $request->escrow_bank_name ?: 'BRI',
                'escrow_account_number'                     => $request->escrow_account_number ?: '-',
                'escrow_account_holder_name'                => $request->escrow_account_holder_name ?: '-',
                'escrow_account_owner_type'                 => $request->escrow_account_owner_type ?: 'Rekening Penampungan Barang Bukti',

                'contact_officer_id'                        => !empty($request->contact_officer_id) ? $request->contact_officer_id : null,
                'contact_officer_role'                      => $contactRole,
                'contact_officer_name'                      => $contactName ?: '-',
                'contact_officer_phone'                     => $contactPhone ?: '-',

                'carbon_copy_2'                             => $request->carbon_copy_2 ?: ('Kapolda ' . ($accident->polres->polda->name ?? 'Jawa Timur')),

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
                ->with('success', 'Surat Permintaan Buka Blokir Rekening Bank berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Preview Dokumen
     */
    public function show($id)
    {
        $document = SuratPermintaanBukaBlokirRekeningBankDocument::with(['accident', 'suspect', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.surat-permintaan-buka-blokir-rekening-bank-document.show', array_merge([
            'document' => $document,
            'accident' => $accident,
        ], $data));
    }

    /**
     * Hapus Dokumen
     */
    public function delete($id)
    {
        $document = SuratPermintaanBukaBlokirRekeningBankDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Permintaan Buka Blokir Rekening Bank berhasil dihapus.');
    }

    /**
     * Unduh File Word (.docx)
     */
    public function download($id)
    {
        $result = $this->generatePopulatedWordFile($id);
        return response()->download($result['filename'], $result['basename'])->deleteFileAfterSend(true);
    }

    /**
     * Mempersiapkan ke-59 variabel template Word & HTML View
     */
    public function prepareDocumentData($document, $accident): array
    {
        $daerahPoliceFullName = strtoupper($document->daerah_police_full_name ?: ($accident->polres->polda->name ?? 'JAWA TIMUR'));
        $resorPoliceFullName = $document->resor_police_full_name ?: ((in_array($accident->polres_id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN')));
        $resorPoliceAddress = $document->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        $documentLocation = $document->document_location ?: ($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan'));
        $documentDate = $document->document_date ?: Carbon::now()->locale('id')->translatedFormat('d F Y');
        $documentNumber = $document->document_number ?: '-';
        $documentClassificationName = $document->document_classification_name ?: 'Biasa';
        $appendix = $document->appendix ?: '-';
        $targetBankName = $document->target_bank_name ?: 'Direktur Kepatuhan PT. Bank Rakyat Indonesia (Persero) Tbk';
        $targetBankLocation = $document->target_bank_location ?: 'Jakarta';

        $dasarKuhap = $document->dasar_kuhap ?: 'Pasal 5, Pasal 7 ayat (1) huruf d dan j, Pasal 11, Pasal 39, Pasal 40, Pasal 44 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana';
        $undangUndangLain = $document->undang_undang_lain ?: 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        $accidentNumber = $document->accident_number ?: ($accident->no_lp ?: '-');
        $accidentDate = $document->accident_date ?: ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-');

        $suratPerintahPenyidikanDocumentNumber = $document->surat_perintah_penyidikan_document_number ?: '-';
        $suratPerintahPenyidikanDocumentDate = $document->surat_perintah_penyidikan_document_date ?: '-';

        $suratPemblokiranNomor = $document->surat_pemblokiran_nomor ?: '-';
        $suratPemblokiranTanggal = $document->surat_pemblokiran_tanggal ?: '-';

        $namaPengadilanNegeri = $document->nama_pengadilan_negeri ?: ($accident->polres->name ?? 'Pasuruan');
        $penetapanCourtNumber = $document->penetapan_court_number ?: '-';
        $penetapanCourtDate = $document->penetapan_court_date ?: '-';

        $suratPerintahKepalaInstansi = $document->surat_perintah_kepala_instansi ?: ('Kepala Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan'));
        $suratPerintahKepalaNomor = $document->surat_perintah_kepala_nomor ?: '-';

        $targetBankLeaderTitle = $document->target_bank_leader_title ?: 'Pemimpin Cabang';
        $satkerPenyidik = $document->satker_penyidik ?: ('Satlantas Kepolisian Resor ' . ($accident->polres->name ?? 'Pasuruan'));

        $dugaanTindakPidana = $document->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalDiduga = $document->pasal_diduga ?: '310 ayat (4)';
        $uuNomor = $document->uu_nomor ?: '22';
        $uuTahun = $document->uu_tahun ?: '2009';
        $uuTentang = $document->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan';
        $tempatKejadian = $document->tempat_kejadian ?: ($accident->address_detail ?: ($accident->location ?: '-'));
        $waktuKejadian = $document->waktu_kejadian ?: '-';

        $suspectName = $document->suspect_name ?: '-';
        $suspectIdentityNumber = $document->suspect_identity_number ?: '-';
        $suspectNationality = $document->suspect_nationality ?: 'Indonesia';
        $suspectGenderName = $document->suspect_gender_name ?: '-';
        $suspectBirthPlace = $document->suspect_birth_place ?: '-';
        $suspectBirthDate = $document->suspect_birth_date ?: '-';
        $suspectJobName = $document->suspect_job_name ?: '-';
        $suspectReligionName = $document->suspect_religion_name ?: '-';
        $suspectFullAddress = $document->suspect_full_address ?: '-';

        $accountNumber1 = $document->account_number_1 ?: '-';
        $accountHolderName1 = $document->account_holder_name_1 ?: $suspectName;
        $accountOwnerType1 = $document->account_owner_type_1 ?: 'Tersangka';
        $accountNumber2Line = $document->account_number_2_line ?: '-';

        $seizureAmount = $document->seizure_amount ?: '0';
        $escrowBankName = $document->escrow_bank_name ?: 'BRI';
        $escrowAccountNumber = $document->escrow_account_number ?: '-';
        $escrowAccountHolderName = $document->escrow_account_holder_name ?: '-';
        $escrowAccountOwnerType = $document->escrow_account_owner_type ?: 'Rekening Penampungan Barang Bukti';

        $contactOfficerRole = $document->contact_officer_role ?: 'Penyidik Pembantu';
        $contactOfficerName = $document->contact_officer_name ?: '-';
        $contactOfficerPhone = $document->contact_officer_phone ?: '-';

        $carbonCopy2 = $document->carbon_copy_2 ?: ('Kapolda ' . ($accident->polres->polda->name ?? 'Jawa Timur'));

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
            'targetBankName'                        => $targetBankName,
            'targetBankLocation'                    => $targetBankLocation,
            'dasarKuhap'                            => $dasarKuhap,
            'undangUndangLain'                      => $undangUndangLain,
            'accidentNumber'                        => $accidentNumber,
            'accidentDate'                          => $accidentDate,
            'suratPerintahPenyidikanDocumentNumber' => $suratPerintahPenyidikanDocumentNumber,
            'suratPerintahPenyidikanDocumentDate'   => $suratPerintahPenyidikanDocumentDate,
            'suratPemblokiranNomor'                 => $suratPemblokiranNomor,
            'suratPemblokiranTanggal'               => $suratPemblokiranTanggal,
            'namaPengadilanNegeri'                  => $namaPengadilanNegeri,
            'penetapanCourtNumber'                  => $penetapanCourtNumber,
            'penetapanCourtDate'                    => $penetapanCourtDate,
            'suratPerintahKepalaInstansi'            => $suratPerintahKepalaInstansi,
            'suratPerintahKepalaNomor'              => $suratPerintahKepalaNomor,
            'targetBankLeaderTitle'                 => $targetBankLeaderTitle,
            'satkerPenyidik'                        => $satkerPenyidik,
            'dugaanTindakPidana'                    => $dugaanTindakPidana,
            'pasalDiduga'                           => $pasalDiduga,
            'uuNomor'                               => $uuNomor,
            'uuTahun'                               => $uuTahun,
            'uuTentang'                             => $uuTentang,
            'tempatKejadian'                        => $tempatKejadian,
            'waktuKejadian'                         => $waktuKejadian,
            'suspectName'                           => $suspectName,
            'suspectIdentityNumber'                 => $suspectIdentityNumber,
            'suspectNationality'                    => $suspectNationality,
            'suspectGenderName'                     => $suspectGenderName,
            'suspectBirthPlace'                     => $suspectBirthPlace,
            'suspectBirthDate'                      => $suspectBirthDate,
            'suspectJobName'                        => $suspectJobName,
            'suspectReligionName'                   => $suspectReligionName,
            'suspectFullAddress'                    => $suspectFullAddress,
            'accountNumber1'                        => $accountNumber1,
            'accountHolderName1'                    => $accountHolderName1,
            'accountOwnerType1'                     => $accountOwnerType1,
            'accountNumber2Line'                    => $accountNumber2Line,
            'seizureAmount'                         => $seizureAmount,
            'escrowBankName'                        => $escrowBankName,
            'escrowAccountNumber'                  => $escrowAccountNumber,
            'escrowAccountHolderName'               => $escrowAccountHolderName,
            'escrowAccountOwnerType'                => $escrowAccountOwnerType,
            'contactOfficerRole'                    => $contactOfficerRole,
            'contactOfficerName'                    => $contactOfficerName,
            'contactOfficerPhone'                   => $contactOfficerPhone,
            'carbon_copy_2'                         => $carbonCopy2,
            'signatoryHeadText'                     => $signatoryHeadText,
            'signatoryPositionName'                 => $signatoryPositionName,
            'signatoryName'                         => $signatoryName,
            'signatoryRankName'                     => $signatoryRankName,
            'signatoryRegisterNumber'               => $signatoryRegisterNumber,
        ];
    }

    /**
     * Generate file Word dengan template surat_permintaan_buka_blokir_rekening_bank.docx
     */
    public function generatePopulatedWordFile($id): array
    {
        if ($id instanceof SuratPermintaanBukaBlokirRekeningBankDocument) {
            $document = $id;
        } else {
            $document = SuratPermintaanBukaBlokirRekeningBankDocument::with(['accident', 'suspect', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);
        }
        $accident = $document->accident ?? ($document->accident_id ? Accident::with(['polres', 'polres.polda'])->find($document->accident_id) : null);

        $templatePath = public_path('word-template/surat_permintaan_buka_blokir_rekening_bank.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('File template Word tidak ditemukan di: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);
        $data = $this->prepareDocumentData($document, $accident);

        foreach ($data as $key => $val) {
            $tp->setValue($key, (string) ($val ?? ''));
        }

        $cleanNomor = str_replace(['/', '\\', ' '], '_', $document->document_number ?: 'Draft');
        $fileName = 'Surat_Permintaan_Buka_Blokir_Bank_' . $cleanNomor . '_' . date('Ymd_His') . '.docx';
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
