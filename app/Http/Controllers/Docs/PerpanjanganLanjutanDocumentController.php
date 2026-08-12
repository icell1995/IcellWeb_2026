<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocument;
use App\Models\Doc\PerpanjanganLanjutanDocument\PerpanjanganLanjutanDocument;
use App\Models\Doc\PerpanjanganLanjutanDocument\PerpanjanganLanjutanDocumentOfficer;
use App\Models\Lib\DetentionType;
use App\Models\Lib\Prison;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Traits\DocsOfficersTraits;
use App\Helpers\PeopleNameHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\TemplateProcessor;

class PerpanjanganLanjutanDocumentController extends Controller
{
    use DocsOfficersTraits;

    /** Induk S-21 (dan rujukan SPH): harus selesai persetujuan unggahan PDF (status 86). */
    private const STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN = ['86'];

    private function isEligiblePrerequisiteStatus(?string $statusId): bool
    {
        return in_array((string) $statusId, self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN, true);
    }

    public function create(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        if (! $accident) {
            return redirect()->back()->with('error', 'Data perkara tidak ditemukan');
        }
        if (! PermintaanPerpanjanganPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->exists()) {
            return redirect()
                ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with(
                    'error',
                    'Surat Permintaan Perpanjangan Penahanan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
        }

        $suspects = Suspect::with(['gender', 'job', 'religion', 'regency'])
            ->where('accident_id', $accidentId)
            ->orderBy('name')
            ->get();

        // Prefill dari dokumen sebelumnya (kalau ada), tapi tetap bisa diubah di form
        $latestS21 = PermintaanPerpanjanganPenahananDocument::with(['suspect'])
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->orderByDesc('document_date')
            ->first();

        $s21Payload = $latestS21?->payload ?? [];
        $s21Refs = (isset($s21Payload['references']) && is_array($s21Payload['references']))
            ? $s21Payload['references']
            : [];

        // Akhir masa tahanan dari dokumen sebelumnya: kolom DB (prioritas) lalu payload
        $s21DetentionEnd = null;
        if ($latestS21) {
            if ($latestS21->detention_end_date) {
                $s21DetentionEnd = $latestS21->detention_end_date->format('Y-m-d');
            } elseif (! empty($s21Payload['detention_end_date'])) {
                $s21DetentionEnd = $s21Payload['detention_end_date'];
            }
        }

        // Lama perpanjangan sebelumnya (hari), default 30 — masa berjalan mulai hari setelah detention_end
        $s21RequestedDays = max(1, (int) ($latestS21->requested_extension_days ?? 30));

        // Hari pertama dokumen ini = sehari setelah masa perpanjangan sebelumnya berakhir
        // (= hari pertama setelah detention_end + s21RequestedDays kalender)
        $defaultStart = null;
        if (! empty($s21DetentionEnd)) {
            try {
                $defaultStart = Carbon::parse($s21DetentionEnd)
                    ->addDay()
                    ->addDays($s21RequestedDays)
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                $defaultStart = null;
            }
        }

        $nextExtensionTo = $this->nextExtensionToForAccident($accidentId);

        $defaults = [
            'suspect_id' => $latestS21?->suspect_id,

            // Sisa masa tahanan
            'remaining_days' => $latestS21?->requested_extension_days,

            // Perpanjangan ke (auto)
            'extension_to' => $nextExtensionTo,

            // Rujukan dari dokumen sebelumnya (kalau ada)
            'references' => $s21Refs,

            // Rentang perpanjangan: mulai setelah akhir masa tahanan sebelumnya
            'extension_start_date' => $defaultStart,
        ];

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
            ->orderByDesc('document_date')
            ->get();
        $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->get();
        $suratPerintahPenahananDocuments = SuratPerintahPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->orderByDesc('document_date')
            ->get();

        $prisons = collect();
        try {
            $prisons = Prison::active()
                ->orderBy('province')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            // optional: jika tabel/seed belum tersedia, dropdown rutan tetap jalan dengan list kosong
            $prisons = collect();
        }

        $detentionTypes = collect();
        try {
            $detentionTypes = DetentionType::select('id', 'type_name')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            $detentionTypes = collect();
        }

        return view('docs.perpanjangan-lanjutan-document.create', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suspects' => $suspects,
            'defaults' => $defaults,
            'leaderOfficers' => $leaderOfficers,
            'authorizedSignatories' => $authorizedSignatories,
            'submitterOfficers' => $submitterOfficers,
            'kepadaPrefillLeaderId' => null,
            'kepadaInternalRows' => collect(),
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratKetetapanTentangPenetapanTersangkaDocuments' => $suratKetetapanTentangPenetapanTersangkaDocuments,
            'suratPerintahPenahananDocuments' => $suratPerintahPenahananDocuments,
            'prisons' => $prisons,
            'detentionTypes' => $detentionTypes,
            'suspectAddresses' => $suspects->pluck('address', 'id'),
            'suspectRegencies' => $suspects->pluck('regency.name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);

        if (! PermintaanPerpanjanganPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->exists()) {
            return redirect()
                ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with(
                    'error',
                    'Surat Permintaan Perpanjangan Penahanan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
        }

        $sphId = trim((string) ($request->suratPerintahPenahanan ?? ''));
        if ($sphId !== '') {
            $sphDoc = SuratPerintahPenahananDocument::query()
                ->where('id', $sphId)
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->first();
            if (! $sphDoc || ! $this->isEligiblePrerequisiteStatus($sphDoc->status_id)) {
                return redirect()->back()
                    ->with('error', 'Surat Perintah Penahanan yang dipilih harus berstatus 86 (unggahan PDF telah disetujui admin).')
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $extensionTo = $this->nextExtensionToForAccident($accidentId);
            $extensionDays = $this->computeDaysInclusive($request->extensionStartDate, $request->extensionEndDate);
            $doc = PerpanjanganLanjutanDocument::create([
                'accident_id' => $accidentId,
                'suspect_id' => $request->suspect,
                'document_number' => htmlspecialchars($request->documentNumber ?? $request->nomor_surat),
                'document_date' => $request->documentDate ?? $request->tanggal,
                'extension_to' => (int) $extensionTo,
                'extension_days' => (int) $extensionDays,
                'extension_start_date' => $request->extensionStartDate,
                'extension_end_date' => $request->extensionEndDate,
                'payload' => $this->buildPayload($request, $accidentId),
            ]);

            $this->syncNormalizedData($doc, $request);

            // Alur status mengikuti SPH: tetap 2 sampai "Isi Nomor" / "Meminta Persetujuan" di Berkas Perkara.

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.')->withInput();
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penahanan Lanjutan berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateForm($request, $id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);

        $doc = PerpanjanganLanjutanDocument::where('id', $id)->firstOrFail();

        $sphId = trim((string) ($request->suratPerintahPenahanan ?? ''));
        if ($sphId !== '') {
            $sphDoc = SuratPerintahPenahananDocument::query()
                ->where('id', $sphId)
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->first();
            if (! $sphDoc || ! $this->isEligiblePrerequisiteStatus($sphDoc->status_id)) {
                return redirect()->back()
                    ->with('error', 'Surat Perintah Penahanan yang dipilih harus berstatus 86 (unggahan PDF telah disetujui admin).')
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $extensionDays = $this->computeDaysInclusive($request->extensionStartDate, $request->extensionEndDate);
            $doc->update([
                'document_number' => htmlspecialchars($request->documentNumber ?? $request->nomor_surat),
                'document_date' => $request->documentDate ?? $request->tanggal,
                'suspect_id' => $request->suspect,
                // extension_to dikunci; tidak boleh diubah melalui form
                'extension_days' => (int) $extensionDays,
                'extension_start_date' => $request->extensionStartDate,
                'extension_end_date' => $request->extensionEndDate,
                'payload' => $this->buildPayload($request, $accidentId),
            ]);

            $this->syncNormalizedData($doc, $request);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data')->withInput();
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penahanan Lanjutan berhasil diperbarui.');
    }

    private function validateForm(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'documentNumber' => 'required|min:3|max:255',
            'documentDate' => 'required|date',
            'suspect' => 'required|uuid',
            'officerLeader' => 'required|string',
            'internalOfficers' => 'required|array|min:1',
            'internalOfficers.*' => 'required|string',
            'signatoryOfficerId' => 'required|string',
            'submittedOfficerId' => 'required|string',
            'extensionStartDate' => 'required|date',
            'extensionEndDate' => 'required|date|after_or_equal:extensionStartDate',
            'suratPerintahPenyidikanDocument' => 'required|uuid',
            'suratKetetapanTentangPenetapanTersangkaDocument' => 'required|uuid',
            'suratPerintahPenahanan' => 'required|uuid',
            'releaseOrderNumber' => 'nullable|string|max:255',
            'releaseOrderDate' => 'nullable|date',
            'hospitalizationOrderNumber' => 'nullable|string|max:255',
            'hospitalizationOrderDate' => 'nullable|date',
            'revokeHospitalizationOrderNumber' => 'nullable|string|max:255',
            'revokeHospitalizationOrderDate' => 'nullable|date',
            'transferDetentionPlaceOrderNumber' => 'nullable|string|max:255',
            'transferDetentionPlaceOrderDate' => 'nullable|date',
            'transferDetentionTypeOrderNumber' => 'nullable|string|max:255',
            'transferDetentionTypeOrderDate' => 'nullable|date',
            'handoverDate' => 'nullable|date',
            'penempatan_rutan_id' => 'nullable|string|max:255',
        ], [
            'documentNumber.required' => 'Nomor Surat harus diisi',
            'documentDate.required' => 'Tanggal harus diisi',
            'suspect.required' => 'Tersangka harus dipilih',
            'officerLeader.required' => 'Ketua Tim Penyidik harus dipilih',
            'internalOfficers.required' => 'Minimal satu anggota tim penyidik harus ditambahkan',
            'internalOfficers.min' => 'Minimal satu anggota tim penyidik harus ditambahkan',
            'signatoryOfficerId.required' => 'Penandatangan harus dipilih',
            'submittedOfficerId.required' => 'Yang menyerahkan harus dipilih',
            'extensionStartDate.required' => 'Tanggal mulai harus diisi',
            'extensionEndDate.required' => 'Tanggal s.d. harus diisi',
            'suratPerintahPenyidikanDocument.required' => 'Surat Perintah Penyidikan harus dipilih',
            'suratKetetapanTentangPenetapanTersangkaDocument.required' => 'Surat Ketetapan tentang Penetapan Tersangka harus dipilih',
            'suratPerintahPenahanan.required' => 'Surat Perintah Penahanan harus dipilih',
        ]);
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $doc = PerpanjanganLanjutanDocument::with(['createdByUser', 'documentCategory', 'suspect'])->where('id', $id)->firstOrFail();

        $suspects = Suspect::with(['gender', 'job', 'religion', 'regency'])
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
            ->orderByDesc('document_date')
            ->get();
        $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->get();
        $suratPerintahPenahananDocuments = SuratPerintahPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->where(function ($q) use ($doc) {
                $q->where(function ($q2) {
                    $q2->whereNull('deleted_at')
                        ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN);
                });
                $selectedSphId = $doc->payload['references']['sph_id'] ?? null;
                if (! empty($selectedSphId)) {
                    $q->orWhere(function ($q2) use ($selectedSphId) {
                        $q2->where('id', $selectedSphId)->whereNull('deleted_at');
                    });
                }
            })
            ->orderByDesc('document_date')
            ->get();

        $prisons = collect();
        try {
            $prisons = Prison::active()
                ->orderBy('province')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            $prisons = collect();
        }

        $detentionTypes = collect();
        try {
            $detentionTypes = DetentionType::select('id', 'type_name')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            $detentionTypes = collect();
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

        return view('docs.perpanjangan-lanjutan-document.edit', [
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
            'suratPerintahPenahananDocuments' => $suratPerintahPenahananDocuments,
            'prisons' => $prisons,
            'detentionTypes' => $detentionTypes,
            'suspectAddresses' => $suspects->pluck('address', 'id'),
            'suspectRegencies' => $suspects->pluck('regency.name', 'id'),
        ]);
    }

    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $doc = PerpanjanganLanjutanDocument::with(['documentCategory', 'suspect'])->where('id', $id)->firstOrFail();
        $defaults = $doc->payload ?? [];

        return view('docs.perpanjangan-lanjutan-document.show', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'doc' => $doc,
            'defaults' => $defaults,
        ]);
    }

    private function buildPayload(Request $request, string $accidentId): array
    {
        $internal = $request->internalOfficers ?? [];
        $kepadaText = $this->buildKepadaDisplayText($request->officerLeader, $internal);

        $sprindikId = $request->suratPerintahPenyidikanDocument;
        $sketId = $request->suratKetetapanTentangPenetapanTersangkaDocument;
        $sphId = $request->suratPerintahPenahanan;

        $suspectName = null;
        if (! empty($request->suspect)) {
            $suspectName = Suspect::where('accident_id', $accidentId)
                ->where('id', $request->suspect)
                ->value('name');
        }
        $suspectName = $suspectName ? (string) $suspectName : '';

        $sphNumber = null;
        $sphDate = null;
        if (! empty($sphId)) {
            $sphDoc = SuratPerintahPenahananDocument::query()
                ->where('accident_id', $accidentId)
                ->where('id', $sphId)
                ->whereNull('deleted_at')
                ->first();
            if ($sphDoc) {
                $sphNumber = (string) ($sphDoc->document_number ?? '');
                $sphDate = $sphDoc->document_date ? $sphDoc->document_date->format('Y-m-d') : null;
            }
        }

        $snapshotOfficer = function (?string $id): array {
            $id = trim((string) $id);
            if ($id === '') {
                return [];
            }
            try {
                $o = Officer::withRelated()->find($id);
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
                    'rank_id' => $o->rank_id ?? null,
                    'rank_name' => (string) ($o->rank?->name ?? ''),
                    'register_number' => (string) ($o->register_number ?? ''),
                    'position_id' => $o->position_id ?? null,
                    'position_name' => (string) ($o->position?->name ?? ''),
                ];
            } catch (\Throwable $e) {
                return [];
            }
        };

        return [
            'references' => [
                'sprindik_document_id' => $sprindikId,
                'sprindik_document_number' => $sprindikId
                    ? (SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->where('id', $sprindikId)->value('document_number'))
                    : null,
                'sket_document_id' => $sketId,
                'sket_document_number' => $sketId
                    ? (SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)->where('id', $sketId)->value('document_number'))
                    : null,
                'sph_id' => $sphId,
                'sph_name' => $sphNumber,
            ],
            'extension_range' => [
                'start' => $request->extensionStartDate,
                'end' => $request->extensionEndDate,
            ],
            'kepada' => [
                'officer_leader_id' => $request->officerLeader,
                'internal_officers' => $internal,
            ],
            'kepada_text' => $kepadaText,
            'alasan' => $request->alasan,
            'penempatan' => [
                'type' => $request->penempatan_type,
                'detail' => $request->penempatan_detail,
                'prison_id' => $request->penempatan_rutan_id,
            ],
            'orders' => [
                'sph_number' => $sphNumber,
                'sph_date' => $sphDate,
                'sph_suspect_name' => $suspectName,
                'release_order_number' => $request->releaseOrderNumber,
                'release_order_date' => $request->releaseOrderDate,
                'release_order_suspect_name' => $suspectName,
                'hospitalization_order_number' => $request->hospitalizationOrderNumber,
                'hospitalization_order_date' => $request->hospitalizationOrderDate,
                'hospitalization_order_suspect_name' => $suspectName,
                'revoke_hospitalization_order_number' => $request->revokeHospitalizationOrderNumber,
                'revoke_hospitalization_order_date' => $request->revokeHospitalizationOrderDate,
                'revoke_hospitalization_order_suspect_name' => $suspectName,
                'transfer_detention_place_order_number' => $request->transferDetentionPlaceOrderNumber,
                'transfer_detention_place_order_date' => $request->transferDetentionPlaceOrderDate,
                'transfer_detention_place_order_suspect_name' => $suspectName,
                'transfer_detention_type_order_number' => $request->transferDetentionTypeOrderNumber,
                'transfer_detention_type_order_date' => $request->transferDetentionTypeOrderDate,
                'transfer_detention_type_order_suspect_name' => $suspectName,
            ],
            'handover' => [
                'date' => $request->handoverDate,
            ],
            'signature' => $snapshotOfficer($request->signatoryOfficerId),
            'submitted' => $snapshotOfficer($request->submittedOfficerId),
            'perpanjangan_ke' => (int) ($request->extensionTo ?? $request->perpanjangan_ke),
            'lama_perpanjangan' => (int) $this->computeDaysInclusive($request->extensionStartDate, $request->extensionEndDate),
        ];
    }

    private function computeDaysInclusive(?string $start, ?string $end): int
    {
        $s = Carbon::parse($start)->startOfDay();
        $e = Carbon::parse($end)->startOfDay();
        return $s->diffInDays($e) + 1;
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
        // Konsisten dengan gaya daftar petugas di Surat Perintah Penyelidikan:
        // "1. Nama Lengkap, Pangkat, NRP, Jabatan"
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

    private function nextExtensionToForAccident(string $accidentId): int
    {
        $max = PerpanjanganLanjutanDocument::where('accident_id', $accidentId)->max('extension_to');
        return ((int) ($max ?? 0)) + 1;
    }

    private function syncNormalizedData(PerpanjanganLanjutanDocument $doc, Request $request): void
    {
        $doc->officers()->delete();

        // Officers snapshot: leader + members (internalOfficers list is register_number)
        $sort = 1;
        $createSnapshotRow = function (Officer $off, string $class) use ($doc, &$sort) {
            $doc->officers()->create([
                'sort' => $sort++,
                'officer_id' => $off->id,
                'register_number' => $off->register_number,
                'first_title' => $off->first_title,
                'first_name' => $off->first_name,
                'last_name' => $off->last_name,
                'last_title' => $off->last_title,
                'rank_id' => $off->rank_id,
                'position_id' => $off->position_id,
                'phone_number' => $off->phone_number,
                'email' => $off->email,
                'police_id' => $off->police_id,
                'status' => PerpanjanganLanjutanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class' => $class,
                'flag' => PerpanjanganLanjutanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method' => PerpanjanganLanjutanDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
            ]);
        };
        if ($request->filled('officerLeader')) {
            $leader = Officer::withRelated()->find($request->officerLeader);
            if ($leader) {
                $createSnapshotRow(
                    $leader,
                    PerpanjanganLanjutanDocumentOfficer::getEnumOption('class', 'LEADER')
                );
            }
        }

        $members = $request->internalOfficers ?? [];
        foreach ($members as $reg) {
            $off = Officer::withRelated()->where('register_number', $reg)->first();
            if (! $off) {
                continue;
            }
            $createSnapshotRow(
                $off,
                PerpanjanganLanjutanDocumentOfficer::getEnumOption('class', 'MEMBER')
            );
        }

        // Tambahkan snapshot agar konsisten dengan dokumen lain: penandatangan & yang menyerahkan
        if ($request->filled('signatoryOfficerId')) {
            $sig = Officer::withRelated()->find($request->signatoryOfficerId);
            if ($sig) {
                $createSnapshotRow(
                    $sig,
                    PerpanjanganLanjutanDocumentOfficer::getEnumOption('class', 'SIGNATORY')
                );
            }
        }

        if ($request->filled('submittedOfficerId')) {
            $sub = Officer::withRelated()->find($request->submittedOfficerId);
            if ($sub) {
                $createSnapshotRow(
                    $sub,
                    PerpanjanganLanjutanDocumentOfficer::getEnumOption('class', 'SUBMITTED')
                );
            }
        }
    }

    private function mergeOfficersFromNormalized(array $payload, $normalizedOfficers): array
    {
        // Build payload minimal untuk download agar tidak bergantung ke master Officer.
        $leader = null;
        $members = [];
        foreach ($normalizedOfficers as $o) {
            if ((string) $o->class === 'LEADER' && $leader === null) {
                $leader = $o;
                continue;
            }
            if ((string) $o->class === 'MEMBER') {
                $members[] = $o;
            }
        }

        $kepada = (isset($payload['kepada']) && is_array($payload['kepada'])) ? $payload['kepada'] : [];
        $kepada['officer_leader_id'] = $leader?->officer_id;
        $kepada['internal_officers'] = array_values(array_filter(array_map(
            fn ($m) => (string) ($m->register_number ?? ''),
            $members
        ), fn ($x) => trim($x) !== ''));
        $payload['kepada'] = $kepada;

        return $payload;
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $doc = PerpanjanganLanjutanDocument::where('id', $id)->firstOrFail();
        $doc->delete();

        return redirect()->route('view_produktivitas_accident', [
            'accident_id' => $accidentId,
        ])->with('success', 'Surat Perintah Penahanan Lanjutan berhasil dihapus.');
    }

    public function download($id)
    {
        // Get URL Parameter (dibuat konsisten seperti controller dokumen lain)
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $perpanjanganLanjutanDocumentId = $id;

        // Get data from database (+ relasi yang dipakai placeholder)
        $doc = PerpanjanganLanjutanDocument::with([
            'accident.polres.polda',
            'suspect.gender',
            'suspect.religion',
            'suspect.country',
            'suspect.province',
            'suspect.regency',
            'suspect.district',
            'suspect.village',
            'suspect.identityType',
        ])->where('id', $perpanjanganLanjutanDocumentId)->firstOrFail();

        // Pastikan accident mengikuti parameter kalau tersedia, fallback ke relasi doc
        $accident = $doc->accident;
        if (! empty($accidentId) && $accident?->id !== $accidentId) {
            $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first() ?? $accident;
        }

        // Payload normalization
        $payload = $doc->payload ?? [];

        // Prefer normalized snapshot (officers)
        $normalizedOfficers = collect();

        try {
            $normalizedOfficers = $doc->officers()->orderBy('sort')->get();
            if ($normalizedOfficers->count() > 0) {
                $payload = $this->mergeOfficersFromNormalized($payload, $normalizedOfficers);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $references = (isset($payload['references']) && is_array($payload['references'])) ? $payload['references'] : [];
        $extensionRange = (isset($payload['extension_range']) && is_array($payload['extension_range'])) ? $payload['extension_range'] : [];

        //===============================================================
        // Template file (dibuat konsisten seperti SPDP: pakai public/word-template)
        // Primary: public/word-template/surat_perintah_penahanan.docx (sudah ada di repo ini)
        // Fallback: public/file/penahanan/perpanjangan-lanjutan/*.docx (kalau Anda simpan template per-kategori)
        $templatePath = public_path('word-template/surat_perintah_penahanan_lanjutan.docx');
        if (! file_exists($templatePath)) {
            $templatePath = $this->pickFirstTemplateFromDir(public_path('file/penahanan/perpanjangan-lanjutan')) ?? '';
        }
        if ($templatePath === '' || ! file_exists($templatePath)) {
            abort(404, 'Template Surat Perintah Penahanan Lanjutan belum tersedia. Taruh .docx di public/word-template (disarankan) atau public/file/penahanan/perpanjangan-lanjutan');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Placeholder default bila data belum tersedia
        $dot = '.......';
        $fill = function ($value) use ($dot) {
            $v = is_string($value) ? trim($value) : (string) ($value ?? '');
            return $v !== '' ? $v : $dot;
        };

        //===============================================================
        // Format core values
        // Hindari double "Nomor : Nomor: ..." kalau user sudah mengetik prefix "Nomor:"
        $documentNumber = (string) ($doc->document_number ?? '');
        $documentNumber = preg_replace('/^\s*Nomor\s*:\s*/i', '', $documentNumber) ?? $documentNumber;
        $documentDate = $this->formatIdDatePdf($doc->document_date ?? null);
        $accidentNumber = $accident?->no_lp ?? '';
        $accidentDate = $this->formatIdDatePdf($accident?->report_date ?? $accident?->accident_date ?? null);
        // Untuk "Dikeluarkan di" umumnya menggunakan wilayah Polres (district).
        $documentLocation = ucwords(strtolower($accident?->polres?->polres_district ?? $accident?->polres?->polres_province ?? ''));

        $suspect = $doc->suspect;
        $suspectName = $suspect?->name ?? '';
        $suspectIdentityNumber = (string) ($suspect?->identity_number ?? $suspect?->identityNumber ?? '');
        $suspectGenderName = (string) ($suspect?->gender?->name ?? $suspect?->gender ?? '');
        $suspectBirthPlace = (string) ($suspect?->birth_place ?? $suspect?->place_of_birth ?? '');
        $suspectBirthDate = $this->formatIdDatePdf($suspect?->birth_date ?? $suspect?->date_of_birth ?? null);
        $suspectReligionName = (string) ($suspect?->religion?->name ?? $suspect?->religion ?? '');
        // Konsisten dengan SPDP: ambil dari relasi job->name dulu, lalu fallback ke kolom.
        $suspectJobName = trim((string) (
            $suspect?->job?->name
            ?? $suspect?->occupation
            ?? $suspect?->job_name
            ?? ''
        ));
        $suspectJobName = $suspectJobName !== '' ? ucwords(strtolower($suspectJobName)) : '-';
        $suspectNationality = (string) (
            $suspect?->country?->name
            ?? $suspect?->nationality
            ?? $suspect?->country
            ?? ''
        );

        $addressParts = [];
        if (! empty($suspect?->address)) {
            $addressParts[] = $suspect->address;
        }
        if (! empty($suspect?->village?->name)) {
            $addressParts[] = 'Kel. '.$suspect->village->name;
        } elseif (! empty($suspect?->sub_district)) {
            $addressParts[] = 'Kel. '.$suspect->sub_district;
        }
        if (! empty($suspect?->district?->name)) {
            $addressParts[] = 'Kec. '.$suspect->district->name;
        } elseif (! empty($suspect?->district)) {
            $addressParts[] = 'Kec. '.$suspect->district;
        }
        if (! empty($suspect?->regency?->name)) {
            $addressParts[] = $suspect->regency->name;
        } elseif (! empty($suspect?->city)) {
            $addressParts[] = $suspect->city;
        }
        if (! empty($suspect?->province?->name)) {
            $addressParts[] = $suspect->province->name;
        } elseif (! empty($suspect?->province)) {
            $addressParts[] = $suspect->province;
        }
        $suspectFullAddress = trim(implode(', ', array_filter($addressParts)));

        // Extension range
        $startRaw = $doc->extension_start_date ?? ($extensionRange['start'] ?? null);
        $endRaw = $doc->extension_end_date ?? ($extensionRange['end'] ?? null);
        $extensionStartDate = $this->formatIdDatePdf($startRaw);
        $extensionEndDate = $this->formatIdDatePdf($endRaw);

        $extensionDays = '';
        if ($doc->extension_days !== null) {
            $extensionDays = (string) $doc->extension_days;
        } elseif ($startRaw && $endRaw) {
            try {
                $extensionDays = (string) $this->computeDaysInclusive(
                    Carbon::parse($startRaw)->format('Y-m-d'),
                    Carbon::parse($endRaw)->format('Y-m-d')
                );
            } catch (\Throwable $e) {
                $extensionDays = '';
            }
        }

        // Untuk template: sisa masa tahanan minimal pakai rentang dokumen ini (hasil logic, bukan data palsu).
        $remainingDetentionDays = $extensionDays !== '' ? $extensionDays : '';

        // References: sprindik date (kalau ada id)
        $sprindikId = $references['sprindik_document_id'] ?? null;
        $sprindikNumber = (string) ($references['sprindik_document_number'] ?? '');
        $sprindikDate = '';
        if (! empty($sprindikId)) {
            $spr = SuratPerintahPenyidikanDocument::where('id', $sprindikId)->first();
            if ($spr) {
                $sprindikNumber = $sprindikNumber !== '' ? $sprindikNumber : (string) ($spr->document_number ?? '');
                $sprindikDate = $this->formatIdDatePdf($spr->document_date ?? null);
            }
        }

        // SKET: ambil tanggal & nama tersangka jika ada di DB, fallback dot
        $sketId = $references['sket_document_id'] ?? null;
        $sketNumber = (string) ($references['sket_document_number'] ?? '');
        $sketDate = '';
        $sketSuspectName = $suspectName;
        if (! empty($sketId)) {
            $sket = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect'])
                ->where('id', $sketId)
                ->first();
            if ($sket) {
                $sketNumber = $sketNumber !== '' ? $sketNumber : (string) ($sket->document_number ?? '');
                $sketDate = $this->formatIdDatePdf($sket->document_date ?? null);
                $sketSuspectName = (string) ($sket->suspect?->first()?->name ?? $sketSuspectName);
            }
        }

        // SPH: bisa dari payload (manual) atau legacy (references.sph_name)
        $orders = (isset($payload['orders']) && is_array($payload['orders'])) ? $payload['orders'] : [];
        $sphNumber = (string) ($orders['sph_number'] ?? ($references['sph_name'] ?? ''));
        $sphDate = (string) ($orders['sph_date'] ?? '');
        $sphSuspectName = (string) ($orders['sph_suspect_name'] ?? $suspectName);

        // =============================================================
        // Kepada (Tim Penyidik) — 100% dari snapshot tabel turunan jika tersedia
        $rankNameCache = [];
        $positionNameCache = [];
        $positionClusterCache = [];

        $rankName = function (?string $id) use (&$rankNameCache): string {
            $key = (string) ($id ?? '');
            if ($key === '') {
                return '';
            }
            if (array_key_exists($key, $rankNameCache)) {
                return (string) $rankNameCache[$key];
            }
            try {
                $rankNameCache[$key] = (string) (Rank::query()->where('id', $key)->value('name') ?? '');
            } catch (\Throwable $e) {
                $rankNameCache[$key] = '';
            }
            return (string) $rankNameCache[$key];
        };

        $positionName = function (?string $id) use (&$positionNameCache): string {
            $key = (string) ($id ?? '');
            if ($key === '') {
                return '';
            }
            if (array_key_exists($key, $positionNameCache)) {
                return (string) $positionNameCache[$key];
            }
            try {
                $positionNameCache[$key] = (string) (Position::query()->where('id', $key)->value('name') ?? '');
            } catch (\Throwable $e) {
                $positionNameCache[$key] = '';
            }
            return (string) $positionNameCache[$key];
        };

        $positionClusterId = function (?string $id) use (&$positionClusterCache): string {
            $key = (string) ($id ?? '');
            if ($key === '') {
                return '';
            }
            if (array_key_exists($key, $positionClusterCache)) {
                return (string) $positionClusterCache[$key];
            }
            try {
                $positionClusterCache[$key] = (string) (Position::query()->where('id', $key)->value('position_cluster_id') ?? '');
            } catch (\Throwable $e) {
                $positionClusterCache[$key] = '';
            }
            return (string) $positionClusterCache[$key];
        };

        $formatSnapshotName = function ($o): string {
            $first = trim((string) (($o->first_title ? $o->first_title.' ' : '').($o->first_name ?? '')));
            $last = trim((string) (($o->last_name ?? '').($o->last_title ? ', '.$o->last_title : '')));
            return trim($first.(($first !== '' && $last !== '') ? ' ' : '').$last);
        };

        $leaderSnap = null;
        $memberSnaps = [];
        foreach (($normalizedOfficers ?? collect()) as $o) {
            if ((string) $o->class === 'LEADER' && $leaderSnap === null) {
                $leaderSnap = $o;
                continue;
            }
            if ((string) $o->class === 'MEMBER') {
                $memberSnaps[] = $o;
            }
        }

        // Kepada text
        $kepadaText = '';
        $rowsSnap = [];
        if ($leaderSnap) {
            $rowsSnap[] = $leaderSnap;
        }
        foreach ($memberSnaps as $m) {
            $rowsSnap[] = $m;
        }

        $lines = [];
        $noLine = 1;
        foreach ($rowsSnap as $o) {
            $nm = $formatSnapshotName($o);
            $rk = $rankName($o->rank_id ?? null);
            $nrp = trim((string) ($o->register_number ?? ''));
            $pos = $positionName($o->position_id ?? null);

            $parts = array_filter([
                $nm,
                $rk,
                ($nrp !== '' ? 'NRP '.$nrp : ''),
                $pos,
            ], fn ($x) => trim((string) $x) !== '');

            $lines[] = $noLine++.'. '.implode(', ', $parts);
        }
        $kepadaText = implode("\n", $lines);
        if ($kepadaText === '' && ! empty($payload['kepada_text']) && is_string($payload['kepada_text'])) {
            // legacy fallback only
            $kepadaText = $payload['kepada_text'];
        }

        // Block officers for template cloning
        $blockOfficers = [];
        $no = 1;
        foreach ($rowsSnap as $o) {
            $blockOfficers[] = [
                'number' => $no++,
                'first_name' => (string) ($o->first_title ? $o->first_title . ' ' . $o->first_name : $o->first_name),
                'last_name' => (string) ($o->last_title ? $o->last_name . ', ' . $o->last_title : $o->last_name),
                'rank_id' => $rankName($o->rank_id ?? null),
                'officer_id' => (string) ($o->register_number ?? ''),
                'position' => $positionName($o->position_id ?? null),
            ];
        }
        $alasanKey = (string) ($payload['alasan'] ?? '');
        $alasanMap = [
            'dikeluarkan' => 'dikeluarkan',
            'melarikan_diri' => 'melarikan diri',
            'dibantarkan' => 'dibantarkan',
            'ditangguhkan' => 'ditangguhkan',
            'dipindahkan' => 'dipindahkan penahanannya ke kesatuan lain',
            'dialihkan' => 'dialihkan jenis penahanannya',
        ];
        // Fallback hardcode bila belum ada data
        $alasanText = $alasanMap[$alasanKey] ?? ($alasanKey !== '' ? $alasanKey : 'dikeluarkan');
        $penem = (isset($payload['penempatan']) && is_array($payload['penempatan'])) ? $payload['penempatan'] : [];
        $penempatanParts = [];
        if (! empty($penem['type'])) {
            $penempatanParts[] = (string) $penem['type'];
        }
        if (! empty($penem['detail'])) {
            $penempatanParts[] = (string) $penem['detail'];
        }
        $penempatan = implode(' - ', array_filter($penempatanParts));

        // Buat 1 kalimat final (memilih 1 dari a/b/c seperti form)
        $penempatanType = (string) ($penem['type'] ?? '');
        $penempatanDetail = (string) ($penem['detail'] ?? '');
        $penempatanPrisonId = (string) ($penem['prison_id'] ?? '');
        $penempatanPrison = $penempatanPrisonId !== '' ? Prison::query()->find($penempatanPrisonId) : null;
        // Dropdown form memakai "{province} — {name}{ (branch) }", tapi untuk kalimat Word
        // kita hanya ingin "{name}" (tanpa prefix provinsi) di bagian "rumah tahanan Negara ...".
        $penempatanPrisonDropdownLabel = '';
        $penempatanPrisonNameOnly = '';
        if ($penempatanPrison) {
            $province = trim((string) ($penempatanPrison->province ?? ''));
            $name = trim((string) ($penempatanPrison->name ?? ''));
            $branch = trim((string) ($penempatanPrison->branch ?? ''));
            $left = $province !== '' ? ($province . ' — ' . $name) : $name;
            $penempatanPrisonDropdownLabel = trim($left . ($branch !== '' ? ' (' . $branch . ')' : ''));
            $penempatanPrisonNameOnly = $name;
        }

        $extractBranchFromLabel = function (string $label): string {
            $label = trim($label);
            if ($label === '') {
                return '';
            }
            if (preg_match('/\(([^)]+)\)\s*$/u', $label, $m)) {
                return trim((string) ($m[1] ?? ''));
            }
            return '';
        };

        $penempatanLine = '';
        if ($penempatanType === 'rutan') {
            // Nama rutan untuk kalimat utama: hanya name (bukan "Provinsi — Name").
            $rutanName = $penempatanPrisonNameOnly !== '' ? $penempatanPrisonNameOnly : $penempatanDetail;

            // Cabang: prioritas kolom branch; fallback parse dari label dropdown (bagian dalam kurung terakhir).
            $branchText = trim((string) ($penempatanPrison?->branch ?? ''));
            if ($branchText === '') {
                $branchText = $extractBranchFromLabel($penempatanPrisonDropdownLabel);
            }
            if ($branchText === '') {
                $branchText = $extractBranchFromLabel($penempatanDetail);
            }

            $cabangClause = $branchText !== ''
                ? (' cabang ' . $branchText . ' (Satker)')
                : ' cabang ... (Satker)';

            $penempatanLine = 'rumah tahanan Negara ' . ($rutanName !== '' ? $rutanName : '...') . $cabangClause;
        } elseif ($penempatanType === 'rumah') {
            $penempatanLine = 'rumah tempat tinggal/kediaman tersangka di ' . ($penempatanDetail !== '' ? $penempatanDetail : '...');
        } elseif ($penempatanType === 'kota') {
            $penempatanLine = 'kota tempat tinggal/kediaman tersangka di ' . ($penempatanDetail !== '' ? $penempatanDetail : '...');
        } else {
            // fallback bila belum ada data di DB
            $penempatanLine = $penempatanDetail !== '' ? $penempatanDetail : 'rumah tahanan Negara ... cabang ... (Satker)';
        }

        //===============================================================
        // Signatory (konsisten: diambil dari Ketua Tim bila ada)
        $polres = $accident?->polres;
        $polda = $polres?->polda;

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . ($polres?->full_name ?? ''),
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . ($polres?->full_name ?? ''),
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . ($polda?->full_name ?? ''),
        ];

        $signaturePayload = (isset($payload['signature']) && is_array($payload['signature'])) ? $payload['signature'] : [];
        $submittedPayload = (isset($payload['submitted']) && is_array($payload['submitted'])) ? $payload['submitted'] : [];

        $signatoryName = '';
        $signatoryRankName = '';
        $signatoryRegisterNumber = '';
        $signatoryPositionHeadText = '';
        if (! empty($signaturePayload)) {
            $signatoryName = (string) ($signaturePayload['name'] ?? '');
            $signatoryRankName = (string) ($signaturePayload['rank_name'] ?? '');
            $signatoryRegisterNumber = (string) ($signaturePayload['register_number'] ?? '');
            $signatoryPositionHeadText = (string) ($signaturePayload['position_name'] ?? '');
        } elseif ($leaderSnap) {
            // fallback legacy: pakai ketua tim
            $signatoryName = $formatSnapshotName($leaderSnap);
            $signatoryRankName = $rankName($leaderSnap->rank_id ?? null);
            $signatoryRegisterNumber = (string) ($leaderSnap->register_number ?? '');
            $signatoryPositionHeadText = $positionName($leaderSnap->position_id ?? null);
        }

        // Assign (dari "Kepada" urutan pertama) → default ketua tim
        $assignName = $leaderSnap ? $formatSnapshotName($leaderSnap) : '';
        $assignRankName = $leaderSnap ? $rankName($leaderSnap->rank_id ?? null) : '';
        $assignRegisterNumber = $leaderSnap ? (string) ($leaderSnap->register_number ?? '') : '';

        // Submitted (yang menyerahkan) → dari payload bila ada
        $submittedName = (string) ($submittedPayload['name'] ?? '');
        $submittedRankName = (string) ($submittedPayload['rank_name'] ?? '');
        $submittedRegisterNumber = (string) ($submittedPayload['register_number'] ?? '');

        // Head text mengikuti pola SPDP, fallback KAPOLRES
        $signatoryHeadText = $signatureTitleText['KAPOLRES'];
        $leaderClusterId = $leaderSnap ? $positionClusterId($leaderSnap->position_id ?? null) : '';
        if ($leaderClusterId !== '') {
            if ($leaderClusterId == '1') {
                $signatoryHeadText = $signatureTitleText['KAPOLRES'];
            } elseif ($leaderClusterId == '9') {
                $signatoryHeadText = $signatureTitleText['NO_DIRLANTAS'];
            } else {
                $signatoryHeadText = $signatureTitleText['NO_KAPOLRES'];
            }
        }

        //===============================================================
        // Set template values (core + placeholder yang mirip penahanan)
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);

        $templateProcessor->setValue('suspectName', $suspectName);
        $templateProcessor->setValue('suspectIdentityNumber', $suspectIdentityNumber);
        $templateProcessor->setValue('suspectGenderName', $suspectGenderName);
        $templateProcessor->setValue('suspectBirthPlace', $suspectBirthPlace);
        $templateProcessor->setValue('suspectBirthDate', $suspectBirthDate);
        $templateProcessor->setValue('suspectReligionName', $suspectReligionName);
        $templateProcessor->setValue('religionName', $suspectReligionName);
        $templateProcessor->setValue('suspectJobName', $suspectJobName);
        $templateProcessor->setValue('suspectNationality', $suspectNationality);
        $templateProcessor->setValue('suspectFullAddress', $suspectFullAddress);

        $templateProcessor->setValue('extensionTo', (string) ($doc->extension_to ?? ''));
        $templateProcessor->setValue('extensionDays', $extensionDays);
        $templateProcessor->setValue('extensionStartDate', $extensionStartDate);
        $templateProcessor->setValue('extensionEndDate', $extensionEndDate);
        $templateProcessor->setValue('kepadaText', $fill($kepadaText));
        // Kirim versi final (yang dipilih)
        $templateProcessor->setValue('alasan', $fill($alasanText));
        $templateProcessor->setValue('penempatan', $fill($penempatanLine));

        $templateProcessor->setValue('sprindikNumber', (string) ($references['sprindik_document_number'] ?? ''));
        $templateProcessor->setValue('sketNumber', $fill($sketNumber));
        $templateProcessor->setValue('sphNumber', $fill($sphNumber));
        // Support key lama bila masih ada di template lain
        $templateProcessor->setValue('sphName', $fill($sphNumber));

        // Placeholder nomor perintah lain: gunakan yang diisi di form (payload.orders), fallback dot
        $templateProcessor->setValue('releaseOrderNumber', $fill($orders['release_order_number'] ?? ''));
        $templateProcessor->setValue('hospitalizationOrderNumber', $fill($orders['hospitalization_order_number'] ?? ''));
        $templateProcessor->setValue('revokeHospitalizationOrderNumber', $fill($orders['revoke_hospitalization_order_number'] ?? ''));
        $templateProcessor->setValue('transferDetentionPlaceOrderNumber', $fill($orders['transfer_detention_place_order_number'] ?? ''));
        $templateProcessor->setValue('transferDetentionTypeOrderNumber', $fill($orders['transfer_detention_type_order_number'] ?? ''));

        // Sisa masa tahanan: pakai logic dari rentang dokumen ini; jika belum bisa dihitung, dot
        $templateProcessor->setValue('remainingDetentionDays', $fill($remainingDetentionDays));

        // Placeholder tambahan yang sering muncul di template (biar tidak tampil ${...})
        $templateProcessor->setValue('sketDate', $fill($sketDate));
        $templateProcessor->setValue('sketSuspectName', $fill($sketSuspectName));

        $templateProcessor->setValue('sphDate', $fill($sphDate));
        $templateProcessor->setValue('sphSuspectName', $fill($sphSuspectName));

        // Tanggal + atas nama perintah lain: ambil dari payload.orders jika ada, fallback dot
        $templateProcessor->setValue('releaseOrderDate', $fill($orders['release_order_date'] ?? ''));
        $templateProcessor->setValue('releaseOrderSuspectName', $fill($orders['release_order_suspect_name'] ?? $suspectName));
        $templateProcessor->setValue('hospitalizationOrderDate', $fill($orders['hospitalization_order_date'] ?? ''));
        $templateProcessor->setValue('hospitalizationOrderSuspectName', $fill($orders['hospitalization_order_suspect_name'] ?? $suspectName));
        $templateProcessor->setValue('revokeHospitalizationOrderDate', $fill($orders['revoke_hospitalization_order_date'] ?? ''));
        $templateProcessor->setValue('revokeHospitalizationOrderSuspectName', $fill($orders['revoke_hospitalization_order_suspect_name'] ?? $suspectName));
        $templateProcessor->setValue('transferDetentionPlaceOrderDate', $fill($orders['transfer_detention_place_order_date'] ?? ''));
        $templateProcessor->setValue('transferDetentionPlaceOrderSuspectName', $fill($orders['transfer_detention_place_order_suspect_name'] ?? $suspectName));
        $templateProcessor->setValue('transferDetentionTypeOrderDate', $fill($orders['transfer_detention_type_order_date'] ?? ''));
        $templateProcessor->setValue('transferDetentionTypeOrderSuspectName', $fill($orders['transfer_detention_type_order_suspect_name'] ?? $suspectName));

        // Untuk kalimat "mulai tanggal ... s.d. ..." pakai rentang dokumen ini (logic)
        $templateProcessor->setValue('remainingDetentionStartDate', $fill($extensionStartDate));
        $templateProcessor->setValue('remainingDetentionEndDate', $fill($extensionEndDate));

        // Serah terima: bisa diinput dari UI; fallback ke tanggal surat
        $handover = (isset($payload['handover']) && is_array($payload['handover'])) ? $payload['handover'] : [];
        $handoverDate = trim((string) ($handover['date'] ?? ''));
        $handoverDayName = '';

        if ($handoverDate === '') {
            try {
                $handoverDayName = Carbon::parse($doc->document_date)->locale('id')->translatedFormat('l');
                $handoverDate = Carbon::parse($doc->document_date)->locale('id')->translatedFormat('d F Y');
            } catch (\Throwable $e) {
                $handoverDayName = '';
                $handoverDate = '';
            }
        } else {
            try {
                $handoverDayName = Carbon::parse($handoverDate)->locale('id')->translatedFormat('l');
                $handoverDate = Carbon::parse($handoverDate)->locale('id')->translatedFormat('d F Y');
            } catch (\Throwable $e) {
                // biarkan string apa adanya
            }
        }
        $templateProcessor->setValue('handoverDayName', $fill($handoverDayName));
        $templateProcessor->setValue('handoverDate', $fill($handoverDate));

        // Render daftar petugas jika template memakai block_officers
        if (! empty($blockOfficers)) {
            $templateProcessor->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);
        } else {
            // hilangkan blok jika tidak ada data
            $templateProcessor->cloneBlock('block_officers', 0, true, false);
        }

        // Placeholder mengikuti template surat_perintah_penahanan.docx (kop & sprindik)
        $templateProcessor->setValue('daerahPoliceFullName', (string) ($polda?->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceFullName', (string) ($polres?->full_name ?? ''));
        $templateProcessor->setValue('resorPoliceAddress', (string) ($polres?->full_address ?? $polres?->address ?? ''));
        $templateProcessor->setValue('suratPerintahPenyidikanNumber', $sprindikNumber);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDate', $sprindikDate);

        // Signatory placeholders (+ variasi key yang sering pecah di docx)
        $templateProcessor->setValue('signatoryHeadText', (string) $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText);
        $templateProcessor->setValue('signatoryRankName', $signatoryRankName);
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);
        $templateProcessor->setValue('signato-ryRegisterNumber', $signatoryRegisterNumber);
        $templateProcessor->setValue('signatoryRegister-Number', $signatoryRegisterNumber);

        // Assign placeholders (dari "Kepada" urutan pertama)
        $templateProcessor->setValue('assignName', $fill($assignName));
        $templateProcessor->setValue('assignRankName', $fill($assignRankName));
        $templateProcessor->setValue('assignRegisterNumber', $fill($assignRegisterNumber));

        // Submitted placeholders (yang menyerahkan)
        $templateProcessor->setValue('submittedName', $fill($submittedName));
        $templateProcessor->setValue('submittedRankName', $fill($submittedRankName));
        $templateProcessor->setValue('submittedRegisterNumber', $fill($submittedRegisterNumber));

        //===============================================================
        // Save and download
        $filename = 'generate/' . $doc->id . ' - Surat Perintah Penahanan Lanjutan - ' . ($polres?->full_name ?? 'ICELL');
        $templateProcessor->saveAs($filename . '.docx');

        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
    }

    private function pickFirstTemplateFromDir(string $dir): ?string
    {
        try {
            $files = glob(rtrim($dir, '/').DIRECTORY_SEPARATOR.'*.docx') ?: [];
            return $files[0] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatIdDatePdf($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return Carbon::parse($value)
                ->locale('id')
                ->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}

