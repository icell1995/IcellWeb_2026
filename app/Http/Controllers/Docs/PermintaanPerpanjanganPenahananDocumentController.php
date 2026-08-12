<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocument;
use App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocumentOfficer;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Lib\Court;
use App\Models\Lib\Location;
use App\Models\Lib\DocumentClassification;
use App\Models\Lib\Prison;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Opt\PositionCluster;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class PermintaanPerpanjanganPenahananDocumentController extends Controller
{
    use DocsOfficersTraits;

    /** Induk SPH: harus selesai persetujuan unggahan PDF (status 86). */
    private const STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN = ['86'];

    /** Perihal untuk template Word — tidak diinput di form (sama pola dengan SPDP). */
    private const DEFAULT_HAL_SUBJECT = 'permohonan perpanjangan penahanan tersangka.';
    private const DEFAULT_REQUESTED_EXTENSION_DAYS = 30;

    private function isEligiblePrerequisiteStatus(?string $statusId): bool
    {
        return in_array((string) $statusId, self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN, true);
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        if (! $accident) {
            return redirect()->back()->with('error', 'Data perkara tidak ditemukan');
        }
        if (! SuratPerintahPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->exists()) {
            return redirect()
                ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with(
                    'error',
                    'Surat Perintah Penahanan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
        }

        $suspects = Suspect::with(['gender', 'religion'])
            ->where('accident_id', $accidentId)
            ->orderBy('name')
            ->get();

        $courts = Court::active()->orderBy('sort')->get();

        // Sprindik tidak dipilih di dokumen ini (hindari dobel dengan SPDP).

        $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->get();

        $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::with(['prosecutor', 'prosecutor.regency'])
            ->where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->first();

        $sket = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect'])
            ->where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->first();

        $sketSuspect = $sket?->suspect?->first();

        $documentClassifications = DocumentClassification::query()
            ->where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)
            ->orderBy('sort')
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

        $contactOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $defaults = [
            'meta' => [
                'document_classification_id' => $spdp?->document_classification_id,
                'classification' => 'Biasa',
                'lampiran' => '1',
                'hal_subject' => self::DEFAULT_HAL_SUBJECT,
                'document_location' => '',
            ],
            'lp' => [
                'number' => $accident?->no_lp ?? null,
                'date' => $accident?->report_date ?? null,
            ],
            'spdp' => [
                'number' => $spdp?->document_number ?? null,
                'date' => $spdp?->document_date ?? null,
            ],
            // Kejaksaan tidak di-pra-pilih dari SPDP — user memilih di form (create blade pakai old() saja).
            'kejaksaan' => [
                'name' => null,
                'regency' => null,
                'prosecutor_id' => null,
            ],
            'references' => [
                'sket_suspect_name' => $sketSuspect?->name ?? null,
            ],
            'requested_extension_days' => 30,
        ];

        $prisons = $this->prisonsSafe();
        $prosecutors = Prosecutor::active()->orderBy('sort')->get();
        $suratPerintahPenahananDocuments = SuratPerintahPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->orderByDesc('document_date')
            ->get();

        return view('docs.permintaan-perpanjangan-penahanan-document.create', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suspects' => $suspects,
            'courts' => $courts,
            'defaults' => $defaults,
            'suratKetetapanTentangPenetapanTersangkaDocuments' => $suratKetetapanTentangPenetapanTersangkaDocuments,
            'suratPemberitahuanDimulainyaPenyidikanDocuments' => $suratPemberitahuanDimulainyaPenyidikanDocuments,
            'prisons' => $prisons,
            'prosecutors' => $prosecutors,
            'suratPerintahPenahananDocuments' => $suratPerintahPenahananDocuments,
            'documentClassifications' => $documentClassifications,
            'authorizedSignatories' => $authorizedSignatories,
            'contactOfficers' => $contactOfficers,
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);

        if (! SuratPerintahPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
            ->exists()) {
            return redirect()
                ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with(
                    'error',
                    'Surat Perintah Penahanan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
        }

        $documentNumber = htmlspecialchars($request->documentNumber ?? $request->nomor_surat);
        $documentDate = htmlspecialchars($request->documentDate ?? $request->tanggal);
        $suspectId = $request->suspect ?? $request->tersangka;
        $requestedExtensionDays = self::DEFAULT_REQUESTED_EXTENSION_DAYS;
        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        $sketId = $request->suratKetetapanTentangPenetapanTersangkaDocument;
        $spdpId = $request->suratPemberitahuanDimulainyaPenyidikanDocument;
        $sphDocId = trim((string) $request->input('suratPerintahPenahanan', ''));

        $sphForAccident = SuratPerintahPenahananDocument::query()
            ->where('id', $sphDocId)
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->first();
        if (! $sphForAccident || ! $this->isEligiblePrerequisiteStatus($sphForAccident->status_id)) {
            return redirect()->back()
                ->with('error', 'Surat Perintah Penahanan yang dipilih harus berstatus 86 (unggahan PDF telah disetujui admin).')
                ->withInput();
        }

        $payload = $this->buildPayload($request, $accidentId, $suspectId, []);

        DB::beginTransaction();
        try {
            $doc = PermintaanPerpanjanganPenahananDocument::create([
                'accident_id' => $accidentId,
                'suspect_id' => $suspectId ?: null,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'requested_extension_days' => $requestedExtensionDays,
                'is_legacy' => $isLegacy,
                'court_id' => $request->court,
                'detention_end_date' => $request->detentionEndDate,
                // sprindik tidak dipakai di dokumen ini
                'sket_document_id' => $sketId ?: null,
                'surat_perintah_penahanan_document_id' => $sphDocId !== '' ? $sphDocId : null,
                'payload' => $payload,
            ]);

            // Normalisasi: snapshot officers/references/laws mengikuti pola dokumen lain (Sprindik/SPDP/Sprinlidik).
            $this->syncNormalizedData($doc, $request, $accidentId);

            // Alur status mengikuti SPH: tetap 2 (dokumen dibuat) sampai user di Berkas Perkara menyelesaikan
            // "Isi Nomor" / "Meminta Persetujuan" → DocumentActionController mengubah ke 3 atau 6.

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Surat Permintaan Perpanjangan Penahanan store failed', ['exception' => $e]);

            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.')->withInput();
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Permintaan Perpanjangan Penahanan berhasil disimpan.');
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $doc = PermintaanPerpanjanganPenahananDocument::where('id', $id)->firstOrFail();
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $suspects = Suspect::with(['gender', 'religion'])
            ->where('accident_id', $accidentId)
            ->orderBy('name')
            ->get();
        $courts = Court::active()->orderBy('sort')->get();

        // Sprindik tidak dipilih di dokumen ini (hindari dobel dengan SPDP).

        $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->get();

        $defaults = array_merge([
            'meta' => [
                'classification' => 'Biasa',
                'lampiran' => '1',
                'hal_subject' => self::DEFAULT_HAL_SUBJECT,
                'document_location' => '',
            ],
        ], $doc->payload ?? []);

        $documentClassifications = DocumentClassification::query()
            ->where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)
            ->orderBy('sort')
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

        $contactOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $prisons = $this->prisonsSafe();
        $prosecutors = Prosecutor::active()->orderBy('sort')->get();
        $suratPerintahPenahananDocuments = SuratPerintahPenahananDocument::query()
            ->where('accident_id', $accidentId)
            ->where(function ($q) use ($doc) {
                $q->where(function ($q2) {
                    $q2->whereNull('deleted_at')
                        ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN);
                });
                if (! empty($doc->surat_perintah_penahanan_document_id)) {
                    $q->orWhere(function ($q2) use ($doc) {
                        $q2->where('id', $doc->surat_perintah_penahanan_document_id)
                            ->whereNull('deleted_at');
                    });
                }
            })
            ->orderByDesc('document_date')
            ->get();

        return view('docs.permintaan-perpanjangan-penahanan-document.edit', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'doc' => $doc,
            'suspects' => $suspects,
            'courts' => $courts,
            'defaults' => $defaults,
            'suratKetetapanTentangPenetapanTersangkaDocuments' => $suratKetetapanTentangPenetapanTersangkaDocuments,
            'suratPemberitahuanDimulainyaPenyidikanDocuments' => $suratPemberitahuanDimulainyaPenyidikanDocuments,
            'prisons' => $prisons,
            'prosecutors' => $prosecutors,
            'suratPerintahPenahananDocuments' => $suratPerintahPenahananDocuments,
            'documentClassifications' => $documentClassifications,
            'authorizedSignatories' => $authorizedSignatories,
            'contactOfficers' => $contactOfficers,
        ]);
    }

    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $doc = PermintaanPerpanjanganPenahananDocument::with(['suspect', 'documentCategory'])->where('id', $id)->firstOrFail();
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $defaults = $doc->payload ?? [];

        return view('docs.permintaan-perpanjangan-penahanan-document.show', [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'doc' => $doc,
            'defaults' => $defaults,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateForm($request, $id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);

        $doc = PermintaanPerpanjanganPenahananDocument::where('id', $id)->firstOrFail();

        $suspectId = $request->suspect ?? $request->tersangka;
        $requestedExtensionDays = (int) ($doc->requested_extension_days ?? self::DEFAULT_REQUESTED_EXTENSION_DAYS);
        $sketId = $request->suratKetetapanTentangPenetapanTersangkaDocument;
        $spdpId = $request->suratPemberitahuanDimulainyaPenyidikanDocument;

        $payload = $this->buildPayload($request, $accidentId, $suspectId, $doc->payload ?? []);
        $sphDocId = trim((string) $request->input('suratPerintahPenahanan', ''));

        $sphForAccident = SuratPerintahPenahananDocument::query()
            ->where('id', $sphDocId)
            ->where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->first();
        if (! $sphForAccident || ! $this->isEligiblePrerequisiteStatus($sphForAccident->status_id)) {
            return redirect()->back()
                ->with('error', 'Surat Perintah Penahanan yang dipilih harus berstatus 86 (unggahan PDF telah disetujui admin).')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $doc->update([
                'suspect_id' => $suspectId ?: null,
                'document_number' => htmlspecialchars($request->documentNumber ?? $request->nomor_surat),
                'document_date' => htmlspecialchars($request->documentDate ?? $request->tanggal),
                'requested_extension_days' => $requestedExtensionDays,
                'is_legacy' => filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN),
                'court_id' => $request->court,
                'detention_end_date' => $request->detentionEndDate,
                // sprindik tidak dipakai di dokumen ini
                'sket_document_id' => $sketId ?: null,
                'surat_perintah_penahanan_document_id' => $sphDocId !== '' ? $sphDocId : null,
                'payload' => $payload,
            ]);

            // Normalisasi: refresh snapshot officers/references/laws.
            $this->syncNormalizedData($doc, $request, $accidentId);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Surat Permintaan Perpanjangan Penahanan update failed', ['exception' => $e]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data')->withInput();
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Permintaan Perpanjangan Penahanan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        DB::beginTransaction();
        try {
            $doc = PermintaanPerpanjanganPenahananDocument::where('id', $id)->firstOrFail();
            $doc->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data.');
        }

        return redirect()
            ->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Permintaan Perpanjangan Penahanan berhasil dihapus.');
    }

    public function download($id)
    {
        // Eager-load relasi penting. Di beberapa environment, lazy-loading bisa dinonaktifkan
        // sehingga tanpa eager-load banyak placeholder akan jatuh ke default ".......".
        $doc = PermintaanPerpanjanganPenahananDocument::with([
            'suspect.job',
            'suspect.gender',
            'suspect.religion',
            'suspect.country',
            'suspect.province',
            'suspect.regency',
            'suspect.district',
            'suspect.village',
            'accident.polres.polda',
        ])
            ->where('id', $id)
            ->firstOrFail();

        $payload = $doc->payload ?? [];

        // =============================================================
        // Officers snapshot (opsional). References & laws snapshots sudah dihapus.
        $normalizedOfficers = collect();

        try {
            $normalizedOfficers = $doc->officers()->orderBy('sort')->get();
            if ($normalizedOfficers->count() > 0) {
                $payload = $this->mergeOfficersFromNormalized($payload, $normalizedOfficers);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $refs = (isset($payload['references']) && is_array($payload['references'])) ? $payload['references'] : [];
        $meta = (isset($payload['meta']) && is_array($payload['meta'])) ? $payload['meta'] : [];
        $lp = (isset($payload['lp']) && is_array($payload['lp'])) ? $payload['lp'] : [];
        $spdp = (isset($payload['spdp']) && is_array($payload['spdp'])) ? $payload['spdp'] : [];
        $kejaksaanMeta = (isset($payload['kejaksaan']) && is_array($payload['kejaksaan'])) ? $payload['kejaksaan'] : [];
        $kejaksaanNameResolved = (string) ($kejaksaanMeta['name'] ?? '');
        // Support key lama (`regency`) + key baru (`location`)
        $kejaksaanLocationResolved = (string) ($kejaksaanMeta['location'] ?? ($kejaksaanMeta['regency'] ?? ''));
        if (empty($kejaksaanNameResolved) && ! empty($kejaksaanMeta['prosecutor_id'])) {
            try {
                $kej = Prosecutor::query()->with(['regency'])->find($kejaksaanMeta['prosecutor_id']);
                $kejaksaanNameResolved = (string) (($kej?->full_name ?? $kej?->name ?? '') ?: '');
                if ($kejaksaanLocationResolved === '') {
                    $kejaksaanLocationResolved = (string) ($kej?->regency?->name ?? '');
                }
            } catch (\Throwable $e) {
                $kejaksaanNameResolved = '';
                $kejaksaanLocationResolved = '';
            }
        }
        $kejaksaan = (isset($payload['kejaksaan_extension']) && is_array($payload['kejaksaan_extension'])) ? $payload['kejaksaan_extension'] : [];
        $perpanjanganOrder = (isset($payload['perpanjangan_order']) && is_array($payload['perpanjangan_order'])) ? $payload['perpanjangan_order'] : [];
        $narrative = (isset($payload['narrative']) && is_array($payload['narrative'])) ? $payload['narrative'] : [];
        $extension = (isset($payload['extension']) && is_array($payload['extension'])) ? $payload['extension'] : [];
        $s21 = (isset($payload['s21_body']) && is_array($payload['s21_body'])) ? $payload['s21_body'] : [];
        $contact = (isset($payload['contact']) && is_array($payload['contact'])) ? $payload['contact'] : [];
        $signature = (isset($payload['signature']) && is_array($payload['signature'])) ? $payload['signature'] : [];

        $templatePath = $this->resolveTemplatePath();
        if (! $templatePath) {
            abort(404, 'Template Surat Permintaan Perpanjangan Penahanan belum tersedia. Letakkan surat_permintaan_perpanjangan_penahanan.docx di public/word-template/ atau .docx di public/file/penahanan/permintaan-perpanjangan-penahanan/');
        }

        $courtId = $doc->court_id ?? ($payload['court_id'] ?? null);
        $court = $courtId ? Court::query()->find($courtId) : null;

        // Pastikan relasi `job` ikut kebaca (SPDP pakai $suspect->job->name).
        $suspect = $doc->suspect;

        $suspectJobName = trim((string) (
            $suspect?->job?->name
            ?? $suspect?->occupation
            ?? $suspect?->job_name
            ?? ''
        ));
        // Konsisten dengan SPDP: kalau ada isi -> rapikan Title Case, kalau kosong -> tampilkan "-" (bukan placeholder ".......")
        $suspectJobName = $suspectJobName !== '' ? ucwords(strtolower($suspectJobName)) : '-';

        // Suspect nationality (samakan dengan SPDP: pakai master country jika ada)
        $suspectNationality = trim((string) (
            $suspect?->country?->name
            ?? $suspect?->nationality
            ?? $suspect?->country
            ?? ''
        ));
        $suspectNationality = $suspectNationality !== '' ? strtoupper($suspectNationality) : '';

        // Suspect full address (samakan dengan SPDP: gabungkan address + lokasi bila tersedia)
        $addressParts = [];
        $addr = trim((string) ($suspect?->address ?? ''));
        if ($addr !== '') {
            $addressParts[] = $addr;
        }
        if (! empty($suspect?->village?->name)) {
            $addressParts[] = 'Kel. '.$suspect->village->name;
        }
        if (! empty($suspect?->district?->name)) {
            $addressParts[] = 'Kec. '.$suspect->district->name;
        }
        if (! empty($suspect?->regency?->name)) {
            $addressParts[] = $suspect->regency->name;
        }
        if (! empty($suspect?->province?->name)) {
            $addressParts[] = $suspect->province->name;
        }
        $suspectFullAddress = trim(implode(', ', array_filter($addressParts)));

        $fill = function (?string $v) {
            $t = trim((string) $v);
            return $t !== '' ? $t : '.......';
        };
        // Untuk placeholder yang memang boleh kosong (mis. baris jabatan ketika HeadText = KAPOLRES)
        $emptyOk = function (?string $v): string {
            return trim((string) $v);
        };

        $stripNomorPrefix = function (?string $s): string {
            $t = trim((string) $s);
            $t = preg_replace('/^Nomor\s*:\s*/iu', '', $t);
            return trim((string) $t);
        };

        $tp = new TemplateProcessor($templatePath);

        $docNum = $stripNomorPrefix($doc->document_number ?? '');

        // =============================================================
        // Alias placeholder agar konsisten dengan dokumen SPDP & dokumen lain
        $accident = $doc->accident;
        $polres = $accident?->polres;
        $polda = $polres?->polda;

        $resorPoliceFullName = '';
        if ($polres) {
            // Pola yang dipakai dokumen lain: "RESOR " + uppercase full_name (dengan pengecualian tertentu)
            $resorPoliceFullName = (in_array((string) $polres->id, ['1114']))
                ? 'DIREKTORAT LALU LINTAS'
                : 'RESOR ' . strtoupper((string) ($polres->full_name ?? $polres->name ?? ''));
        }

        $polresAlamat = '';
        try {
            if ($polres) {
                $polresAlamat = ucwords(strtolower(trim(
                    (string) ($polres->address ?? '') .
                    (!empty($polres->polres_district) ? ', ' . $polres->polres_district : '') .
                    (!empty($polres->polres_zipcode) ? ', ' . $polres->polres_zipcode : '')
                )));
            }
        } catch (\Throwable $e) {
            $polresAlamat = '';
        }

        // Header SPDP-like
        $daerahPoliceFullName = strtoupper((string) ($polda?->full_name ?? $polda?->name ?? ''));
        $resorPoliceAddress = $polresAlamat;

        // Placeholder gaya lama (masih dipertahankan)
        $tp->setValue('polda_name', $fill((string) ($polda?->full_name ?? $polda?->name ?? '')));
        $tp->setValue('polres_name', $fill($resorPoliceFullName));
        $tp->setValue('polres_alamat', $fill($polresAlamat));

        // Placeholder gaya SPDP (Docs controller)
        $tp->setValue('daerahPoliceFullName', $fill($daerahPoliceFullName));
        $tp->setValue('resorPoliceFullName', $fill($resorPoliceFullName));
        $tp->setValue('resorPoliceAddress', $fill($resorPoliceAddress));

        // Alias field surat
        $tp->setValue('no_spdp', $fill($docNum)); // alias: nomor surat
        $tp->setValue('klasifikasi', $fill($meta['classification'] ?? ''));
        $tp->setValue('perihal', $fill($meta['hal_subject'] ?? self::DEFAULT_HAL_SUBJECT));
        $tp->setValue('accident_date', $fill($this->formatIdDate($accident?->accident_date ?? $accident?->report_date ?? null)));
        $tp->setValue('accident_day', $fill($accident?->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : ''));
        $tp->setValue('accident_time', $fill($accident?->accident_time ? Carbon::parse($accident->accident_time)->locale('id')->translatedFormat('H:i') : ''));
        $tp->setValue('road_name', $fill((string) ($accident?->road_name ?? '')));

        $tp->setValue('documentNumber', $fill($docNum));
        $documentDateText = $fill($this->formatIdDate($doc->document_date ?? null));
        $documentLocationRaw = trim((string) ($meta['document_location'] ?? ''));
        $defaultDocLoc = '';
        if ($polres) {
            // Konsisten dengan SPDP Docs: lokasi surat diambil dari provinsi polres
            $defaultDocLoc = ucwords(strtolower((string) ($polres->polres_province ?? $polres->polres_district ?? '')));
        }
        $documentLocationResolved = $documentLocationRaw !== '' ? $documentLocationRaw : $defaultDocLoc;
        $documentLocationText = $fill($documentLocationResolved);
        $tp->setValue('documentDate', $documentDateText);
        $tp->setValue('documentLocation', $documentLocationText);
        // Alias untuk konsistensi dengan template SPDP lama yang memakai `spdp_date`
        // (di dokumen ini artinya: tanggal surat)
        $tp->setValue('spdp_date', $documentDateText);
        // Alias opsional (kalau ada template lama yang memakai format SPDP untuk lokasi)
        $tp->setValue('spdp_location', $documentLocationText);
        $tp->setValue('classification', $fill($meta['classification'] ?? ''));
        $tp->setValue('lampiran', $fill($meta['lampiran'] ?? ''));
        $tp->setValue('appendix', $fill($meta['lampiran'] ?? ''));
        $tp->setValue('halSubject', $fill($meta['hal_subject'] ?? self::DEFAULT_HAL_SUBJECT));

        $documentClassificationId = (string) ($meta['document_classification_id'] ?? '');
        $documentClassificationName = $this->resolveClassificationName($documentClassificationId);
        $tp->setValue('documentClassificationName', $fill($documentClassificationName));

        $courtName = $court ? ($court->full_name ?: $court->name) : '';
        $tp->setValue('courtName', $fill($courtName));
        $courtCityLine = $this->resolveCourtRegencyName($courtId ? (string) $courtId : '');
        if ($courtCityLine === '') {
            $courtCityLine = (string) ($payload['court_city'] ?? '');
        }
        $tp->setValue('courtCity', $fill($courtCityLine));
        $tp->setValue('regency', $fill($courtCityLine)); // alias: gaya SPDP (di ....)

        $tp->setValue('accidentNumber', $fill($doc->accident?->no_lp ?? $lp['number'] ?? ''));
        $tp->setValue('lpNumber', $fill($lp['number'] ?? $doc->accident?->no_lp ?? ''));
        $tp->setValue('lpDate', $fill($this->formatIdDate($lp['date'] ?? $doc->accident?->report_date ?? null)));

        // Sprindik tidak dipakai di dokumen ini (hindari dobel dengan SPDP)
        $tp->setValue('sprindikNumber', $fill(''));
        $tp->setValue('sprindikDate', $fill(''));
        $tp->setValue('no_sprindik', $fill(''));

        $tp->setValue('sketNumber', $fill($refs['sket_document_number'] ?? ''));
        $tp->setValue('sketDate', $fill($this->formatIdDate($refs['sket_document_date'] ?? null)));
        $tp->setValue('sketSuspectName', $fill($refs['sket_suspect_name'] ?? $suspect?->name ?? ''));

        $tp->setValue('sphNumber', $fill($refs['sph_number'] ?? ''));
        $tp->setValue('sphDate', $fill($this->formatIdDate($refs['sph_date'] ?? null)));
        $tp->setValue('sphSuspectName', $fill($refs['sph_suspect_name'] ?? $suspect?->name ?? ''));

        $tp->setValue('spdpNumber', $fill($spdp['number'] ?? ''));
        $tp->setValue('spdpDate', $fill($this->formatIdDate($spdp['date'] ?? null)));
        $tp->setValue('nama_kejaksaan', $fill($kejaksaanNameResolved));
        $tp->setValue('prosecutorName', $fill($kejaksaanNameResolved));
        // Samakan gaya SPDP (Title Case, bukan ALL CAPS)
        $tp->setValue('prosecutorLocation', $fill(ucwords(strtolower($kejaksaanLocationResolved))));
        // Alias yang sering muncul di template SPDP lama
        $tp->setValue('no_spdp_ref', $fill($spdp['number'] ?? ''));

        $tp->setValue('kejaksaanExtensionNumber', $fill($kejaksaan['number'] ?? ''));
        $tp->setValue('kejaksaanExtensionDate', $fill($this->formatIdDate($kejaksaan['date'] ?? null)));
        $tp->setValue('kejaksaanExtensionSuspectName', $fill($kejaksaan['suspect_name'] ?? ''));

        $tp->setValue('perpanjanganOrderNumber', $fill($perpanjanganOrder['number'] ?? ''));
        $tp->setValue('perpanjanganOrderDate', $fill($this->formatIdDate($perpanjanganOrder['date'] ?? null)));
        $tp->setValue('perpanjanganOrderSuspectName', $fill($perpanjanganOrder['suspect_name'] ?? ''));

        // Rujukan d: section UU yang dikenakan sudah dihapus dari form dokumen ini.
        // Tetap set placeholder ini agar template lama tidak menampilkan ${crimeArticlesText}.
        $tp->setValue('crimeArticlesText', $fill(''));

        $unitLine = $narrative['unit_extra'] ?? '';
        if (trim((string) $unitLine) === '' && $doc->accident?->polres) {
            $unitLine = (string) ($doc->accident->polres->full_name ?? $doc->accident->polres->name ?? '');
        }

        // Jika dokumen lama belum punya payload No.2, auto-fill saat download dari Sprindik/SPDP.
        $needsAutoS21 = false;
        foreach (['satker_penyidik', 'dugaan_tindak_pidana', 'pasal_diduga', 'tempat_kejadian', 'kurun_waktu'] as $k) {
            if (trim((string) ($s21[$k] ?? '')) === '') {
                $needsAutoS21 = true;
                break;
            }
        }
        if ($needsAutoS21) {
            $auto = $this->resolveS21BodyAuto($doc);
            // isi hanya yang kosong, jangan timpa input manual yang sudah ada
            foreach ($auto as $k => $v) {
                if (trim((string) ($s21[$k] ?? '')) === '' && trim((string) $v) !== '') {
                    $s21[$k] = $v;
                }
            }
        }

        // Paragraf 2-3 (mengikuti format surat)
        $tp->setValue('satker_penyidik', $fill($s21['satker_penyidik'] ?? $unitLine));
        $tp->setValue('dugaan_tindak_pidana', $fill($s21['dugaan_tindak_pidana'] ?? ''));
        $tp->setValue('pasal_diduga', $fill($s21['pasal_diduga'] ?? ''));
        $tp->setValue('tempat_kejadian', $fill($s21['tempat_kejadian'] ?? ''));
        $tp->setValue('kurun_waktu', $fill($s21['kurun_waktu'] ?? ''));
        $tp->setValue('alasan_perpanjangan', $fill($s21['alasan_perpanjangan'] ?? ''));
        // Jika tidak ada input khusus kejaksaan_akhir_tanggal, fallback ke akhir masa penahanan berjalan (detentionEndDate)
        $kejAkhir = $s21['kejaksaan_akhir_tanggal'] ?? null;
        if (empty($kejAkhir)) {
            $kejAkhir = $doc->detention_end_date ?? ($payload['detention_end_date'] ?? null);
        }
        $tp->setValue('kejaksaan_akhir_tanggal', $fill($this->formatIdDate($kejAkhir)));
        $tp->setValue('rutan_name', $fill($extension['prison_line'] ?? ''));

        $tp->setValue('investigatingUnitText', $fill($unitLine));
        $tp->setValue('caseNarrativeText', $fill($narrative['case_extra'] ?? ''));

        $tp->setValue('detentionEndDate', $fill($this->formatIdDate($doc->detention_end_date ?? ($payload['detention_end_date'] ?? null))));
        $tp->setValue('requestedExtensionDays', (string) max(1, (int) ($doc->requested_extension_days ?? 30)));

        $tp->setValue('extensionPlaceName', $fill($extension['prison_line'] ?? ''));
        $tp->setValue('extensionStartDate', $fill($this->formatIdDate($extension['start_date'] ?? null)));
        $tp->setValue('extensionEndDate', $fill($this->formatIdDate($extension['end_date'] ?? null)));

        $tp->setValue('suspectName', $fill($suspect?->name ?? ''));
        $tp->setValue('nama_tersangka', $fill($suspect?->name ?? ''));
        $tp->setValue('suspectIdentityNumber', $fill($suspect?->identity_number ?? ''));
        $tp->setValue('suspectGenderName', $fill($suspect?->gender?->name ?? ''));
        $tp->setValue('jenis_kelamin', $fill($suspect?->gender?->name ?? ''));
        $tp->setValue('suspectBirthPlace', $fill($suspect?->birth_place ?? ''));
        $tp->setValue('suspectBirthDate', $fill($this->formatIdDate($suspect?->birth_date ?? null)));
        $tp->setValue('tempat_lahir', $fill($suspect?->birth_place ?? ''));
        $tp->setValue('tanggal_lahir', $fill($this->formatIdDate($suspect?->birth_date ?? null)));
        $tp->setValue('suspectJobName', $fill($suspectJobName));
        $tp->setValue('pekerjaan', $fill($suspectJobName));
        $tp->setValue('suspectReligionName', $fill($suspect?->religion?->name ?? ''));
        $tp->setValue('agama', $fill($suspect?->religion?->name ?? ''));
        $tp->setValue('suspectNationality', $fill($suspectNationality));
        $tp->setValue('suspectFullAddress', $fill($suspectFullAddress !== '' ? $suspectFullAddress : ($suspect?->address ?? '')));
        $tp->setValue('alamat', $fill($suspect?->address ?? ''));

        // Block suspects mengikuti SPDP Docs (walau hanya 1 tersangka, template bisa pakai block)
        $blockSuspects = [
            [
                'suspectName' => $fill($suspect?->name ?? ''),
                'suspectIdentityNumber' => $fill($suspect?->identity_number ?? ''),
                'suspectGenderName' => $fill($suspect?->gender?->name ?? ''),
                'suspectBirthPlace' => $fill($suspect?->birth_place ?? ''),
                'suspectBirthDate' => $fill($this->formatIdDate($suspect?->birth_date ?? null)),
                'suspectJobName' => $fill($suspectJobName),
                'suspectReligionName' => $fill($suspect?->religion?->name ?? ''),
                'suspectNationality' => $fill($suspectNationality),
                'suspectFullAddress' => $fill($suspectFullAddress !== '' ? $suspectFullAddress : ($suspect?->address ?? '')),
            ],
        ];
        try {
            $tp->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        } catch (\Throwable $e) {
            // jika template tidak punya block_suspects, abaikan
        }

        $tp->setValue('contactOfficerName', $fill($contact['officer_name'] ?? ''));
        $tp->setValue('contactOfficerPhone', $fill($contact['officer_phone'] ?? ''));

        $tp->setValue('signatoryTitle', $fill($signature['title'] ?? ''));
        $tp->setValue('signatoryName', $fill($signature['name'] ?? ''));
        $tp->setValue('signatoryRankNrp', $fill($signature['rank_nrp'] ?? ''));

        // Penandatangan mengikuti pola SPDP Docs (HeadText a.n. + PositionName)
        $signatureTitleText = [
            'KAPOLRES' => $polres ? ('KEPALA KEPOLISIAN RESOR ' . (string) ($polres->full_name ?? $polres->name ?? '')) : '',
            'NO_KAPOLRES' => $polres ? ('a.n. KEPALA KEPOLISIAN RESOR ' . (string) ($polres->full_name ?? $polres->name ?? '')) : '',
            'NO_DIRLANTAS' => $polda ? ('a.n. DIREKTUR LALU LINTAS POLDA ' . (string) ($polda->full_name ?? $polda->name ?? '')) : '',
        ];

        $sigOfficerId = (string) ($signature['officer_id'] ?? '');
        $sigOfficer = null;
        $signatoryClusterId = '';
        $signatoryClusterAliasName = '';
        $signatoryPositionId = (string) ($signature['position_id'] ?? '');

        // Jika ada snapshot officers, jangan bergantung ke master Officer untuk download.
        if (($normalizedOfficers ?? collect())->count() > 0) {
            try {
                $signatorySnapshot = $normalizedOfficers->firstWhere('class', 'SIGNATORY');
                if ($signatorySnapshot && empty($signatoryPositionId)) {
                    $signatoryPositionId = (string) ($signatorySnapshot->position_id ?? '');
                }
            } catch (\Throwable $e) {
                // ignore
            }

            if ($signatoryPositionId !== '') {
                try {
                    $signatoryClusterId = (string) (Position::query()->where('id', $signatoryPositionId)->value('position_cluster_id') ?? '');
                } catch (\Throwable $e) {
                    $signatoryClusterId = '';
                }
                if ($signatoryClusterId !== '') {
                    try {
                        $signatoryClusterAliasName = trim((string) (PositionCluster::query()->where('id', $signatoryClusterId)->value('alias_name') ?? ''));
                    } catch (\Throwable $e) {
                        $signatoryClusterAliasName = '';
                    }
                }
            }
        } elseif ($sigOfficerId !== '') {
            // Legacy fallback saja (dokumen lama tanpa snapshot)
            if ($sigOfficerId !== '') {
                try {
                    // NOTE: withRelated() tidak memuat position.positionCluster, padahal SPDP butuh alias_name.
                    $sigOfficer = Officer::withRelated()
                        ->with(['position.positionCluster'])
                        ->find($sigOfficerId);
                } catch (\Throwable $e) {
                    $sigOfficer = null;
                }
            }
        }

        // === Penandatangan: konsisten SPDP (a.n. + alias jabatan)
        // Problem data: beberapa posisi non-Kapolres bisa punya position_cluster_id=1.
        // Jika ada alias_name (mis. "KASAT LANTAS"), kita paksa pola a.n. + tampilkan alias.
        $signatoryPosition = $sigOfficer?->position;
        $clusterId = $signatoryClusterId !== '' ? $signatoryClusterId : (string) ($signatoryPosition?->position_cluster_id ?? '');
        $clusterAliasName = $signatoryClusterAliasName !== '' ? $signatoryClusterAliasName : trim((string) ($sigOfficer?->position?->positionCluster?->alias_name ?? ''));

        $signatoryHeadText = $signatureTitleText['NO_KAPOLRES'];
        $signatoryPositionName = $clusterAliasName !== '' ? $clusterAliasName : 'KASAT LANTAS';

        if ($clusterId === '9') {
            $signatoryHeadText = $signatureTitleText['NO_DIRLANTAS'];
        } elseif ($clusterId === '1' && $clusterAliasName === '') {
            // Kapolres murni: tanpa a.n. dan tanpa baris jabatan
            $signatoryHeadText = $signatureTitleText['KAPOLRES'];
            $signatoryPositionName = '';
        }

        // Placeholder lama (beberapa template lama masih pakai ini)
        $tp->setValue('officer_signature_title_text', $fill($signatoryHeadText));
        $tp->setValue('pejabat_name', $fill($signature['name'] ?? ''));
        // Field gabungan: "Pangkat NRP xxxxx" di SPDP biasanya dipisah
        $rankNrp = (string) ($signature['rank_nrp'] ?? '');
        $rankOnly = $rankNrp;
        $nrpOnly = '';
        if (preg_match('/\\bNRP\\b\\s*(.+)$/iu', $rankNrp, $mNrp)) {
            $nrpOnly = trim($mNrp[1]);
            $rankOnly = trim(preg_replace('/\\bNRP\\b\\s*.+$/iu', '', $rankNrp));
        }
        $tp->setValue('pejabat_rank', $fill($rankOnly));
        $tp->setValue('pejabat_nrp', $fill($nrpOnly));

        // Placeholder penandatangan gaya SPDP Docs
        $tp->setValue('signatoryHeadText', $fill($signatoryHeadText));
        // jangan pakai filler "......." untuk baris yang memang boleh kosong
        $tp->setValue('signatoryPositionName', $emptyOk($signatoryPositionName));
        $tp->setValue('signatoryName', $fill((string) ($signature['name'] ?? '')));
        $tp->setValue('signatoryRankName', $fill(strtoupper((string) $rankOnly)));
        $tp->setValue('signatoryRegisterNumber', $fill((string) $nrpOnly));

        $cc = (isset($payload['carbon_copies']) && is_array($payload['carbon_copies'])) ? $payload['carbon_copies'] : [];
        $cc = array_values(array_filter($cc, fn ($x) => trim((string) $x) !== ''));
        $cc = array_map(function ($x) {
            $s = trim((string) $x);
            $s = str_replace("\xc2\xa0", ' ', $s);

            return trim(preg_replace('/\s+/u', ' ', $s));
        }, $cc);
        $tp->setValue('carbonCopiesBlock', $fill(implode('; ', $cc)));
        // Tembusan gaya SPDP Docs (iterasi)
        if (! empty($cc)) {
            $no = 1;
            $blockCarbonCopies = [];
            foreach ($cc as $v) {
                $blockCarbonCopies[] = [
                    'carbon_copy_iteration' => $no++,
                    'carbon_copy_name' => $v,
                ];
            }
            try {
                $tp->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);
            } catch (\Throwable $e) {
                // jika template tidak memakai row tembusan, abaikan
            }
        } else {
            $tp->setValue('carbon_copy_iteration', '');
            $tp->setValue('carbon_copy_name', '');
        }

        $filename = 'generate/' . $doc->id . ' - Surat Permintaan Perpanjangan Penahanan - ' . ($doc->accident?->polres?->full_name ?? 'ICELL');
        $tp->saveAs($filename . '.docx');

        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
    }

    private function buildPayload(Request $request, string $accidentId, $suspectId, array $existingPayload = []): array
    {
        $get = function (string $key, $default = null) use ($request, $existingPayload) {
            if ($request->has($key)) {
                return $request->input($key);
            }
            return data_get($existingPayload, $key, $default);
        };

        $spdpId = $request->suratPemberitahuanDimulainyaPenyidikanDocument;
        $sketId = $request->suratKetetapanTentangPenetapanTersangkaDocument;

        $spdpPicked = $spdpId
            ? SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)->where('id', $spdpId)->first()
            : null;
        $sket = $sketId
            ? SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)->where('id', $sketId)->first()
            : null;

        $suspectName = $suspectId
            ? Suspect::where('id', $suspectId)->value('name')
            : null;

        $accident = Accident::query()->where('id', $accidentId)->first();
        $spdpLatest = SuratPemberitahuanDimulainyaPenyidikanDocument::query()
            ->where('accident_id', $accidentId)
            ->orderByDesc('document_date')
            ->first();

        $autoS21 = $this->resolveS21BodyAutoFromContext(
            $accident,
            $accidentId,
            $spdpPicked?->surat_perintah_penyidikan_document_id ?? null,
            $spdpLatest?->surat_perintah_penyidikan_document_id ?? null
        );

        $prisonLine = '';
        $pid = $request->extension_prison_id;
        if (! empty($pid)) {
            try {
                $p = Prison::query()->find($pid);
                if ($p) {
                    $prisonLine = trim($p->province.' — '.$p->name.($p->branch ? ' ('.$p->branch.')' : ''));
                }
            } catch (\Throwable $e) {
                $prisonLine = '';
            }
        }

        $sphId = trim((string) $request->input('suratPerintahPenahanan', ''));
        $sphNumber = null;
        $sphDate = null;
        if ($sphId !== '') {
            try {
                $sphDoc = SuratPerintahPenahananDocument::query()
                    ->where('accident_id', $accidentId)
                    ->where('id', $sphId)
                    ->whereNull('deleted_at')
                    ->first();
                if ($sphDoc) {
                    $sphNumber = (string) ($sphDoc->document_number ?? '');
                    $sphDate = $sphDoc->document_date ? $sphDoc->document_date->format('Y-m-d') : null;
                }
            } catch (\Throwable $e) {
                $sphNumber = null;
                $sphDate = null;
            }
        }

        $refs = [
            'spdp_document_id' => $spdpId,
            'sket_document_id' => $sketId,
            'sket_document_number' => $sket?->document_number,
            'sket_document_date' => $sket?->document_date,
            'sket_suspect_name' => $suspectName,
            'sph_id' => $sphId !== '' ? $sphId : ($existingPayload['references']['sph_id'] ?? null),
            // SPH (nomor) sekarang diambil dari dokumen `doc.surat_perintah_penahanan_documents.document_number`
            'sph_number' => $sphNumber !== null ? (string) $sphNumber : ($existingPayload['references']['sph_number'] ?? null),
            'sph_date' => $sphDate !== null ? $sphDate : ($existingPayload['references']['sph_date'] ?? null),
            // "Atas nama" tidak diinput; ambil dari tersangka yang dipilih.
            'sph_suspect_name' => $suspectName,
        ];

        $classId = $request->documentClassification;
        $classificationName = $this->resolveClassificationName((string) $classId);
        if ($classificationName === '') {
            $classificationName = trim((string) $request->classificationPreserve);
        }

        $signaturePayload = $this->resolveSignatureFromRequest($request);

        $lampiranVal = $request->input('lampiran');
        if ($lampiranVal === null || $lampiranVal === '') {
            $lampiranVal = data_get($existingPayload, 'meta.lampiran') ?? '1';
        }

        // LP & SPDP dari database; jika tidak ada, boleh manual.
        // Masa perpanjangan otomatis: (Akhir masa penahanan berjalan + 1) s/d (mulai + (defaultHari-1))
        $extStart = null;
        $extEnd = null;
        try {
            $det = trim((string) $request->detentionEndDate);
            if ($det !== '') {
                $sd = Carbon::parse($det)->addDay();
                $ed = (clone $sd)->addDays(self::DEFAULT_REQUESTED_EXTENSION_DAYS - 1);
                $extStart = $sd->format('Y-m-d');
                $extEnd = $ed->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            $extStart = null;
            $extEnd = null;
        }
        $lpNumber = (string) $get('lpNumber', data_get($existingPayload, 'lp.number', $accident?->no_lp));
        $lpDate = $get('lpDate', data_get($existingPayload, 'lp.date', $accident?->report_date));
        $spdpFromDb = $spdpPicked ?: $spdpLatest;
        $spdpNumber = (string) $get('spdpNumber', data_get($existingPayload, 'spdp.number', $spdpFromDb?->document_number));
        $spdpDate = $get('spdpDate', data_get($existingPayload, 'spdp.date', $spdpFromDb?->document_date));

        // Kejaksaan (nama + lokasi) — konsisten pola SPDP Docs
        $kejPid = trim((string) $request->input('kejaksaanProsecutorId', ''));
        $kejName = trim((string) $request->input('namaKejaksaan', ''));
        $kejLoc = trim((string) $request->input('kejaksaanLocation', ''));
        if ($kejPid !== '') {
            try {
                $kej = Prosecutor::query()->with(['regency'])->find($kejPid);
                if ($kej) {
                    if ($kejName === '') {
                        $kejName = trim((string) ($kej->full_name ?? $kej->name ?? ''));
                    }
                    if ($kejLoc === '') {
                        $kejLoc = trim((string) ($kej->regency?->name ?? ''));
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $contactOfficerId = $request->input('contactOfficerId');
        $contactOfficerName = trim((string) ($request->contactOfficerName ?? ''));
        $contactOfficerPhone = trim((string) ($request->contactOfficerPhone ?? ''));
        if ($contactOfficerId) {
            try {
                $off = Officer::withRelated()->selectFullName()->find($contactOfficerId);
                if ($off && $contactOfficerName === '') {
                    $contactOfficerName = (string) ($off->full_name ?? '');
                }
                if ($off && $contactOfficerPhone === '') {
                    $contactOfficerPhone = (string) ($off->phone_number ?? $off->phone ?? '');
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // Jika kejaksaanAkhirTanggal tidak diinput, samakan dengan akhir masa penahanan berjalan.
        $kejAkhirTanggal = $get('kejaksaanAkhirTanggal', data_get($existingPayload, 's21_body.kejaksaan_akhir_tanggal', null));
        if (empty($kejAkhirTanggal)) {
            $kejAkhirTanggal = $request->detentionEndDate;
        }

        // Nilai No.2 akan diisi dari (urut prioritas): input form → payload lama → auto dari Sprindik/Accident
        $satkerPenyidikResolved = (string) $get('satkerPenyidik', data_get($existingPayload, 's21_body.satker_penyidik', ''));
        if (trim($satkerPenyidikResolved) === '') {
            $satkerPenyidikResolved = (string) ($autoS21['satker_penyidik'] ?? '');
        }

        $dugaanTindakPidanaResolved = (string) $get('dugaanTindakPidana', data_get($existingPayload, 's21_body.dugaan_tindak_pidana', ''));
        if (trim($dugaanTindakPidanaResolved) === '') {
            $dugaanTindakPidanaResolved = (string) ($autoS21['dugaan_tindak_pidana'] ?? '');
        }

        $pasalDidugaResolved = (string) $get('pasalDiduga', data_get($existingPayload, 's21_body.pasal_diduga', ''));
        if (trim($pasalDidugaResolved) === '') {
            $pasalDidugaResolved = (string) ($autoS21['pasal_diduga'] ?? '');
        }

        $tempatKejadianResolved = (string) $get('tempatKejadian', data_get($existingPayload, 's21_body.tempat_kejadian', ''));
        if (trim($tempatKejadianResolved) === '') {
            $tempatKejadianResolved = (string) ($autoS21['tempat_kejadian'] ?? '');
        }

        $kurunWaktuResolved = (string) $get('kurunWaktu', data_get($existingPayload, 's21_body.kurun_waktu', ''));
        if (trim($kurunWaktuResolved) === '') {
            $kurunWaktuResolved = (string) ($autoS21['kurun_waktu'] ?? '');
        }

        return [
            'meta' => [
                'document_classification_id' => $classId ?: null,
                'classification' => $classificationName,
                'lampiran' => (string) $lampiranVal,
                'hal_subject' => self::DEFAULT_HAL_SUBJECT,
                'document_location' => (string) $get('documentLocation', data_get($existingPayload, 'meta.document_location', '')),
            ],
            'references' => $refs,
            'court_id' => $request->court,
            'court_city' => $this->resolveCourtRegencyName((string) ($request->court ?? '')),
            'lp' => [
                'number' => $lpNumber,
                'date' => $lpDate,
            ],
            'spdp' => [
                'number' => $spdpNumber,
                'date' => $spdpDate,
            ],
            'kejaksaan' => [
                'prosecutor_id' => $kejPid !== '' ? $kejPid : null,
                'name' => $kejName,
                // Simpan dua key (baru + legacy) agar download & data lama tetap aman
                'location' => $kejLoc,
                'regency' => $kejLoc,
            ],
            'kejaksaan_extension' => [
                'number' => (string) $get('kejaksaanExtensionNumber', data_get($existingPayload, 'kejaksaan_extension.number', '')),
                'date' => $get('kejaksaanExtensionDate', data_get($existingPayload, 'kejaksaan_extension.date', null)),
                // "Atas nama" tidak diinput; ambil dari tersangka yang dipilih.
                'suspect_name' => (string) ($suspectName ?? ''),
            ],
            'perpanjangan_order' => [
                'number' => (string) $get('perpanjanganOrderNumber', data_get($existingPayload, 'perpanjangan_order.number', '')),
                'date' => $get('perpanjanganOrderDate', data_get($existingPayload, 'perpanjangan_order.date', null)),
                // "Atas nama" tidak diinput; ambil dari tersangka yang dipilih.
                'suspect_name' => (string) ($suspectName ?? ''),
            ],
            'narrative' => [
                'unit_extra' => (string) $get('investigatingUnitText', data_get($existingPayload, 'narrative.unit_extra', '')),
                'case_extra' => (string) $get('caseNarrativeText', data_get($existingPayload, 'narrative.case_extra', '')),
            ],
            's21_body' => [
                'satker_penyidik' => $satkerPenyidikResolved,
                'dugaan_tindak_pidana' => $dugaanTindakPidanaResolved,
                'pasal_diduga' => $pasalDidugaResolved,
                'tempat_kejadian' => $tempatKejadianResolved,
                'kurun_waktu' => $kurunWaktuResolved,
                'alasan_perpanjangan' => (string) $get('alasanPerpanjangan', data_get($existingPayload, 's21_body.alasan_perpanjangan', '')),
                'kejaksaan_akhir_tanggal' => $kejAkhirTanggal,
            ],
            'extension' => [
                'prison_id' => $request->extension_prison_id,
                'prison_line' => $prisonLine,
                'start_date' => $extStart,
                'end_date' => $extEnd,
            ],
            'contact' => [
                'officer_id' => $contactOfficerId ?: null,
                'officer_name' => $contactOfficerName,
                'officer_phone' => $contactOfficerPhone,
            ],
            'signature' => $signaturePayload,
            'detention_end_date' => $request->detentionEndDate,
            'carbon_copies' => $request->carbonCopies ?? [],
        ];
    }

    /**
     * Auto isi blok No.2 dari Sprindik (via SPDP) + data perkara.
     * Return keys: satker_penyidik, dugaan_tindak_pidana, pasal_diduga, tempat_kejadian, kurun_waktu.
     */
    private function resolveS21BodyAuto(PermintaanPerpanjanganPenahananDocument $doc): array
    {
        $payload = $doc->payload ?? [];
        $refs = (isset($payload['references']) && is_array($payload['references'])) ? $payload['references'] : [];
        $spdpId = $refs['spdp_document_id'] ?? null;

        $spdpPicked = null;
        $spdpLatest = null;
        try {
            if (! empty($spdpId)) {
                $spdpPicked = SuratPemberitahuanDimulainyaPenyidikanDocument::query()
                    ->where('accident_id', $doc->accident_id)
                    ->where('id', $spdpId)
                    ->first();
            }
            $spdpLatest = SuratPemberitahuanDimulainyaPenyidikanDocument::query()
                ->where('accident_id', $doc->accident_id)
                ->orderByDesc('document_date')
                ->first();
        } catch (\Throwable $e) {
            $spdpPicked = null;
            $spdpLatest = null;
        }

        return $this->resolveS21BodyAutoFromContext(
            $doc->accident,
            (string) $doc->accident_id,
            $spdpPicked?->surat_perintah_penyidikan_document_id ?? null,
            $spdpLatest?->surat_perintah_penyidikan_document_id ?? null
        );
    }

    private function resolveS21BodyAutoFromContext($accident, string $accidentId, ?string $sprindikIdPrimary, ?string $sprindikIdFallback): array
    {
        $resolveAyatNumberFromChapter = function (?string $chapter): ?int {
            $t = trim((string) $chapter);
            if ($t === '') {
                return null;
            }
            if (preg_match('/\\bayat\\b\\s*\\(?\\s*(\\d+)\\s*\\)?/iu', $t, $m)) {
                return (int) $m[1];
            }
            return null;
        };

        $extractAyatTextFromDescription = function (?string $description, ?int $ayatNumber): string {
            if (! $ayatNumber) {
                return '';
            }

            $html = (string) ($description ?? '');
            if (trim($html) === '') {
                return '';
            }

            // Ikuti pola Tahap 1: marker ayat "(n)" harus muncul setelah start, <br>, <p>, atau newline.
            $n = (int) $ayatNumber;
            $pattern = '/(?:^|<br[^>]*>|<p>|[\r\n]+)\s*\(' . preg_quote((string) $n, '/') . '\)\s*(.*?)(?=(?:<br[^>]*>|<p>|[\r\n]+)\s*\(\d+\)|$)/is';
            if (! preg_match($pattern, $html, $m)) {
                return '';
            }

            $txt = strip_tags((string) ($m[1] ?? ''));
            $txt = preg_replace('/^\s*\(\d+\)\s*/', '', (string) $txt);
            $txt = preg_replace('/\s+/', ' ', (string) $txt);
            $txt = trim((string) $txt);

            return $txt;
        };

        $sprindikFromSpdp = null;
        $sprindikId = $sprindikIdPrimary ?: $sprindikIdFallback;
        if (! empty($sprindikId)) {
            try {
                $sprindikFromSpdp = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentLaws.crimeConstitution'])
                    ->where('accident_id', $accidentId)
                    ->where('id', $sprindikId)
                    ->first();
            } catch (\Throwable $e) {
                $sprindikFromSpdp = null;
            }
        }

        $sprindikLawTextParts = [];
        $sprindikAyatTexts = [];
        try {
            if ($sprindikFromSpdp) {
                $laws = $sprindikFromSpdp->suratPerintahPenyidikanDocumentLaws ?? collect();
                foreach ($laws as $law) {
                    if (($law->flag ?? '') !== 'MAIN') {
                        continue;
                    }
                    $chapter = trim((string) ($law->constitution_chapter ?? ''));
                    $constitutionName = trim((string) ($law->crimeConstitution?->name ?? ''));
                    $line = trim($chapter . ' ' . $constitutionName);
                    if ($line !== '') {
                        $sprindikLawTextParts[] = $line;
                    }

                    $ayatNo = $resolveAyatNumberFromChapter($chapter);
                    $desc = $law->crimeConstitution?->description ?? null;
                    $ayatText = $extractAyatTextFromDescription($desc, $ayatNo);
                    if ($ayatText !== '') {
                        $sprindikAyatTexts[] = $ayatText;
                    }
                }
            }
        } catch (\Throwable $e) {
            $sprindikLawTextParts = [];
            $sprindikAyatTexts = [];
        }

        $autoSatker = '';
        try {
            $autoSatker = (string) ($accident?->polres?->full_name ?? $accident?->polres?->name ?? '');
        } catch (\Throwable $e) {
            $autoSatker = '';
        }

        $autoTempatKejadian = trim((string) ($accident?->road_name ?? ''));

        $autoKurunWaktu = '';
        try {
            $dt = $accident?->accident_date ?? null;
            $tm = $accident?->accident_time ?? null;
            if ($dt) {
                $autoKurunWaktu = Carbon::parse($dt)->locale('id')->translatedFormat('d F Y');
                if (! empty($tm)) {
                    $autoKurunWaktu .= ' sekitar pukul ' . Carbon::parse($tm)->locale('id')->translatedFormat('H:i') . ' WIB';
                }
            }
        } catch (\Throwable $e) {
            $autoKurunWaktu = '';
        }

        return [
            'satker_penyidik' => $autoSatker,
            'dugaan_tindak_pidana' => trim(implode("\n\n", array_values(array_unique(array_filter($sprindikAyatTexts))))),
            'pasal_diduga' => trim(implode(', ', array_values(array_unique(array_filter($sprindikLawTextParts))))),
            'tempat_kejadian' => $autoTempatKejadian,
            'kurun_waktu' => $autoKurunWaktu,
        ];
    }

    private function resolveClassificationName(string $id): string
    {
        if ($id === '') {
            return '';
        }
        try {
            return (string) (DocumentClassification::query()->find($id)?->name ?? '');
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Menyelaraskan pola SPDP: pemilih petugas penandatangan mengisi blok tanda tangan (boleh ditimpa manual).
     *
     * @return array{officer_id: ?string, title: string, name: string, rank_nrp: string}
     */
    private function resolveSignatureFromRequest(Request $request): array
    {
        $title = trim((string) $request->signatoryTitle);
        $name = trim((string) $request->signatoryName);
        $rankNrp = trim((string) $request->signatoryRankNrp);
        $officerId = null;

        if ($request->filled('signatory')) {
            try {
                $off = Officer::withRelated()->selectFullName()->find($request->signatory);
                if ($off) {
                    $officerId = (string) $off->id;
                    $name = (string) (($off->full_name ?? '') ?: $name);
                    $title = (string) (($off->position?->name ?? '') ?: $title);
                    $rk = trim((string) ($off->rank?->name ?? ''));
                    $nrp = trim((string) ($off->register_number ?? ''));
                    $line = trim($rk.(($rk !== '' && $nrp !== '') ? ' ' : '').($nrp !== '' ? 'NRP '.$nrp : ''));
                    $rankNrp = $line !== '' ? $line : $rankNrp;
                }
            } catch (\Throwable $e) {
            }
        }

        return [
            'officer_id' => $officerId,
            'title' => $title,
            'name' => $name,
            'rank_nrp' => $rankNrp,
        ];
    }

    private function prisonsSafe()
    {
        try {
            return Prison::active()
                ->orderBy('province')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    // Section "Undang-Undang yang Dikenakan" sudah dihapus dari dokumen ini.

    private function resolveTemplatePath(): ?string
    {
        $candidates = [
            public_path('word-template/surat_permintaan_perpanjangan_penahanan.docx'),
        ];
        foreach ($candidates as $p) {
            if (is_readable($p)) {
                return $p;
            }
        }
        try {
            $files = glob(public_path('file/penahanan/permintaan-perpanjangan-penahanan').DIRECTORY_SEPARATOR.'*.docx') ?: [];
            return $files[0] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Kota/kabupaten pengadilan dari master `lib.courts` → `lib.locations`
     * (naik parent sampai ketemu lokasi bertipe REGENCY).
     */
    private function resolveCourtRegencyName(?string $courtId): string
    {
        if ($courtId === null || $courtId === '') {
            return '';
        }
        try {
            $court = Court::query()->find($courtId);
            $locationId = $court?->location_id;
            if (! $locationId) {
                return '';
            }
            $loc = Location::query()->find($locationId);
            for ($i = 0; $loc !== null && $i < 12; $i++) {
                if (($loc->class ?? '') === Location::getEnumOption('class', 'REG')) {
                    $name = trim((string) (($loc->full_name ?? '') !== '' ? $loc->full_name : $loc->name));

                    return $name;
                }
                $parentId = $loc->parent_id ?? null;
                if (! $parentId) {
                    break;
                }
                $loc = Location::query()->find($parentId);
            }
        } catch (\Throwable $e) {
            return '';
        }

        return '';
    }

    private function formatIdDate($value): string
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

    private function validateForm(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'documentNumber' => 'required|min:5|max:255',
            'documentDate' => 'required|date',
            'suspect' => 'required|uuid',
            'detentionEndDate' => 'required|date',
            'court' => 'required|string',
            'suratPemberitahuanDimulainyaPenyidikanDocument' => 'nullable|uuid',
            'suratKetetapanTentangPenetapanTersangkaDocument' => 'required|uuid',
            'suratPerintahPenahanan' => 'required|uuid',
            // requestedExtensionDays tidak diinput di form (default dokumen)
            'documentClassification' => ['nullable', 'string', Rule::exists(DocumentClassification::class, 'id')],
            'classificationPreserve' => 'nullable|string|max:120',
            'lampiran' => ['nullable', 'regex:/^$|^[1-9][0-9]{0,2}$/'],
            'documentLocation' => 'nullable|string|max:255',
            'lpNumber' => 'nullable|string|max:255',
            'lpDate' => 'nullable|date',
            'spdpNumber' => 'nullable|string|max:255',
            'spdpDate' => 'nullable|date',
            'namaKejaksaan' => 'nullable|string|max:255',
            'kejaksaanProsecutorId' => 'nullable|string|max:255',
            'signatory' => ['nullable', 'string', Rule::exists(Officer::class, 'id')],
            'kejaksaanExtensionNumber' => 'nullable|string|max:255',
            'kejaksaanExtensionDate' => 'nullable|date',
            'perpanjanganOrderNumber' => 'nullable|string|max:255',
            'perpanjanganOrderDate' => 'nullable|date',
            'investigatingUnitText' => 'nullable|string|max:500',
            'caseNarrativeText' => 'nullable|string|max:5000',
            'satkerPenyidik' => 'nullable|string|max:500',
            'dugaanTindakPidana' => 'nullable|string|max:5000',
            'pasalDiduga' => 'nullable|string|max:500',
            'tempatKejadian' => 'nullable|string|max:500',
            'kurunWaktu' => 'nullable|string|max:500',
            'alasanPerpanjangan' => 'nullable|string|max:5000',
            'kejaksaanAkhirTanggal' => 'nullable|date',
            'extension_prison_id' => 'nullable|string|max:255',
            // extensionStartDate/extensionEndDate tidak diinput (dihitung otomatis)
            'contactOfficerName' => 'nullable|string|max:255',
            'contactOfficerPhone' => 'nullable|string|max:100',
            'contactOfficerId' => ['nullable', 'string', Rule::exists(Officer::class, 'id')],
            'signatoryTitle' => 'nullable|string|max:120',
            'signatoryName' => 'nullable|string|max:255',
            'signatoryRankNrp' => 'nullable|string|max:255',
            'carbonCopies' => 'nullable|array',
            'carbonCopies.*' => 'nullable|string|max:500',
        ], [
            'documentNumber.required' => 'Nomor Dokumen harus diisi',
            'documentDate.required' => 'Tanggal Dokumen harus diisi',
            'suspect.required' => 'Tersangka harus dipilih',
            'detentionEndDate.required' => 'Akhir Masa Penahanan harus diisi',
            'court.required' => 'Pengadilan Negeri harus dipilih',
            'suratPemberitahuanDimulainyaPenyidikanDocument.required' => 'Surat Pemberitahuan Dimulainya Penyidikan harus dipilih',
            'suratKetetapanTentangPenetapanTersangkaDocument.required' => 'Surat Ketetapan tentang Penetapan Tersangka harus dipilih',
            'suratPerintahPenahanan.required' => 'Surat Perintah Penahanan harus dipilih',
        ]);

        $validator->after(function (\Illuminate\Validation\Validator $v) use ($request) {
            $spdpId = trim((string) $request->input('suratPemberitahuanDimulainyaPenyidikanDocument', ''));
            $spdpManualNo = trim((string) $request->input('spdpNumber', ''));
            $spdpManualDate = trim((string) $request->input('spdpDate', ''));
            if ($spdpId === '') {
                if ($spdpManualNo === '' || $spdpManualDate === '') {
                    $v->errors()->add(
                        'suratPemberitahuanDimulainyaPenyidikanDocument',
                        'Surat Pemberitahuan Dimulainya Penyidikan harus dipilih (atau isi manual nomor & tanggal jika belum ada di database).'
                    );
                    if ($spdpManualNo === '') {
                        $v->errors()->add('spdpNumber', 'Nomor SPDP harus diisi.');
                    }
                    if ($spdpManualDate === '') {
                        $v->errors()->add('spdpDate', 'Tanggal SPDP harus diisi.');
                    }
                }
            }

            // Section "Undang-Undang yang Dikenakan" sudah dihapus dari dokumen ini.
        });

        return $validator;
    }

    private function syncNormalizedData(PermintaanPerpanjanganPenahananDocument $doc, Request $request, string $accidentId): void
    {
        // Officers snapshot
        $doc->officers()->delete();

        if ($request->filled('signatory')) {
            $this->createOfficerSnapshotRow(
                $doc,
                (string) $request->signatory,
                PermintaanPerpanjanganPenahananDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                1
            );
        }

        if ($request->filled('contactOfficerId')) {
            $this->createOfficerSnapshotRow(
                $doc,
                (string) $request->contactOfficerId,
                PermintaanPerpanjanganPenahananDocumentOfficer::getEnumOption('class', 'CONTACT'),
                2
            );
        }
        // References & laws snapshots removed (payload remains source-of-truth).
    }

    private function createOfficerSnapshotRow(
        PermintaanPerpanjanganPenahananDocument $doc,
        string $officerId,
        string $class,
        int $sort
    ): void {
        $off = Officer::withRelated()->find($officerId);
        if (! $off) {
            return;
        }

        $doc->officers()->create([
            'sort' => $sort,
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
            'status' => PermintaanPerpanjanganPenahananDocumentOfficer::getEnumOption('status', 'PRESENT'),
            'class' => $class,
            'flag' => PermintaanPerpanjanganPenahananDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
            'insert_method' => PermintaanPerpanjanganPenahananDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
        ]);
    }

    /**
     * Merge normalized officers into payload format (for download compatibility).
     *
     * @param  array<int, PermintaanPerpanjanganPenahananDocumentOfficer>|\Illuminate\Support\Collection  $normalizedOfficers
     */
    private function mergeOfficersFromNormalized(array $payload, $normalizedOfficers): array
    {
        $contact = (isset($payload['contact']) && is_array($payload['contact'])) ? $payload['contact'] : [];
        $signature = (isset($payload['signature']) && is_array($payload['signature'])) ? $payload['signature'] : [];

        foreach ($normalizedOfficers as $o) {
            if ((string) $o->class === 'CONTACT') {
                $contact['officer_id'] = $o->officer_id ?? null;
                $contact['officer_name'] = trim((string) (($o->first_title ? $o->first_title.' ' : '').($o->first_name ?? '').' '.($o->last_name ?? '').($o->last_title ? ', '.$o->last_title : '')));
                $contact['officer_phone'] = $o->phone_number ?? null;
            }
            if ((string) $o->class === 'SIGNATORY') {
                $signature['officer_id'] = $o->officer_id ?? null;
                $signature['name'] = trim((string) (($o->first_title ? $o->first_title.' ' : '').($o->first_name ?? '').' '.($o->last_name ?? '').($o->last_title ? ', '.$o->last_title : '')));

                // Tambahan field agar download tidak perlu query master Officer saat snapshot tersedia.
                $signature['rank_id'] = $o->rank_id ?? null;
                $signature['position_id'] = $o->position_id ?? null;
                $signature['register_number'] = $o->register_number ?? null;

                // Pre-build string lama "Pangkat NRP xxxx" kalau template lama masih pakai.
                $rankName = '';
                if (! empty($o->rank_id)) {
                    try {
                        $rankName = (string) (Rank::query()->where('id', $o->rank_id)->value('name') ?? '');
                    } catch (\Throwable $e) {
                        $rankName = '';
                    }
                }
                $nrp = trim((string) ($o->register_number ?? ''));
                $signature['rank_nrp'] = trim($rankName . ($nrp !== '' ? ' NRP ' . $nrp : ''));
            }
        }

        $payload['contact'] = $contact;
        $payload['signature'] = $signature;
        return $payload;
    }
}
