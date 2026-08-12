<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer;
use App\Models\Accident;
use App\Models\Officer;

use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\SuspectSource;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Models\Lib\DocumentClassification;
use App\Models\Suspect;
use App\Models\ReportedPerson;
use App\Models\Informant;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Traits\DocsOfficersTraits;

class SuratPemberitahuanDimulainyaPenyidikanDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function create()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::where('id', $accidentId)->first();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suratPerintahTugasDocuments = SuratPerintahTugasDocument::where('accident_id', $accidentId)
            ->whereHasMorph('related', get_class(new SuratPerintahPenyidikanDocument()))
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

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

        $suspectSources = SuspectSource::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspects = Suspect::with(['suratKetetapanTentangPenetapanTersangkaDocument'])
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $informants = Informant::where('accident_id', $accidentId)->get();

        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)
            ->get();

        $viewData = [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'authorizedSignatories' => $authorizedSignatories,
            'suspectSources' => $suspectSources,
            'prosecutors' => $prosecutors,
            'courts' => $courts,
            'suspectSources' => $suspectSources,
            'documentClassifications' => $documentClassifications,
            'suratPerintahTugasDocuments' => $suratPerintahTugasDocuments,
            'suspects' => $suspects,
            'reportedPersons' => $reportedPersons,
            'informants' => $informants
        ];

        return view('docs.surat-pemberitahuan-dimulainya-penyidikan-document.create', $viewData);
    }

    public function store(Request $request)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get URL Parameter
        $accidentId = htmlspecialchars($request->accident_id);

        // Define & Sanitize Text Input
        $user = Auth::user();
        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = htmlspecialchars($request->documentDate);
        $documentClassificationId = htmlspecialchars($request->documentClassification);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $suratPerintahTugasDocumentId = htmlspecialchars($request->suratPerintahTugasDocument);
        $isSuspectExists = ($request->isSuspectExists == 'true') ? true : false;
        $prosecutorId = htmlspecialchars($request->prosecutor);
        $courtId = htmlspecialchars($request->court);
        $appendix = htmlspecialchars($request->appendix);
        $signatoryId = htmlspecialchars($request->signatory);
        $carbonCopies = $request->carbonCopies;

        $suspects = $request->suspects;

        $informant = $request->informant;
        $reportedPerson = $request->reportedPerson;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Check if document number already exist
        $exists = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::create([
                'accident_id' => $accidentId,
                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                'surat_perintah_tugas_document_id' => $suratPerintahTugasDocumentId,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'document_classification_id' => $documentClassificationId,
                'is_suspect_exists' => $isSuspectExists,
                'prosecutor_id' => $prosecutorId,
                'court_id' => $courtId,
                'appendix' => $appendix,
                'carbon_copies' => $carbonCopies,
                'is_legacy' => $isLegacy,
            ]);

            $suratPemberitahuanDimulainyaPenyidikanDocumentId = $suratPemberitahuanDimulainyaPenyidikanDocument->id;

            // SIGNATORY
            $signatory = Officer::where('id', $signatoryId)->first();
            $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()->create([
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
                'register_number' => $signatory->register_number,

                'first_title' => $signatory->first_title,
                'first_name' => $signatory->first_name,
                'last_name' => $signatory->last_name,
                'last_title' => $signatory->last_title,

                'rank_id' =>  $signatory->rank_id,
                'position_id' =>  $signatory->position_id,
                'phone_number' => $signatory->phone_number,
                'email' => $signatory->email,

                'police_id' => $signatory->police_id,
                'status' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'IMPORT'),
            ]);

            // SUSPECT
            if ($isSuspectExists) {
                foreach ($suspects as $suspect) {
                    $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->attach($suspect);
                }
            } else {
                $suratPemberitahuanDimulainyaPenyidikanDocument->reportedPersons()->attach($reportedPerson);
                $suratPemberitahuanDimulainyaPenyidikanDocument->informants()->attach($informant);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.');
        }

        // Redirect
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function show($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $id;

        $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::withRelated()
            ->where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)
            ->first();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $viewData = [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPemberitahuanDimulainyaPenyidikanDocument' => $suratPemberitahuanDimulainyaPenyidikanDocument,
            'suratPemberitahuanDimulainyaPenyidikanDocumentId' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
        ];

        return view('docs.surat-pemberitahuan-dimulainya-penyidikan-document.show', $viewData);
    }

    public function edit($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $id;

        $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::with(['suratPemberitahuanDimulainyaPenyidikanDocumentOfficers', 'suratPerintahPenyidikanDocument'])->where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)->first();
        $accident = Accident::where('id', $accidentId)->first();

        $selectedSuspects = $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->get()->pluck('id')->toArray();

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

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

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suratPerintahTugasDocuments = SuratPerintahTugasDocument::where('accident_id', $accidentId)
            ->whereHasMorph('related', 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument')
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suspectSources = SuspectSource::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspects = Suspect::with(['suratKetetapanTentangPenetapanTersangkaDocument'])
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $informants = Informant::where('accident_id', $accidentId)->get();

        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)
            ->get();

        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPemberitahuanDimulainyaPenyidikanDocument' => $suratPemberitahuanDimulainyaPenyidikanDocument,
            'suratPemberitahuanDimulainyaPenyidikanDocumentId' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
            'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers' => $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers,
            'ranks' => $ranks,
            'positions' => $positions,
            'officers' => $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suspectSources' => $suspectSources,
            'prosecutors' => $prosecutors,
            'courts' => $courts,
            'suspectSources' => $suspectSources,
            'documentClassifications' => $documentClassifications,
            'suratPerintahTugasDocuments' => $suratPerintahTugasDocuments,
            'suspects' => $suspects,
            'informants' => $informants,
            'reportedPersons' => $reportedPersons,
            'selectedSuspects' => $selectedSuspects,
        ];

        return view('docs.surat-pemberitahuan-dimulainya-penyidikan-document.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $id;

        // Define & Sanitize Text Input
        $user = Auth::user();
        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = htmlspecialchars($request->documentDate);
        $documentClassificationId = htmlspecialchars($request->documentClassification);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $suratPerintahTugasDocumentId = htmlspecialchars($request->suratPerintahTugasDocument);
        $isSuspectExists = ($request->isSuspectExists == 'true') ? true : false;
        $prosecutorId = htmlspecialchars($request->prosecutor);
        $courtId = htmlspecialchars($request->court);
        $appendix = htmlspecialchars($request->appendix);
        $signatoryId = htmlspecialchars($request->signatory);
        $carbonCopies = $request->carbonCopies;

        $suspects = $request->suspects;

        $informant = $request->informant;
        $reportedPerson = $request->reportedPerson;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Check if document number already exist
        $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::findOrFail($id);
        $oldDocumentNumber = $suratPemberitahuanDimulainyaPenyidikanDocument->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $request->input('accident_id'))
                ->where('document_number', 'ILIKE', $documentNumber)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
            }
        }

        DB::beginTransaction();
        try {
            // Update to database
            $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)->first();

            $suratPemberitahuanDimulainyaPenyidikanDocument->update([
                'accident_id' => $accidentId,
                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                'surat_perintah_tugas_document_id' => $suratPerintahTugasDocumentId,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'document_classification_id' => $documentClassificationId,
                'is_suspect_exists' => $isSuspectExists,
                'prosecutor_id' => $prosecutorId,
                'court_id' => $courtId,
                'appendix' => $appendix,
                'carbon_copies' => $carbonCopies,
                'is_legacy' => $isLegacy,
            ]);

            $suratPemberitahuanDimulainyaPenyidikanDocumentId = $suratPemberitahuanDimulainyaPenyidikanDocument->id;

            // SIGNATORY
            $signatory = Officer::where('id', $signatoryId)->first();
            $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()
                ->updateOrCreate(
                    [
                        'class' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    ],
                    [
                        'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
                        'register_number' => $signatory->register_number,

                        'first_title' => $signatory->first_title,
                        'first_name' => $signatory->first_name,
                        'last_name' => $signatory->last_name,
                        'last_title' => $signatory->last_title,

                        'rank_id' => $signatory->rank_id,
                        'position_id' => $signatory->position_id,
                        'phone_number' => $signatory->phone_number,
                        'email' => $signatory->email,

                        'police_id' => $signatory->police_id,
                        'status' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'class' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                        'flag' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                        'insert_method' => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'IMPORT'),
                    ]
                );

            // SUSPECT
            if ($isSuspectExists) {
                $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->detach();
                foreach ($suspects as $suspect) {
                    $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->attach($suspect);
                }
            } else {
                $suratPemberitahuanDimulainyaPenyidikanDocument->reportedPersons()->detach();
                $suratPemberitahuanDimulainyaPenyidikanDocument->informants()->detach();

                $suratPemberitahuanDimulainyaPenyidikanDocument->reportedPersons()->attach($reportedPerson);
                $suratPemberitahuanDimulainyaPenyidikanDocument->informants()->attach($informant);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function delete($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $id;

        DB::beginTransaction();
        try {
            // Delete from database
            $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)->first();
            $suratPemberitahuanDimulainyaPenyidikanDocument->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data.');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function download($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $id;

        $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::withRelated()->where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)->first();
        $signatory = $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers->where('class', '=', SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->first();
        $suspects = $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->get();
        $reportedPersons = $suratPemberitahuanDimulainyaPenyidikanDocument->reportedPersons()->get();

        $isSuspectExist = (!empty($suspects->toArray())) ? true : false;
        $isReportedPersons = (!empty($reportedPersons->toArray())) ? true : false;

        $suspectExistText = '';
        if($isSuspectExist){
            $suspectExistText = 'dengan identitas TERSANGKA sebagai berikut:';
        }elseif($isReportedPersons){
            $suspectExistText = 'dengan identitas TERLAPOR sebagai berikut:';
        }

        $accident = Accident::with(['polres', 'police'])->where('id', $accidentId)->first();

        $tempQrCodePath = storage_path('images/qrcode-signature-' . $suratPemberitahuanDimulainyaPenyidikanDocument->id . '.png');
        QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->merge(public_path('images/logo2x.png'), .2, true)
            ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=' . $suratPemberitahuanDimulainyaPenyidikanDocument->id, $tempQrCodePath);

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name,
        ];

        $signaturePositionName = [
            'KAPOLRES' => '',
            'NO_KAPOLRES' => $signatory->position->positionCluster->alias_name ?? '',
            'NO_DIRLANTAS' => $signatory->position->positionCluster->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_pemberitahuan_dimulainya_penyidikan.docx');

        $signatoryPosition = $signatory->position()->first();
        $signatoryHeadText = 'a.n. <BR/>';
        $signatoryPositionName = 'KASAT LANTAS';
        if (!empty($signatoryPosition)) {
            if ($signatoryPosition->position_cluster_id == '1') {
                $signatoryHeadText = $signatureTitleText['KAPOLRES'];
                $signatoryPositionName = $signaturePositionName['KAPOLRES'];
            } else if ($signatoryPosition->position_cluster_id == '9') {
                $signatoryHeadText = $signatureTitleText['NO_DIRLANTAS'];
                $signatoryPositionName = $signaturePositionName['NO_DIRLANTAS'];
            } else {
                $signatoryHeadText = $signatureTitleText['NO_KAPOLRES'];
                $signatoryPositionName = $signaturePositionName['NO_KAPOLRES'];
            }
        }

        $workUnitName = '';
        if(!empty($accident->police)){
            if($accident->police->class == 'DAERAH'){
                $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
            }else if($accident->police->class == 'RESOR'){
                $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
            }
        }

        $documentDate = Carbon::parse($suratPemberitahuanDimulainyaPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber = $suratPemberitahuanDimulainyaPenyidikanDocument->document_number;
        $appendix = $suratPemberitahuanDimulainyaPenyidikanDocument->appendix;

        $documentClassification = $suratPemberitahuanDimulainyaPenyidikanDocument->documentClassification;
        $documentClassificationName = $documentClassification->name ?? '';

        $accidentNumber = $accident->no_lp;
        $accidentDate = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');
        $reportDate = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y');
        $accidentDay = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l');
        $accidentTime = Carbon::parse($accident->accident_time)->format('H:m');
        $accidentRoad = $accident->road_name;
        $accidentDesc = $accident->damage_lose_desc;

        $prosecutor = $suratPemberitahuanDimulainyaPenyidikanDocument->prosecutor;
        $prosecutorName = $prosecutor->full_name ?? '';
        $prosecutorRegencyName = $prosecutor->regency->name ?? '';

        $prosecutorLocation = ucwords(strtolower($prosecutorRegencyName));

        $no = 1;
        $blockSuspects = [];
        foreach($suspects as $suspect){
            $suspectProperties = $suspect->properties;
            $blockSuspects[] = [
                'suspectName' => $suspect->name ?? '',
                'suspectIdentityNumber' => $suspect->identity_number ?? '',
                'suspectNationality' => $suspect->nationality ?? '',
                'suspectGenderName' => (isset($suspect->gender->name)) ? (($suspectProperties['is_unknown_gender'] == true) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->gender->name)) ) : '',
                'suspectBirthPlace' => (isset($suspect->birth_place)) ? (($suspectProperties['is_unknown_birth_place'] == true) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->birth_place)) ) : '',
                'suspectBirthDate' => (isset($suspect->birth_date)) ? (($suspectProperties['is_unknown_birth_date'] == true) ? 'TIDAK DIKETAHUI' : Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y')) : '',
                'suspectJobName' => (isset($suspect->job->name)) ? ucwords(strtolower($suspect->job->name)) : '',
                'suspectReligionName' => (isset($suspect->religion->name)) ? ucwords(strtolower($suspect->religion->name)) : '',

                'suspectCountryName' => (isset($suspect->country->name)) ? ucwords(strtolower($suspect->country->name)) : '',
                'suspectProvinceName' => (isset($suspect->province->name)) ? ucwords(strtolower($suspect->province->name)) : '',
                'suspectRegencyName' => (isset($suspect->regency->name)) ? ucwords(strtolower($suspect->regency->name)) : '',
                'suspectDistrictName' => (isset($suspect->district->name)) ? ucwords(strtolower($suspect->district->name)) : '',
                'suspectVillageName' => (isset($suspect->village->name)) ? ucwords(strtolower($suspect->village->name)) : '',
                'suspectAddress' => $suspect->address ?? '',
                'suspectFullAddress' => ($suspectProperties['is_unknown_address'] == true) 
                    ? 'TIDAK DIKETAHUI' 
                    : (($suspect->country_id == 'C101') 
                        ? ucwords(strtolower($suspect->address . ', ' . ($suspect->village->name ?? '') . ', ' . ($suspect->district->name ?? '') . ', ' . ($suspect->regency->name ?? '') . ', ' . ($suspect->province->name ?? '')))
                        : ucwords(strtolower($suspect->address . ', ' . ($suspect->country->name ?? '')))),
            ];
        }

        foreach($reportedPersons as $reportedPerson){
            $blockSuspects[] = [
                'suspectName' => $reportedPerson->name ?? '',
                'suspectIdentityNumber' => $reportedPerson->identity_number ?? '',
                'suspectNationality' => $reportedPerson->nationality->name ?? '',
                'suspectGenderName' => (isset($reportedPerson->gender->name)) ? (($reportedPerson->is_unknown_gender == true) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($reportedPerson->gender->name)) ) : '',
                'suspectBirthPlace' => (isset($reportedPerson->birth_place)) ? (($reportedPerson->is_unknown_birth_place == true) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($reportedPerson->birth_place)) ) : '',
                'suspectBirthDate' => (isset($reportedPerson->birth_date)) ? (($reportedPerson->is_unknown_birth_date == true) ? 'TIDAK DIKETAHUI' : Carbon::parse($reportedPerson->birth_date)->locale('id')->translatedFormat('d F Y')) : '',
                'suspectJobName' => (isset($reportedPerson->job->name)) ? ucwords(strtolower($reportedPerson->job->name)) : '',
                'suspectReligionName' => (isset($reportedPerson->religion->name)) ? ucwords(strtolower($reportedPerson->religion->name)) : '',

                'suspectCountryName' => (isset($reportedPerson->country->name)) ? ucwords(strtolower($reportedPerson->country->name)) : '',
                'suspectProvinceName' => (isset($reportedPerson->province->name)) ? ucwords(strtolower($reportedPerson->province->name)) : '',
                'suspectRegencyName' => (isset($reportedPerson->regency->name)) ? ucwords(strtolower($reportedPerson->regency->name)) : '',
                'suspectDistrictName' => (isset($reportedPerson->district->name)) ? ucwords(strtolower($reportedPerson->district->name)) : '',
                'suspectVillageName' => (isset($reportedPerson->village->name)) ? ucwords(strtolower($reportedPerson->village->name)) : '',
                'suspectAddress' => $reportedPerson->address ?? '',
                'suspectFullAddress' => ($reportedPerson->is_unknown_address == true) 
                                            ? 'TIDAK DIKETAHUI' 
                                            : ( ($reportedPerson->country->id == 'C101') 
                                                ? ucwords(strtolower($reportedPerson->address . ', ' . $reportedPerson->village->name . ', ' . $reportedPerson->district->name . ', ' . $reportedPerson->regency->name . ', ' . $reportedPerson->province->name)) 
                                                : ucwords(strtolower($reportedPerson->address . ', ' . $reportedPerson->country->name))
                                            ),
            ];
        }

        $suratPerintahPenyidikanDocument = $suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $suratPerintahPenyidikanDocument->document_number;
        $suratPerintahPenyidikanDocumentDocumentDay = Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('l');
        $suratPerintahPenyidikanDocumentDocumentDate = Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y');

        $daerahPolice = $accident->polres->polda;
        $daerahPoliceFullName = strtoupper($daerahPolice->full_name);

        $resorPolice = $accident->polres;
        $resorPoliceAddress = $resorPolice->address . ', ' . $resorPolice->polres_zipcode;
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);
        $resorPoliceProvinceName = $resorPolice->polres_province;

        $documentLocation = ucwords(strtolower($resorPoliceProvinceName));

        $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
        $signatoryRankName = $signatory->rank->full_name ?? '';
        $signatoryRegisterNumber = $signatory->register_number;

        $carbonCopies = $suratPemberitahuanDimulainyaPenyidikanDocument->carbon_copies ?? [];
        $no = 1;
        $blockCarbonCopies = [];
        foreach ($carbonCopies as $carbonCopy => $value) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => $no,
                'carbon_copy_name' => $value,
            ];

            $no++;
        }

        $references = [
            [
                'reference_iteration' => 'a.',
                'reference_name' => 'Pasal 109 ayat (1) Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana;'
            ],
            [
                'reference_iteration' => 'b.',
                'reference_name' => 'Pasal 16 Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'
            ],
            [
                'reference_iteration' => 'c.',
                'reference_name' => 'Laporan Polisi Nomor : ' . $accidentNumber . ', tanggal ' . $reportDate
            ],
            [
                'reference_iteration' => 'd.',
                'reference_name' => 'Surat Perintah Penyidikan Nomor : ' . $suratPerintahPenyidikanDocumentNumber . ', tanggal ' . $suratPerintahPenyidikanDocumentDocumentDate
            ]
        ];

        $crimeClassText = 'Kejahatan Lalu Lintas';
        $crimeConstitutionText = '';
        
        $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws;
        $countSuratPerintahPenyidikanDocumentLaw = $suratPerintahPenyidikanDocumentLaws->count();
        $suratPerintahPenyidikanDocumentLawIteration = 1;
        foreach($suratPerintahPenyidikanDocumentLaws as $suratPerintahPenyidikanDocumentLaw){
            $coma = ($suratPerintahPenyidikanDocumentLawIteration == $countSuratPerintahPenyidikanDocumentLaw) ? '' : ', ';

            if($suratPerintahPenyidikanDocumentLaw->flag == 'MAIN'){
                $crimeConstitution = $suratPerintahPenyidikanDocumentLaw->crimeConstitution ?? '';
                $crimeConstitutionChapter = $suratPerintahPenyidikanDocumentLaw->constitution_chapter ?? '';
                $crimeConstitutionName = $crimeConstitution->name ?? '';
                $crimeConstitutionText .= $crimeConstitutionChapter . ' ' . $crimeConstitutionName . $coma;
            }elseif($suratPerintahPenyidikanDocumentLaw->flag == 'ADDITIONAL'){
                $crimeConstitution = $suratPerintahPenyidikanDocumentLaw->constitution ?? '';
                $crimeConstitutionText .= $crimeConstitution . $coma;
            }

            $suratPerintahPenyidikanDocumentLawIteration++;
        }

        //===============================================================
        $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);
        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);
        
        $templateProcessor->setImageValue('QRCodeImage', [
            'path' => $tempQrCodePath,
            'width' => 111,
            'height' => 111,
        ]);

        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('appendix', $appendix);
        $templateProcessor->setValue('documentLocation', $documentLocation);

        $templateProcessor->setValue('documentClassificationName', $documentClassificationName);

        $templateProcessor->setValue('isSuspectExist', $isSuspectExist);
        $templateProcessor->setValue('suspectExistText', $suspectExistText);

        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('reportDate', $reportDate);
        $templateProcessor->setValue('accidentDay', $accidentDay);
        $templateProcessor->setValue('accidentTime', $accidentTime);
        $templateProcessor->setValue('accidentRoad', $accidentRoad);
        $templateProcessor->setValue('accidentDesc', $accidentDesc);

        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('prosecutorLocation', $prosecutorLocation);

        $templateProcessor->setValue('workUnitName', $workUnitName);

        $templateProcessor->setValue('crimeClass', $crimeClassText);
        $templateProcessor->setValue('crimeConstitution', $crimeConstitutionText);

        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $suratPerintahPenyidikanDocumentNumber);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDocumentDay', $suratPerintahPenyidikanDocumentDocumentDay);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDocumentDate', $suratPerintahPenyidikanDocumentDocumentDate);

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);

        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);

        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPositionName);

        $filename = 'generate/' . $suratPemberitahuanDimulainyaPenyidikanDocument->id . ' - Surat Pemberitahuan Dimulainya Penyidikan - ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
    }
    
    // =====( API )=====
    public function validateRequestForm(Request $request)
    {
        try {
            $validator = $this->validateForm($request);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Silahkan menunggu proses simpan data',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Terjadi kesalahan pada sistem.',
                'code' => 500,
            ], 500);
        }
    }

    private function validateForm(Request $request)
    {
        $accidentId = $request->query('accident_id');
        $accident = Accident::where('id', $accidentId)->first();

        return Validator::make($request->all(), [
            'documentNumber' => 'required | min:5 | max:255 | regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*\/).+$/',
            'documentDate' => 'required',
            'documentClassification' => 'required',

            'suratPerintahPenyidikanDocument' => 'required',
            'suratPerintahTugasDocument' => 'required',

            'isSuspectExists' => 'required',

            'prosecutor' => 'required',
            'court' => 'required',

            'appendix' => 'required | numeric | max:999 | min:1',

            'signatory' => 'required',

            'suspects' => 'required_if:isSuspectExists,true',

            'reportedPerson' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->specialInfo != 'TABRAK_LARI' && $request->isSuspectExists != 'true';
                })
            ],

            'isLegacy' => [
                // tidak pakai requiredIf karena ini bukan soal required-nya,
                // tapi validasi jika diisi
                function ($attribute, $value, $fail) use ($accident) {
                    // Jika checkbox tidak dicentang, maka nilainya kosong/null => valid
                    if (empty($value)) {
                        return;
                    }

                    $reportDate = strtotime($accident->report_date);
                    $cutoff = strtotime('2024-01-01');

                    $isWhitelisted = $accident->police->is_whitelisted_document_legacy ?? false;
                    $startWhitelist = strtotime($accident->police->start_date_whitelisted_document_legacy ?? '1900-01-01');
                    $endWhitelist = strtotime($accident->police->end_date_whitelisted_document_legacy ?? '2100-01-01');

                    $isValid = $reportDate < $cutoff ||
                            ($isWhitelisted &&
                            $startWhitelist <= $reportDate &&
                            $reportDate <= $endWhitelist);

                    if (!$isValid) {
                        $fail("Keterangan dokumen legacy tidak valid, tanggal laporan harus sebelum 2024 atau termasuk dalam rentang waktu yang diizinkan.");
                    }
                }
            ],

            'carbonCopies' => 'required',
            'carbonCopies.*' => 'required',
        ], [
            'documentNumber.required' => 'Mohon mengisi Nomor Dokumen.',
            'documentNumber.max' => 'No Dokumen maksimal 255 karakter.',
            'documentNumber.min' => 'No Dokumen harus lengkap',
            'documentNumber.regex' => 'No Dokumen harus lengkap',

            'documentDate.required' => 'Mohon mengisi Tanggal Surat.',
            'documentClassification.required' => 'Mohon mengisi Klasifikasi Surat.',

            'suratPerintahPenyidikanDocument.required' => 'Mohon mengisi Surat Perintah Penyidikan.',
            'suratPerintahTugasDocument.required' => 'Mohon mengisi Surat Perintah Tugas.',

            'isSuspectExists.required' => 'Mohon mengisi Apakah tersangka ada.',
            'prosecutor.required' => 'Mohon mengisi Kejaksaan.',
            'court.required' => 'Mohon mengisi Pengadilan.',

            'appendix.required' => 'Mohon mengisi Lampiran.',
            'appendix.numeric' => 'Lampiran harus berupa angka.',
            'appendix.max' => 'Lampiran maksimal 999.',
            'appendix.min' => 'Lampiran minimal 1.',

            'signatory.required' => 'Mohon mengisi Penandatangan.',

            'suspects.required_if' => 'Mohon mengisi Tersangka.',

            'reportedPerson.required' => 'Mohon mengisi Terlapor.',

            'carbonCopies' => 'Mohon mengisi Tembusan.',
            'carbonCopies.*' => 'Mohon Jangan Kosongkan Isi Tembusan, Hapus Jika Memang Tidak Ada.',
        ]);
    }
}
