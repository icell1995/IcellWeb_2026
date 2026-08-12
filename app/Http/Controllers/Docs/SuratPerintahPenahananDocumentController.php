<?php

namespace App\Http\Controllers\Docs;

use App\Helpers\PeopleNameHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuratPerintahPenahananDocumentStoreRequest;
use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Lib\DetentionType;
use App\Models\Lib\Police;
use App\Models\Lib\Prison;
use App\Models\Officer;
use App\Models\Suspect;
use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SuratPerintahPenahananDocumentController extends Controller
{
    //
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::where('id', $accidentId)
            ->first();

        $policeId = $accident->polres_id;

        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suratPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->select('id', 'name', 'identity_number', 'address', 'regency_id')
            ->orderBy('name')
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

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->whereIn('officers.police_id', $getOldNewPolresIds)
            ->orderBy('first_name')
            ->get();

        $prisons = Prison::select('id', 'name', 'branch')
            ->orderBy('name')->get();

        $detentionTypes = DetentionType::select('id', 'type_name')
            ->orderBy('id')->get();

        $allOfficers = $officers->map(function ($officer) {
            return [
                'id'              => $officer->id,
                'register_number' => $officer->register_number,
                'full_name'       => $officer->full_name,
                'position_name'   => $officer->position->name ?? '',
                'rank_name'       => $officer->rank->name ?? '-',
                'police_name'     => $officer->police->full_name ?? '-',
            ];
        })->values();

        $viewData = [
            'accidentId'                        => $accidentId,
            'accident'                          => $accident,
            'suratPerintahPenyidikanDocuments'  => $suratPerintahPenyidikanDocuments,
            'suratPenetapanTersangkaDocuments'  => $suratPenetapanTersangkaDocuments,
            'suspects'                          => $suspects,
            'authorizedSignatories'             => $authorizedSignatories,
            'officers'                          => $officers,
            'allOfficers'                       => $allOfficers,
            'prisons'                           => $prisons,
            'detentionTypes'                    => $detentionTypes,
            'suspectAddresses'                  => $suspects->pluck('address', 'id'),
            'suspectRegencies'                  => $suspects->pluck('regency.name', 'id'),
        ];

        return view('docs.surat-perintah-penahanan-document.create', $viewData);
    }

    public function store(SuratPerintahPenahananDocumentStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validatedData();

            $documentData = collect($data)->only([
                'accident_id',
                'surat_perintah_penyidikan_document_id',
                'surat_ketetapan_penetapan_tersangka_id',
                'document_number',
                'document_date',
                'jenis_penahanan',
                'lokasi_penahanan',
                'cabang_penahanan',
                'released_at',
                'is_active',
                'is_legacy',
            ])->toArray();

            $documentData['created_by_user_id'] = auth()->id();
            $documentData['ip_addresses'] = [$request->ip()];

            $jenis = trim($data['jenis_penahanan'] ?? '');

            if ($jenis === 'Penahanan Rumah') {
                $documentData['lokasi_penahanan'] = $data['alamat_penahanan'] ?? null;
                $documentData['cabang_penahanan'] = null;
            } elseif ($jenis === 'Penahanan Kota') {
                $documentData['lokasi_penahanan'] = $data['kota_penahanan'] ?? null;
                $documentData['cabang_penahanan'] = null;
            } elseif ($jenis === 'Penahanan Rumah Tahanan Negara') {
                $documentData['lokasi_penahanan'] = $data['lokasi_penahanan'] ?? null;
                $documentData['cabang_penahanan'] = $data['cabang_penahanan'] ?? null;
            } else {
                $documentData['lokasi_penahanan'] = null;
                $documentData['cabang_penahanan'] = null;
            }

            $document = SuratPerintahPenahananDocument::create($documentData);

            $document->suspect()->attach($data['tersangka_id']);

            // 1. Ketua Tim Penyidik
            $ketua = Officer::findOrFail($data['officer_id']);
            $document->suratPerintahPenahananDocumentOfficers()->create(
                $this->mapOfficer($ketua, 'LEADER')   // atau 'KETUA'
            );

            // 2. Anggota Penyidik (personnel[])
            if (!empty($data['personnel'])) {
                $penyidikList = Officer::whereIn('id', $data['personnel'])->get();
                foreach ($penyidikList as $penyidik) {
                    $document->suratPerintahPenahananDocumentOfficers()->create(
                        $this->mapOfficer($penyidik, 'MEMBER')
                    );
                }
            }

            // 3. Penandatangan (Signatory)
            $signatory = Officer::findOrFail($data['signatory_id']);
            $document->suratPerintahPenahananDocumentOfficers()->create(
                $this->mapOfficer($signatory, 'SIGNATORY')
            );

            DB::commit();

            return redirect()
                ->route('view_produktivitas_accident', [
                    'accident_id' => $document->accident_id
                ])
                ->with('success', 'Surat Perintah Penahanan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal simpan Surat Perintah Penahanan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $document = SuratPerintahPenahananDocument::with([
            'suratPerintahPenahananDocumentOfficers.position.positionCluster',
            'suratPerintahPenahananDocumentOfficers.rank',
            'suspect',
        ])->where('id', $id)->firstOrFail();

        $accident = Accident::where('id', $accidentId)->firstOrFail();

        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suratPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->select('id', 'name', 'identity_number', 'address', 'regency_id')
            ->orderBy('name')
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

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->whereIn('officers.police_id', $getOldNewPolresIds)
            ->orderBy('first_name')
            ->get();

        $prisons = Prison::select('id', 'name', 'branch')
            ->orderBy('name')->get();

        $detentionTypes = DetentionType::select('id', 'type_name')
            ->orderBy('id')->get();

        $allOfficers = $officers->map(function ($officer) {
            return [
                'id'              => $officer->id,
                'register_number' => $officer->register_number,
                'full_name'       => $officer->full_name,
                'position_name'   => $officer->position->name ?? '',
                'rank_name'       => $officer->rank->name ?? '-',
                'police_name'     => $officer->police->full_name ?? '-',
            ];
        })->values();

        $snapshots = $document->suratPerintahPenahananDocumentOfficers ?? collect();
        $leaderReg = (string) ($snapshots->firstWhere('class', 'LEADER')->register_number ?? '');
        $signatoryReg = (string) ($snapshots->firstWhere('class', 'SIGNATORY')->register_number ?? '');
        $memberRegs = $snapshots->where('class', 'MEMBER')->pluck('register_number')->filter()->values();

        $leaderId = $leaderReg !== '' ? (string) (Officer::query()->where('register_number', $leaderReg)->value('id') ?? '') : '';
        $signatoryId = $signatoryReg !== '' ? (string) (Officer::query()->where('register_number', $signatoryReg)->value('id') ?? '') : '';

        $selectedMembers = [];
        foreach ($memberRegs as $reg) {
            $idFound = (string) (Officer::query()->where('register_number', (string) $reg)->value('id') ?? '');
            if ($idFound === '') {
                continue;
            }
            $m = $allOfficers->firstWhere('id', $idFound);
            if ($m) {
                $selectedMembers[] = $m;
            }
        }

        $suspectId = (string) optional($document->suspect->first())->id;

        return view('docs.surat-perintah-penahanan-document.edit', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'document' => $document,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratPenetapanTersangkaDocuments' => $suratPenetapanTersangkaDocuments,
            'suspects' => $suspects,
            'authorizedSignatories' => $authorizedSignatories,
            'officers' => $officers,
            'allOfficers' => $allOfficers,
            'prisons' => $prisons,
            'detentionTypes' => $detentionTypes,
            'suspectAddresses' => $suspects->pluck('address', 'id'),
            'suspectRegencies' => $suspects->pluck('regency.name', 'id'),
            'defaults' => [
                'document_number' => $document->document_number,
                'document_date' => optional($document->document_date)->format('Y-m-d'),
                'surat_perintah_penyidikan_document_id' => $document->surat_perintah_penyidikan_document_id,
                'surat_ketetapan_penetapan_tersangka_id' => $document->surat_ketetapan_penetapan_tersangka_id,
                'jenis_penahanan' => $document->jenis_penahanan,
                'lokasi_penahanan' => $document->lokasi_penahanan,
                'cabang_penahanan' => $document->cabang_penahanan,
                'tersangka_id' => $suspectId,
                'officer_id' => $leaderId,
                'signatory_id' => $signatoryId,
            ],
            'selectedMembers' => $selectedMembers,
        ]);
    }

    public function update(SuratPerintahPenahananDocumentStoreRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validatedData();

            $document = SuratPerintahPenahananDocument::where('id', $id)->firstOrFail();

            $documentData = collect($data)->only([
                'surat_perintah_penyidikan_document_id',
                'surat_ketetapan_penetapan_tersangka_id',
                'document_number',
                'document_date',
                'jenis_penahanan',
                'lokasi_penahanan',
                'cabang_penahanan',
                'released_at',
                'is_active',
                'is_legacy',
            ])->toArray();

            $jenis = trim($data['jenis_penahanan'] ?? '');
            if ($jenis === 'Penahanan Rumah') {
                $documentData['lokasi_penahanan'] = $data['alamat_penahanan'] ?? null;
                $documentData['cabang_penahanan'] = null;
            } elseif ($jenis === 'Penahanan Kota') {
                $documentData['lokasi_penahanan'] = $data['kota_penahanan'] ?? null;
                $documentData['cabang_penahanan'] = null;
            } elseif ($jenis === 'Penahanan Rumah Tahanan Negara') {
                $documentData['lokasi_penahanan'] = $data['lokasi_penahanan'] ?? null;
                $documentData['cabang_penahanan'] = $data['cabang_penahanan'] ?? null;
            } else {
                $documentData['lokasi_penahanan'] = null;
                $documentData['cabang_penahanan'] = null;
            }

            $documentData['updated_by_user_id'] = auth()->id();
            $document->update($documentData);

            // Sync suspect pivot
            $document->suspect()->sync([$data['tersangka_id']]);

            // Sync officers snapshots
            $document->suratPerintahPenahananDocumentOfficers()->delete();

            $ketua = Officer::findOrFail($data['officer_id']);
            $document->suratPerintahPenahananDocumentOfficers()->create($this->mapOfficer($ketua, 'LEADER'));

            if (! empty($data['personnel'])) {
                $penyidikList = Officer::whereIn('id', $data['personnel'])->get();
                foreach ($penyidikList as $penyidik) {
                    $document->suratPerintahPenahananDocumentOfficers()->create($this->mapOfficer($penyidik, 'MEMBER'));
                }
            }

            $signatory = Officer::findOrFail($data['signatory_id']);
            $document->suratPerintahPenahananDocumentOfficers()->create($this->mapOfficer($signatory, 'SIGNATORY'));

            DB::commit();

            return redirect()
                ->route('view_produktivitas_accident', ['accident_id' => $document->accident_id])
                ->with('success', 'Surat Perintah Penahanan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update Surat Perintah Penahanan: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: '.$e->getMessage()]);
        }
    }

    public function delete($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenahananDocumentId = $id;

        DB::beginTransaction();
        try {
            // Delete from database
            $suratPerintahPenahananDocument = SuratPerintahPenahananDocument::where('id', $suratPerintahPenahananDocumentId)->first();
            $suratPerintahPenahananDocument->delete();

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
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenahananDocumentId = $id;

        $suratPerintahPenahananDocument = SuratPerintahPenahananDocument::with(['suratPerintahPenahananDocumentOfficers', 'suratKetetapanPenetapanTersangkaDocument', 'suspect', 'suratPerintahPenyidikanDocument'])->where('id', $suratPerintahPenahananDocumentId)->first();
        $signatory = $suratPerintahPenahananDocument->suratPerintahPenahananDocumentOfficers->where('class', '=', SuratPerintahPenahananDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->first();
        $suspect = $suratPerintahPenahananDocument->suspect->first();

        $accident = Accident::with(['polres'])->where('id', $accidentId)->first();

        $signatoryHeadText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name,
        ];

        $signatoryPositionHeadText = [
            'NO_KAPOLRES' => $signatory->position->positionCluster->alias_name ?? '',
            'NO_DIRLANTAS' => $signatory->position->positionCluster->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penahanan.docx');

        if (isset($signatory->position)) {
            if ($signatory->position->position_cluster_id == '1') {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionHeadText', '');
            } else if ($signatory->position->position_cluster_id == '9') {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_DIRLANTAS']);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText['NO_DIRLANTAS']);
            } else {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText['NO_KAPOLRES']);
            }
        }

        $documentDate = Carbon::parse($suratPerintahPenahananDocument->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber = $suratPerintahPenahananDocument->document_number;

        $accidentNumber = $accident->no_lp;
        $accidentDate = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');
        $reportDate = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y');

        $suspectName = $suspect->name ?? '';
        $suspectIdentityNumber = $suspect->identity_number ?? '';
        $suspectNationality = $suspect->nationality ?? '';
        $suspectBirthPlace = $suspect->birth_place ?? '';
        $suspectBirthDate = (!empty($suspect->birth_date)) ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
        $suspectGender = $suspect->gender()->first() ?? '';
        $suspectGenderName = $suspectGender->name ?? '';
        $suspectJob = $suspect->job()->first() ?? '';
        $suspectJobName = $suspectJob->name ?? '';
        $suspectReligion = $suspect->religion()->first() ?? '';
        $suspectReligionName = $suspectReligion->name ?? '';

        $suspectCountry = $suspect->country()->first() ?? '';
        $suspectCountryName = $suspectCountry->name ?? '';
        $suspectProvince = $suspect->province()->first() ?? '';
        $suspectProvinceName = ($suspectProvince) ? ', ' . $suspectProvince->name : '';
        $suspectRegency = $suspect->regency()->first() ?? '';
        $suspectRegencyName = ($suspectRegency) ? ', ' . $suspectRegency->name : '';
        $suspectDistrict = $suspect->district()->first() ?? '';
        $suspectDistrictName = ($suspectDistrict) ? ', ' . $suspectDistrict->name : '';
        $suspectVillage = $suspect->village()->first() ?? '';
        $suspectVillageName = ($suspectVillage) ? ', ' . $suspectVillage->name : '';
        $suspectAddress = $suspect->address ?? '';
        $suspectFullAddress = ucwords(strtolower($suspectAddress . $suspectVillageName . $suspectDistrictName . $suspectRegencyName . $suspectProvinceName));

        $suratPerintahPenyidikanDocument = $suratPerintahPenahananDocument->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $suratPerintahPenyidikanDocument->document_number;
        $suratPerintahPenyidikanDocumentDocumentDate = Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y');

        $suratKetetapanPenetapanTersangkaDocument = $suratPerintahPenahananDocument->suratKetetapanPenetapanTersangkaDocument;
        $suratKetetapanPenetapanTersangkaDocumentNumber = $suratKetetapanPenetapanTersangkaDocument->document_number;
        $suratKetetapanPenetapanTersangkaDocumentDate = Carbon::parse($suratKetetapanPenetapanTersangkaDocument->document_date)->locale('id')->translatedFormat('d F Y');

        $daerahPolice = $accident->polres->polda;
        $daerahPoliceFullName = $daerahPolice->full_name;

        $resorPolice = $accident->polres;
        $resorPoliceAddress = $resorPolice->address . ', ' . $resorPolice->polres_zipcode;
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);
        $resorPoliceProvinceName = $resorPolice->polres_province;

        $documentLocation = $resorPoliceProvinceName;

        $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
        $signatoryRankName = $signatory->rank->name ?? '';
        $signatoryRegisterNumber = $signatory->register_number;

        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentLocation', $documentLocation);

        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('reportDate', $reportDate);

        $templateProcessor->setValue('suspectName', $suspectName);
        $templateProcessor->setValue('suspectIdentityNumber', $suspectIdentityNumber);
        $templateProcessor->setValue('suspectNationality', $suspectNationality);
        $templateProcessor->setValue('suspectBirthPlace', $suspectBirthPlace);
        $templateProcessor->setValue('suspectBirthDate', $suspectBirthDate);
        $templateProcessor->setValue('suspectGenderName', $suspectGenderName);
        $templateProcessor->setValue('suspectJobName', $suspectJobName);
        $templateProcessor->setValue('suspectReligionName', $suspectReligionName);
        $templateProcessor->setValue('suspectFullAddress', $suspectFullAddress);

        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $suratPerintahPenyidikanDocumentNumber);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDate', $suratPerintahPenyidikanDocumentDocumentDate);

        $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentNumber', $suratKetetapanPenetapanTersangkaDocumentNumber);
        $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentDate', $suratKetetapanPenetapanTersangkaDocumentDate);

        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($daerahPoliceFullName));

        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($resorPoliceFullName));

        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $filename = 'generate/' . Str::uuid() . ' - Surat Perintah Penahanan - Resor ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
    }

    private function mapOfficer($officer, string $class): array
    {
        return [
            'officer_id'      => $officer->id,
            'register_number' => $officer->register_number,
            'first_title'     => $officer->first_title,
            'first_name'      => $officer->first_name,
            'last_name'       => $officer->last_name,
            'last_title'      => $officer->last_title,

            'rank_id' => $officer->rank_id,
            'position_id' =>  $officer->position_id,

            'phone_number' => $officer->phone_number,
            'email'        => $officer->email,
            'police_id'    => $officer->police_id,

            'class'         => $class,
            'insert_method' => 'MANUAL',
            'status'        => 'PRESENT',
            'flag'          => 'INTERNAL',
        ];
    }

    public function getPolices(Request $request)
    {
        $policeClass = $request->policeClass;
        $policeId = $request->policeId;

        try {
            switch ($policeClass) {
                case 'DAERAH':
                    $polices = Police::where('is_active', true)
                        ->where('class', $policeClass)
                        ->orderBy('sort', 'asc')
                        ->get();
                    break;

                case 'RESOR':
                    $polices = Police::where('is_active', true)
                        ->where('class', $policeClass)
                        ->where('parent_id', $policeId)
                        ->orderBy('sort', 'asc')
                        ->get();
                    break;
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $polices
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan pada sistem'
            ], 500);
        }
    }
}
