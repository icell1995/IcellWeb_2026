<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\BeritaAcaraPenahanan;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Lib\Timezone;
use App\Models\Lib\Gender;
use App\Models\Lib\Religion;
use App\Models\Lib\Job;
use App\Models\Lib\Education;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Ethnic;
use App\Models\Lib\IdentityType;
use App\Models\Lib\Location;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BeritaAcaraPenahananDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function show(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        $accident = Accident::with(['polres.polda'])->where('id', $accidentId)->first();

        if (!$accident) {
            return redirect()->back()->with('error', 'Data Perkara Tidak Ditemukan');
        }

        $document = BeritaAcaraPenahanan::where('id', $id)->first();
        $suspectId = $document->suspect_id ?? null;
        $suspect = $suspectId 
            ? Suspect::with(['gender', 'religion', 'job'])->where('id', $suspectId)->first()
            : Suspect::with(['gender', 'religion', 'job'])->where('accident_id', $accidentId)->first();

        $officerLeaderId = $document->officer_leader_id ?? null;
        $officerLeader = $officerLeaderId
            ? Officer::withRelated()->selectFullName()->where('id', $officerLeaderId)->first()
            : Officer::withRelated()->selectFullName()
                ->where('police_id', $accident->polres_id)
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()->active()->valid()->first();

        $officerLeaderFullName = $officerLeader
            ? strtoupper(trim(($officerLeader->first_title ? $officerLeader->first_title . ' ' : '') . $officerLeader->first_name . ' ' . $officerLeader->last_name . ($officerLeader->last_title ? ', ' . $officerLeader->last_title : '')))
            : '-';

        $companionOfficers = [];
        $internalOfficersNrp = $document->internal_officers ?? [];
        if (!empty($internalOfficersNrp)) {
            foreach ($internalOfficersNrp as $nrp) {
                $off = Officer::withRelated()->selectFullName()->where('register_number', $nrp)->first();
                if ($off) {
                    $companionOfficers[] = [
                        'name' => strtoupper($off->full_name ?? '-'),
                        'rank_nrp' => strtoupper($off->rank->name ?? '-') . ' / ' . ($off->register_number ?? '-'),
                        'position' => strtoupper($off->position->name ?? '-'),
                    ];
                }
            }
        }

        $docDate = $document && $document->document_date ? Carbon::parse($document->document_date) : Carbon::now();
        $startDate = $document && $document->start_date ? Carbon::parse($document->start_date) : Carbon::now();
        $endDate = $document && $document->end_date ? Carbon::parse($document->end_date) : Carbon::now()->addDays(19);
        $spHanDate = $document && $document->reference_document_date ? Carbon::parse($document->reference_document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $timezone = Timezone::where('id', $document->timezone_id ?? '')->first();

        return view('docs.berita-acara-penahanan-document.show', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'beritaAcaraPenahananDocumentId' => $id,
            'document' => $document,
            'dayName' => $docDate->locale('id')->translatedFormat('l'),
            'documentDateText' => $docDate->locale('id')->translatedFormat('d F Y'),
            'time' => $document->time ?? '09:00',
            'timezone' => $timezone->name ?? 'WIB',
            'place' => $document->place ?? ('Ruang Satreskrim ' . ($accident->polres->full_name ?? '')),
            'officerLeaderName' => $officerLeaderFullName,
            'officerLeaderRank' => strtoupper($officerLeader->rank->name ?? '-'),
            'officerLeaderNrp' => $officerLeader->register_number ?? '-',
            'investigatorRole' => $document->investigator_role ?? 'Penyidik',
            'companionOfficers' => $companionOfficers,
            'spHanNumber' => $document->reference_document_number ?? ($document->initial ?? 'SP.Han/01/VIII/RES.1.24/2026/Satreskrim'),
            'spHanDate' => $spHanDate,
            'suspectName' => strtoupper($suspect->name ?? '-'),
            'suspectIdentityNumber' => $suspect->identity_number ?? '-',
            'suspectBirthPlace' => strtoupper($suspect->birth_place ?? '-'),
            'suspectBirthDate' => $suspect && $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-',
            'suspectGender' => strtoupper($suspect->gender->name ?? '-'),
            'suspectJob' => strtoupper($suspect->job->name ?? '-'),
            'suspectNationality' => strtoupper($suspect->nationality ?? 'INDONESIA'),
            'suspectReligion' => strtoupper($suspect->religion->name ?? '-'),
            'suspectAddress' => strtoupper($suspect->address ?? '-'),
            'crimeDescription' => strtoupper($document->properties['crime_description'] ?? $this->getCrimeDetailsFromSprindik($accidentId)['crimeDescription']),
            'crimeArticle' => preg_replace('/^pasal\s+/i', '', trim($document->properties['crime_article'] ?? $this->getCrimeDetailsFromSprindik($accidentId)['crimeArticle'])),
            'detentionPlace' => strtoupper($document->detention_place ?? ('Rumah Tahanan Negara ' . ($accident->polres->full_name ?? ''))),
            'detentionBranch' => strtoupper($document->detention_branch ?? ($accident->polres->full_name ?? '')),
            'startDate' => $startDate->locale('id')->translatedFormat('d F Y'),
            'endDate' => $endDate->locale('id')->translatedFormat('d F Y'),
            'task' => $document->task ?? 'Melakukan penahanan tersangka sesuai SOP',
            'healthCondition' => $document->health_condition ?? 'dalam keadaan sehat',
        ]);
    }

    public function create(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        $accident = Accident::where('id', $accidentId)->first();

        if (!$accident) {
            return redirect()->back()->with('error', 'Data Perkara Tidak Ditemukan');
        }

        $police = Police::with('parent')->where('id', $accident->polres_id)->first();
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $internalOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $positions = Position::whereIn('police_id', [$police->id ?? null, $police->parent->id ?? null, $police->parent->parent->id ?? null])
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $timezones = Timezone::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $jobs = Job::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $maritalStatuses = MaritalStatus::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $ethnics = Ethnic::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $identityTypes = IdentityType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $countries = Location::where('is_active', true)
            ->where('class', 'COUNTRY')
            ->orderBy('sort')
            ->get();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentLaws.crimeConstitution'])
            ->where('accident_id', $accidentId)
            ->get();

        $crimeDetails = $this->getCrimeDetailsFromSprindik($accidentId);
        $defaultCrimeArticle = $crimeDetails['crimeArticle'];
        $defaultCrimeDescription = $crimeDetails['crimeDescription'];

        // Dokumen SP-HAN (Otomatis / Dummy jika modul 0601 belum aktif)
        $suratPerintahPenahananDocuments = collect([
            (object)[
                'id' => 'sph-dummy-1',
                'document_number' => 'SP.Han/01/VIII/RES.1.24/2026/Satreskrim',
                'document_date' => date('Y-m-d', strtotime('-1 days')),
                'suspect_id' => $suspects->first()->id ?? null,
                'detention_place' => 'Rumah Tahanan Negara ' . ($police->name ?? 'Polres'),
                'detention_branch' => ($police->name ?? 'Polres'),
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+19 days')),
            ]
        ]);

        return view('docs.berita-acara-penahanan-document.create', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'police' => $police,
            'internalOfficers' => $internalOfficers,
            'authorizedSignatories' => $authorizedSignatories,
            'suspects' => $suspects,
            'ranks' => $ranks,
            'positions' => $positions,
            'timezones' => $timezones,
            'genders' => $genders,
            'religions' => $religions,
            'jobs' => $jobs,
            'educations' => $educations,
            'maritalStatuses' => $maritalStatuses,
            'ethnics' => $ethnics,
            'identityTypes' => $identityTypes,
            'countries' => $countries,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratPerintahPenahananDocuments' => $suratPerintahPenahananDocuments,
            'defaultCrimeArticle' => $defaultCrimeArticle,
            'defaultCrimeDescription' => $defaultCrimeDescription,
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->input('accidentId') ?? $request->input('accident_id'));
        $accident   = Accident::with(['polres.polda'])->where('id', $accidentId)->first();

        if (!$accident) {
            return redirect()->back()->with('error', 'Data Perkara Tidak Ditemukan');
        }

        // --- Penyidik Pembuat BA ---
        $officerLeader = Officer::withRelated()->selectFullName()
            ->where('id', $request->input('officerLeader'))->first();

        // --- Tersangka ---
        $suspect = Suspect::with(['gender', 'religion', 'job'])
            ->where('id', $request->input('suspect'))->first();

        // --- Timezone ---
        $timezone = Timezone::where('id', $request->input('timezone'))->first();

        // --- Tanggal dokumen ---
        $documentDate  = Carbon::parse($request->input('documentDate'));
        $startDate     = Carbon::parse($request->input('startDate'));
        $endDate       = Carbon::parse($request->input('endDate'));
        $spHanDate     = $request->input('suratPerintahPenahananDate')
            ? Carbon::parse($request->input('suratPerintahPenahananDate'))->locale('id')->translatedFormat('d F Y')
            : '-';

        // --- Personil Penyidik Pendamping (Internal) ---
        $blockOfficers = [];
        $officerCounter = 1;

        $internalOfficers = $request->input('internalOfficers', []);
        foreach ($internalOfficers as $nrp) {
            $off = Officer::withRelated()->selectFullName()->where('register_number', $nrp)->orWhere('id', $nrp)->first();
            if ($off) {
                $blockOfficers[] = [
                    'personnel_no'       => ($officerCounter++) . '.',
                    'personnel_name'     => strtoupper($off->full_name ?? '-'),
                    'personnel_rank_nrp' => strtoupper($off->rank->name ?? '-') . ' / ' . ($off->register_number ?? '-'),
                    'personnel_position' => strtoupper($off->position->name ?? '-'),
                ];
            }
        }

        // --- Pasal & Tindak Pidana dari Sprindik (Sesuai Logika Senior) ---
        $crimeDetails = $this->getCrimeDetailsFromSprindik($accidentId);
        $crimeDescription = $request->input('crimeDescription') ?: $crimeDetails['crimeDescription'];
        $crimeArticleRaw = $request->input('crimeArticle');
        if (empty($crimeArticleRaw) || $crimeArticleRaw === '-') {
            $crimeArticleRaw = $crimeDetails['crimeArticle'];
        }
        $crimeArticle = preg_replace('/^pasal\s+/i', '', trim($crimeArticleRaw));

        // --- Nama penyidik untuk dokumen ---
        $officerLeaderFullName = $officerLeader
            ? strtoupper(trim(($officerLeader->first_title ? $officerLeader->first_title . ' ' : '') . $officerLeader->first_name . ' ' . $officerLeader->last_name . ($officerLeader->last_title ? ', ' . $officerLeader->last_title : '')))
            : '-';

        // --- Template Processor Word Document ---
        $templatePath = file_exists(public_path('word-template/berita_acara_penahanan.docx'))
            ? public_path('word-template/berita_acara_penahanan.docx')
            : 'word-template/berita_acara_penahanan.docx';
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        if (empty($blockOfficers)) {
            $templateProcessor->cloneBlock('block_officers', 0, true, false);
        } else {
            $templateProcessor->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);
        }

        $templateProcessor->setValue('daerahPoliceFullName',    strtoupper($accident->polres->polda->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceFullName',      strtoupper($accident->polres->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceAddress',       ucwords(strtolower($accident->polres->address ?? '')));
        $templateProcessor->setValue('dayName',                 $documentDate->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('documentDay',             $documentDate->locale('id')->translatedFormat('d'));
        $templateProcessor->setValue('documentMonth',           $documentDate->locale('id')->translatedFormat('F'));
        $templateProcessor->setValue('documentYear',            $documentDate->locale('id')->translatedFormat('Y'));
        $templateProcessor->setValue('documentDateText',        $documentDate->locale('id')->translatedFormat('d-m-Y'));
        $templateProcessor->setValue('time',                    $request->input('time', '--:--'));
        $templateProcessor->setValue('timezone',                strtoupper($timezone->name ?? ''));
        $templateProcessor->setValue('officerLeaderName',       $officerLeaderFullName);
        $templateProcessor->setValue('officerLeaderRank',       strtoupper($officerLeader->rank->name ?? '-'));
        $templateProcessor->setValue('officerLeaderNrp',        $officerLeader->register_number ?? '-');
        $templateProcessor->setValue('investigatorRole',        $request->input('investigatorRole', 'Penyidik'));
        $rawSpHan = $request->input('suratPerintahPenahananDocument', '-');
        $spHanDocNumber = $request->input('suratPerintahPenahananDocumentNumber');
        if (empty($spHanDocNumber) || in_array($spHanDocNumber, ['sph-dummy-1', 'sph-dummy-2'])) {
            $spHanDocNumber = (in_array($rawSpHan, ['sph-dummy-1', 'sph-dummy-2']) || str_starts_with($rawSpHan, 'sph-'))
                ? 'SP.Han/01/VIII/RES.1.24/2026/Satreskrim'
                : $rawSpHan;
        }

        $templateProcessor->setValue('spHanNumber',             $spHanDocNumber);
        $templateProcessor->setValue('spHanDate',               $spHanDate);
        $templateProcessor->setValue('suspectName',             strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('suspectNameShort',         strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('suspectIdentityNumber',   $suspect->identity_number ?? '-');
        $templateProcessor->setValue('suspectBirthPlace',       strtoupper($suspect->birth_place ?? '-'));
        $templateProcessor->setValue('suspectBirthDate',        $suspect && $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('suspectGender',           strtoupper($suspect->gender->name ?? '-'));
        $templateProcessor->setValue('suspectJob',              strtoupper($suspect->job->name ?? '-'));
        $templateProcessor->setValue('suspectNationality',      strtoupper($suspect->nationality ?? 'INDONESIA'));
        $templateProcessor->setValue('suspectReligion',         strtoupper($suspect->religion->name ?? '-'));
        $templateProcessor->setValue('suspectAddress',          strtoupper($suspect->address ?? '-'));
        $templateProcessor->setValue('crimeDescription',        strtoupper($crimeDescription));
        $templateProcessor->setValue('crimeArticle',            $crimeArticle);
        $templateProcessor->setValue('detentionPlace',          strtoupper($request->input('detentionPlace', '-')));
        $templateProcessor->setValue('detentionBranch',         strtoupper($request->input('detentionBranch', '-')));
        $templateProcessor->setValue('startDate',               $startDate->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('endDate',                 $endDate->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('task',                    $request->input('task', '-'));
        $templateProcessor->setValue('healthCondition',         $request->input('healthCondition', '-'));
        $templateProcessor->setValue('place',                   strtoupper($request->input('place', '-')));
        $templateProcessor->setValue('suspectSignatureName',    strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('officerSignatureTitle',   $request->input('investigatorRole', 'Penyidik'));
        $templateProcessor->setValue('officerSignatureName',    $officerLeaderFullName);
        $templateProcessor->setValue('officerSignatureRankNrp', ($officerLeader ? strtoupper($officerLeader->rank->name ?? '') . ' NRP. ' . $officerLeader->register_number : '-'));

        $documentId = (string) Uuid::generate();
        $generatedFileName = $documentId . ' - Berita Acara Penahanan - ' . strtoupper($accident->polres->full_name ?? '') . '.docx';
        $destinationDir = public_path('file/penahanan/berita-acara-penahanan');
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }
        $templateProcessor->saveAs($destinationDir . '/' . $generatedFileName);

        $properties = [
            'accident_number' => $accident->no_lp,
            'surat_perintah_penahanan_document_id' => $request->input('suratPerintahPenahananDocument'),
            'surat_perintah_penahanan_document_number' => $spHanDocNumber,
            'surat_perintah_penahanan_date' => $request->input('suratPerintahPenahananDate'),
            'time' => $request->input('time'),
            'timezone_id' => $request->input('timezone'),
            'place' => $request->input('place'),
            'officer_leader_id' => $request->input('officerLeader'),
            'investigator_role' => $request->input('investigatorRole', 'Penyidik'),
            'internal_officers' => $request->input('internalOfficers', []),
            'suspect_id' => $request->input('suspect'),
            'detention_place' => $request->input('detentionPlace'),
            'detention_branch' => $request->input('detentionBranch'),
            'start_date' => $request->input('startDate'),
            'end_date' => $request->input('endDate'),
            'task' => $request->input('task'),
            'health_condition' => $request->input('healthCondition'),
            'document_date' => $request->input('documentDate'),
            'crime_description' => $crimeDescription,
            'crime_article' => $crimeArticle,
        ];

        // Simpan Record ke Database
        DB::beginTransaction();
        try {
            $user = Auth::id() ?? (Auth::user()->name ?? 'system');

            BeritaAcaraPenahanan::create([
                'id'          => $documentId,
                'accident_id' => $accidentId,
                'name'        => $generatedFileName,
                'category'    => 'D040102',
                'initial'     => $spHanDocNumber,
                'created_by'  => $user,
                'status_id'   => '2',
                'document_date' => $request->input('documentDate') ?? date('Y-m-d'),
                'properties'  => $properties,
            ]);

            Accident::where('id', $accidentId)->update([
                'last_update' => Carbon::now(),
                'category'    => 'D040102',
                'tipe_update' => 'GENERATE'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data ke database: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Penahanan (BA-HAN) berhasil disimpan');
    }

    public function edit(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        $accident = Accident::where('id', $accidentId)->first();

        if (!$accident) {
            return redirect()->back()->with('error', 'Data Perkara Tidak Ditemukan');
        }

        $police = Police::with('parent')->where('id', $accident->polres_id)->first();
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $internalOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $positions = Position::whereIn('police_id', [$police->id ?? null, $police->parent->id ?? null, $police->parent->parent->id ?? null])
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $timezones = Timezone::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $jobs = Job::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $maritalStatuses = MaritalStatus::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $ethnics = Ethnic::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $identityTypes = IdentityType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $countries = Location::where('is_active', true)
            ->where('class', 'COUNTRY')
            ->orderBy('sort')
            ->get();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentLaws.crimeConstitution'])
            ->where('accident_id', $accidentId)
            ->get();

        $defaultCrimeArticle = '';
        $latestSprindik = $suratPerintahPenyidikanDocuments->last();
        if ($latestSprindik && $latestSprindik->suratPerintahPenyidikanDocumentLaws) {
            $pasalList = $latestSprindik->suratPerintahPenyidikanDocumentLaws
                ->map(function($law) {
                    $parts = [];
                    if (!empty($law->constitution_chapter)) {
                        $parts[] = $law->constitution_chapter;
                    }
                    if (!empty($law->constitution)) {
                        $parts[] = $law->constitution;
                    }
                    if (empty($parts) && $law->crimeConstitution) {
                        if (!empty($law->crimeConstitution->chapter)) {
                            $parts[] = $law->crimeConstitution->chapter;
                        }
                        if (!empty($law->crimeConstitution->name)) {
                            $parts[] = $law->crimeConstitution->name;
                        }
                    }
                    return !empty($parts) ? implode(' ', $parts) : null;
                })
                ->filter()
                ->values()
                ->toArray();
            $defaultCrimeArticle = implode(', ', $pasalList);
        }

        // Dokumen SP-HAN
        $suratPerintahPenahananDocuments = collect([
            (object)[
                'id' => 'sph-dummy-1',
                'document_number' => 'SP.Han/01/VIII/RES.1.24/2026/Satreskrim',
                'document_date' => date('Y-m-d', strtotime('-1 days')),
                'suspect_id' => $suspects->first()->id ?? null,
                'detention_place' => 'Rumah Tahanan Negara ' . ($police->name ?? 'Polres'),
                'detention_branch' => ($police->name ?? 'Polres'),
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+19 days')),
            ]
        ]);

        $beritaAcaraPenahanan = BeritaAcaraPenahanan::where('id', $id)->first();
        $existingCompanionOfficers = [];
        $internalOfficersNrp = $beritaAcaraPenahanan->internal_officers ?? [];
        if (!empty($internalOfficersNrp)) {
            foreach ($internalOfficersNrp as $nrp) {
                $off = Officer::withRelated()->selectFullName()->where('register_number', $nrp)->first();
                if ($off) {
                    $existingCompanionOfficers[] = [
                        'register_number' => $off->register_number,
                        'full_name' => $off->full_name,
                        'rank_name' => $off->rank->name ?? '-',
                        'position_name' => $off->position->name ?? '-',
                        'police_name' => $off->police->name ?? '-',
                    ];
                }
            }
        }

        return view('docs.berita-acara-penahanan-document.edit', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'beritaAcaraPenahananDocumentId' => $id,
            'document' => $beritaAcaraPenahanan,
            'beritaAcaraPenahanan' => $beritaAcaraPenahanan,
            'police' => $police,
            'internalOfficers' => $internalOfficers,
            'existingCompanionOfficers' => $existingCompanionOfficers,
            'suspects' => $suspects,
            'ranks' => $ranks,
            'positions' => $positions,
            'timezones' => $timezones,
            'genders' => $genders,
            'religions' => $religions,
            'jobs' => $jobs,
            'educations' => $educations,
            'maritalStatuses' => $maritalStatuses,
            'ethnics' => $ethnics,
            'identityTypes' => $identityTypes,
            'countries' => $countries,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratPerintahPenahananDocuments' => $suratPerintahPenahananDocuments,
            'defaultCrimeArticle' => $defaultCrimeArticle,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->input('accidentId') ?? $request->input('accident_id'));
        $accident   = Accident::with(['polres.polda'])->where('id', $accidentId)->first();

        if (!$accident) {
            return redirect()->back()->with('error', 'Data Perkara Tidak Ditemukan');
        }

        $existingDoc = BeritaAcaraPenahanan::where('id', $id)->first();
        if (!$existingDoc) {
            return redirect()->back()->with('error', 'Dokumen Berita Acara Penahanan Tidak Ditemukan');
        }

        // --- Penyidik Pembuat BA ---
        $officerLeader = Officer::withRelated()->selectFullName()
            ->where('id', $request->input('officerLeader'))->first();

        // --- Tersangka ---
        $suspect = Suspect::with(['gender', 'religion', 'job'])
            ->where('id', $request->input('suspect'))->first();

        // --- Timezone ---
        $timezone = Timezone::where('id', $request->input('timezone'))->first();

        // --- Tanggal dokumen ---
        $documentDate  = Carbon::parse($request->input('documentDate'));
        $startDate     = Carbon::parse($request->input('startDate'));
        $endDate       = Carbon::parse($request->input('endDate'));
        $spHanDate     = $request->input('suratPerintahPenahananDate')
            ? Carbon::parse($request->input('suratPerintahPenahananDate'))->locale('id')->translatedFormat('d F Y')
            : '-';

        // --- Personil Penyidik Pendamping (Internal) ---
        $blockOfficers = [];
        $officerCounter = 1;

        $internalOfficers = $request->input('internalOfficers', []);
        foreach ($internalOfficers as $nrp) {
            $off = Officer::withRelated()->selectFullName()->where('register_number', $nrp)->orWhere('id', $nrp)->first();
            if ($off) {
                $blockOfficers[] = [
                    'personnel_no'       => ($officerCounter++) . '.',
                    'personnel_name'     => strtoupper($off->full_name ?? '-'),
                    'personnel_rank_nrp' => strtoupper($off->rank->name ?? '-') . ' / ' . ($off->register_number ?? '-'),
                    'personnel_position' => strtoupper($off->position->name ?? '-'),
                ];
            }
        }

        // --- Pasal & Tindak Pidana dari Sprindik (Sesuai Logika Senior) ---
        $crimeDetails = $this->getCrimeDetailsFromSprindik($accidentId);
        $crimeDescription = $request->input('crimeDescription')
            ?? ($existingDoc->properties['crime_description'] ?? $crimeDetails['crimeDescription']);
        $crimeArticleRaw = $request->input('crimeArticle');
        if (empty($crimeArticleRaw) || $crimeArticleRaw === '-') {
            $crimeArticleRaw = $existingDoc->properties['crime_article'] ?? $crimeDetails['crimeArticle'];
        }
        $crimeArticle = preg_replace('/^pasal\s+/i', '', trim($crimeArticleRaw));

        // --- Nama penyidik untuk dokumen ---
        $officerLeaderFullName = $officerLeader
            ? strtoupper(trim(($officerLeader->first_title ? $officerLeader->first_title . ' ' : '') . $officerLeader->first_name . ' ' . $officerLeader->last_name . ($officerLeader->last_title ? ', ' . $officerLeader->last_title : '')))
            : '-';

        // --- Template Processor Word Document ---
        $templatePath = file_exists(public_path('word-template/berita_acara_penahanan.docx'))
            ? public_path('word-template/berita_acara_penahanan.docx')
            : 'word-template/berita_acara_penahanan.docx';
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        if (empty($blockOfficers)) {
            $templateProcessor->cloneBlock('block_officers', 0, true, false);
        } else {
            $templateProcessor->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);
        }

        $templateProcessor->setValue('daerahPoliceFullName',    strtoupper($accident->polres->polda->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceFullName',      strtoupper($accident->polres->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceAddress',       ucwords(strtolower($accident->polres->address ?? '')));
        $templateProcessor->setValue('dayName',                 $documentDate->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('documentDay',             $documentDate->locale('id')->translatedFormat('d'));
        $templateProcessor->setValue('documentMonth',           $documentDate->locale('id')->translatedFormat('F'));
        $templateProcessor->setValue('documentYear',            $documentDate->locale('id')->translatedFormat('Y'));
        $templateProcessor->setValue('documentDateText',        $documentDate->locale('id')->translatedFormat('d-m-Y'));
        $templateProcessor->setValue('time',                    $request->input('time', '--:--'));
        $templateProcessor->setValue('timezone',                strtoupper($timezone->name ?? ''));
        $templateProcessor->setValue('officerLeaderName',       $officerLeaderFullName);
        $templateProcessor->setValue('officerLeaderRank',       strtoupper($officerLeader->rank->name ?? '-'));
        $templateProcessor->setValue('officerLeaderNrp',        $officerLeader->register_number ?? '-');
        $templateProcessor->setValue('investigatorRole',        $request->input('investigatorRole', 'Penyidik'));
        $rawSpHan = $request->input('suratPerintahPenahananDocument', '-');
        $spHanDocNumber = $request->input('suratPerintahPenahananDocumentNumber');
        if (empty($spHanDocNumber) || in_array($spHanDocNumber, ['sph-dummy-1', 'sph-dummy-2'])) {
            $spHanDocNumber = (in_array($rawSpHan, ['sph-dummy-1', 'sph-dummy-2']) || str_starts_with($rawSpHan, 'sph-'))
                ? 'SP.Han/01/VIII/RES.1.24/2026/Satreskrim'
                : $rawSpHan;
        }

        $templateProcessor->setValue('spHanNumber',             $spHanDocNumber);
        $templateProcessor->setValue('spHanDate',               $spHanDate);
        $templateProcessor->setValue('suspectName',             strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('suspectNameShort',         strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('suspectIdentityNumber',   $suspect->identity_number ?? '-');
        $templateProcessor->setValue('suspectBirthPlace',       strtoupper($suspect->birth_place ?? '-'));
        $templateProcessor->setValue('suspectBirthDate',        $suspect && $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('suspectGender',           strtoupper($suspect->gender->name ?? '-'));
        $templateProcessor->setValue('suspectJob',              strtoupper($suspect->job->name ?? '-'));
        $templateProcessor->setValue('suspectNationality',      strtoupper($suspect->nationality ?? 'INDONESIA'));
        $templateProcessor->setValue('suspectReligion',         strtoupper($suspect->religion->name ?? '-'));
        $templateProcessor->setValue('suspectAddress',          strtoupper($suspect->address ?? '-'));
        $templateProcessor->setValue('crimeDescription',        strtoupper($crimeDescription));
        $templateProcessor->setValue('crimeArticle',            $crimeArticle);
        $templateProcessor->setValue('detentionPlace',          strtoupper($request->input('detentionPlace', '-')));
        $templateProcessor->setValue('detentionBranch',         strtoupper($request->input('detentionBranch', '-')));
        $templateProcessor->setValue('startDate',               $startDate->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('endDate',                 $endDate->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('task',                    $request->input('task', '-'));
        $templateProcessor->setValue('healthCondition',         $request->input('healthCondition', '-'));
        $templateProcessor->setValue('place',                   strtoupper($request->input('place', '-')));
        $templateProcessor->setValue('suspectSignatureName',    strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('officerSignatureTitle',   $request->input('investigatorRole', 'Penyidik'));
        $templateProcessor->setValue('officerSignatureName',    $officerLeaderFullName);
        $templateProcessor->setValue('officerSignatureRankNrp', ($officerLeader ? strtoupper($officerLeader->rank->name ?? '') . ' NRP. ' . $officerLeader->register_number : '-'));

        $destinationDir = public_path('file/penahanan/berita-acara-penahanan');
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $fileName = $existingDoc->name ?? ($existingDoc->id . ' - Berita Acara Penahanan - ' . strtoupper($accident->polres->full_name ?? '') . '.docx');
        $templateProcessor->saveAs($destinationDir . '/' . $fileName);

        $properties = [
            'accident_number' => $accident->no_lp,
            'surat_perintah_penahanan_document_id' => $request->input('suratPerintahPenahananDocument'),
            'surat_perintah_penahanan_document_number' => $spHanDocNumber,
            'surat_perintah_penahanan_date' => $request->input('suratPerintahPenahananDate'),
            'time' => $request->input('time'),
            'timezone_id' => $request->input('timezone'),
            'place' => $request->input('place'),
            'officer_leader_id' => $request->input('officerLeader'),
            'investigator_role' => $request->input('investigatorRole', 'Penyidik'),
            'internal_officers' => $request->input('internalOfficers', []),
            'suspect_id' => $request->input('suspect'),
            'detention_place' => $request->input('detentionPlace'),
            'detention_branch' => $request->input('detentionBranch'),
            'start_date' => $request->input('startDate'),
            'end_date' => $request->input('endDate'),
            'task' => $request->input('task'),
            'health_condition' => $request->input('healthCondition'),
            'document_date' => $request->input('documentDate'),
            'crime_description' => $crimeDescription,
            'crime_article' => $crimeArticle,
        ];

        // Update Record di Database
        DB::beginTransaction();
        try {
            $existingDoc->initial = $spHanDocNumber;
            $existingDoc->properties = $properties;
            $existingDoc->updated_at = Carbon::now();
            $existingDoc->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Penahanan (BA-HAN) berhasil diperbarui');
    }

    public function delete(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->query('accident_id') ?? $request->input('accident_id'));

        DB::beginTransaction();
        try {
            $document = BeritaAcaraPenahanan::where('id', $id)->first();
            if ($document) {
                $filePath = public_path('file/penahanan/berita-acara-penahanan/' . $document->name);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $document->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Berita Acara Penahanan berhasil dihapus');
    }

    public function download(Request $request, $id)
    {
        $document = BeritaAcaraPenahanan::where('id', $id)->first();
        if (!$document) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan');
        }

        $accidentId = htmlspecialchars($request->query('accident_id', $document->accident_id));
        $accident = Accident::with(['polres.polda'])->where('id', $accidentId)->first();

        if (!$accident) {
            return redirect()->back()->with('error', 'Data Perkara Tidak Ditemukan');
        }

        $destinationDir = public_path('file/penahanan/berita-acara-penahanan');
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $fileName = $document->name ?? ($document->id . ' - Berita Acara Penahanan - ' . strtoupper($accident->polres->full_name ?? '') . '.docx');
        $filePath = $destinationDir . '/' . $fileName;

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileName);
        }

        // Generate on the fly jika fisik file belum ada di disk (sama persis seperti LHGP)
        $documentDate = $document->document_date ? Carbon::parse($document->document_date) : Carbon::now();
        $startDate = $document->start_date ? Carbon::parse($document->start_date) : Carbon::now();
        $endDate = $document->end_date ? Carbon::parse($document->end_date) : Carbon::now()->addDays(19);
        $spHanDate = $document->reference_document_date ? Carbon::parse($document->reference_document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $timezone = Timezone::where('id', $document->timezone_id ?? 1)->first();

        $officerLeader = Officer::withRelated()
            ->selectFullName()
            ->where('id', $document->officer_leader_id)
            ->orWhere('register_number', $document->officer_leader_id)
            ->first();

        $officerLeaderFullName = $officerLeader
            ? strtoupper(trim(($officerLeader->first_title ? $officerLeader->first_title . ' ' : '') . $officerLeader->first_name . ' ' . $officerLeader->last_name . ($officerLeader->last_title ? ', ' . $officerLeader->last_title : '')))
            : '-';

        $suspect = Suspect::with(['gender', 'religion', 'job'])->where('id', $document->suspect_id)->first();

        $blockOfficers = [];
        $officerCounter = 1;
        $internalOfficersNrp = $document->internal_officers ?? [];
        if (!empty($internalOfficersNrp)) {
            foreach ($internalOfficersNrp as $nrp) {
                $off = Officer::withRelated()->selectFullName()->where('register_number', $nrp)->orWhere('id', $nrp)->first();
                if ($off) {
                    $offFullName = strtoupper(trim(($off->first_title ? $off->first_title . ' ' : '') . $off->first_name . ' ' . $off->last_name . ($off->last_title ? ', ' . $off->last_title : '')));
                    $blockOfficers[] = [
                        'personnel_no'       => ($officerCounter++) . '.',
                        'personnel_name'     => $offFullName,
                        'personnel_rank_nrp' => strtoupper($off->rank->name ?? '-') . ' / ' . ($off->register_number ?? '-'),
                        'personnel_position' => strtoupper($off->position->name ?? '-'),
                    ];
                }
            }
        }

        $templatePath = file_exists(public_path('word-template/berita_acara_penahanan.docx'))
            ? public_path('word-template/berita_acara_penahanan.docx')
            : 'word-template/berita_acara_penahanan.docx';
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        if (empty($blockOfficers)) {
            $templateProcessor->cloneBlock('block_officers', 0, true, false);
        } else {
            $templateProcessor->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);
        }

        $templateProcessor->setValue('daerahPoliceFullName',    strtoupper($accident->polres->polda->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceFullName',     strtoupper($accident->polres->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceAddress',      ucwords(strtolower($accident->polres->address ?? '')));
        $templateProcessor->setValue('dayName',                 $documentDate->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('documentDay',             $documentDate->locale('id')->translatedFormat('d'));
        $templateProcessor->setValue('documentMonth',           $documentDate->locale('id')->translatedFormat('F'));
        $templateProcessor->setValue('documentYear',            $documentDate->locale('id')->translatedFormat('Y'));
        $templateProcessor->setValue('documentDateText',        $documentDate->locale('id')->translatedFormat('d-m-Y'));
        $templateProcessor->setValue('time',                    $document->time ?? '09:00');
        $templateProcessor->setValue('timezone',                strtoupper($timezone->name ?? 'WIB'));
        $templateProcessor->setValue('officerLeaderName',       $officerLeaderFullName);
        $templateProcessor->setValue('officerLeaderRank',       strtoupper($officerLeader->rank->name ?? '-'));
        $templateProcessor->setValue('officerLeaderNrp',        $officerLeader->register_number ?? '-');
        $templateProcessor->setValue('investigatorRole',        $document->investigator_role ?? 'Penyidik');
        $templateProcessor->setValue('spHanNumber',             $document->reference_document_number ?? ($document->initial ?? 'SP.Han/01/VIII/RES.1.24/2026/Satreskrim'));
        $templateProcessor->setValue('spHanDate',               $spHanDate);
        $templateProcessor->setValue('suspectName',             strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('suspectNameShort',        strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('suspectIdentityNumber',   $suspect->identity_number ?? '-');
        $templateProcessor->setValue('suspectBirthPlace',       strtoupper($suspect->birth_place ?? '-'));
        $templateProcessor->setValue('suspectBirthDate',        $suspect && $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('suspectGender',           strtoupper($suspect->gender->name ?? '-'));
        $templateProcessor->setValue('suspectJob',              strtoupper($suspect->job->name ?? '-'));
        $templateProcessor->setValue('suspectNationality',      strtoupper($suspect->nationality ?? 'INDONESIA'));
        $templateProcessor->setValue('suspectReligion',         strtoupper($suspect->religion->name ?? '-'));
        $templateProcessor->setValue('suspectAddress',          strtoupper($suspect->address ?? '-'));
        $crimeDetails = $this->getCrimeDetailsFromSprindik($accidentId);
        $crimeDescription = $document->properties['crime_description'] ?? $crimeDetails['crimeDescription'];
        $crimeArticleRaw = $document->properties['crime_article'] ?? $crimeDetails['crimeArticle'];
        $crimeArticle = preg_replace('/^pasal\s+/i', '', trim($crimeArticleRaw));

        $templateProcessor->setValue('crimeDescription',        strtoupper($crimeDescription));
        $templateProcessor->setValue('crimeArticle',            $crimeArticle);
        $templateProcessor->setValue('detentionPlace',          strtoupper($document->detention_place ?? ('Rumah Tahanan Negara ' . ($accident->polres->full_name ?? ''))));
        $templateProcessor->setValue('detentionBranch',         strtoupper($document->detention_branch ?? ($accident->polres->full_name ?? '')));
        $templateProcessor->setValue('startDate',               $startDate->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('endDate',                 $endDate->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('task',                    $document->task ?? 'Melakukan penahanan tersangka sesuai SOP');
        $templateProcessor->setValue('healthCondition',         $document->health_condition ?? 'dalam keadaan sehat');
        $templateProcessor->setValue('place',                   strtoupper($document->place ?? ('Ruang Satreskrim ' . ($accident->polres->full_name ?? ''))));
        $templateProcessor->setValue('suspectSignatureName',    strtoupper($suspect->name ?? '-'));
        $templateProcessor->setValue('officerSignatureTitle',   $document->investigator_role ?? 'Penyidik');
        $templateProcessor->setValue('officerSignatureName',    $officerLeaderFullName);
        $templateProcessor->setValue('officerSignatureRankNrp', ($officerLeader ? strtoupper($officerLeader->rank->name ?? '') . ' NRP. ' . $officerLeader->register_number : '-'));

        $templateProcessor->saveAs($filePath);

        return response()->download($filePath, $fileName);
    }

    public function getInternalOfficers(Request $request)
    {
        $accidentId = $request->accident_id;
        $selectedLeaderOfficerRegisterNumber = $request->selectedLeaderOfficerRegisterNumber;

        try {
            $accident = Accident::with(['polres'])->where('id', $accidentId)->first();
            $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

            $officers = Officer::withRelated()
                ->selectFullName()
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()
                ->active()
                ->valid()
                ->whereIn('officers.police_id', $getOldNewPolresIds)
                ->where('officers.register_number', '!=', $selectedLeaderOfficerRegisterNumber)
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $officers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function validateRequestForm(Request $request)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal, silakan periksa isian form',
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data valid dan siap diproses',
        ], 200);
    }

    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'accidentId' => 'required',
            'suratPerintahPenahananDocument' => 'required',
            'time' => 'required',
            'timezone' => 'required',
            'place' => 'required|max:255',
            'investigatorRole' => 'required',
            'officerLeader' => 'required',
            'suspect' => 'required',
            'detentionPlace' => 'required|max:255',
            'detentionBranch' => 'required|max:255',
            'startDate' => 'required',
            'endDate' => 'required',
            'task' => 'required',
            'healthCondition' => 'required',
            'documentDate' => 'required',
        ], [
            'accidentId.required' => 'Data Perkara harus dipilih',
            'suratPerintahPenahananDocument.required' => 'Surat Perintah Penahanan harus dipilih',
            'time.required' => 'Waktu Pelaksanaan harus diisi',
            'timezone.required' => 'Zona Waktu harus dipilih',
            'place.required' => 'Tempat Pelaksanaan harus diisi',
            'place.max' => 'Tempat Pelaksanaan maksimal 255 karakter',
            'investigatorRole.required' => 'Jabatan Selaku Penyidik harus dipilih',
            'officerLeader.required' => 'Penyidik Pembuat Berita Acara harus dipilih',
            'suspect.required' => 'Tersangka yang Ditahan harus dipilih',
            'detentionPlace.required' => 'Rumah Tahanan Negara harus diisi',
            'detentionPlace.max' => 'Rumah Tahanan Negara maksimal 255 karakter',
            'detentionBranch.required' => 'Cabang Rutan / Satker harus diisi',
            'detentionBranch.max' => 'Cabang Rutan / Satker maksimal 255 karakter',
            'startDate.required' => 'Tanggal Mulai Penahanan harus diisi',
            'endDate.required' => 'Tanggal Berakhir Penahanan harus diisi',
            'task.required' => 'Uraian Pelaksanaan harus diisi',
            'healthCondition.required' => 'Keadaan Kesehatan harus diisi',
            'documentDate.required' => 'Tanggal Ditandatangani Dokumen harus diisi',
        ]);
    }

    private function getCrimeDetailsFromSprindik($accidentId)
    {
        $suratPerintahPenyidikan = SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType'
        ])->where('accident_id', $accidentId)->latest()->first();

        $pasalList = '-';
        $dugaanTindakPidanaList = '-';

        if ($suratPerintahPenyidikan && $suratPerintahPenyidikan->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $laws = $suratPerintahPenyidikan->suratPerintahPenyidikanDocumentLaws;
            $pasalParts = [];
            $dugaanParts = [];

            foreach ($laws as $law) {
                $chapter = trim($law->constitution_chapter ?? '');
                $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));

                if ($law->crimeType && !empty($law->crimeType->name)) {
                    $dugaanParts[] = $law->crimeType->name;
                } elseif (!empty($law->crime_description)) {
                    $dugaanParts[] = $law->crime_description;
                }
            }
            
            $pasalList = implode(', ', array_unique($pasalParts));
            $dugaanTindakPidanaList = !empty($dugaanParts) ? implode(', ', array_unique($dugaanParts)) : '-';
        }

        $cleanArticle = preg_replace('/^pasal\s+/i', '', trim($pasalList));

        return [
            'crimeDescription' => !empty($dugaanTindakPidanaList) ? $dugaanTindakPidanaList : '-',
            'crimeArticle'     => !empty($cleanArticle) ? $cleanArticle : '310 Ayat (4) UU Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan',
        ];
    }
}
