<?php

namespace App\Http\Controllers\Docs;

use App\Helpers\PeopleNameHelper;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocumentOfficer;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Officer;
use App\Models\Suspect;
use App\Services\Doc\DocService;
use Carbon\Carbon;
use App\Traits\DocsOfficersTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPerintahPenangkapanDocumentController extends Controller
{
    use DocsOfficersTraits;

    public function __construct(
        private DocService $docService
    ) {
    }

    public function create(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        if (! $accident) {
            return redirect()->back()->with('error', 'Data perkara tidak ditemukan');
        }

        $suspects = Suspect::with(['gender', 'job', 'religion', 'regency', 'country'])
            ->where('accident_id', $accidentId)
            ->orderBy('name')
            ->get();

        $leaderOfficers = $this->leaderOfficersForAccident($accident);

        $authorizedSignatories = collect();
        $submitterOfficers = collect();
        try {
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
            $submitterOfficers = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->whereHasUserActive()
                ->active()
                ->valid()
                ->orderBy('first_name')
                ->get();
        } catch (\Throwable $e) {
            $authorizedSignatories = collect();
            $submitterOfficers = collect();
        }

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderByDesc('document_date')
            ->get();

        $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderByDesc('document_date')
            ->get();

        $suratPerintahTugasDocuments = $this->suratPerintahTugasDocumentsForPenangkapan($accidentId)->get();

        return view('docs.surat-perintah-penangkapan-document.create', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suspects' => $suspects,
            'defaults' => [],
            'leaderOfficers' => $leaderOfficers,
            'authorizedSignatories' => $authorizedSignatories,
            'submitterOfficers' => $submitterOfficers,
            'kepadaPrefillLeaderId' => null,
            'kepadaInternalRows' => collect(),
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratKetetapanTentangPenetapanTersangkaDocuments' => $suratKetetapanTentangPenetapanTersangkaDocuments,
            'suratPerintahTugasDocuments' => $suratPerintahTugasDocuments,
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);

        DB::beginTransaction();
        try {
            $validUntil = $request->validUntilDate;

            $doc = SuratPerintahPenangkapanDocument::create([
                'accident_id' => $accidentId,
                'suspect_id' => $request->suspect,
                'document_number' => htmlspecialchars($request->documentNumber ?? ''),
                'document_date' => $request->documentDate,
                'sprindik_document_id' => $request->suratPerintahPenyidikanDocument,
                'sket_document_id' => $request->suratKetetapanTentangPenetapanTersangkaDocument,
                'surat_perintah_tugas_document_id' => $request->suratPerintahTugasDocument,
                'valid_until_date' => $validUntil,
                'payload' => $this->buildPayload($request, $accidentId),
                'created_by_user_id' => Auth::id(),
                'ip_addresses' => [$request->ip()],
            ]);

            $this->syncNormalizedData($doc, $request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.')->withInput();
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penangkapan berhasil disimpan.');
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $doc = SuratPerintahPenangkapanDocument::with(['createdByUser', 'documentCategory', 'suspect'])->where('id', $id)->firstOrFail();

        $suspects = Suspect::with(['gender', 'job', 'religion', 'regency', 'country'])
            ->where('accident_id', $accidentId)
            ->orderBy('name')
            ->get();

        $defaults = $doc->payload ?? [];
        $leaderOfficers = $this->leaderOfficersForAccident($accident);

        $authorizedSignatories = collect();
        $submitterOfficers = collect();
        try {
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
            $submitterOfficers = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->whereHasUserActive()
                ->active()
                ->valid()
                ->orderBy('first_name')
                ->get();
        } catch (\Throwable $e) {
            $authorizedSignatories = collect();
            $submitterOfficers = collect();
        }

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderByDesc('document_date')
            ->get();
        $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderByDesc('document_date')
            ->get();

        $suratPerintahTugasDocuments = $this->suratPerintahTugasDocumentsForPenangkapan($accidentId)->get();
        if ($doc->surat_perintah_tugas_document_id) {
            $currentSpt = SuratPerintahTugasDocument::where('accident_id', $accidentId)
                ->where('id', $doc->surat_perintah_tugas_document_id)
                ->first();
            if ($currentSpt && ! $suratPerintahTugasDocuments->contains('id', $currentSpt->id)) {
                $suratPerintahTugasDocuments = $suratPerintahTugasDocuments->prepend($currentSpt);
            }
        }

        $kepadaPrefillLeaderId = null;
        $kepadaInternalRows = collect();
        if (old('officerLeader')) {
            $kepadaPrefillLeaderId = old('officerLeader');
            $regs = old('internalOfficers', []);
            $kepadaInternalRows = $this->officersByRegisterNumbersOrdered($regs);
        } elseif (isset($defaults['kepada']) && is_array($defaults['kepada'])) {
            $kepadaPrefillLeaderId = $defaults['kepada']['officer_leader_id'] ?? null;
            $regs = $defaults['kepada']['internal_officers'] ?? [];
            $kepadaInternalRows = $this->officersByRegisterNumbersOrdered($regs);
        }

        return view('docs.surat-perintah-penangkapan-document.edit', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'doc' => $doc,
            'suspects' => $suspects,
            'defaults' => $defaults,
            'leaderOfficers' => $leaderOfficers,
            'authorizedSignatories' => $authorizedSignatories,
            'submitterOfficers' => $submitterOfficers,
            'kepadaPrefillLeaderId' => $kepadaPrefillLeaderId,
            'kepadaInternalRows' => $kepadaInternalRows,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratKetetapanTentangPenetapanTersangkaDocuments' => $suratKetetapanTentangPenetapanTersangkaDocuments,
            'suratPerintahTugasDocuments' => $suratPerintahTugasDocuments,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateForm($request, $id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);

        $doc = SuratPerintahPenangkapanDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $validUntil = $request->validUntilDate;

            $doc->update([
                'suspect_id' => $request->suspect,
                'document_number' => htmlspecialchars($request->documentNumber ?? ''),
                'document_date' => $request->documentDate,
                'sprindik_document_id' => $request->suratPerintahPenyidikanDocument,
                'sket_document_id' => $request->suratKetetapanTentangPenetapanTersangkaDocument,
                'surat_perintah_tugas_document_id' => $request->suratPerintahTugasDocument,
                'valid_until_date' => $validUntil,
                'payload' => $this->buildPayload($request, $accidentId, is_array($doc->payload) ? $doc->payload : []),
                'updated_by_user_id' => Auth::id(),
            ]);

            $this->syncNormalizedData($doc, $request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data')->withInput();
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penangkapan berhasil diperbarui.');
    }

    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $doc = SuratPerintahPenangkapanDocument::with(['documentCategory', 'suspect'])->where('id', $id)->firstOrFail();
        $defaults = $doc->payload ?? [];

        return view('docs.surat-perintah-penangkapan-document.show', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'doc' => $doc,
            'defaults' => $defaults,
        ]);
    }

    public function download($id)
    {
        $accidentId = htmlspecialchars((string) request()->query('accident_id'));

        $templatePath = public_path('word-template/surat_perintah_penangkapan.docx');
        if (! is_file($templatePath)) {
            return redirect()->back()->with('error', 'Template Word belum tersedia: surat_perintah_penangkapan.docx');
        }

        $doc = SuratPerintahPenangkapanDocument::with([
            'officers.rank',
            'officers.position',
            'suspect.gender',
            'suspect.job',
            'suspect.religion',
            'suspect.country',
            'suspect.province',
            'suspect.regency',
            'suspect.district',
            'suspect.village',
            'accident.polres.polda',
        ])->where('id', $id)->firstOrFail();

        if ((string) $doc->accident_id !== (string) $accidentId) {
            abort(404);
        }

        $accident = $doc->accident;
        if (! $accident) {
            return redirect()->back()->with('error', 'Data perkara tidak ditemukan.');
        }

        $signatoryRow = $doc->officers->firstWhere('class', SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'SIGNATORY'));
        $leaderRow = $doc->officers->firstWhere('class', SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'LEADER'))
            ?? $doc->officers->firstWhere('class', SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'MEMBER'));
        $submittedRow = $doc->officers->firstWhere('class', SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'SUBMITTED'));

        $signatoryOfficerModel = $signatoryRow?->officer_id
            ? Officer::withRelated()->find($signatoryRow->officer_id)
            : null;

        $sprindik = $doc->sprindik_document_id
            ? SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->where('id', $doc->sprindik_document_id)->first()
            : null;

        $suspect = $doc->suspect;

        try {
            $tp = new TemplateProcessor($templatePath);

            $daerahPolice = $accident->polres?->polda;
            $daerahPoliceFullName = $daerahPolice?->full_name ?? '';
            $resorPolice = $accident->polres;
            $resorPoliceAddress = trim(($resorPolice->address ?? '').', '.($resorPolice->polres_zipcode ?? ''), ' ,');
            $resorPoliceFullName = ($resorPolice && in_array($resorPolice->id, ['1114'], true))
                ? 'DIREKTORAT LALU LINTAS'
                : ('RESOR '.strtoupper((string) ($resorPolice->full_name ?? '')));

            $documentLocation = ucwords(strtolower(
                $resorPolice?->polres_district ?? $resorPolice?->polres_province ?? ''
            ));

            $signatoryHeadText = '';
            $signatoryPositionHeadText = '';
            if ($signatoryOfficerModel && isset($signatoryOfficerModel->position)) {
                $signatoryHeadTextMap = [
                    'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR '.$accident->polres->full_name,
                    'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR '.$accident->polres->full_name,
                    'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA '.$accident->polres->polda->full_name,
                ];
                $alias = $signatoryOfficerModel->position?->positionCluster?->alias_name ?? '';
                $signatoryPositionHeadTextMap = [
                    'NO_KAPOLRES' => $alias,
                    'NO_DIRLANTAS' => $alias,
                ];
                $clusterId = (string) ($signatoryOfficerModel->position->position_cluster_id ?? '');
                if ($clusterId === '1') {
                    $signatoryHeadText = $signatoryHeadTextMap['KAPOLRES'];
                    $signatoryPositionHeadText = '';
                } elseif ($clusterId === '9') {
                    $signatoryHeadText = $signatoryHeadTextMap['NO_DIRLANTAS'];
                    $signatoryPositionHeadText = $signatoryPositionHeadTextMap['NO_DIRLANTAS'];
                } else {
                    $signatoryHeadText = $signatoryHeadTextMap['NO_KAPOLRES'];
                    $signatoryPositionHeadText = $signatoryPositionHeadTextMap['NO_KAPOLRES'];
                }
            }

            $reportDateRaw = $accident->report_date ?? $accident->accident_date;
            $accidentDateFormatted = $reportDateRaw
                ? Carbon::parse($reportDateRaw)->locale('id')->translatedFormat('d F Y')
                : '-';

            $documentDateFormatted = $doc->document_date
                ? Carbon::parse($doc->document_date)->locale('id')->translatedFormat('d F Y')
                : '-';

            $sprindikNum = $sprindik?->document_number ?? '-';
            $sprindikDocDate = $sprindik?->document_date
                ? Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('d F Y')
                : '-';

            $suspectName = $suspect?->name ?? '';
            $suspectIdentityNumber = $suspect?->identity_number ?? '';
            $suspectBirthPlace = $suspect ? ($suspect->birth_place ?? $suspect->place_of_birth ?? '') : '';
            $bdRaw = $suspect ? ($suspect->birth_date ?? $suspect->date_of_birth ?? null) : null;
            $suspectBirthDate = $bdRaw
                ? Carbon::parse($bdRaw)->locale('id')->translatedFormat('d F Y')
                : '-';
            $suspectGenderName = $suspect?->gender?->name ?? '';
            $suspectReligionName = $suspect?->religion?->name ?? '';
            $suspectJobName = $suspect?->job?->name ?? '';
            $suspectNationality = '';
            if ($suspect?->country) {
                $suspectNationality = (string) ($suspect->country->full_name ?? $suspect->country->name ?? '');
            }

            $suspectAddress = (string) ($suspect?->address ?? '');
            $suspectProvinceName = $suspect?->province ? ', '.$suspect->province->name : '';
            $suspectRegencyName = $suspect?->regency ? ', '.$suspect->regency->name : '';
            $suspectDistrictName = $suspect?->district ? ', '.$suspect->district->name : '';
            $suspectVillageName = $suspect?->village ? ', '.$suspect->village->name : '';
            $suspectFullAddress = ucwords(strtolower($suspectAddress.$suspectVillageName.$suspectDistrictName.$suspectRegencyName.$suspectProvinceName));

            $signatoryName = $signatoryRow
                ? PeopleNameHelper::getFullName(
                    $signatoryRow->first_title,
                    $signatoryRow->first_name,
                    $signatoryRow->last_name,
                    $signatoryRow->last_title
                )
                : '';
            $signatoryRankName = $signatoryRow?->rank?->name ?? '';
            $signatoryRegisterNumber = $signatoryRow?->register_number ?? '';

            $orderRecipientName = $leaderRow
                ? PeopleNameHelper::getFullName(
                    $leaderRow->first_title,
                    $leaderRow->first_name,
                    $leaderRow->last_name,
                    $leaderRow->last_title
                )
                : $signatoryName;
            $orderRecipientRankName = $leaderRow?->rank?->name ?? '';
            $orderRecipientRegisterNumber = $leaderRow?->register_number ?? '';

            $submittedTarget = $submittedRow ?? $signatoryRow;
            $submittedName = $submittedTarget
                ? PeopleNameHelper::getFullName(
                    $submittedTarget->first_title,
                    $submittedTarget->first_name,
                    $submittedTarget->last_name,
                    $submittedTarget->last_title
                )
                : '';
            $submittedRankName = $submittedTarget?->rank?->name ?? '';
            $submittedRegisterNumber = $submittedTarget?->register_number ?? '';

            $tp->setValue('daerahPoliceFullName', strtoupper($daerahPoliceFullName));
            $tp->setValue('resorPoliceFullName', strtoupper((string) $resorPoliceFullName));
            $tp->setValue('resorPoliceAddress', $resorPoliceAddress);
            $tp->setValue('documentNumber', (string) ($doc->document_number ?? ''));
            $tp->setValue('accidentNumber', (string) ($accident->no_lp ?? ''));
            $tp->setValue('accidentDate', $accidentDateFormatted);
            $tp->setValue('suratPerintahPenyidikanNumber', $sprindikNum);
            $tp->setValue('suratPerintahPenyidikanDocumentDate', $sprindikDocDate);

            $payload = $doc->payload ?? [];
            $manualDasar = $payload['manual_dasar'] ?? [];
            $crimePayload = $payload['crime'] ?? [];

            $sptFromDb = $doc->surat_perintah_tugas_document_id
                ? (
                    SuratPerintahTugasDocument::where('accident_id', $accidentId)
                        ->where('id', $doc->surat_perintah_tugas_document_id)
                        ->where('related_type', SuratPerintahPenyidikanDocument::class)
                        ->first()
                    ?? SuratPerintahTugasDocument::where('accident_id', $accidentId)
                        ->where('id', $doc->surat_perintah_tugas_document_id)
                        ->first()
                )
                : null;
            if ($sptFromDb) {
                $sptNum = (string) ($sptFromDb->document_number ?? '');
                $sptDateFmt = $sptFromDb->document_date
                    ? Carbon::parse($sptFromDb->document_date)->locale('id')->translatedFormat('d F Y')
                    : '-';
            } else {
                $sptNum = (string) ($manualDasar['surat_perintah_tugas_number'] ?? '');
                $sptDateRaw = $manualDasar['surat_perintah_tugas_date'] ?? null;
                $sptDateFmt = $sptDateRaw
                    ? Carbon::parse($sptDateRaw)->locale('id')->translatedFormat('d F Y')
                    : '-';
            }

            $sketFromDb = $doc->sket_document_id
                ? SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)->where('id', $doc->sket_document_id)->first()
                : null;
            $sketNumLine = (string) ($sketFromDb?->document_number ?? ($manualDasar['sket_number'] ?? ''));
            $sketDateSource = $sketFromDb?->document_date ?? ($manualDasar['sket_date'] ?? null);
            $sketDateFmt = $sketDateSource
                ? Carbon::parse($sketDateSource)->locale('id')->translatedFormat('d F Y')
                : '-';
            $sketAtasNama = (string) ($suspect?->name ?? ($manualDasar['sket_atas_nama'] ?? ''));

            $tp->setValue('suratPerintahTugasNumber', $sptNum);
            $tp->setValue('suratPerintahTugasDateFormatted', $sptDateFmt);
            $tp->setValue('sketNumber', $sketNumLine);
            $tp->setValue('sketDateFormatted', $sketDateFmt);
            $tp->setValue('sketAtasNama', $sketAtasNama);

            $crimeDesc = trim((string) ($crimePayload['description'] ?? ''));
            $crimeArt  = trim((string) ($crimePayload['articles'] ?? ''));

            // Auto-fill dari Sprindik jika payload kosong — konsisten dengan SPPP
            if (($crimeDesc === '' || $crimeArt === '') && $doc->sprindik_document_id) {
                try {
                    $sprindik = SuratPerintahPenyidikanDocument::with([
                        'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
                    ])->where('id', $doc->sprindik_document_id)->first();

                    if ($sprindik) {
                        $lawTextParts = [];
                        $ayatTexts    = [];

                        foreach (($sprindik->suratPerintahPenyidikanDocumentLaws ?? collect()) as $law) {
                            if (($law->flag ?? '') !== 'MAIN') {
                                continue;
                            }
                            $chapter          = trim((string) ($law->constitution_chapter ?? ''));
                            $constitutionName = trim((string) ($law->crimeConstitution?->name ?? ''));
                            $line             = trim($chapter.' '.$constitutionName);
                            if ($line !== '') {
                                $lawTextParts[] = $line;
                            }

                            // Ekstrak teks ayat dari description (sama persis SPPP)
                            $ayatNo = null;
                            if (preg_match('/\bayat\b\s*\(?\s*(\d+)\s*\)?/iu', $chapter, $mAyat)) {
                                $ayatNo = (int) $mAyat[1];
                            }
                            $desc = $law->crimeConstitution?->description ?? null;
                            if ($ayatNo && $desc) {
                                $pattern = '/(?:^|<br[^>]*>|<p>|[\r\n]+)\s*\('.$ayatNo.'\)\s*(.*?)(?=(?:<br[^>]*>|<p>|[\r\n]+)\s*\(\d+\)|$)/is';
                                if (preg_match($pattern, (string) $desc, $mDesc)) {
                                    $txt = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($mDesc[1] ?? ''))));
                                    if ($txt !== '') {
                                        $ayatTexts[] = $txt;
                                    }
                                }
                            }
                        }

                        if ($crimeArt === '' && ! empty($lawTextParts)) {
                            $crimeArt = trim(implode(', ', array_values(array_unique(array_filter($lawTextParts)))));
                        }
                        if ($crimeDesc === '' && ! empty($ayatTexts)) {
                            $crimeDesc = trim(implode("\n\n", array_values(array_unique(array_filter($ayatTexts)))));
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore — fallback ke dash di bawah
                }
            }

            $tp->setValue('crimeDescription', $crimeDesc !== '' ? $crimeDesc : '—');
            $tp->setValue('allegedArticles', $crimeArt !== '' ? $crimeArt : '—');


            $validUntilFmt = $doc->valid_until_date
                ? Carbon::parse($doc->valid_until_date)->locale('id')->translatedFormat('d F Y')
                : '-';
            $tp->setValue('validUntilDateFormatted', $validUntilFmt);

            $handDate = $payload['handover']['date'] ?? null;
            if (! empty($handDate)) {
                $hc = Carbon::parse($handDate)->locale('id');
                $handoverIntro = 'Pada hari ini '.$hc->translatedFormat('l').', tanggal '.$hc->translatedFormat('d F Y');
            } elseif ($doc->document_date) {
                // Fallback ke tanggal surat jika handover date tidak diisi (konsisten dengan S22)
                $hc = Carbon::parse($doc->document_date)->locale('id');
                $handoverIntro = 'Pada hari ini '.$hc->translatedFormat('l').', tanggal '.$hc->translatedFormat('d F Y');
            } else {
                $handoverIntro = 'Pada hari ini ................................, tanggal ................................';
            }
            $tp->setValue('handoverIntro', $handoverIntro);

            $tp->setValue('suspectName', $suspectName);
            $tp->setValue('suspectIdentityNumber', $suspectIdentityNumber);
            $tp->setValue('suspectBirthPlace', $suspectBirthPlace);
            $tp->setValue('suspectBirthDate', $suspectBirthDate);
            $tp->setValue('suspectGenderName', $suspectGenderName);
            $tp->setValue('suspectJobName', $suspectJobName);
            $tp->setValue('suspectNationality', $suspectNationality);
            $tp->setValue('suspectFullAddress', $suspectFullAddress);
            $tp->setValue('signatoryHeadText', $signatoryHeadText);
            $tp->setValue('signatoryPositionHeadText', $signatoryPositionHeadText);
            $tp->setValue('orderRecipientName', $orderRecipientName);
            $tp->setValue('orderRecipientRankName', strtoupper((string) $orderRecipientRankName));
            $tp->setValue('orderRecipientRegisterNumber', $orderRecipientRegisterNumber);
            $tp->setValue('signatoryName', $signatoryName);
            $tp->setValue('signatoryRankName', strtoupper((string) $signatoryRankName));
            $tp->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);
            $tp->setValue('submittedName', $submittedName);
            $tp->setValue('submittedRankName', strtoupper((string) $submittedRankName));
            $tp->setValue('submittedRegisterNumber', $submittedRegisterNumber);
            $tp->setValue('documentDate', $documentDateFormatted);
            $tp->setValue('documentLocation', $documentLocation);
            $tp->setValue('suspectReligionName', $suspectReligionName);

            // Hyphen variants: Word kadang memecah nama variabel di tengah baris saat render docx
            $tp->setValue('sura-tPerintahPenyidikanDocumentDate', $sprindikDocDate);
            $tp->setValue('sketDa-teFormatted', $sketDateFmt);
            $tp->setValue('order-RecipientRegisterNumber', $orderRecipientRegisterNumber);
            $tp->setValue('submittedRegister-Number', $submittedRegisterNumber);
            $tp->setValue('signatoryRegister-Number', $signatoryRegisterNumber);

            // Block officers untuk template ${block_officers}...${/block_officers}
            $blockOfficers = [];
            $noOfficer = 1;
            $leaderClass = SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'LEADER');
            $memberClass = SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'MEMBER');
            foreach ($doc->officers->sortBy('sort') as $o) {
                if ((string) $o->class !== $leaderClass && (string) $o->class !== $memberClass) {
                    continue;
                }
                $blockOfficers[] = [
                    'number'     => (string) $noOfficer++,
                    'rank_id'    => (string) ($o->rank?->name ?? ''),
                    'first_name' => trim(($o->first_title ? $o->first_title.' ' : '').$o->first_name),
                    'last_name'  => trim($o->last_name.($o->last_title ? ', '.$o->last_title : '')),
                    'officer_id' => (string) ($o->register_number ?? ''),
                    'position'   => (string) ($o->position?->name ?? ''),
                ];
            }
            if (! empty($blockOfficers)) {
                $tp->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);
            } else {
                $tp->cloneBlock('block_officers', 0, true, false);
            }

            $filename = 'generate/'.Str::uuid().' - Surat Perintah Penangkapan - '.($accident->polres->full_name ?? 'doc');
            $tp->saveAs($filename.'.docx');

            return response()->download($filename.'.docx')->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            Log::error('SPP download failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Gagal mengunduh dokumen Word.');
        }
    }

    /**
     * SPT untuk S-6: hanya yang terdaftar mengacu pada SPRINDIK / penyidikan (related_type SPRINDIK).
     * SPT yang mengacu pada SPRINLIDIK / penyelidikan tidak ditampilkan di form ini.
     */
    private function suratPerintahTugasDocumentsForPenangkapan(string $accidentId)
    {
        return SuratPerintahTugasDocument::query()
            ->where('accident_id', $accidentId)
            ->where('related_type', SuratPerintahPenyidikanDocument::class)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderByDesc('document_date');
    }

    private function validateForm(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'documentNumber' => 'required|min:3|max:255',
            'documentDate' => 'required|date',
            'suspect' => 'required|uuid',
            'suratPerintahPenyidikanDocument' => 'required|uuid',
            'suratKetetapanTentangPenetapanTersangkaDocument' => 'required|uuid',
            'suratPerintahTugasDocument' => [
                'required',
                'uuid',
                function (string $attribute, mixed $value, \Closure $fail) use ($request, $id) {
                    $accidentId = htmlspecialchars((string) $request->input('accident_id'));
                    $okStatus = SuratPerintahTugasDocument::where('accident_id', $accidentId)
                        ->where('id', $value)
                        ->where('related_type', SuratPerintahPenyidikanDocument::class)
                        ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                        ->exists();
                    if ($okStatus) {
                        return;
                    }
                    if ($id) {
                        $currentFk = SuratPerintahPenangkapanDocument::where('id', $id)->value('surat_perintah_tugas_document_id');
                        if ((string) $currentFk === (string) $value) {
                            $existsForAccident = SuratPerintahTugasDocument::where('accident_id', $accidentId)
                                ->where('id', $value)
                                ->where('related_type', SuratPerintahPenyidikanDocument::class)
                                ->exists();
                            if ($existsForAccident) {
                                return;
                            }
                        }
                    }
                    $fail('Surat Perintah Tugas harus mengacu pada Surat Perintah Penyidikan (bukan penyelidikan), dan valid untuk perkara ini.');
                },
            ],
            'crimeDescription' => 'nullable|string|max:2000',
            'allegedArticles' => 'nullable|string|max:500',
            'officerLeader' => 'required|string',
            'internalOfficers' => 'required|array|min:1',
            'internalOfficers.*' => 'required|string',
            'signatoryOfficerId' => 'required|string',
            'submittedOfficerId' => 'required|string',
            'handoverDate'    => 'nullable|date',
            'validUntilDate'  => 'required|date',
        ], [
            'documentNumber.required' => 'Nomor Surat harus diisi',
            'documentDate.required' => 'Tanggal Surat harus diisi',
            'suspect.required' => 'Tersangka harus dipilih',
            'suratPerintahPenyidikanDocument.required' => 'Surat Perintah Penyidikan harus dipilih',
            'suratKetetapanTentangPenetapanTersangkaDocument.required' => 'Surat Ketetapan tentang Penetapan Tersangka harus dipilih',
            'suratPerintahTugasDocument.required' => 'Surat Perintah Tugas harus dipilih',
            'officerLeader.required' => 'Ketua Tim Penyidik harus dipilih',
            'internalOfficers.required' => 'Minimal satu anggota tim penyidik harus ditambahkan',
            'internalOfficers.min' => 'Minimal satu anggota tim penyidik harus ditambahkan',
            'signatoryOfficerId.required' => 'Yang Menandatangani harus dipilih',
            'submittedOfficerId.required' => 'Yang menyerahkan harus dipilih',
        ]);
    }

    /**
     * Ringkasan identitas untuk cetak — diambil dari master tersangka (sama pola input dengan S-22: hanya pilih tersangka).
     */
    private function snapshotSuspectIdentity(?string $suspectId, string $accidentId): array
    {
        $suspectId = trim((string) $suspectId);
        if ($suspectId === '') {
            return [];
        }

        $s = Suspect::with(['gender', 'job', 'religion', 'country'])
            ->where('accident_id', $accidentId)
            ->where('id', $suspectId)
            ->first();

        if (! $s) {
            return [];
        }

        $birth = trim((string) ($s->birth_place ?? $s->place_of_birth ?? ''));
        $bdRaw = $s->birth_date ?? $s->date_of_birth ?? null;
        if (! empty($bdRaw)) {
            $bd = $bdRaw instanceof Carbon ? $bdRaw->format('Y-m-d') : substr((string) $bdRaw, 0, 10);
            $birth .= ($birth !== '' ? ', ' : '') . $bd;
        }

        return [
            'name' => $s->name,
            'nik' => $s->identity_number ?? '',
            'birth_place_date' => $birth,
            'gender' => $s->gender->name ?? '',
            'religion' => $s->religion->name ?? '',
            'job' => $s->job->name ?? '',
            'nationality' => $s->country ? ($s->country->full_name ?? $s->country->name ?? '') : '',
            'address' => trim((string) ($s->address ?? '')),
        ];
    }

    private function buildPayload(Request $request, string $accidentId, ?array $previousPayload = null): array
    {
        $prev = is_array($previousPayload) ? $previousPayload : [];
        $internal = $request->internalOfficers ?? [];
        $kepadaText = $this->buildKepadaDisplayText($request->officerLeader, $internal);

        $sprindikId = $request->suratPerintahPenyidikanDocument;
        $sketId = $request->suratKetetapanTentangPenetapanTersangkaDocument;
        $sketRow = $sketId
            ? SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)->where('id', $sketId)->first()
            : null;
        $sketDateStored = null;
        if ($sketRow && $sketRow->document_date) {
            $sketDateStored = $sketRow->document_date instanceof Carbon
                ? $sketRow->document_date->format('Y-m-d')
                : substr((string) $sketRow->document_date, 0, 10);
        }

        $snapshotOfficer = function (?string $oid): array {
            $oid = trim((string) $oid);
            if ($oid === '') {
                return [];
            }
            try {
                $o = Officer::withRelated()->find($oid);
                if (! $o) {
                    return [];
                }
                $name = trim((string) ($o->full_name ?? ''));
                if ($name === '') {
                    $name = PeopleNameHelper::getFullName($o->first_title, $o->first_name, $o->last_name, $o->last_title);
                }

                return [
                    'officer_id' => (string) ($o->id ?? ''),
                    'name' => (string) $name,
                    'register_number' => (string) ($o->register_number ?? ''),
                    'rank_name' => (string) ($o->rank->name ?? ''),
                    'position_name' => (string) ($o->position->name ?? ''),
                ];
            } catch (\Throwable $e) {
                return [];
            }
        };

        $accident = Accident::with('polres')->where('id', $accidentId)->first();
        $lpDate = null;
        if ($accident && ! empty($accident->report_date)) {
            $lpDate = is_string($accident->report_date)
                ? substr($accident->report_date, 0, 10)
                : $accident->report_date->format('Y-m-d');
        }

        $sptFk = $request->suratPerintahTugasDocument;
        $sptDoc = SuratPerintahTugasDocument::where('accident_id', $accidentId)->where('id', $sptFk)->first();
        $sptDateStored = null;
        if ($sptDoc && $sptDoc->document_date) {
            $sptDateStored = $sptDoc->document_date instanceof Carbon
                ? $sptDoc->document_date->format('Y-m-d')
                : substr((string) $sptDoc->document_date, 0, 10);
        }

        return [
            'dasar' => [
                'lp_number' => $accident?->no_lp,
                'lp_date' => $lpDate,
            ],
            'references' => [
                'sprindik_document_id' => $sprindikId,
                'sprindik_document_number' => $sprindikId
                    ? SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->where('id', $sprindikId)->value('document_number')
                    : null,
                'sket_document_id' => $sketId,
                'sket_document_number' => $sketRow?->document_number,
                'sket_document_date' => $sketDateStored,
                'surat_perintah_tugas_document_id' => $sptFk,
                'surat_perintah_tugas_document_number' => $sptDoc?->document_number,
                'surat_perintah_tugas_document_date' => $sptDateStored,
            ],
            'kepada' => [
                'officer_leader_id' => $request->officerLeader,
                'internal_officers' => $internal,
            ],
            'kepada_text' => $kepadaText,
            'identity' => $this->snapshotSuspectIdentity($request->suspect, $accidentId),
            'issued' => [
                'location' => $accident?->polres->full_name ?? '',
                'date' => $request->documentDate,
            ],
            'handover' => [
                'date' => $request->has('handoverDate')
                    ? $request->handoverDate
                    : (($prev['handover'] ?? [])['date'] ?? null),
            ],
            'signature' => $snapshotOfficer($request->signatoryOfficerId),
            'submitted' => $snapshotOfficer($request->submittedOfficerId),
            'manual_dasar' => [],
            'crime' => [
                'description' => $request->has('crimeDescription')
                    ? htmlspecialchars(trim((string) $request->crimeDescription))
                    : (string) (($prev['crime'] ?? [])['description'] ?? ''),
                'articles' => $request->has('allegedArticles')
                    ? htmlspecialchars(trim((string) $request->allegedArticles))
                    : (string) (($prev['crime'] ?? [])['articles'] ?? ''),
            ],
        ];
    }

    private function syncNormalizedData(SuratPerintahPenangkapanDocument $doc, Request $request): void
    {
        $doc->officers()->delete();

        $sort = 1;
        $push = function (Officer $off, string $class) use ($doc, &$sort) {
            $data = $this->mapOfficer($off, $class);
            $data['sort'] = $sort++;
            $doc->officers()->create($data);
        };

        if ($request->filled('officerLeader')) {
            $leader = Officer::withRelated()->find($request->officerLeader);
            if ($leader) {
                $push($leader, SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'LEADER'));
            }
        }

        foreach ($request->internalOfficers ?? [] as $reg) {
            $off = Officer::withRelated()->where('register_number', $reg)->first();
            if ($off) {
                $push($off, SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'MEMBER'));
            }
        }

        if ($request->filled('signatoryOfficerId')) {
            $sig = Officer::withRelated()->find($request->signatoryOfficerId);
            if ($sig) {
                $push($sig, SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'SIGNATORY'));
            }
        }

        if ($request->filled('submittedOfficerId')) {
            $sub = Officer::withRelated()->find($request->submittedOfficerId);
            if ($sub) {
                $push($sub, SuratPerintahPenangkapanDocumentOfficer::getEnumOption('class', 'SUBMITTED'));
            }
        }
    }

    private function mapOfficer(Officer $officer, string $class): array
    {
        return [
            'officer_id' => $officer->id,
            'register_number' => $officer->register_number,
            'first_title' => $officer->first_title,
            'first_name' => $officer->first_name,
            'last_name' => $officer->last_name,
            'last_title' => $officer->last_title,
            'rank_id' => $officer->rank_id,
            'position_id' => $officer->position_id,
            'phone_number' => $officer->phone_number,
            'email' => $officer->email,
            'police_id' => $officer->police_id,
            'class' => $class,
            'insert_method' => 'MANUAL',
            'status' => 'PRESENT',
            'flag' => 'INTERNAL',
        ];
    }

    private function leaderOfficersForAccident(Accident $accident)
    {
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        return Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();
    }

    /**
     * @param  array<int, string>  $registerNumbers
     */
    private function officersByRegisterNumbersOrdered(array $registerNumbers)
    {
        if (empty($registerNumbers)) {
            return collect();
        }

        return Officer::withRelated()
            ->whereIn('register_number', $registerNumbers)
            ->get()
            ->sortBy(function ($o) use ($registerNumbers) {
                return array_search($o->register_number, $registerNumbers, true);
            })
            ->values();
    }

    /**
     * @param  array<int, string>  $internalRegisterNumbers
     */
    private function buildKepadaDisplayText(?string $leaderId, array $internalRegisterNumbers): string
    {
        $rows = [];

        $leader = $leaderId ? Officer::withRelated()->find($leaderId) : null;
        if ($leader) {
            $rows[] = $leader;
        }

        $members = $this->officersByRegisterNumbersOrdered($internalRegisterNumbers);
        foreach ($members as $m) {
            $rows[] = $m;
        }

        $lines = [];
        $no = 1;
        foreach ($rows as $o) {
            $name = trim((string) ($o->full_name ?? ''));
            $rank = trim((string) ($o->rank->name ?? ''));
            $nrp = trim((string) ($o->register_number ?? ''));
            $pos = trim((string) ($o->position->name ?? ''));

            $parts = array_filter([
                $name,
                $rank,
                ($nrp !== '' ? 'NRP '.$nrp : ''),
                $pos,
            ]);

            $lines[] = $no++.'. '.implode(', ', $parts);
        }

        return implode("\n", $lines);
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $doc = SuratPerintahPenangkapanDocument::where('id', $id)->firstOrFail();
        $doc->delete();

        return redirect()->route('view_produktivitas_accident', [
            'accident_id' => $accidentId,
        ])->with('success', 'Surat Perintah Penangkapan berhasil dihapus.');
    }
}
