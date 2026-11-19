<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanUpayaDiversiDocument\SuratPemberitahuanUpayaDiversiDocument;
use App\Models\Doc\SuratPemberitahuanUpayaDiversiDocument\SuratPemberitahuanUpayaDiversiDocumentOfficer;
use App\Models\Accident;
use App\Models\Officer;

use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Models\Lib\DocumentClassification;
use App\Models\Suspect;

use App\Traits\DocsOfficersTraits;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\PeopleNameHelper;

class SuratPemberitahuanUpayaDiversiDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::where('id', $accidentId)->first();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
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

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        // Tersangka pada perkara ini
        $suspects = Suspect::withRelated()
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $viewData = [
            'accidentId'                                      => $accidentId,
            'accident'                                        => $accident,
            'suratPerintahPenyidikanDocuments'                => $suratPerintahPenyidikanDocuments,
            'suratPemberitahuanDimulainyaPenyidikanDocuments' => $suratPemberitahuanDimulainyaPenyidikanDocuments,
            'authorizedSignatories'                           => $authorizedSignatories,
            'prosecutors'                                     => $prosecutors,
            'courts'                                          => $courts,
            'documentClassifications'                         => $documentClassifications,
            'suspects'                                        => $suspects,
        ];

        return view('docs.surat-pemberitahuan-upaya-diversi-document.create', $viewData);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accidentId ?? $request->query('accident_id'));
        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = htmlspecialchars($request->documentDate);
        $documentClassificationId = htmlspecialchars($request->documentClassification);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = htmlspecialchars($request->suratPemberitahuanDimulainyaPenyidikanDocument);
        $suspectId = htmlspecialchars($request->suspectId);
        $prosecutorId = htmlspecialchars($request->prosecutor);
        $courtId = htmlspecialchars($request->court);
        $appendix = htmlspecialchars($request->appendix ?? 0);
        $signatoryId = htmlspecialchars($request->signatory);
        $carbonCopies = $request->carbonCopies ?? [];
        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Cek duplikasi nomor dokumen pada perkara ini
        $exists = SuratPemberitahuanUpayaDiversiDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' sudah dibuat sebelumnya.')->withInput();
        }

        DB::beginTransaction();
        try {
            $suspect = Suspect::find($suspectId);
            $suspectAge = null;
            if ($suspect && !empty($suspect->birth_date)) {
                $birthDate = Carbon::parse($suspect->birth_date);
                $targetDate = !empty($documentDate) ? Carbon::parse($documentDate) : Carbon::now();
                $diff = $birthDate->diff($targetDate);
                $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
            }

            $spudDocument = SuratPemberitahuanUpayaDiversiDocument::create([
                'accident_id'                                           => $accidentId,
                'surat_perintah_penyidikan_document_id'                => $suratPerintahPenyidikanDocumentId,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
                'suspect_id'                                            => $suspectId,
                'document_number'                                       => $documentNumber,
                'document_date'                                         => $documentDate,
                'document_classification_id'                            => $documentClassificationId,
                'prosecutor_id'                                         => $prosecutorId,
                'court_id'                                              => $courtId,
                'appendix'                                              => (int) $appendix,
                'carbon_copies'                                         => $carbonCopies,
                'messages'                                              => $suspectAge ? ['suspect_age' => $suspectAge] : null,
                'is_legacy'                                             => $isLegacy,
                'created_by_user_id'                                    => Auth::id(),
            ]);

            // Simpan Penandatangan (Signatory)
            $signatory = Officer::where('id', $signatoryId)->first();
            if ($signatory) {
                $spudDocument->suratPemberitahuanUpayaDiversiDocumentOfficers()->create([
                    'register_number' => $signatory->register_number,
                    'first_title'     => $signatory->first_title,
                    'first_name'      => $signatory->first_name,
                    'last_name'       => $signatory->last_name,
                    'last_title'      => $signatory->last_title,
                    'rank_id'         => $signatory->rank_id,
                    'position_id'     => $signatory->position_id,
                    'phone_number'    => $signatory->phone_number,
                    'email'           => $signatory->email,
                    'police_id'       => $signatory->police_id,
                    'status'          => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('status', 'PRESENT'),
                    'class'           => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    'flag'            => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    'insert_method'   => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                ]);
            }

            // Update suspect terpilih menjadi is_child = true
            if (!empty($suspectId)) {
                Suspect::where('id', $suspectId)->update(['is_child' => true]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Pemberitahuan Upaya Diversi berhasil dibuat.');
    }

    public function show($id)
    {
        $spudDocument = SuratPemberitahuanUpayaDiversiDocument::withRelated()->where('id', $id)->firstOrFail();
        $accidentId = htmlspecialchars(request()->query('accident_id') ?? $spudDocument->accident_id);
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $viewData = [
            'accidentId'   => $accidentId,
            'accident'     => $accident,
            'spudDocument' => $spudDocument,
        ];

        return view('docs.surat-pemberitahuan-upaya-diversi-document.show', $viewData);
    }

    public function edit($id)
    {
        $spudDocument = SuratPemberitahuanUpayaDiversiDocument::with([
            'suratPemberitahuanUpayaDiversiDocumentOfficers',
            'suspect',
            'suratPerintahPenyidikanDocument',
            'suratPemberitahuanDimulainyaPenyidikanDocument',
        ])->where('id', $id)->firstOrFail();

        $accidentId = htmlspecialchars(request()->query('accident_id') ?? $spudDocument->accident_id);
        $accident = Accident::where('id', $accidentId)->first();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
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

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('sort')->get();
        $courts = Court::where('is_active', true)->orderBy('sort')->get();
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        $suspects = Suspect::withRelated()
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $viewData = [
            'accidentId'                                      => $accidentId,
            'accident'                                        => $accident,
            'spudDocument'                                    => $spudDocument,
            'suratPerintahPenyidikanDocuments'                => $suratPerintahPenyidikanDocuments,
            'suratPemberitahuanDimulainyaPenyidikanDocuments' => $suratPemberitahuanDimulainyaPenyidikanDocuments,
            'authorizedSignatories'                           => $authorizedSignatories,
            'prosecutors'                                     => $prosecutors,
            'courts'                                          => $courts,
            'documentClassifications'                         => $documentClassifications,
            'suspects'                                        => $suspects,
        ];

        return view('docs.surat-pemberitahuan-upaya-diversi-document.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accidentId ?? $request->query('accident_id'));
        $spudDocument = SuratPemberitahuanUpayaDiversiDocument::where('id', $id)->firstOrFail();

        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = htmlspecialchars($request->documentDate);
        $documentClassificationId = htmlspecialchars($request->documentClassification);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = htmlspecialchars($request->suratPemberitahuanDimulainyaPenyidikanDocument);
        $suspectId = htmlspecialchars($request->suspectId);
        $prosecutorId = htmlspecialchars($request->prosecutor);
        $courtId = htmlspecialchars($request->court);
        $appendix = htmlspecialchars($request->appendix ?? 0);
        $signatoryId = htmlspecialchars($request->signatory);
        $carbonCopies = $request->carbonCopies ?? [];
        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);
        $oldDocumentNumber = $spudDocument->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = SuratPemberitahuanUpayaDiversiDocument::where('accident_id', $accidentId)
                ->where('document_number', 'ILIKE', $documentNumber)
                ->exists();
            if ($exists) {
                return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' sudah dibuat sebelumnya.')->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $suspect = Suspect::find($suspectId);
            $suspectAge = null;
            if ($suspect && !empty($suspect->birth_date)) {
                $birthDate = Carbon::parse($suspect->birth_date);
                $targetDate = !empty($documentDate) ? Carbon::parse($documentDate) : Carbon::now();
                $diff = $birthDate->diff($targetDate);
                $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
            }
            $messages = $spudDocument->messages ?? [];
            if ($suspectAge) {
                $messages['suspect_age'] = $suspectAge;
            }

            $spudDocument->update([
                'surat_perintah_penyidikan_document_id'                => $suratPerintahPenyidikanDocumentId,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
                'suspect_id'                                            => $suspectId,
                'document_number'                                       => $documentNumber,
                'document_date'                                         => $documentDate,
                'document_classification_id'                            => $documentClassificationId,
                'prosecutor_id'                                         => $prosecutorId,
                'court_id'                                              => $courtId,
                'appendix'                                              => (int) $appendix,
                'carbon_copies'                                         => $carbonCopies,
                'messages'                                              => $messages,
                'is_legacy'                                             => $isLegacy,
                'updated_by_user_id'                                    => Auth::id(),
            ]);

            // Update Penandatangan (Signatory)
            $signatory = Officer::where('id', $signatoryId)->first();
            if ($signatory) {
                $spudDocument->suratPemberitahuanUpayaDiversiDocumentOfficers()->updateOrCreate(
                    [
                        'class' => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    ],
                    [
                        'register_number' => $signatory->register_number,
                        'first_title'     => $signatory->first_title,
                        'first_name'      => $signatory->first_name,
                        'last_name'       => $signatory->last_name,
                        'last_title'      => $signatory->last_title,
                        'rank_id'         => $signatory->rank_id,
                        'position_id'     => $signatory->position_id,
                        'phone_number'    => $signatory->phone_number,
                        'email'           => $signatory->email,
                        'police_id'       => $signatory->police_id,
                        'status'          => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'flag'            => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                        'insert_method'   => SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                    ]
                );
            }

            // Update suspect terpilih menjadi is_child = true
            if (!empty($suspectId)) {
                Suspect::where('id', $suspectId)->update(['is_child' => true]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Pemberitahuan Upaya Diversi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $spudDocument = SuratPemberitahuanUpayaDiversiDocument::where('id', $id)->firstOrFail();
        $accidentId = htmlspecialchars(request()->query('accident_id') ?? $spudDocument->accident_id);

        DB::beginTransaction();
        try {
            $spudDocument->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Pemberitahuan Upaya Diversi berhasil dihapus.');
    }

    public function download($id)
    {
        $spudDocument = SuratPemberitahuanUpayaDiversiDocument::withRelated()->where('id', $id)->firstOrFail();
        $accidentId = htmlspecialchars(request()->query('accident_id') ?? $spudDocument->accident_id);
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->first();

        $signatory = $spudDocument->suratPemberitahuanUpayaDiversiDocumentOfficers
            ->where('class', SuratPemberitahuanUpayaDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'))
            ->first();



        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? ''),
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? ''),
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . ($accident->polres->polda->full_name ?? ''),
        ];

        $signaturePositionName = [
            'KAPOLRES' => '',
            'NO_KAPOLRES' => $signatory->position->positionCluster->alias_name ?? '',
            'NO_DIRLANTAS' => $signatory->position->positionCluster->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('word-template/surat_pemberitahuan_upaya_diversi.docx'));

        $signatoryPosition = $signatory ? $signatory->position()->first() : null;
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
        if (!empty($accident->police)) {
            if ($accident->police->class == 'DAERAH') {
                $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
            } else if ($accident->police->class == 'RESOR') {
                $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
            }
        }

        $documentDate = Carbon::parse($spudDocument->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber = $spudDocument->document_number;
        $appendix = $spudDocument->appendix ?? '-';

        $documentClassification = $spudDocument->documentClassification;
        $documentClassificationName = $documentClassification->name ?? 'BIASA';

        $accidentNumber = $accident->no_lp ?? '-';
        $accidentDate = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');
        $reportDate = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y');
        $accidentDay = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l');
        $accidentRoad = $accident->road_name ?? '-';

        $prosecutor = $spudDocument->prosecutor;
        $prosecutorName = $prosecutor->full_name ?? ($prosecutor->name ?? '');
        $prosecutorRegencyName = $prosecutor->regency->name ?? '';
        $prosecutorLocation = ucwords(strtolower($prosecutorRegencyName));

        $daerahPolice = $accident->polres->polda ?? null;
        $daerahPoliceFullName = $daerahPolice ? strtoupper($daerahPolice->full_name) : '';

        $resorPolice = $accident->polres;
        $resorPoliceAddress = ($resorPolice->address ?? '') . ', ' . ($resorPolice->polres_zipcode ?? '');
        $resorPoliceFullName = (isset($resorPolice->id) && in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name ?? '');
        $resorPoliceProvinceName = $resorPolice->polres_province ?? '';

        $documentLocation = ucwords(strtolower($resorPoliceProvinceName));

        $signatoryName = $signatory ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title) : '';
        $signatoryRankName = $signatory && $signatory->rank ? $signatory->rank->full_name : '';
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        $suratPerintahPenyidikanDocument = $spudDocument->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $suratPerintahPenyidikanDocument->document_number ?? '-';
        $suratPerintahPenyidikanDocumentDate = $suratPerintahPenyidikanDocument ? Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $suratPemberitahuanDimulainyaPenyidikanDocument = $spudDocument->suratPemberitahuanDimulainyaPenyidikanDocument;
        $suratPemberitahuanDimulainyaPenyidikanDocumentNumber = $suratPemberitahuanDimulainyaPenyidikanDocument->document_number ?? '-';
        $suratPemberitahuanDimulainyaPenyidikanDocumentDate = $suratPemberitahuanDimulainyaPenyidikanDocument ? Carbon::parse($suratPemberitahuanDimulainyaPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $references = [
            [
                'reference_iteration' => 'a.',
                'reference_name' => 'Pasal 16 Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'
            ],
            [
                'reference_iteration' => 'b.',
                'reference_name' => 'Pasal 7 Dan Pasal 8 Ayat (2) Undang-Undang Nomor 11 Tahun 2012 tentang Sistem Peradilan Pidana Anak (SPPA);'
            ],
            [
                'reference_iteration' => 'c.',
                'reference_name' => 'Laporan Polisi Nomor: ' . $accidentNumber . ' tanggal ' . $reportDate . ';'
            ],
            [
                'reference_iteration' => 'd.',
                'reference_name' => 'Surat Perintah Penyidikan Nomor: ' . $suratPerintahPenyidikanDocumentNumber . ' tanggal ' . $suratPerintahPenyidikanDocumentDate . ';'
            ],
            [
                'reference_iteration' => 'e.',
                'reference_name' => 'Surat Pemberitahuan Dimulainya Penyidikan Nomor: ' . $suratPemberitahuanDimulainyaPenyidikanDocumentNumber . ' tanggal ' . $suratPemberitahuanDimulainyaPenyidikanDocumentDate . '.'
            ]
        ];

        $crimeClassText = 'Kejahatan Lalu Lintas';
        $crimeConstitutionText = '';
        if ($suratPerintahPenyidikanDocument) {
            $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws;
            $countLaws = $suratPerintahPenyidikanDocumentLaws ? $suratPerintahPenyidikanDocumentLaws->count() : 0;
            $lawIteration = 1;
            if ($suratPerintahPenyidikanDocumentLaws) {
                foreach ($suratPerintahPenyidikanDocumentLaws as $law) {
                    $comma = ($lawIteration == $countLaws) ? '' : ', ';
                    if ($law->flag == 'MAIN') {
                        $crimeConstitution = $law->crimeConstitution ?? '';
                        $crimeConstitutionChapter = $law->constitution_chapter ?? '';
                        $crimeConstitutionName = $crimeConstitution->name ?? '';
                        $crimeConstitutionText .= $crimeConstitutionChapter . ' ' . $crimeConstitutionName . $comma;
                    } elseif ($law->flag == 'ADDITIONAL') {
                        $crimeConstitution = $law->constitution ?? '';
                        $crimeConstitutionText .= $crimeConstitution . $comma;
                    }
                    $lawIteration++;
                }
            }
        }

        // Suspect identity (Anak)
        $suspect = $spudDocument->suspect;
        $blockSuspects = [];
        if ($suspect) {
            $suspectProperties = $suspect->properties ?? [];

            $suspectAge = $spudDocument->messages['suspect_age'] ?? null;
            if (empty($suspectAge) && !empty($suspect->birth_date) && empty($suspectProperties['is_unknown_birth_date'])) {
                $birthDate = Carbon::parse($suspect->birth_date);
                $targetDate = !empty($spudDocument->document_date) ? Carbon::parse($spudDocument->document_date) : Carbon::now();
                $diff = $birthDate->diff($targetDate);
                $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
            }

            $fullAddress = (!empty($suspectProperties['is_unknown_address']))
                ? 'TIDAK DIKETAHUI'
                : ucwords(strtolower(
                    ($suspect->address ?? '') . ', ' .
                    ($suspect->village->name ?? '') . ', ' .
                    ($suspect->district->name ?? '') . ', ' .
                    ($suspect->regency->name ?? '') . ', ' .
                    ($suspect->province->name ?? '')
                ));

            $blockSuspects[] = [
                'suspectName'           => $suspect->name ?? '',
                'suspectIdentityNumber' => $suspect->identity_number ?? '',
                'suspectNationality'    => $suspect->nationality ?? '',
                'suspectGenderName'     => (isset($suspect->gender->name)) ? ((!empty($suspectProperties['is_unknown_gender'])) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->gender->name))) : '',
                'suspectBirthPlace'     => (isset($suspect->birth_place)) ? ((!empty($suspectProperties['is_unknown_birth_place'])) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->birth_place))) : '',
                'suspectBirthDate'      => (isset($suspect->birth_date)) ? ((!empty($suspectProperties['is_unknown_birth_date'])) ? 'TIDAK DIKETAHUI' : Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y')) : '',
                'suspectAge'            => $suspectAge,
                'suspectJobName'        => (isset($suspect->job->name)) ? ucwords(strtolower($suspect->job->name)) : '',
                'suspectReligionName'   => (isset($suspect->religion->name)) ? ucwords(strtolower($suspect->religion->name)) : '',
                'suspectFullAddress'    => $fullAddress,
            ];
        }

        $carbonCopies = $spudDocument->carbon_copies ?? [];
        $blockCarbonCopies = [];
        $no = 1;
        foreach ($carbonCopies as $cc) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => $no,
                'carbon_copy_name'      => $cc,
            ];
            $no++;
        }

        $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);
        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

        $prosecutorTitle = 'Kepala ' . ucwords(strtolower($prosecutorName));
        $templateProcessor->setValue('prosecutorTitle', $prosecutorTitle);

        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('appendix', $appendix);
        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('documentClassificationName', $documentClassificationName);

        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('reportDate', $reportDate);
        $templateProcessor->setValue('accidentDay', $accidentDay);
        $templateProcessor->setValue('accidentRoad', $accidentRoad);

        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('prosecutorLocation', $prosecutorLocation);

        $templateProcessor->setValue('workUnitName', $workUnitName);

        $templateProcessor->setValue('crimeClass', $crimeClassText);
        $templateProcessor->setValue('crimeConstitution', $crimeConstitutionText);

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);

        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPositionName);

        $filename = 'generate/' . $spudDocument->id . ' - Surat Pemberitahuan Upaya Diversi - ' . ($accident->polres->full_name ?? '');
        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0755, true);
        }
        $fullPath = public_path($filename . '.docx');
        $templateProcessor->saveAs($fullPath);
        return response()->download($fullPath)->deleteFileAfterSend(true);
    }

    public function generateWord(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id') ?? $request->input('accidentId'));
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->first();

        // Signatory dari form input
        $signatoryId = $request->input('signatory');
        $signatory = $signatoryId ? Officer::withRelated()->find($signatoryId) : null;



        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? ''),
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? ''),
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . ($accident->polres->polda->full_name ?? ''),
        ];

        $signaturePositionName = [
            'KAPOLRES' => '',
            'NO_KAPOLRES' => $signatory->position->positionCluster->alias_name ?? '',
            'NO_DIRLANTAS' => $signatory->position->positionCluster->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('word-template/surat_pemberitahuan_upaya_diversi.docx'));

        $signatoryPosition = $signatory ? $signatory->position()->first() : null;
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
        if (!empty($accident->police)) {
            if ($accident->police->class == 'DAERAH') {
                $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
            } else if ($accident->police->class == 'RESOR') {
                $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
            }
        }

        $inputDocDate = $request->input('documentDate');
        $documentDate = !empty($inputDocDate) ? Carbon::parse($inputDocDate)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y');
        $documentNumber = $request->input('documentNumber') ?? 'SPUD/00/X/2026/Lantas';
        $appendix = $request->input('appendix') ?? '-';

        $documentClassificationId = $request->input('documentClassification');
        $documentClassification = $documentClassificationId ? DocumentClassification::find($documentClassificationId) : null;
        $documentClassificationName = $documentClassification ? $documentClassification->name : 'BIASA';

        $accidentNumber = $accident->no_lp ?? '-';
        $accidentDate = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');
        $reportDate = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y');
        $accidentDay = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l');
        $accidentRoad = $accident->road_name ?? '-';

        $prosecutorId = $request->input('prosecutor');
        $prosecutor = $prosecutorId ? Prosecutor::find($prosecutorId) : null;
        $prosecutorName = $prosecutor ? ($prosecutor->full_name ?? ($prosecutor->name ?? '')) : 'KEJAKSAAN NEGERI';
        $prosecutorRegencyName = ($prosecutor && $prosecutor->regency) ? $prosecutor->regency->name : '';
        $prosecutorLocation = ucwords(strtolower($prosecutorRegencyName ?: ($accident->polres->polres_district ?? 'Tempat')));

        $daerahPolice = $accident->polres->polda ?? null;
        $daerahPoliceFullName = $daerahPolice ? strtoupper($daerahPolice->full_name) : '';

        $resorPolice = $accident->polres;
        $resorPoliceAddress = ($resorPolice->address ?? '') . ', ' . ($resorPolice->polres_zipcode ?? '');
        $resorPoliceFullName = (isset($resorPolice->id) && in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name ?? '');
        $resorPoliceProvinceName = $resorPolice->polres_province ?? '';

        $documentLocation = ucwords(strtolower($resorPoliceProvinceName));

        $signatoryName = $signatory ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title) : '';
        $signatoryRankName = $signatory && $signatory->rank ? $signatory->rank->full_name : '';
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        $suratPerintahPenyidikanDocumentId = $request->input('suratPerintahPenyidikanDocument');
        $suratPerintahPenyidikanDocument = $suratPerintahPenyidikanDocumentId ? SuratPerintahPenyidikanDocument::find($suratPerintahPenyidikanDocumentId) : SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->first();
        $suratPerintahPenyidikanDocumentNumber = $suratPerintahPenyidikanDocument->document_number ?? '-';
        $suratPerintahPenyidikanDocumentDate = $suratPerintahPenyidikanDocument ? Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $request->input('suratPemberitahuanDimulainyaPenyidikanDocument');
        $suratPemberitahuanDimulainyaPenyidikanDocument = $suratPemberitahuanDimulainyaPenyidikanDocumentId ? SuratPemberitahuanDimulainyaPenyidikanDocument::find($suratPemberitahuanDimulainyaPenyidikanDocumentId) : SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)->first();
        $suratPemberitahuanDimulainyaPenyidikanDocumentNumber = $suratPemberitahuanDimulainyaPenyidikanDocument->document_number ?? '-';
        $suratPemberitahuanDimulainyaPenyidikanDocumentDate = $suratPemberitahuanDimulainyaPenyidikanDocument ? Carbon::parse($suratPemberitahuanDimulainyaPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $references = [
            [
                'reference_iteration' => 'a.',
                'reference_name' => 'Pasal 16 Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'
            ],
            [
                'reference_iteration' => 'b.',
                'reference_name' => 'Pasal 7 Dan Pasal 8 Ayat (2) Undang-Undang Nomor 11 Tahun 2012 tentang Sistem Peradilan Pidana Anak (SPPA);'
            ],
            [
                'reference_iteration' => 'c.',
                'reference_name' => 'Laporan Polisi Nomor: ' . $accidentNumber . ' tanggal ' . $reportDate . ';'
            ],
            [
                'reference_iteration' => 'd.',
                'reference_name' => 'Surat Perintah Penyidikan Nomor: ' . $suratPerintahPenyidikanDocumentNumber . ' tanggal ' . $suratPerintahPenyidikanDocumentDate . ';'
            ],
            [
                'reference_iteration' => 'e.',
                'reference_name' => 'Surat Pemberitahuan Dimulainya Penyidikan Nomor: ' . $suratPemberitahuanDimulainyaPenyidikanDocumentNumber . ' tanggal ' . $suratPemberitahuanDimulainyaPenyidikanDocumentDate . '.'
            ]
        ];

        $crimeClassText = 'Kejahatan Lalu Lintas';
        $crimeConstitutionText = '';
        if ($suratPerintahPenyidikanDocument) {
            $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws;
            $countLaws = $suratPerintahPenyidikanDocumentLaws ? $suratPerintahPenyidikanDocumentLaws->count() : 0;
            $lawIteration = 1;
            if ($suratPerintahPenyidikanDocumentLaws) {
                foreach ($suratPerintahPenyidikanDocumentLaws as $law) {
                    $comma = ($lawIteration == $countLaws) ? '' : ', ';
                    if ($law->flag == 'MAIN') {
                        $crimeConstitution = $law->crimeConstitution ?? '';
                        $crimeConstitutionChapter = $law->constitution_chapter ?? '';
                        $crimeConstitutionName = $crimeConstitution->name ?? '';
                        $crimeConstitutionText .= $crimeConstitutionChapter . ' ' . $crimeConstitutionName . $comma;
                    } elseif ($law->flag == 'ADDITIONAL') {
                        $crimeConstitution = $law->constitution ?? '';
                        $crimeConstitutionText .= $crimeConstitution . $comma;
                    }
                    $lawIteration++;
                }
            }
        }

        // Suspect identity (Anak)
        $suspectId = $request->input('suspectId');
        $suspect = $suspectId ? Suspect::withRelated()->find($suspectId) : Suspect::where('accident_id', $accidentId)->first();
        $blockSuspects = [];
        if ($suspect) {
            $suspectProperties = $suspect->properties ?? [];

            $suspectAge = '-';
            if (!empty($suspect->birth_date) && empty($suspectProperties['is_unknown_birth_date'])) {
                $birthDate = Carbon::parse($suspect->birth_date);
                $targetDate = !empty($request->input('documentDate')) ? Carbon::parse($request->input('documentDate')) : Carbon::now();
                $diff = $birthDate->diff($targetDate);
                $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
            }

            $fullAddress = (!empty($suspectProperties['is_unknown_address']))
                ? 'TIDAK DIKETAHUI'
                : ucwords(strtolower(
                    ($suspect->address ?? '') . ', ' .
                    ($suspect->village->name ?? '') . ', ' .
                    ($suspect->district->name ?? '') . ', ' .
                    ($suspect->regency->name ?? '') . ', ' .
                    ($suspect->province->name ?? '')
                ));

            $blockSuspects[] = [
                'suspectName'           => $suspect->name ?? '',
                'suspectIdentityNumber' => $suspect->identity_number ?? '',
                'suspectNationality'    => $suspect->nationality ?? '',
                'suspectGenderName'     => (isset($suspect->gender->name)) ? ((!empty($suspectProperties['is_unknown_gender'])) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->gender->name))) : '',
                'suspectBirthPlace'     => (isset($suspect->birth_place)) ? ((!empty($suspectProperties['is_unknown_birth_place'])) ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->birth_place))) : '',
                'suspectBirthDate'      => (isset($suspect->birth_date)) ? ((!empty($suspectProperties['is_unknown_birth_date'])) ? 'TIDAK DIKETAHUI' : Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y')) : '',
                'suspectAge'            => $suspectAge,
                'suspectJobName'        => (isset($suspect->job->name)) ? ucwords(strtolower($suspect->job->name)) : '',
                'suspectReligionName'   => (isset($suspect->religion->name)) ? ucwords(strtolower($suspect->religion->name)) : '',
                'suspectFullAddress'    => $fullAddress,
            ];
        }

        $carbonCopies = $request->input('carbonCopies') ?? [];
        $courtId = $request->input('court');
        if (empty($carbonCopies) && $courtId) {
            $court = Court::find($courtId);
            if ($court) {
                $carbonCopies[] = 'Ketua ' . ucwords(strtolower($court->name));
            }
        }

        $blockCarbonCopies = [];
        $no = 1;
        foreach ($carbonCopies as $cc) {
            if (!empty($cc)) {
                $blockCarbonCopies[] = [
                    'carbon_copy_iteration' => $no,
                    'carbon_copy_name'      => $cc,
                ];
                $no++;
            }
        }

        $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);
        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

        $prosecutorTitle = 'Kepala ' . ucwords(strtolower($prosecutorName));
        $templateProcessor->setValue('prosecutorTitle', $prosecutorTitle);

        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('appendix', $appendix);
        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('documentClassificationName', $documentClassificationName);

        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('reportDate', $reportDate);
        $templateProcessor->setValue('accidentDay', $accidentDay);
        $templateProcessor->setValue('accidentRoad', $accidentRoad);

        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('prosecutorLocation', $prosecutorLocation);

        $templateProcessor->setValue('workUnitName', $workUnitName);

        $templateProcessor->setValue('crimeClass', $crimeClassText);
        $templateProcessor->setValue('crimeConstitution', $crimeConstitutionText);

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);

        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPositionName);

        $filename = 'generate/Preview - Surat Pemberitahuan Upaya Diversi - ' . ($accident->polres->full_name ?? '');
        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0755, true);
        }
        $fullPath = public_path($filename . '.docx');
        $templateProcessor->saveAs($fullPath);
        return response()->download($fullPath)->deleteFileAfterSend(true);
    }

    public function apiValidateRequestForm(Request $request)
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
        return Validator::make($request->all(), [
            'documentNumber'                                   => 'required|min:3|max:255',
            'documentDate'                                     => 'required',
            'documentClassification'                           => 'required',
            'suratPerintahPenyidikanDocument'                  => 'required',
            'suratPemberitahuanDimulainyaPenyidikanDocument'   => 'required',
            'suspectId'                                        => 'required',
            'prosecutor'                                       => 'required',
            'court'                                            => 'required',
            'appendix'                                         => 'required|numeric|min:0',
            'signatory'                                        => 'required',
        ], [
            'documentNumber.required'                                 => 'Mohon mengisi Nomor Dokumen.',
            'documentNumber.min'                                      => 'Nomor Dokumen minimal 3 karakter.',
            'documentNumber.max'                                      => 'Nomor Dokumen maksimal 255 karakter.',
            'documentDate.required'                                   => 'Mohon mengisi Tanggal Ditandatangani Dokumen.',
            'documentClassification.required'                         => 'Mohon memilih Klasifikasi Surat.',
            'suratPerintahPenyidikanDocument.required'                => 'Mohon memilih Surat Perintah Penyidikan.',
            'suratPemberitahuanDimulainyaPenyidikanDocument.required' => 'Mohon memilih Surat Pemberitahuan Dimulainya Penyidikan (SPDP).',
            'suspectId.required'                                      => 'Mohon memilih Tersangka Anak.',
            'prosecutor.required'                                     => 'Mohon memilih Kejaksaan Penerima.',
            'court.required'                                          => 'Mohon memilih Pengadilan sebagai Tembusan.',
            'appendix.required'                                       => 'Mohon mengisi Jumlah Lampiran.',
            'signatory.required'                                      => 'Mohon memilih Penandatangan Surat.',
        ]);
    }
}
