<?php

namespace App\Http\Controllers\Docs;

use App\Helpers\PeopleNameHelper;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\CaseVehicle;
use App\Models\Doc\DaftarPencarianBarangDocument\DaftarPencarianBarangDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Officer;
use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\TemplateProcessor;
use Webpatser\Uuid\Uuid;

class DaftarPencarianBarangDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected DocService $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Tampilkan form pembuatan Daftar Pencarian Barang (DPB)
     */
    public function create(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with([
            'polres',
            'polres.polda',
            'caseVehicle',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahPenyidikanDocuments.suratPerintahPenyidikanDocumentLaws'
        ])->where('id', $accidentId)->firstOrFail();

        // 1. Data Kop Surat
        $defaultDaerahPolice = !empty($accident->polres->polda->name) ? 'POLDA ' . strtoupper($accident->polres->polda->name) : 'POLDA JAWA TIMUR';
        $defaultResorPolice = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN'));
        $defaultResorPoliceAddress = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        // 2. Metadata
        $defaultKota = strtoupper($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan'));
        $defaultDocDate = Carbon::now()->locale('id')->translatedFormat('d F Y');
        $defaultDocNumber = 'DPB/' . ($accident->id_accident ?? '....') . '/' . Carbon::now()->format('m') . '/RES.1.24/' . Carbon::now()->format('Y') . '/Satlantas';

        // 3. Rujukan LP & Penetapan Izin
        $defaultNoLp = $accident->no_lp ?: ('LP/A/' . ($accident->id_accident ?? '....') . '/' . Carbon::now()->format('m') . '/' . Carbon::now()->format('Y') . '/SPKT.SATLANTAS');
        $defaultTglLp = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y');
        $defaultCourtPermitText = 'Surat Perintah Penyitaan Nomor : SP.Sita/' . ($accident->id_accident ?? '....') . '/' . Carbon::now()->format('m') . '/RES.1.24/' . Carbon::now()->format('Y') . '/Satlantas';

        // 4. Sprindik & Perkara
        $sprindikDocument = $accident->suratPerintahPenyidikanDocuments->first();
        $defaultTindakPidana = 'Kecelakaan Lalu Lintas';
        $defaultPasal = '310 Ayat (4)';
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

        // 5. Kendaraan / Barang Bukti Terdata
        $caseVehicles = CaseVehicle::where('accident_id', $accidentId)->get();
        $firstVehicle = $caseVehicles->first();

        // 6. Petugas Narahubung & Pejabat Penandatangan
        $getOldNewPolresIds = [$accident->polres_id];
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

        return view('docs.daftar-pencarian-barang-document.create', [
            'accident'                  => $accident,
            'accidentId'                => $accidentId,
            'caseVehicles'              => $caseVehicles,
            'firstVehicle'              => $firstVehicle,
            'officers'                  => $officers,
            'authorizedSignatories'     => $authorizedSignatories,
            'defaultDaerahPolice'       => $defaultDaerahPolice,
            'defaultResorPolice'        => $defaultResorPolice,
            'defaultResorPoliceAddress' => $defaultResorPoliceAddress,
            'defaultKota'               => $defaultKota,
            'defaultDocDate'            => $defaultDocDate,
            'defaultDocNumber'          => $defaultDocNumber,
            'defaultNoLp'               => $defaultNoLp,
            'defaultTglLp'              => $defaultTglLp,
            'defaultCourtPermitText'    => $defaultCourtPermitText,
            'defaultTindakPidana'       => $defaultTindakPidana,
            'defaultPasal'              => $defaultPasal,
            'defaultUuNomor'            => $defaultUuNomor,
            'defaultUuTahun'            => $defaultUuTahun,
            'defaultUuTentang'          => $defaultUuTentang,
            'defaultTkp'                => $defaultTkp,
            'defaultWaktuKejadian'      => $defaultWaktuKejadian,
        ]);
    }

    /**
     * Simpan Daftar Pencarian Barang (DPB)
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'document_number'    => 'required|string|max:255',
            'item_name'          => 'required|string|max:255',
        ], [
            'document_number.required' => 'Nomor Daftar Pencarian Barang (DPB) wajib diisi.',
            'item_name.required'       => 'Nama Barang yang dicari wajib diisi.',
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

            DaftarPencarianBarangDocument::create([
                'id'                        => $docId,
                'accident_id'               => $accidentId,
                'status_id'                 => '2',
                'document_category_id'      => '0705',

                // Kop Polda & Polres otomatis dari relasi no LP / perkara
                'daerah_police_full_name'   => $request->daerah_police_full_name ?: (!empty($accident->polres->polda->name) ? 'POLDA ' . strtoupper($accident->polres->polda->name) : 'POLDA SUMATERA UTARA'),
                'resor_police_full_name'    => $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))),
                'resor_police_address'      => $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')),

                'document_number'           => $request->document_number,
                'accident_number'           => $request->accident_number,
                'accident_date'             => $request->accident_date,
                'court_permit_text'         => $request->court_permit_text ?: '-',

                'dugaan_tindak_pidana'      => $request->dugaan_tindak_pidana,
                'pasal_diduga'              => $request->pasal_diduga,
                'uu_nomor'                  => $request->uu_nomor ?: '22',
                'uu_tahun'                  => $request->uu_tahun ?: '2009',
                'uu_tentang'                => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'           => $request->tempat_kejadian,
                'waktu_kejadian'            => $request->waktu_kejadian,

                'contact_officer_id'        => !empty($request->contact_officer_id) ? $request->contact_officer_id : null,
                'contact_officer_role'      => $contactRole,
                'contact_officer_name'      => $contactName ?: '-',
                'contact_officer_phone'     => $contactPhone ?: '-',

                // Rincian Barang
                'case_vehicle_id'           => !empty($request->case_vehicle_id) ? $request->case_vehicle_id : null,
                'item_name'                 => $request->item_name,
                'quantity'                  => $request->quantity ?: '1 (satu) unit',

                'registration_number'       => $request->registration_number ?: '-',
                'cylinder_capacity'         => $request->cylinder_capacity ?: '-',
                'fuel_type'                 => $request->fuel_type ?: '-',
                'tnkb_color'                => $request->tnkb_color ?: '-',
                'registration_year'         => $request->registration_year ?: '-',
                'bpkb_number'               => $request->bpkb_number ?: '-',
                'special_signs_other'       => $request->special_signs_other ?: '-',

                'item_type'                 => $request->item_type ?: '-',
                'item_category'             => $request->item_category ?: '-',
                'item_model'                => $request->item_model ?: '-',
                'physical_features_other'   => $request->physical_features_other ?: '-',

                'item_color'                => $request->item_color ?: '-',
                'manufacture_year'          => $request->manufacture_year ?: '-',
                'estimated_price'           => $request->estimated_price ?: '-',
                'trade_mark'                => $request->trade_mark ?: '-',
                'engine_frame_number'       => $request->engine_frame_number ?: '-',
                'manufacture_place'         => $request->manufacture_place ?: '-',
                'loss_chronology'           => $request->loss_chronology ?: '-',
                'important_notes'           => $request->important_notes ?: '-',

                'kepada_3'                  => $request->kepada_3 ?: 'Pertinggal',

                'document_location'         => $request->document_location ?: ($accident->polres->polres_district ?? 'PASURUAN'),
                'document_date'             => $request->document_date ?: Carbon::now()->locale('id')->translatedFormat('d F Y'),

                'signatory_id'              => !empty($request->signatory_id) ? $request->signatory_id : null,
                'signatory_head_text'       => $request->signatory_head_text ?: ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN') . "\n" . $signatoryPosition),
                'signatory_position_name'   => $signatoryPosition,
                'signatory_name'            => $signatoryName ?: '-',
                'signatory_rank_name'       => $signatoryRank ?: '-',
                'signatory_register_number' => $signatoryNrp ?: '-',

                'created_by_user_id'        => Auth::id(),
                'updated_by_user_id'        => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Pencarian Barang (DPB) berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilkan form edit Daftar Pencarian Barang
     */
    public function edit($id, $accidentId = null)
    {
        $document = DaftarPencarianBarangDocument::findOrFail($id);
        $accidentId = $document->accident_id ?? $accidentId;
        $accident = Accident::with(['polres', 'polres.polda', 'caseVehicle'])->where('id', $accidentId)->firstOrFail();

        $getOldNewPolresIds = [$accident->polres_id];
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

        $caseVehicles = CaseVehicle::where('accident_id', $accidentId)->get();

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.daftar-pencarian-barang-document.edit', array_merge([
            'document'              => $document,
            'accident'              => $accident,
            'accidentId'            => $accidentId,
            'caseVehicles'          => $caseVehicles,
            'officers'              => $officers,
            'authorizedSignatories' => $authorizedSignatories,
        ], $data));
    }

    /**
     * Update Dokumen Daftar Pencarian Barang
     */
    public function update(Request $request, $id)
    {
        $document = DaftarPencarianBarangDocument::findOrFail($id);
        $accidentId = $document->accident_id ?? $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'document_number' => 'required|string|max:255',
            'item_name'       => 'required|string|max:255',
        ], [
            'document_number.required' => 'Nomor Daftar Pencarian Barang (DPB) wajib diisi.',
            'item_name.required'       => 'Nama Barang yang dicari wajib diisi.',
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
                'daerah_police_full_name'   => $request->daerah_police_full_name ?: (!empty($accident->polres->polda->name) ? 'POLDA ' . strtoupper($accident->polres->polda->name) : 'POLDA SUMATERA UTARA'),
                'resor_police_full_name'    => $request->resor_police_full_name ?: ((in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'SIMALUNGUN'))),
                'resor_police_address'      => $request->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')),

                'document_number'           => $request->document_number,
                'accident_number'           => $request->accident_number,
                'accident_date'             => $request->accident_date,
                'court_permit_text'         => $request->court_permit_text ?: '-',

                'dugaan_tindak_pidana'      => $request->dugaan_tindak_pidana,
                'pasal_diduga'              => $request->pasal_diduga,
                'uu_nomor'                  => $request->uu_nomor ?: '22',
                'uu_tahun'                  => $request->uu_tahun ?: '2009',
                'uu_tentang'                => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tempat_kejadian'           => $request->tempat_kejadian,
                'waktu_kejadian'            => $request->waktu_kejadian,

                'contact_officer_id'        => !empty($request->contact_officer_id) ? $request->contact_officer_id : null,
                'contact_officer_role'      => $contactRole,
                'contact_officer_name'      => $contactName ?: '-',
                'contact_officer_phone'     => $contactPhone ?: '-',

                // Rincian Barang
                'case_vehicle_id'           => !empty($request->case_vehicle_id) ? $request->case_vehicle_id : null,
                'item_name'                 => $request->item_name,
                'quantity'                  => $request->quantity ?: '1 (satu) unit',

                'registration_number'       => $request->registration_number ?: '-',
                'cylinder_capacity'         => $request->cylinder_capacity ?: '-',
                'fuel_type'                 => $request->fuel_type ?: '-',
                'tnkb_color'                => $request->tnkb_color ?: '-',
                'registration_year'         => $request->registration_year ?: '-',
                'bpkb_number'               => $request->bpkb_number ?: '-',
                'special_signs_other'       => $request->special_signs_other ?: '-',

                'item_type'                 => $request->item_type ?: '-',
                'item_category'             => $request->item_category ?: '-',
                'item_model'                => $request->item_model ?: '-',
                'physical_features_other'   => $request->physical_features_other ?: '-',

                'item_color'                => $request->item_color ?: '-',
                'manufacture_year'          => $request->manufacture_year ?: '-',
                'estimated_price'           => $request->estimated_price ?: '-',
                'trade_mark'                => $request->trade_mark ?: '-',
                'engine_frame_number'       => $request->engine_frame_number ?: '-',
                'manufacture_place'         => $request->manufacture_place ?: '-',
                'loss_chronology'           => $request->loss_chronology ?: '-',
                'important_notes'           => $request->important_notes ?: '-',

                'kepada_3'                  => $request->kepada_3 ?: 'Pertinggal',

                'document_location'         => $request->document_location ?: ($accident->polres->polres_district ?? 'PASURUAN'),
                'document_date'             => $request->document_date ?: Carbon::now()->locale('id')->translatedFormat('d F Y'),

                'signatory_id'              => !empty($request->signatory_id) ? $request->signatory_id : null,
                'signatory_head_text'       => $request->signatory_head_text ?: ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN') . "\n" . $signatoryPosition),
                'signatory_position_name'   => $signatoryPosition,
                'signatory_name'            => $signatoryName ?: '-',
                'signatory_rank_name'       => $signatoryRank ?: '-',
                'signatory_register_number' => $signatoryNrp ?: '-',

                'updated_by_user_id'        => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Daftar Pencarian Barang (DPB) berhasil diperbarui.');
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
        $document = DaftarPencarianBarangDocument::with(['accident', 'caseVehicle', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.daftar-pencarian-barang-document.show', array_merge([
            'document' => $document,
            'accident' => $accident,
        ], $data));
    }

    /**
     * Hapus Dokumen
     */
    public function delete($id)
    {
        $document = DaftarPencarianBarangDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Daftar Pencarian Barang (DPB) berhasil dihapus.');
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
     * Persiapkan 45 variabel template Word & HTML View
     */
    public function prepareDocumentData(DaftarPencarianBarangDocument $document, ?Accident $accident): array
    {
        $daerahPoliceFullName = $document->daerah_police_full_name ?: (!empty($accident->polres->polda->name) ? 'POLDA ' . strtoupper($accident->polres->polda->name) : 'POLDA JAWA TIMUR');
        $resorPoliceFullName = $document->resor_police_full_name ?: ((in_array($accident->polres_id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN')));
        $resorPoliceAddress = $document->resor_police_address ?: ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        $documentNumber = $document->document_number ?: '-';
        $accidentNumber = $document->accident_number ?: ($accident->no_lp ?: '-');
        $accidentDate = $document->accident_date ?: ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-');
        $courtPermitText = $document->court_permit_text ?: '-';

        $dugaanTindakPidana = $document->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalDiduga = $document->pasal_diduga ?: '310 Ayat (4)';
        $uuNomor = $document->uu_nomor ?: '22';
        $uuTahun = $document->uu_tahun ?: '2009';
        $uuTentang = $document->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan';
        $tempatKejadian = $document->tempat_kejadian ?: ($accident->address_detail ?: ($accident->location ?: '-'));
        $waktuKejadian = $document->waktu_kejadian ?: '-';

        $contactOfficerRole = $document->contact_officer_role ?: 'Penyidik Pembantu';
        $contactOfficerName = $document->contact_officer_name ?: '-';
        $contactOfficerPhone = $document->contact_officer_phone ?: '-';

        $itemName = $document->item_name ?: '1 (satu) unit Kendaraan Bermotor';
        $quantity = $document->quantity ?: '1 (satu) unit';

        $registrationNumber = $document->registration_number ?: '-';
        $cylinderCapacity = $document->cylinder_capacity ?: '-';
        $fuelType = $document->fuel_type ?: '-';
        $tnkbColor = $document->tnkb_color ?: '-';
        $registrationYear = $document->registration_year ?: '-';
        $bpkbNumber = $document->bpkb_number ?: '-';
        $specialSignsOther = $document->special_signs_other ?: '-';

        $itemType = $document->item_type ?: '-';
        $itemCategory = $document->item_category ?: '-';
        $itemModel = $document->item_model ?: '-';
        $physicalFeaturesOther = $document->physical_features_other ?: '-';

        $itemColor = $document->item_color ?: '-';
        $manufactureYear = $document->manufacture_year ?: '-';
        $estimatedPrice = $document->estimated_price ?: '-';
        $tradeMark = $document->trade_mark ?: '-';
        $engineFrameNumber = $document->engine_frame_number ?: '-';
        $manufacturePlace = $document->manufacture_place ?: '-';
        $lossChronology = $document->loss_chronology ?: '-';
        $importantNotes = $document->important_notes ?: '-';

        $kepada_3 = $document->kepada_3 ?: 'Pertinggal';

        $documentLocation = $document->document_location ?: ($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan'));
        $documentDate = $document->document_date ?: Carbon::now()->locale('id')->translatedFormat('d F Y');

        $signatoryHeadText = $document->signatory_head_text ?: ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? 'PASURUAN') . "\n" . ($document->signatory_position_name ?: 'KASAT LANTAS'));
        $signatoryName = $document->signatory_name ?: '....................................';
        $signatoryRankName = $document->signatory_rank_name ?: '-';
        $signatoryRegisterNumber = $document->signatory_register_number ?: '-';

        return [
            'daerahPoliceFullName'    => $daerahPoliceFullName,
            'resorPoliceFullName'     => $resorPoliceFullName,
            'resorPoliceAddress'      => $resorPoliceAddress,
            'documentNumber'          => $documentNumber,
            'accidentNumber'          => $accidentNumber,
            'accidentDate'            => $accidentDate,
            'courtPermitText'         => $courtPermitText,
            'dugaanTindakPidana'      => $dugaanTindakPidana,
            'pasalDiduga'             => $pasalDiduga,
            'uuNomor'                 => $uuNomor,
            'uuTahun'                 => $uuTahun,
            'uuTentang'               => $uuTentang,
            'tempatKejadian'          => $tempatKejadian,
            'waktuKejadian'           => $waktuKejadian,
            'contactOfficerRole'      => $contactOfficerRole,
            'contactOfficerName'      => $contactOfficerName,
            'contactOfficerPhone'     => $contactOfficerPhone,
            'itemName'                => $itemName,
            'quantity'                => $quantity,
            'registrationNumber'      => $registrationNumber,
            'cylinderCapacity'        => $cylinderCapacity,
            'fuelType'                => $fuelType,
            'tnkbColor'               => $tnkbColor,
            'registrationYear'        => $registrationYear,
            'bpkbNumber'              => $bpkbNumber,
            'specialSignsOther'       => $specialSignsOther,
            'itemType'                => $itemType,
            'itemCategory'            => $itemCategory,
            'itemModel'               => $itemModel,
            'physicalFeaturesOther'   => $physicalFeaturesOther,
            'itemColor'               => $itemColor,
            'manufactureYear'         => $manufactureYear,
            'estimatedPrice'          => $estimatedPrice,
            'tradeMark'               => $tradeMark,
            'engineFrameNumber'       => $engineFrameNumber,
            'manufacturePlace'        => $manufacturePlace,
            'lossChronology'          => $lossChronology,
            'importantNotes'          => $importantNotes,
            'kepada_3'                => $kepada_3,
            'documentLocation'        => $documentLocation,
            'documentDate'            => $documentDate,
            'signatoryHeadText'       => $signatoryHeadText,
            'signatoryName'           => $signatoryName,
            'signatoryRankName'       => $signatoryRankName,
            'signatoryRegisterNumber' => $signatoryRegisterNumber,
        ];
    }

    /**
     * Generate file Word dengan template daftar_pencarian_barang.docx
     */
    public function generatePopulatedWordFile($id): array
    {
        if ($id instanceof DaftarPencarianBarangDocument) {
            $document = $id;
        } else {
            $document = DaftarPencarianBarangDocument::with(['accident', 'caseVehicle', 'contactOfficer', 'signatoryOfficer'])->findOrFail($id);
        }
        $accident = $document->accident ?? ($document->accident_id ? Accident::with(['polres', 'polres.polda'])->find($document->accident_id) : null);

        $templatePath = public_path('word-template/daftar_pencarian_barang.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('File template Word tidak ditemukan di: ' . $templatePath);
        }

        $tp = new TemplateProcessor($templatePath);
        $data = $this->prepareDocumentData($document, $accident);

        foreach ($data as $key => $val) {
            $tp->setValue($key, (string) ($val ?? ''));
        }

        $cleanNomor = str_replace(['/', '\\', ' '], '_', $document->document_number ?: 'Draft');
        $fileName = 'Daftar_Pencarian_Barang_DPB_' . $cleanNomor . '_' . date('Ymd_His') . '.docx';
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
