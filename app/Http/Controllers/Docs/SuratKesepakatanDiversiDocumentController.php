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
use App\Models\ReportedPerson;
use App\Models\InvolvedPeople;
use App\Models\Stg\DorsVictim;
use App\Models\Stg\DorsAccident;
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

        // Korban / Pihak terlibat pada perkara ini (DORS, Involved People, Pelapor, Terlapor)
        $victims = $this->getVictimsForAccident($accidentId);

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
            'victims'               => $victims,
            'reportingPersons'      => $victims,
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
        if (empty($diversionDay) && !empty($diversionDate)) {
            try {
                $diversionDay = Carbon::parse($diversionDate)->locale('id')->translatedFormat('l');
            } catch (\Exception $e) {}
        }
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

        $victims = $this->getVictimsForAccident($accidentId);

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
            'victims'               => $victims,
            'reportingPersons'      => $victims,
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
        if (empty($diversionDay) && !empty($diversionDate)) {
            try {
                $diversionDay = Carbon::parse($diversionDate)->locale('id')->translatedFormat('l');
            } catch (\Exception $e) {}
        }
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

        // Master Data Dictionaries for name resolution
        $genders = Gender::pluck('name', 'id')->toArray();
        $religions = Religion::pluck('name', 'id')->toArray();
        $jobs = Job::pluck('name', 'id')->toArray();
        $nationalities = Nationality::pluck('name', 'id')->toArray();

        $resolveValue = function($primaryKey, $fallbackKey, $map, $default = '-') use ($payload) {
            $val = $payload[$primaryKey] ?? ($payload[$fallbackKey] ?? null);
            if ($val === null || $val === '') return $default;
            if (isset($map[$val])) return strtoupper($map[$val]);
            if (!is_numeric($val) && strlen(trim($val)) > 0) return strtoupper(trim($val));
            return $default;
        };

        $formatBirth = function($placeKey, $dateKey) use ($payload) {
            $place = trim($payload[$placeKey] ?? '');
            $date = trim($payload[$dateKey] ?? '');
            $formattedDate = '';
            if (!empty($date)) {
                try {
                    $formattedDate = Carbon::parse($date)->locale('id')->translatedFormat('d F Y');
                } catch (\Exception $e) {
                    $formattedDate = $date;
                }
            }
            if (!empty($place) && !empty($formattedDate)) {
                return "{$place} / {$formattedDate}";
            } elseif (!empty($place)) {
                return $place;
            } elseif (!empty($formattedDate)) {
                return $formattedDate;
            }
            return '-';
        };

        $resolveAge = function($prefix) use ($payload) {
            $y = $payload["{$prefix}AgeYear"] ?? null;
            $m = $payload["{$prefix}AgeMonth"] ?? null;
            $d = $payload["{$prefix}AgeDay"] ?? null;

            $y = ($y !== null && $y !== '') ? (int)$y : null;
            $m = ($m !== null && $m !== '') ? (int)$m : null;
            $d = ($d !== null && $d !== '') ? (int)$d : null;

            if (($y === null || $m === null || $d === null) && !empty($payload["{$prefix}BirthDate"])) {
                try {
                    $diff = Carbon::parse($payload["{$prefix}BirthDate"])->diff(Carbon::now());
                    $y = $y ?? $diff->y;
                    $m = $m ?? $diff->m;
                    $d = $d ?? $diff->d;
                } catch (\Exception $e) {}
            }

            return [
                'year'  => ($y !== null) ? (string)$y : '-',
                'month' => ($m !== null) ? (string)$m : '0',
                'day'   => ($d !== null) ? (string)$d : '0',
            ];
        };

        // Pihak I (Anak)
        $childAge = $resolveAge('child');
        $templateProcessor->setValue('childName', $payload['childName'] ?? ($document->suspect->name ?? '-'));
        $templateProcessor->setValue('childIdentityNumber', $payload['childIdentityNumber'] ?? ($document->suspect->identity_number ?? '-'));
        $templateProcessor->setValue('childNationality', $resolveValue('childNationality', 'childNationalityName', $nationalities, 'WNI'));
        $templateProcessor->setValue('childGenderName', $resolveValue('childGender', 'childGenderName', $genders, '-'));
        $templateProcessor->setValue('childBirthPlaceDate', $formatBirth('childBirthPlace', 'childBirthDate'));
        $templateProcessor->setValue('childAgeYear', $childAge['year']);
        $templateProcessor->setValue('childAgeMonth', $childAge['month']);
        $templateProcessor->setValue('childAgeDay', $childAge['day']);
        $templateProcessor->setValue('childJobName', $resolveValue('childJob', 'childJobName', $jobs, '-'));
        $templateProcessor->setValue('childReligionName', $resolveValue('childReligion', 'childReligionName', $religions, '-'));
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
        $templateProcessor->setValue('childGuardianNationality', $resolveValue('childGuardianNationality', 'childGuardianNationalityName', $nationalities, 'WNI'));
        $templateProcessor->setValue('childGuardianGenderName', $resolveValue('childGuardianGender', 'childGuardianGenderName', $genders, '-'));
        $templateProcessor->setValue('childGuardianBirthPlaceDate', $formatBirth('childGuardianBirthPlace', 'childGuardianBirthDate'));
        $templateProcessor->setValue('childGuardianJobName', $resolveValue('childGuardianJob', 'childGuardianJobName', $jobs, '-'));
        $templateProcessor->setValue('childGuardianReligionName', $resolveValue('childGuardianReligion', 'childGuardianReligionName', $religions, '-'));
        $templateProcessor->setValue('childGuardianAddress', $payload['childGuardianAddress'] ?? '-');
        $templateProcessor->setValue('childGuardianFamilyRelation', $payload['childGuardianRelation'] ?? ($payload['childGuardianFamilyRelation'] ?? '-'));

        // Pihak II (Korban)
        $victimAge = $resolveAge('victim');
        $templateProcessor->setValue('victimName', $payload['victimName'] ?? '-');
        $templateProcessor->setValue('victimIdentityNumber', $payload['victimIdentityNumber'] ?? '-');
        $templateProcessor->setValue('victimNationality', $resolveValue('victimNationality', 'victimNationalityName', $nationalities, 'WNI'));
        $templateProcessor->setValue('victimGenderName', $resolveValue('victimGender', 'victimGenderName', $genders, '-'));
        $templateProcessor->setValue('victimBirthPlaceDate', $formatBirth('victimBirthPlace', 'victimBirthDate'));
        $templateProcessor->setValue('victimAgeYear', $victimAge['year']);
        $templateProcessor->setValue('victimAgeMonth', $victimAge['month']);
        $templateProcessor->setValue('victimAgeDay', $victimAge['day']);
        $templateProcessor->setValue('victimJobName', $resolveValue('victimJob', 'victimJobName', $jobs, '-'));
        $templateProcessor->setValue('victimReligionName', $resolveValue('victimReligion', 'victimReligionName', $religions, '-'));
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
            $vgNat = $resolveValue('victimGuardianNationality', 'victimGuardianNationalityName', $nationalities, 'WNI');
            $vgGender = $resolveValue('victimGuardianGender', 'victimGuardianGenderName', $genders, '-');
            $vgBirth = $formatBirth('victimGuardianBirthPlace', 'victimGuardianBirthDate');
            $vgJob = $resolveValue('victimGuardianJob', 'victimGuardianJobName', $jobs, '-');
            $vgRel = $resolveValue('victimGuardianReligion', 'victimGuardianReligionName', $religions, '-');
            $vgAddr = $payload['victimGuardianAddress'] ?? '-';
            $vgFamilyRel = $payload['victimGuardianRelation'] ?? ($payload['victimGuardianFamilyRelation'] ?? '-');

            $templateProcessor->cloneBlock('block_victim_guardian', 1, true, false, [[
                'victimGuardianSentence' => $vgSentence,
                'victimGuardianName' => $vgName,
                'victimGuardianIdentityNumber' => $vgIdNumber,
                'victimGuardianNationality' => $vgNat,
                'victimGuardianGenderName' => $vgGender,
                'victimGuardianBirthPlaceDate' => $vgBirth,
                'victimGuardianJobName' => $vgJob,
                'victimGuardianReligionName' => $vgRel,
                'victimGuardianAddress' => $vgAddr,
                'victimGuardianFamilyRelation' => $vgFamilyRel,
            ]]);
        } else {
            $templateProcessor->cloneBlock('block_victim_guardian', 0);
        }

        // Musyawarah
        $diversionDateRaw = $document->diversion_date ?? ($payload['diversionDate'] ?? null);
        $diversionDay = $document->diversion_day ?? ($payload['diversionDay'] ?? null);
        if (empty($diversionDay) && !empty($diversionDateRaw)) {
            try {
                $diversionDay = Carbon::parse($diversionDateRaw)->locale('id')->translatedFormat('l');
            } catch (\Exception $e) {}
        }
        $diversionDateFormatted = '-';
        if (!empty($diversionDateRaw)) {
            try {
                $diversionDateFormatted = Carbon::parse($diversionDateRaw)->locale('id')->translatedFormat('d F Y');
            } catch (\Exception $e) {
                $diversionDateFormatted = $diversionDateRaw;
            }
        }
        $templateProcessor->setValue('diversionDay', $diversionDay ?? '-');
        $templateProcessor->setValue('diversionDate', $diversionDateFormatted);
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

        $buildPasalXml = function ($subtitle, $content, $points) {
            $hasSubtitle = !empty(trim($subtitle ?? ''));
            $hasContent = !empty(trim($content ?? ''));
            $validPoints = array_values(array_filter($points ?? [], fn($x) => !empty(trim($x))));
            $hasPoints = count($validPoints) > 0;

            if (!$hasSubtitle && !$hasContent && !$hasPoints) {
                return '-';
            }

            $xml = '</w:t></w:r></w:p>';
            $xml .= '<w:tbl>';
            $xml .= '<w:tblPr>';
            $xml .= '<w:tblStyle w:val="TableNormal"/>';
            $xml .= '<w:tblW w:w="9923" w:type="dxa"/>';
            $xml .= '<w:jc w:val="center"/>';
            $xml .= '<w:tblBorders>';
            $xml .= '<w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/>';
            $xml .= '<w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/>';
            $xml .= '<w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/>';
            $xml .= '<w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/>';
            $xml .= '<w:insideH w:val="none" w:sz="0" w:space="0" w:color="auto"/>';
            $xml .= '<w:insideV w:val="none" w:sz="0" w:space="0" w:color="auto"/>';
            $xml .= '</w:tblBorders>';
            $xml .= '<w:tblLayout w:type="fixed"/>';
            $xml .= '<w:tblCellMar>';
            $xml .= '<w:top w:w="20" w:type="dxa"/>';
            $xml .= '<w:bottom w:w="20" w:type="dxa"/>';
            $xml .= '<w:left w:w="0" w:type="dxa"/>';
            $xml .= '<w:right w:w="0" w:type="dxa"/>';
            $xml .= '</w:tblCellMar>';
            $xml .= '</w:tblPr>';
            $xml .= '<w:tblGrid><w:gridCol w:w="504"/><w:gridCol w:w="9419"/></w:tblGrid>';

            if ($hasSubtitle) {
                $subEscaped = htmlspecialchars(trim($subtitle), ENT_XML1, 'UTF-8');
                $xml .= '<w:tr>';
                $xml .= '<w:tc><w:tcPr><w:gridSpan w:val="2"/><w:tcW w:w="9923" w:type="dxa"/><w:tcBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr>';
                $xml .= '<w:p><w:pPr><w:spacing w:before="0" w:after="40"/><w:jc w:val="center"/></w:pPr>';
                $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:b/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                $xml .= '<w:t>' . $subEscaped . '</w:t></w:r></w:p></w:tc></w:tr>';
            }

            if ($hasContent) {
                $contEscaped = htmlspecialchars(trim($content), ENT_XML1, 'UTF-8');
                $contWithBr = str_replace(["\r\n", "\n", "\r"], '</w:t><w:br/><w:t>', $contEscaped);
                $xml .= '<w:tr>';
                $xml .= '<w:tc><w:tcPr><w:gridSpan w:val="2"/><w:tcW w:w="9923" w:type="dxa"/><w:tcBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr>';
                $xml .= '<w:p><w:pPr><w:spacing w:before="0" w:after="40"/><w:jc w:val="both"/></w:pPr>';
                $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                $xml .= '<w:t>' . $contWithBr . '</w:t></w:r></w:p></w:tc></w:tr>';
            }

            if ($hasPoints) {
                $ptNum = 1;
                foreach ($validPoints as $pt) {
                    $rawPt = trim($pt);
                    $cleanPt = preg_replace('/^(\\[?\\d+\\]?[\\.\\)]\\s*|\\d+\\.\\s*)/', '', $rawPt);
                    $ptEscaped = htmlspecialchars($cleanPt, ENT_XML1, 'UTF-8');
                    $ptWithBr = str_replace(["\r\n", "\n", "\r"], '</w:t><w:br/><w:t>', $ptEscaped);

                    $xml .= '<w:tr>';
                    $xml .= '<w:tc><w:tcPr><w:tcW w:w="504" w:type="dxa"/><w:vAlign w:val="top"/><w:tcBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr>';
                    $xml .= '<w:p><w:pPr><w:spacing w:before="0" w:after="0"/><w:jc w:val="left"/></w:pPr>';
                    $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                    $xml .= '<w:t>' . $ptNum . '.</w:t></w:r></w:p></w:tc>';

                    $xml .= '<w:tc><w:tcPr><w:tcW w:w="9419" w:type="dxa"/><w:vAlign w:val="top"/><w:tcBorders><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:tcBorders></w:tcPr>';
                    $xml .= '<w:p><w:pPr><w:spacing w:before="0" w:after="0"/><w:jc w:val="both"/></w:pPr>';
                    $xml .= '<w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                    $xml .= '<w:t>' . $ptWithBr . '</w:t></w:r></w:p></w:tc>';
                    $xml .= '</w:tr>';

                    $ptNum++;
                }
            }

            $xml .= '</w:tbl>';
            $xml .= '<w:p><w:pPr><w:spacing w:before="0" w:after="120"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="22"/></w:rPr><w:t>';
            return $xml;
        };

        $blockPasals = [];
        foreach ($pasals as $p) {
            $num = $p['number'] ?? 1;
            $title = 'Pasal ' . $num . '.';

            $blockPasals[] = [
                'pasalTitle'   => $title,
                'pasalContent' => $buildPasalXml($p['subtitle'] ?? '', $p['content'] ?? '', $p['points'] ?? []),
            ];
        }

        $templateProcessor->cloneBlock('block_pasals', count($blockPasals), true, false, $blockPasals);

        // Signatures (Sesuai Kondisi Korban Dewasa vs Korban Anak & Pelaku Anak)
        $victimName = trim($payload['victimName'] ?? '');
        $childName = trim($payload['childName'] ?? ($document->suspect->name ?? ''));

        $templateProcessor->setValue('victimSignatoryName', !empty($victimName) ? '(' . $victimName . ')' : '(..........................)');
        $templateProcessor->setValue('childSignatoryName', !empty($childName) ? '(' . $childName . ')' : '(..........................)');

        // Helper untuk label peran pendamping (Orang Tua / Wali / Pendamping)
        $resolveGuardianRoleLabel = function ($from, $fromDetail) {
            $from = trim($from ?? '');
            if (empty($from)) {
                return 'Orang Tua';
            }
            if ($from === 'Wali') {
                return 'Wali';
            }
            if ($from === 'Orang Tua') {
                return 'Orang Tua';
            }
            if (str_contains(strtolower($from), 'pendamping') || $from === 'Pendamping dari ......') {
                $detail = trim($fromDetail ?? '');
                return !empty($detail) ? "Pendamping dari {$detail}" : "Pendamping";
            }
            return $from;
        };

        // Pelaku Anak: Selalu ada nama orang tua/wali/pendamping
        $childGuardianName = trim($payload['childGuardianName'] ?? '');
        $childGuardianRole = $resolveGuardianRoleLabel($payload['childGuardianFrom'] ?? 'Orang Tua', $payload['childGuardianFromDetail'] ?? '');
        $templateProcessor->setValue('childGuardianSignatoryName', !empty($childGuardianName) ? '(' . $childGuardianName . ')' : '(..........................)');
        $templateProcessor->setValue('childGuardianRoleLabel', $childGuardianRole);

        // Korban: Kondisi Dewasa langsung ttd vs Korban Anak didampingi
        if ($isVictimAccompanied) {
            $victimGuardianName = trim($payload['victimGuardianName'] ?? '');
            $victimGuardianRole = $resolveGuardianRoleLabel($payload['victimGuardianFrom'] ?? 'Orang Tua', $payload['victimGuardianFromDetail'] ?? '');
            $templateProcessor->setValue('victimGuardianSignatoryName', !empty($victimGuardianName) ? '(' . $victimGuardianName . ')' : '(..........................)');
            $templateProcessor->setValue('victimGuardianRoleLabel', $victimGuardianRole);
        } else {
            // Korban Dewasa langsung ttd (tanpa pendamping)
            $templateProcessor->setValue('victimGuardianSignatoryName', '');
            $templateProcessor->setValue('victimGuardianRoleLabel', '');
        }

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

    /**
     * Mengambil daftar korban / pihak terlibat perkara untuk opsi Pihak II (Korban)
     */
    private function getVictimsForAccident($accidentId)
    {
        $accident = Accident::find($accidentId);
        if (!$accident) {
            return collect();
        }

        // Suspects pada perkara ini (untuk mengecualikan tersangka agar tidak menjadi korban)
        $suspects = Suspect::where('accident_id', $accidentId)->get();
        $suspectNiks = $suspects->pluck('identity_number')->filter()->toArray();
        $suspectNames = $suspects->pluck('name')->map(function ($n) {
            return strtoupper(trim($n));
        })->toArray();

        $victims = collect();

        // 1. DORS Victims (jika ada dors_id)
        if (!empty($accident->dors_id)) {
            $dorsVictims = DorsVictim::where('dors_id', $accident->dors_id)->get();
            if ($dorsVictims->isEmpty()) {
                $dorsAccident = DorsAccident::where('dors_id', $accident->dors_id)->first();
                if ($dorsAccident) {
                    $dorsVictims = DorsVictim::where('dors_accident_id', $dorsAccident->id)->get();
                }
            }

            foreach ($dorsVictims as $dv) {
                $cleanNik = trim($dv->nik ?? '');
                $cleanName = strtoupper(trim($dv->nama ?? ''));

                if ((!empty($cleanNik) && in_array($cleanNik, $suspectNiks)) || (!empty($cleanName) && in_array($cleanName, $suspectNames))) {
                    continue;
                }

                $genderId = null;
                if (!empty($dv->gender)) {
                    $gUpper = strtoupper($dv->gender);
                    if (str_contains($gUpper, 'LAKI') || $gUpper === 'L' || $gUpper === 'PRIA') {
                        $genderId = 1;
                    } elseif (str_contains($gUpper, 'PEREMPUAN') || $gUpper === 'P' || $gUpper === 'WANITA') {
                        $genderId = 2;
                    }
                }

                $religionId = null;
                if (!empty($dv->agama)) {
                    $aUpper = strtoupper($dv->agama);
                    if (str_contains($aUpper, 'ISLAM')) $religionId = 1;
                    elseif (str_contains($aUpper, 'KATOLIK')) $religionId = 3;
                    elseif (str_contains($aUpper, 'KRISTEN') || str_contains($aUpper, 'PROTESTAN')) $religionId = 2;
                    elseif (str_contains($aUpper, 'HINDU')) $religionId = 4;
                    elseif (str_contains($aUpper, 'BUDDHA')) $religionId = 5;
                    elseif (str_contains($aUpper, 'KONG')) $religionId = 6;
                }

                $jobId = null;
                if (!empty($dv->pekerjaan)) {
                    $jUpper = strtoupper(trim($dv->pekerjaan));
                    $jobMatch = Job::whereRaw('LOWER(name) = ?', [strtolower($jUpper)])->first();
                    if ($jobMatch) {
                        $jobId = $jobMatch->id;
                    } elseif (str_contains($jUpper, 'PNS') || str_contains($jUpper, 'PEGAWAI NEGERI')) $jobId = 4;
                    elseif (str_contains($jUpper, 'MAHASISWA')) $jobId = 5;
                    elseif (str_contains($jUpper, 'PELAJAR')) $jobId = 9;
                    elseif (str_contains($jUpper, 'WIRASWASTA') || str_contains($jUpper, 'WIRAUSAHA') || str_contains($jUpper, 'PEDAGANG')) $jobId = 10;
                    elseif (str_contains($jUpper, 'SWASTA') || str_contains($jUpper, 'KARYAWAN')) $jobId = 6;
                    elseif (str_contains($jUpper, 'POLRI') || str_contains($jUpper, 'POLISI')) $jobId = 7;
                    elseif (str_contains($jUpper, 'TNI')) $jobId = 12;
                    elseif (str_contains($jUpper, 'BURUH')) $jobId = 8;
                    elseif (str_contains($jUpper, 'TANI') || str_contains($jUpper, 'PETANI')) $jobId = 15;
                    elseif (str_contains($jUpper, 'GURU')) $jobId = 18;
                    elseif (str_contains($jUpper, 'DOSEN')) $jobId = 17;
                }

                $identityTypeId = 10;
                if (!empty($dv->jenis_identitas)) {
                    $ji = strtoupper($dv->jenis_identitas);
                    if (str_contains($ji, 'SIM')) $identityTypeId = 13;
                    elseif (str_contains($ji, 'PASSPORT')) $identityTypeId = 12;
                    elseif (str_contains($ji, 'KK')) $identityTypeId = 8;
                }

                $statusLabel = !empty($dv->status_korban) ? 'Korban (' . $dv->status_korban . ')' : 'Korban';

                $birthDateFormatted = null;
                $birthDateRaw = $dv->tgl_lahir;
                if (!empty($birthDateRaw)) {
                    try {
                        $birthDateFormatted = Carbon::parse($birthDateRaw)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }

                $victims->push((object)[
                    'id'                => $dv->id,
                    'source'            => 'dors',
                    'name'              => $dv->nama,
                    'identity_type_id'  => $identityTypeId,
                    'identity_number'   => $cleanNik,
                    'gender_id'         => $genderId,
                    'birth_place'       => $dv->tempat_lahir,
                    'birth_date'        => $birthDateFormatted,
                    'age'               => null,
                    'nationality_id'    => 1,
                    'job_id'            => $jobId,
                    'religion_id'       => $religionId,
                    'address'           => $dv->alamat,
                    'role_label'        => $statusLabel,
                ]);
            }
        }

        // 2. Involved Peoples (Pihak terlibat laka dari IRSMS)
        $involvedPeoples = InvolvedPeople::withRelated()
            ->where('accident_id', $accidentId)
            ->whereNotIn('class', ['WITNESS'])
            ->get();

        foreach ($involvedPeoples as $ip) {
            $cleanNik = trim($ip->identity_number ?? '');
            $cleanName = strtoupper(trim($ip->name ?? ''));

            if ((!empty($cleanNik) && in_array($cleanNik, $suspectNiks)) || (!empty($cleanName) && in_array($cleanName, $suspectNames))) {
                continue;
            }

            // Hindari duplikat jika sudah ada dari DORS
            $existing = $victims->first(function ($v) use ($cleanNik, $cleanName) {
                if (!empty($cleanNik) && $v->identity_number === $cleanNik) return true;
                if (!empty($cleanName) && strtoupper(trim($v->name)) === $cleanName) return true;
                return false;
            });

            if ($existing) {
                // Enrich existing DORS victim jika data di involved people lebih lengkap
                if (empty($existing->birth_date) && !empty($ip->birth_date)) {
                    try {
                        $existing->birth_date = Carbon::parse($ip->birth_date)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }
                if (empty($existing->age) && !empty($ip->age)) {
                    $existing->age = $ip->age;
                }
                if (empty($existing->birth_place) && !empty($ip->birth_place)) {
                    $existing->birth_place = $ip->birth_place;
                }
                if (empty($existing->gender_id) && !empty($ip->gender_id)) {
                    $existing->gender_id = $ip->gender_id;
                }
                if (empty($existing->address) && !empty($ip->address)) {
                    $existing->address = $ip->address;
                }
            } else {
                $idTypeId = $ip->identity_type_id;
                if (empty($idTypeId) && strlen($cleanNik) === 16) {
                    $idTypeId = 10;
                }

                $classLabel = 'Pihak Terlibat';
                if ($ip->class === 'DRIVER') $classLabel = 'Pengemudi';
                elseif ($ip->class === 'PASSENGER') $classLabel = 'Penumpang/Korban';
                elseif ($ip->class === 'PEDESTRIAN') $classLabel = 'Pejalan Kaki/Korban';

                $birthDateFormatted = null;
                if (!empty($ip->birth_date)) {
                    try {
                        $birthDateFormatted = Carbon::parse($ip->birth_date)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }

                $victims->push((object)[
                    'id'                => $ip->id,
                    'source'            => 'involved',
                    'name'              => $ip->name,
                    'identity_type_id'  => $idTypeId,
                    'identity_number'   => $cleanNik,
                    'gender_id'         => $ip->gender_id,
                    'birth_place'       => $ip->birth_place,
                    'birth_date'        => $birthDateFormatted,
                    'age'               => $ip->age,
                    'nationality_id'    => $ip->nationality ?? 1,
                    'job_id'            => $ip->job_id,
                    'religion_id'       => $ip->religion_id,
                    'address'           => $ip->address,
                    'role_label'        => $classLabel,
                ]);
            }
        }

        // 3. Reporting Persons (Pelapor perkara jika ada)
        $reportingPersons = ReportingPerson::where('accident_id', $accidentId)->get();
        foreach ($reportingPersons as $rp) {
            $cleanNik = trim($rp->identity_number ?? '');
            $cleanName = strtoupper(trim($rp->name ?? ''));

            if ((!empty($cleanNik) && in_array($cleanNik, $suspectNiks)) || (!empty($cleanName) && in_array($cleanName, $suspectNames))) {
                continue;
            }

            $existing = $victims->first(function ($v) use ($cleanNik, $cleanName) {
                if (!empty($cleanNik) && $v->identity_number === $cleanNik) return true;
                if (!empty($cleanName) && strtoupper(trim($v->name)) === $cleanName) return true;
                return false;
            });

            if (!$existing) {
                $birthDateFormatted = null;
                if (!empty($rp->birth_date)) {
                    try {
                        $birthDateFormatted = Carbon::parse($rp->birth_date)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }

                $victims->push((object)[
                    'id'                => $rp->id,
                    'source'            => 'reporting',
                    'name'              => $rp->name,
                    'identity_type_id'  => $rp->identity_type_id,
                    'identity_number'   => $cleanNik,
                    'gender_id'         => $rp->gender_id,
                    'birth_place'       => $rp->birth_place,
                    'birth_date'        => $birthDateFormatted,
                    'age'               => $rp->age,
                    'nationality_id'    => $rp->nationality_id ?? 1,
                    'job_id'            => $rp->job_id,
                    'religion_id'       => $rp->religion_id,
                    'address'           => $rp->address,
                    'role_label'        => 'Pelapor',
                ]);
            }
        }

        // 4. Reported Persons (Terlapor lain jika ada dan bukan tersangka)
        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();
        foreach ($reportedPersons as $rep) {
            $cleanNik = trim($rep->identity_number ?? '');
            $cleanName = strtoupper(trim($rep->name ?? ''));

            if ((!empty($cleanNik) && in_array($cleanNik, $suspectNiks)) || (!empty($cleanName) && in_array($cleanName, $suspectNames))) {
                continue;
            }

            $existing = $victims->first(function ($v) use ($cleanNik, $cleanName) {
                if (!empty($cleanNik) && $v->identity_number === $cleanNik) return true;
                if (!empty($cleanName) && strtoupper(trim($v->name)) === $cleanName) return true;
                return false;
            });

            if (!$existing) {
                $birthDateFormatted = null;
                if (!empty($rep->birth_date)) {
                    try {
                        $birthDateFormatted = Carbon::parse($rep->birth_date)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }

                $victims->push((object)[
                    'id'                => $rep->id,
                    'source'            => 'reported',
                    'name'              => $rep->name,
                    'identity_type_id'  => $rep->identity_type_id,
                    'identity_number'   => $cleanNik,
                    'gender_id'         => $rep->gender_id,
                    'birth_place'       => $rep->birth_place,
                    'birth_date'        => $birthDateFormatted,
                    'age'               => $rep->age,
                    'nationality_id'    => $rep->nationality_id ?? 1,
                    'job_id'            => $rep->job_id,
                    'religion_id'       => $rep->religion_id,
                    'address'           => $rep->address,
                    'role_label'        => 'Pihak Terlapor',
                ]);
            }
        }

        return $victims;
    }
}
