<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use App\Helpers\PeopleNameHelper;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\ReportingPerson;
use App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocument;
use App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocumentOfficer;

use App\Models\Lib\Gender;
use App\Models\Lib\Religion;
use App\Models\Lib\Job;
use App\Models\Lib\Nationality;
use App\Models\Lib\IdentityType;

class SuratKesepakatanDiversiDocumentController extends Controller
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

        // Penyidik Pembantu (MEMBER) - untuk opsi Fasilitator Diversi
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        // Tersangka pada perkara ini
        $suspects = Suspect::withRelated()
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        // Korban / Pelapor pada perkara ini
        $reportingPersons = ReportingPerson::where('accident_id', $accidentId)->get();

        // Master data
        $identityTypes = IdentityType::active()->get();
        $genders = Gender::where('is_active', true)->get();
        $religions = Religion::where('is_active', true)->get();
        $jobs = Job::where('is_active', true)->orderBy('name')->get();
        $nationalities = Nationality::active()->orderBy('sort')->get();

        $viewData = [
            'accidentId'            => $accidentId,
            'accident'              => $accident,
            'authorizedSignatories' => $authorizedSignatories,
            'officers'              => $officers,
            'suspects'              => $suspects,
            'reportingPersons'      => $reportingPersons,
            'identityTypes'         => $identityTypes,
            'genders'               => $genders,
            'religions'             => $religions,
            'jobs'                  => $jobs,
            'nationalities'         => $nationalities,
        ];

        return view('docs.surat-kesepakatan-diversi-document.create', $viewData);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accidentId') ?? $request->query('accident_id');
        $diversionDate = $request->input('diversionDate');
        $documentDate = $request->input('documentDate') ?? $diversionDate;
        $diversionDay = $request->input('diversionDay');
        $diversionRoom = $request->input('diversionRoom');
        $diversionStreet = $request->input('diversionStreet');
        $suspectId = $request->input('childSelect') ?? $request->input('suspect_id');
        $facilitatorId = $request->input('facilitatorOfficer') ?? $request->input('facilitatorId');

        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'accidentId']);

            $document = SuratKesepakatanDiversiDocument::create([
                'accident_id'          => $accidentId,
                'document_number'      => !empty($documentNumber) ? $documentNumber : null,
                'document_date'        => !empty($documentDate) ? $documentDate : null,
                'diversion_day'        => $diversionDay,
                'diversion_date'       => !empty($diversionDate) ? $diversionDate : null,
                'diversion_room'       => $diversionRoom,
                'diversion_street'     => $diversionStreet,
                'suspect_id'           => $suspectId,
                'payload'              => $payload,
                'created_by_user_id'   => Auth::id(),
            ]);

            if (!empty($facilitatorId)) {
                $officer = Officer::find($facilitatorId);
                if ($officer) {
                    $document->suratKesepakatanDiversiDocumentOfficers()->create([
                        'register_number' => $officer->register_number,
                        'first_title'     => $officer->first_title,
                        'first_name'      => $officer->first_name,
                        'last_name'       => $officer->last_name,
                        'last_title'      => $officer->last_title,
                        'rank_id'         => $officer->rank_id,
                        'position_id'     => $officer->position_id,
                        'phone_number'    => $officer->phone_number,
                        'email'           => $officer->email,
                        'police_id'       => $officer->police_id,
                        'status'          => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'class'           => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                        'flag'            => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                        'insert_method'   => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Kesepakatan Diversi berhasil disimpan.');
    }

    public function show($id)
    {
        $document = SuratKesepakatanDiversiDocument::withRelated()->where('id', $id)->firstOrFail();
        $accidentId = request()->query('accident_id') ?? $document->accident_id;
        return redirect()->route('doc.surat-kesepakatan-diversi-document.edit', ['id' => $id, 'accident_id' => $accidentId]);
    }

    public function edit($id)
    {
        $document = SuratKesepakatanDiversiDocument::withRelated()->where('id', $id)->firstOrFail();
        $accidentId = request()->query('accident_id') ?? $document->accident_id;
        $accident = Accident::where('id', $accidentId)->firstOrFail();

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

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::withRelated()
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $reportingPersons = ReportingPerson::where('accident_id', $accidentId)->get();

        $identityTypes = IdentityType::active()->get();
        $genders = Gender::where('is_active', true)->get();
        $religions = Religion::where('is_active', true)->get();
        $jobs = Job::where('is_active', true)->orderBy('name')->get();
        $nationalities = Nationality::active()->orderBy('sort')->get();

        $payload = $document->payload ?? [];

        $viewData = [
            'document'              => $document,
            'payload'               => $payload,
            'accidentId'            => $accidentId,
            'accident'              => $accident,
            'authorizedSignatories' => $authorizedSignatories,
            'officers'              => $officers,
            'suspects'              => $suspects,
            'reportingPersons'      => $reportingPersons,
            'identityTypes'         => $identityTypes,
            'genders'               => $genders,
            'religions'             => $religions,
            'jobs'                  => $jobs,
            'nationalities'         => $nationalities,
        ];

        return view('docs.surat-kesepakatan-diversi-document.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        $document = SuratKesepakatanDiversiDocument::where('id', $id)->firstOrFail();
        $accidentId = $request->input('accidentId') ?? $request->query('accident_id') ?? $document->accident_id;
        $diversionDate = $request->input('diversionDate');
        $documentDate = $request->input('documentDate') ?? $diversionDate;
        $diversionDay = $request->input('diversionDay');
        $diversionRoom = $request->input('diversionRoom');
        $diversionStreet = $request->input('diversionStreet');
        $suspectId = $request->input('childSelect') ?? $request->input('suspect_id');
        $facilitatorId = $request->input('facilitatorOfficer') ?? $request->input('facilitatorId');

        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'accidentId']);

            $document->update([
                'document_number'      => !empty($documentNumber) ? $documentNumber : null,
                'document_date'        => !empty($documentDate) ? $documentDate : null,
                'diversion_day'        => $diversionDay,
                'diversion_date'       => !empty($diversionDate) ? $diversionDate : null,
                'diversion_room'       => $diversionRoom,
                'diversion_street'     => $diversionStreet,
                'suspect_id'           => $suspectId,
                'payload'              => $payload,
                'updated_by_user_id'   => Auth::id(),
            ]);

            if (!empty($facilitatorId)) {
                $officer = Officer::find($facilitatorId);
                if ($officer) {
                    $document->suratKesepakatanDiversiDocumentOfficers()->updateOrCreate(
                        [
                            'class' => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                        ],
                        [
                            'register_number' => $officer->register_number,
                            'first_title'     => $officer->first_title,
                            'first_name'      => $officer->first_name,
                            'last_name'       => $officer->last_name,
                            'last_title'      => $officer->last_title,
                            'rank_id'         => $officer->rank_id,
                            'position_id'     => $officer->position_id,
                            'phone_number'    => $officer->phone_number,
                            'email'           => $officer->email,
                            'police_id'       => $officer->police_id,
                            'status'          => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('status', 'PRESENT'),
                            'flag'            => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                            'insert_method'   => SuratKesepakatanDiversiDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                        ]
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Kesepakatan Diversi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $document = SuratKesepakatanDiversiDocument::where('id', $id)->firstOrFail();
        $accidentId = request()->query('accident_id') ?? $document->accident_id;

        DB::beginTransaction();
        try {
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Kesepakatan Diversi berhasil dihapus.');
    }

    public function download($id)
    {
        $document = SuratKesepakatanDiversiDocument::withRelated()->where('id', $id)->firstOrFail();
        $accidentId = request()->query('accident_id') ?? $document->accident_id;
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->first();

        $templatePath = public_path('word-template/surat_kesepakatan_diversi.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template dokumen tidak ditemukan.');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $daerahPolice = $accident->polres->polda ?? null;
        $daerahPoliceFullName = $daerahPolice ? strtoupper($daerahPolice->full_name) : '';

        $resorPolice = $accident->polres;
        $resorPoliceAddress = ($resorPolice->address ?? '') . ', ' . ($resorPolice->polres_zipcode ?? '');
        $resorPoliceFullName = (isset($resorPolice->id) && in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name ?? '');

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);

        $payload = $document->payload ?? [];

        // Pihak I (Anak)
        $templateProcessor->setValue('childName', $payload['childName'] ?? ($document->suspect->name ?? '-'));
        $templateProcessor->setValue('childIdentityNumber', $payload['childIdentityNumber'] ?? ($document->suspect->identity_number ?? '-'));
        $templateProcessor->setValue('childNationality', $payload['childNationality'] ?? 'INDONESIA');
        $templateProcessor->setValue('childGenderName', $payload['childGenderName'] ?? '-');
        $templateProcessor->setValue('childBirthPlaceDate', ($payload['childBirthPlace'] ?? '') . ' / ' . ($payload['childBirthDate'] ?? ''));
        $templateProcessor->setValue('childAgeYear', $payload['childAgeYear'] ?? '-');
        $templateProcessor->setValue('childAgeMonth', $payload['childAgeMonth'] ?? '-');
        $templateProcessor->setValue('childAgeDay', $payload['childAgeDay'] ?? '-');
        $templateProcessor->setValue('childJobName', $payload['childJobName'] ?? '-');
        $templateProcessor->setValue('childReligionName', $payload['childReligionName'] ?? '-');
        $templateProcessor->setValue('childAddress', $payload['childAddress'] ?? '-');

        // Pendamping Anak (3 Opsi Standar Blanko: Orang Tua / Wali / Pendamping dari ......)
        $cgFrom = $payload['childGuardianFrom'] ?? 'Orang Tua';
        $cgSentence = 'orang tua';
        if ($cgFrom === 'Wali') {
            $cgSentence = 'wali';
        } elseif (str_contains(strtolower($cgFrom), 'pendamping') || $cgFrom === 'Pendamping dari ......') {
            $cgDetail = trim($payload['childGuardianFromDetail'] ?? '');
            $cgSentence = !empty($cgDetail) ? "pendamping dari {$cgDetail}" : "pendamping dari ......";
        } elseif ($cgFrom === 'Orang Tua') {
            $cgSentence = 'orang tua';
        } else {
            $cgSentence = strtolower($cgFrom);
        }
        $templateProcessor->setValue('childGuardianSentence', $cgSentence);
        $templateProcessor->setValue('childGuardianFrom', $cgSentence);
        $templateProcessor->setValue('childGuardianName', $payload['childGuardianName'] ?? '-');
        $templateProcessor->setValue('childGuardianIdentityNumber', $payload['childGuardianIdentityNumber'] ?? ($payload['childGuardianIdentity'] ?? '-'));
        $templateProcessor->setValue('childGuardianNationality', $payload['childGuardianNationality'] ?? 'INDONESIA');
        $templateProcessor->setValue('childGuardianGenderName', $payload['childGuardianGenderName'] ?? '-');
        $templateProcessor->setValue('childGuardianBirthPlaceDate', ($payload['childGuardianBirthPlace'] ?? '') . ' / ' . ($payload['childGuardianBirthDate'] ?? ''));
        $templateProcessor->setValue('childGuardianJobName', $payload['childGuardianJobName'] ?? '-');
        $templateProcessor->setValue('childGuardianReligionName', $payload['childGuardianReligionName'] ?? '-');
        $templateProcessor->setValue('childGuardianAddress', $payload['childGuardianAddress'] ?? '-');
        $templateProcessor->setValue('childGuardianFamilyRelation', $payload['childGuardianRelation'] ?? ($payload['childGuardianFamilyRelation'] ?? '-'));

        // Pihak II (Korban)
        $templateProcessor->setValue('victimName', $payload['victimName'] ?? '-');
        $templateProcessor->setValue('victimIdentityNumber', $payload['victimIdentityNumber'] ?? '-');
        $templateProcessor->setValue('victimNationality', $payload['victimNationality'] ?? 'INDONESIA');
        $templateProcessor->setValue('victimGenderName', $payload['victimGenderName'] ?? '-');
        $templateProcessor->setValue('victimBirthPlaceDate', ($payload['victimBirthPlace'] ?? '') . ' / ' . ($payload['victimBirthDate'] ?? ''));
        $templateProcessor->setValue('victimAgeYear', $payload['victimAgeYear'] ?? '-');
        $templateProcessor->setValue('victimAgeMonth', $payload['victimAgeMonth'] ?? '-');
        $templateProcessor->setValue('victimAgeDay', $payload['victimAgeDay'] ?? '-');
        $templateProcessor->setValue('victimJobName', $payload['victimJobName'] ?? '-');
        $templateProcessor->setValue('victimReligionName', $payload['victimReligionName'] ?? '-');
        $templateProcessor->setValue('victimAddress', $payload['victimAddress'] ?? '-');

        // Pendamping Korban (Jika Korban Didampingi)
        $isVictimAccompanied = ($payload['isVictimAccompanied'] ?? '0') == '1';
        if ($isVictimAccompanied) {
            $vgFrom = $payload['victimGuardianFrom'] ?? 'Orang Tua';
            $vgSentence = 'orang tua';
            if ($vgFrom === 'Wali') {
                $vgSentence = 'wali';
            } elseif (str_contains(strtolower($vgFrom), 'pendamping') || $vgFrom === 'Pendamping dari ......') {
                $vgDetail = trim($payload['victimGuardianFromDetail'] ?? '');
                $vgSentence = !empty($vgDetail) ? "pendamping dari {$vgDetail}" : "pendamping dari ......";
            } elseif ($vgFrom === 'Orang Tua') {
                $vgSentence = 'orang tua';
            } else {
                $vgSentence = strtolower($vgFrom);
            }

            $vgName = $payload['victimGuardianName'] ?? '-';
            $vgIdNumber = $payload['victimGuardianIdentityNumber'] ?? ($payload['victimGuardianIdentity'] ?? '-');
            $vgNat = $payload['victimGuardianNationality'] ?? 'INDONESIA';
            $vgGender = $payload['victimGuardianGenderName'] ?? '-';
            $vgBirth = ($payload['victimGuardianBirthPlace'] ?? '') . ' / ' . ($payload['victimGuardianBirthDate'] ?? '');
            $vgJob = $payload['victimGuardianJobName'] ?? '-';
            $vgRel = $payload['victimGuardianReligionName'] ?? '-';
            $vgAddr = $payload['victimGuardianAddress'] ?? '-';
            $vgFamilyRel = $payload['victimGuardianRelation'] ?? ($payload['victimGuardianFamilyRelation'] ?? '-');

            $vgText = "korban didampingi {$vgSentence}, dengan identitas sebagai berikut:</w:t><w:br/><w:t>" .
                      "  nama : {$vgName}</w:t><w:br/><w:t>" .
                      "  nomor identitas : {$vgIdNumber}</w:t><w:br/><w:t>" .
                      "  kewarganegaraan : {$vgNat}</w:t><w:br/><w:t>" .
                      "  jenis kelamin : {$vgGender}</w:t><w:br/><w:t>" .
                      "  tempat/tanggal lahir : {$vgBirth}</w:t><w:br/><w:t>" .
                      "  pekerjaan : {$vgJob}</w:t><w:br/><w:t>" .
                      "  agama : {$vgRel}</w:t><w:br/><w:t>" .
                      "  alamat : {$vgAddr}</w:t><w:br/><w:t>" .
                      "  hubungan keluarga : {$vgFamilyRel}";
            $templateProcessor->setValue('victimGuardianSection', $vgText);
        } else {
            $templateProcessor->setValue('victimGuardianSection', '');
        }

        // Musyawarah
        $templateProcessor->setValue('diversionDay', $document->diversion_day ?? ($payload['diversionDay'] ?? '-'));
        $templateProcessor->setValue('diversionDate', !empty($document->diversion_date) ? Carbon::parse($document->diversion_date)->locale('id')->translatedFormat('d F Y') : ($payload['diversionDate'] ?? '-'));
        $templateProcessor->setValue('diversionRoom', $document->diversion_room ?? ($payload['diversionRoom'] ?? '-'));
        $templateProcessor->setValue('diversionStreet', $document->diversion_street ?? ($payload['diversionStreet'] ?? '-'));

        // Fasilitator
        $officer = $document->suratKesepakatanDiversiDocumentOfficers->first();
        $facilitatorName = $officer ? PeopleNameHelper::getFullName($officer->first_title, $officer->first_name, $officer->last_name, $officer->last_title) : ($payload['facilitatorName'] ?? '-');
        $facilitatorRankNrp = $officer ? (($officer->rank->name ?? '') . ' / NRP ' . ($officer->register_number ?? '')) : ($payload['facilitatorRankNrp'] ?? '-');
        $templateProcessor->setValue('facilitatorName', $facilitatorName);
        $templateProcessor->setValue('facilitatorRankNrp', $facilitatorRankNrp);

        // Pasal-Pasal (Format Baru Berbasis Array Pasals & Poin Dinamis)
        $pasals = $payload['pasals'] ?? [];
        if (empty($pasals)) {
            $p1Points = [];
            if (!empty($payload['dealCompensationCheck'])) {
                $p1Points[] = "Pihak keluarga Anak memberikan kerugian berupa uang sebesar Rp " . ($payload['compensationAmount'] ?? '') . ", yang akan dibayarkan selama " . ($payload['compensationPeriod'] ?? '') . " dengan pertimbangan: " . ($payload['compensationConsideration'] ?? '');
            }
            if (!empty($payload['dealRehabCheck'])) {
                $p1Points[] = "Terhadap Anak diberikan Rehabilitasi Sosial dan psikososial yang dilakukan oleh " . ($payload['rehabOrganizer'] ?? '') . " selama " . ($payload['rehabPeriod'] ?? '') . " dengan pertimbangan: " . ($payload['rehabConsideration'] ?? '');
            }
            if (!empty($payload['dealBapasCheck'])) {
                $p1Points[] = "Anak dikembalikan ke orang tua dengan pengawasan dari " . ($payload['bapasSupervisionName'] ?? '') . " dan orang tua, dengan pertimbangan: " . ($payload['parentSupervisionConsideration'] ?? '');
            }
            if (!empty($payload['dealCommunityCheck'])) {
                $p1Points[] = "Anak melakukan Pelayanan Masyarakat di " . ($payload['communityServiceLocation'] ?? '') . " selama " . ($payload['communityServicePeriod'] ?? '') . " dengan pertimbangan: " . ($payload['communityServiceConsideration'] ?? '');
            }

            $pasals = [
                [
                    'number' => 1,
                    'subtitle' => $payload['pasal1Subtitle'] ?? '',
                    'content' => $payload['pasal1Content'] ?? '',
                    'points' => $p1Points,
                ],
            ];
            if (!empty($payload['pasal2Content'])) {
                $pasals[] = [
                    'number' => 2,
                    'subtitle' => $payload['pasal2Subtitle'] ?? '',
                    'content' => $payload['pasal2Content'],
                    'points' => [],
                ];
            }
            if (!empty($payload['pasal3Content'])) {
                $pasals[] = [
                    'number' => 3,
                    'subtitle' => $payload['pasal3Subtitle'] ?? '',
                    'content' => $payload['pasal3Content'],
                    'points' => [],
                ];
            }
        }

        $blockPasals = [];
        foreach ($pasals as $p) {
            $num = $p['number'] ?? 1;
            $title = 'Pasal ' . $num . '.';
            if (!empty($p['subtitle'])) {
                $title .= '</w:t><w:br/><w:t>' . $p['subtitle'];
            }

            $lines = [];
            if (!empty($p['content'])) {
                $lines[] = trim($p['content']);
            }
            if (!empty($p['points']) && is_array($p['points'])) {
                foreach ($p['points'] as $ptIdx => $pointText) {
                    if (!empty(trim($pointText))) {
                        $lines[] = '[' . ($ptIdx + 1) . '] ' . trim($pointText);
                    }
                }
            }

            $contentFormatted = implode('</w:t><w:br/><w:t>', $lines);
            if (empty($contentFormatted)) {
                $contentFormatted = '-';
            }

            $blockPasals[] = [
                'pasalTitle'   => $title,
                'pasalContent' => $contentFormatted,
            ];
        }

        $templateProcessor->cloneBlock('block_pasals', count($blockPasals), true, false, $blockPasals);

        // Signatures
        $templateProcessor->setValue('victimSignatoryName', $payload['victimName'] ?? '-');
        $templateProcessor->setValue('childSignatoryName', $payload['childName'] ?? ($document->suspect->name ?? '-'));
        $templateProcessor->setValue('victimGuardianSignatoryName', $payload['victimGuardianName'] ?? '');
        $templateProcessor->setValue('childGuardianSignatoryName', $payload['childGuardianName'] ?? '');

        // Saksi
        $templateProcessor->setValue('bapasOfficerName', $payload['bapasOfficerName'] ?? '..................................');
        $templateProcessor->setValue('bapasOfficerRankNip', $payload['bapasOfficerRankNip'] ?? 'NIP. .............................');
        $templateProcessor->setValue('socialWorkerName', $payload['socialWorkerName'] ?? '..................................');
        $templateProcessor->setValue('socialWorkerRankNip', $payload['socialWorkerRankNip'] ?? 'NIP. .............................');
        $templateProcessor->setValue('communityWitnessName', $payload['communityWitnessName'] ?? '..................................');

        $filename = 'generate/' . $document->id . ' - Surat Kesepakatan Diversi - ' . ($accident->polres->full_name ?? '');
        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0755, true);
        }
        $fullPath = public_path($filename . '.docx');
        $templateProcessor->saveAs($fullPath);
        return response()->download($fullPath)->deleteFileAfterSend(true);
    }
}
