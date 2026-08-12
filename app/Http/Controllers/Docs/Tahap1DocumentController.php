<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Doc\Tahap1Document\Tahap1Document;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Lib\Prosecutor;
use App\Models\Suspect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\DocsOfficersTraits;
use App\Helpers\PeopleNameHelper;
use App\Models\DaftarBarangBukti;
use App\Models\Lib\Prison;

class Tahap1DocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort(404, 'Halaman tidak ditemukan');
    }

    /**
     * Show the form for creating a new document
     */
    public function create(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id'));
        
        if (!$accidentId) {
            return redirect()->back()->with('error', 'ID Perkara tidak ditemukan');
        }

        $accident = Accident::where('id', $accidentId)->first();
        
        if (!$accident) {
            return redirect()->back()->with('error', 'Data perkara tidak ditemukan');
        }

        // Related Documents
        $suratPerintahPenyidikanDocuments = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratKetetapanTentangPenetapanTersangkaDocuments = \App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Prosecutors
        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        // Signatories
        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);
        
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

        // Suspects
        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', 'TERSANGKA')
            ->get();

        // Get existing evidence from the global pool for this accident
        $daftarBarangBukti = DaftarBarangBukti::where('accident_id', $accidentId)->get();

        // Standard dummy variables for shared modal compatibility
        $surat_penyitaan = collect();
        $officer = collect();

        $document = null;

        $prisons = Prison::where('is_active', true)
            ->orderBy('name')
            ->get();

        $authorizedOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        return view('docs.tahap-1.create', compact(
            'accident',
            'accidentId',
            'suratPerintahPenyidikanDocuments',
            'suratPemberitahuanDimulainyaPenyidikanDocuments',
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'prosecutors',
            'authorizedSignatories',
            'suspects',
            'document',
            'daftarBarangBukti',
            'surat_penyitaan',
            'officer',
            'prisons',
            'authorizedOfficers'
        ));
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'accident_id' => 'required|uuid|exists:accidents,id',
                'document_number' => 'required|string|max:255',
                'document_date' => 'required|date',
                'klasifikasi' => 'required|string|max:255',
                'lampiran' => 'nullable|string|max:255',
                'prosecutor_id' => 'nullable|string|max:255|exists:App\Models\Lib\Prosecutor,id',
                'surat_perintah_penyidikan_id' => 'nullable|uuid',
                'surat_pemberitahuan_dimulainya_penyidikan_id' => 'nullable|uuid',
                'surat_ketetapan_penetapan_tersangka_id' => 'nullable|uuid',
                'berkas_perkara_number' => 'required|string|max:255',
                'berkas_perkara_date' => 'required|date',
                'berkas_perkara_rangkap' => 'required|integer',
                'pasal_disangkakan' => 'nullable|string',
                'penahanan_rutan' => 'nullable|string|max:255',
                'penahanan_cabang' => 'nullable|string|max:255',
                'penahanan_start_date' => 'nullable|date',
                'penahanan_end_date' => 'nullable|date',
                'surat_perintah_penahanan_number' => 'nullable|string|max:255',
                'surat_perintah_penahanan_date' => 'nullable|date',
                'surat_perpanjangan_penahanan_number' => 'nullable|string|max:255',
                'surat_perpanjangan_penahanan_date' => 'nullable|date',
                'surat_perpanjangan_penahanan_court_number' => 'nullable|string|max:255',
                'surat_perpanjangan_penahanan_court_date' => 'nullable|date',
                'penahanan_status' => 'required|string|in:DITAHAN,DITANGGUHKAN,TIDAK_DITAHAN',
                'surat_penangguhan_penahanan_number' => 'nullable|required_if:penahanan_status,DITANGGUHKAN|string|max:255',
                'surat_penangguhan_penahanan_date' => 'nullable|required_if:penahanan_status,DITANGGUHKAN|date',
                'barang_bukti_storage' => 'nullable|string|max:255',
                'investigator_pangkat_nama' => 'nullable|string|max:255',
                'investigator_hp' => 'nullable|string|max:255',
                'signatory' => 'required|string|exists:officers,id',
                'suspects' => 'required|array',
                'suspects.*' => 'exists:suspects,id',
                'barang_bukti' => 'nullable|array',
                'jumlah_bb' => 'nullable|integer',
                'tembusan' => 'nullable|array',
            ]);

            $signatoryOfficer = Officer::with(['rank', 'position', 'police'])->findOrFail($validated['signatory']);
            unset($validated['signatory']);

            $suspectsInput = $validated['suspects'];
            unset($validated['suspects']);

            // Tembusan defaults to empty if none provided
            if (empty($validated['tembusan'])) {
                $validated['tembusan'] = [];
            }

            $validated['document_category_id'] = '0805'; // TAHAP I
            $validated['status_id'] = '2'; // DIBUAT
            $validated['created_by_user_id'] = Auth::id();

            // Audit Trail
            $validated['ip_addresses'] = [$request->ip()];
            $validated['timestamps'] = [
                [
                    'status_id' => '2',
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'updated_by' => Auth::id(),
                    'message' => 'Dokumen dibuat'
                ]
            ];
            $validated['submitted_at'] = Carbon::now();

            $document = Tahap1Document::create($validated);

            if (!empty($suspectsInput)) {
                $document->suspects()->sync($suspectsInput);
            }

            // Save signatory
            $document->officers()->create([
                'officer_id' => $signatoryOfficer->id,
                'register_number' => $signatoryOfficer->register_number,
                'full_name' => PeopleNameHelper::getFullName($signatoryOfficer->first_title, $signatoryOfficer->first_name, $signatoryOfficer->last_name, $signatoryOfficer->last_title),
                'rank' => $signatoryOfficer->rank ? $signatoryOfficer->rank->name : null,
                'position' => $signatoryOfficer->position ? $signatoryOfficer->position->name : null,
                'police_name' => $signatoryOfficer->police ? $signatoryOfficer->police->name : null,
                'class' => 'SIGNATORY',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Pengiriman Berkas Perkara (Tahap I) berhasil disimpan',
                    'redirect' => route('view_produktivitas_accident', ['accident_id' => $validated['accident_id']])
                ]);
            }

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $validated['accident_id']])
                ->with('success', 'Surat Pengiriman Berkas Perkara (Tahap I) berhasil disimpan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing Tahap I document: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing
     */
    public function edit($id)
    {
        $document = Tahap1Document::with(['accident', 'officers', 'attachments', 'suspects'])->findOrFail($id);
        
        if (!$document->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui');
        }

        $accident = $document->accident;
        $accidentId = $accident->id;

        // Related Documents
        $suratPerintahPenyidikanDocuments = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratKetetapanTentangPenetapanTersangkaDocuments = \App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Prosecutors
        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        // Signatories
        $policeId = $accident->polres_id;
        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);
        
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

        // Suspects
        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', 'TERSANGKA')
            ->get();

        // Get existing evidence from the global pool for this accident
        $daftarBarangBukti = DaftarBarangBukti::where('accident_id', $accidentId)->get();

        // Standard dummy variables for shared modal compatibility
        $surat_penyitaan = collect();
        $officer = collect();

        $prisons = Prison::where('is_active', true)
            ->orderBy('name')
            ->get();

        $authorizedOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        return view('docs.tahap-1.edit', compact(
            'accident',
            'accidentId',
            'suratPerintahPenyidikanDocuments',
            'suratPemberitahuanDimulainyaPenyidikanDocuments',
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'prosecutors',
            'authorizedSignatories',
            'suspects',
            'document',
            'daftarBarangBukti',
            'surat_penyitaan',
            'officer',
            'prisons',
            'authorizedOfficers'
        ));
    }

    /**
     * Update the document
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $document = Tahap1Document::findOrFail($id);
            
            if (!$document->isEditable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak dapat diedit karena sudah disetujui'
                ], 403);
            }

            $validated = $request->validate([
                'document_number' => 'required|string|max:255',
                'document_date' => 'required|date',
                'klasifikasi' => 'required|string|max:255',
                'lampiran' => 'nullable|string|max:255',
                'prosecutor_id' => 'nullable|string|max:255|exists:App\Models\Lib\Prosecutor,id',
                'surat_perintah_penyidikan_id' => 'nullable|uuid',
                'surat_pemberitahuan_dimulainya_penyidikan_id' => 'nullable|uuid',
                'surat_ketetapan_penetapan_tersangka_id' => 'nullable|uuid',
                'berkas_perkara_number' => 'required|string|max:255',
                'berkas_perkara_date' => 'required|date',
                'berkas_perkara_rangkap' => 'required|integer',
                'pasal_disangkakan' => 'nullable|string',
                'penahanan_rutan' => 'nullable|string|max:255',
                'penahanan_cabang' => 'nullable|string|max:255',
                'penahanan_start_date' => 'nullable|date',
                'penahanan_end_date' => 'nullable|date',
                'surat_perintah_penahanan_number' => 'nullable|string|max:255',
                'surat_perintah_penahanan_date' => 'nullable|date',
                'surat_perpanjangan_penahanan_number' => 'nullable|string|max:255',
                'surat_perpanjangan_penahanan_date' => 'nullable|date',
                'surat_perpanjangan_penahanan_court_number' => 'nullable|string|max:255',
                'surat_perpanjangan_penahanan_court_date' => 'nullable|date',
                'penahanan_status' => 'required|string|in:DITAHAN,DITANGGUHKAN,TIDAK_DITAHAN',
                'surat_penangguhan_penahanan_number' => 'nullable|required_if:penahanan_status,DITANGGUHKAN|string|max:255',
                'surat_penangguhan_penahanan_date' => 'nullable|required_if:penahanan_status,DITANGGUHKAN|date',
                'barang_bukti_storage' => 'nullable|string|max:255',
                'investigator_pangkat_nama' => 'nullable|string|max:255',
                'investigator_hp' => 'nullable|string|max:255',
                'signatory' => 'required|string|exists:officers,id',
                'suspects' => 'required|array',
                'suspects.*' => 'exists:suspects,id',
                'barang_bukti' => 'nullable|array',
                'jumlah_bb' => 'nullable|integer',
                'tembusan' => 'nullable|array',
            ]);

            $signatoryOfficer = Officer::with(['rank', 'position', 'police'])->findOrFail($validated['signatory']);
            unset($validated['signatory']);

            $suspectsInput = $validated['suspects'];
            unset($validated['suspects']);

            $validated['updated_by_user_id'] = Auth::id();

            // Append to Audit Trail
            $timestamps = $document->timestamps ?? [];
            $timestamps[] = [
                'status_id' => $document->status_id,
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen diperbarui'
            ];
            $validated['timestamps'] = $timestamps;

            $ipAddresses = $document->ip_addresses ?? [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }
            $validated['ip_addresses'] = $ipAddresses;

            // Tembusan defaults to empty if none provided
            if (empty($validated['tembusan'])) {
                $validated['tembusan'] = [];
            }

            $document->update($validated);

            if (!empty($suspectsInput)) {
                $document->suspects()->sync($suspectsInput);
            }

            // Update signatory
            $document->officers()->where('class', 'SIGNATORY')->delete();
            $document->officers()->create([
                'officer_id' => $signatoryOfficer->id,
                'register_number' => $signatoryOfficer->register_number,
                'full_name' => PeopleNameHelper::getFullName($signatoryOfficer->first_title, $signatoryOfficer->first_name, $signatoryOfficer->last_name, $signatoryOfficer->last_title),
                'rank' => $signatoryOfficer->rank ? $signatoryOfficer->rank->name : null,
                'position' => $signatoryOfficer->position ? $signatoryOfficer->position->name : null,
                'police_name' => $signatoryOfficer->police ? $signatoryOfficer->police->name : null,
                'class' => 'SIGNATORY',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Pengiriman Berkas Perkara (Tahap I) berhasil diperbarui',
                    'redirect' => route('view_produktivitas_accident', ['accident_id' => $document->accident_id])
                ]);
            }

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $document->accident_id])
                ->with('success', 'Surat Pengiriman Berkas Perkara (Tahap I) berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Tahap I document: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified document
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $document = Tahap1Document::findOrFail($id);
            
            if (!$document->isEditable()) {
                return redirect()->back()->with('error', 'Dokumen tidak dapat dihapus karena sudah disetujui');
            }

            $accidentId = $document->accident_id;
            $document->delete();

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Pengiriman Berkas Perkara (Tahap I) berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting Tahap I document: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Submit document for approval
     */
    public function submit($id)
    {
        try {
            DB::beginTransaction();

            $document = Tahap1Document::findOrFail($id);
            
            // Audit Trail
            $timestamps = $document->timestamps ?? [];
            $timestamps[] = [
                'status_id' => '3',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen diajukan untuk persetujuan'
            ];

            $ipAddresses = $document->ip_addresses ?? [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }

            // Update status to waiting approval (status_id = 3 - MENUNGGU PERSETUJUAN)
            $document->update([
                'status_id' => '3',
                'submitted_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestamps,
                'ip_addresses' => $ipAddresses,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diajukan untuk persetujuan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve document
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $document = Tahap1Document::findOrFail($id);
            
            // Audit Trail
            $timestamps = $document->timestamps ?? [];
            $timestamps[] = [
                'status_id' => '5',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen disetujui'
            ];

            $ipAddresses = $document->ip_addresses ?? [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }

            // Update status to approved (status_id = 5 - DISETUJUI)
            $document->update([
                'status_id' => '5',
                'approved_at' => Carbon::now(),
                'released_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestamps,
                'ip_addresses' => $ipAddresses,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document as Word file
     */
    public function download($id)
    {
        $document = Tahap1Document::with([
            'accident.polres.polda',
            'accident.police',
            'suratPerintahPenyidikan',
            'suratPemberitahuanDimulainyaPenyidikan',
            'suratKetetapanTentangPenetapanTersangka',
            'status',
            'officers',
            'suspects.gender',
            'suspects.job',
            'suspects.religion',
            'suspects.country',
            'suspects.province',
            'suspects.regency',
            'suspects.district',
            'suspects.village',
            'prosecutor.regency'
        ])->findOrFail($id);

        $accident = $document->accident;
        $signatory = $document->officers->where('class', 'SIGNATORY')->first();

        $signatoryHeadText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name,
        ];

        $signatoryPositionId = $signatory ? (is_array($signatory->position) ? ($signatory->position['id'] ?? null) : $signatory->position_id) : null;
        $signatoryPositionDetail = $signatoryPositionId
            ? \App\Models\Lib\Position::with('positionCluster')->find($signatoryPositionId)
            : null;

        $signatoryPositionHeadText = [
            'NO_KAPOLRES'  => $signatoryPositionDetail?->positionCluster?->alias_name ?? '',
            'NO_DIRLANTAS' => $signatoryPositionDetail?->positionCluster?->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/berkas_perkara_tahap_I.docx');

        if (isset($signatoryPositionDetail)) {
            if ($signatoryPositionDetail->position_cluster_id == '1') {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionName', '');
            } else if ($signatoryPositionDetail->position_cluster_id == '9') {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_DIRLANTAS']);
                $templateProcessor->setValue('signatoryPositionName', $signatoryPositionHeadText['NO_DIRLANTAS']);
            } else {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionName', $signatoryPositionHeadText['NO_KAPOLRES']);
            }
        } else {
            $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_KAPOLRES']);
            $templateProcessor->setValue('signatoryPositionName', 'KASAT LANTAS'); // Fallback matching original
        }

        $daerahPolice = $accident->polres->polda;
        $daerahPoliceFullName = strtoupper($daerahPolice->full_name ?? '');

        $resorPolice = $accident->polres;
        $resorPoliceAddress = ($resorPolice->address ?? '') . ', ' . ($resorPolice->polres_zipcode ?? '');
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name ?? '');
        $resorPoliceProvinceName = $resorPolice->polres_province;

        $documentLocation = ucwords(strtolower($resorPoliceProvinceName ?? ''));

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('documentLocation', $documentLocation);

        $documentDate = Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y');
        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $document->document_number);
        $templateProcessor->setValue('documentClassificationName', $document->klasifikasi);
        $templateProcessor->setValue('appendix', $document->lampiran ?? '-');
        $templateProcessor->setValue('perihal', $document->perihal);

        $prosecutor = $document->prosecutor;
        $prosecutorName = $prosecutor->name ?? '-';
        $prosecutorLocation = ucwords(strtolower($prosecutor->regency->name ?? ''));
        $templateProcessor->setValue('prosecutorName', strtoupper($prosecutorName));
        $templateProcessor->setValue('prosecutorLocation', strtoupper($prosecutorLocation ?: $documentLocation));

        $sprindikNumber = $document->suratPerintahPenyidikan->document_number ?? '-';
        $sprindikDate = $document->suratPerintahPenyidikan && $document->suratPerintahPenyidikan->document_date ? Carbon::parse($document->suratPerintahPenyidikan->document_date)->locale('id')->translatedFormat('d F Y') : '-';
        
        $spdpNumber = $document->suratPemberitahuanDimulainyaPenyidikan->document_number ?? '-';
        $spdpDate = $document->suratPemberitahuanDimulainyaPenyidikan && $document->suratPemberitahuanDimulainyaPenyidikan->document_date ? Carbon::parse($document->suratPemberitahuanDimulainyaPenyidikan->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $tapTersangkaNumber = $document->suratKetetapanTentangPenetapanTersangka->document_number ?? '-';
        $tapTersangkaDate = $document->suratKetetapanTentangPenetapanTersangka && $document->suratKetetapanTentangPenetapanTersangka->document_date ? Carbon::parse($document->suratKetetapanTentangPenetapanTersangka->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $references = [
            ['reference_iteration' => 'a.', 'reference_name' => 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'],
            ['reference_iteration' => 'b.', 'reference_name' => 'Pasal 3 dan Pasal 618 Undang-Undang Nomor 1 Tahun 2023 tentang Kitab Undang-Undang Hukum Pidana;'],
            ['reference_iteration' => 'c.', 'reference_name' => 'Pasal 8, Pasal 11, Pasal 60 ayat (3), Pasal 61, Pasal 62 dan Pasal 361 Undang-Undang Nomor 20 Tahun 2025 tentang Kitab Undang-Undang Hukum Acara Pidana;'],
            ['reference_iteration' => 'd.', 'reference_name' => 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;'],
            ['reference_iteration' => 'e.', 'reference_name' => 'Laporan Polisi Nomor: ' . $accident->no_lp . ', tanggal ' . Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') . ';'],
            ['reference_iteration' => 'f.', 'reference_name' => 'Surat Perintah Penyidikan Nomor: ' . $sprindikNumber . ', tanggal ' . $sprindikDate . ';'],
            ['reference_iteration' => 'g.', 'reference_name' => 'Surat Pemberitahuan Dimulainya Penyidikan Nomor: ' . $spdpNumber . ', tanggal ' . $spdpDate . ';'],
            ['reference_iteration' => 'h.', 'reference_name' => 'Surat Ketetapan tentang Penetapan Tersangka Nomor: ' . $tapTersangkaNumber . ', tanggal ' . $tapTersangkaDate . ' atas nama ' . ($document->suspects->first()->name ?? '') . '.'],
        ];
        $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);

        $templateProcessor->setValue('berkasNumber', $document->berkas_perkara_number);
        $templateProcessor->setValue('berkasDate', Carbon::parse($document->berkas_perkara_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('berkasRangkap', $document->berkas_perkara_rangkap);

        $blockSuspects = [];
        foreach($document->suspects as $suspect){
            $suspectProperties = $suspect->properties ?? [];
            
            $age = '-';
            if ($suspect->birth_date) {
                $age = Carbon::parse($suspect->birth_date)->age . ' Tahun';
            }

            $fullAddress = ($suspectProperties['is_unknown_address'] ?? false) 
                ? 'TIDAK DIKETAHUI' 
                : (($suspect->country_id == 'C101') 
                    ? ucwords(strtolower(($suspect->address ?? '') . ', ' . ($suspect->village->name ?? '') . ', ' . ($suspect->district->name ?? '') . ', ' . ($suspect->regency->name ?? '') . ', ' . ($suspect->province->name ?? '')))
                    : ucwords(strtolower(($suspect->address ?? '') . ', ' . ($suspect->country->name ?? ''))));

            $blockSuspects[] = [
                'suspectName' => strtoupper($suspect->name),
                'suspectAge' => $age,
                'suspectJobName' => ucwords(strtolower($suspect->job->name ?? '-')),
                'suspectFullAddress' => $fullAddress,
            ];
        }
        $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        
        // Add suspectName outside the block for the Perihal section
        $firstSuspect = $document->suspects->first();
        $templateProcessor->setValue('suspectName', strtoupper($firstSuspect->name ?? ''));

        $templateProcessor->setValue('dugaanTindakPidana', $document->dugaan_tindak_pidana ?? '-');
        
        $sprindikIdRef = $document->surat_perintah_penyidikan_id;
        $onTheFlyPasalString = $sprindikIdRef ? $this->formatSprindikLawsString($sprindikIdRef) : '';
        $finalPasalString = $onTheFlyPasalString ?: ($document->pasal_disangkakan ?? '-');
        
        $templateProcessor->setValue('pasalDisangkakan', $finalPasalString);

        $blockEvidences = [];
        $barangBuktiList = $document->barang_bukti ?? [];
        foreach ($barangBuktiList as $index => $bb) {
            $blockEvidences[] = [
                'bb_iteration' => ($index + 1),
                'bb_name' => $bb,
            ];
        }
        $templateProcessor->cloneBlock('block_evidences', 0, true, false, $blockEvidences);

        $status = $document->penahanan_status ?? 'DITAHAN';
        $detentionParagraph = "";

        if ($status == 'TIDAK_DITAHAN') {
            $detentionParagraph = "TIDAK DILAKUKAN PENAHANAN";
        } else {
            $rutan = $document->penahanan_rutan ?? '......';
            $cabang = $document->penahanan_cabang ?? '......';
            $startDate = $document->penahanan_start_date ? Carbon::parse($document->penahanan_start_date)->locale('id')->translatedFormat('d F Y') : '......';
            $endDate = $document->penahanan_end_date ? Carbon::parse($document->penahanan_end_date)->locale('id')->translatedFormat('d F Y') : '......';
            
            $sppNo = $document->surat_perintah_penahanan_number ?? '......';
            $sppDate = $document->surat_perintah_penahanan_date ? Carbon::parse($document->surat_perintah_penahanan_date)->locale('id')->translatedFormat('d F Y') : '......';
            
            $spppNo = $document->surat_perpanjangan_penahanan_number ?? '......';
            $spppDate = $document->surat_perpanjangan_penahanan_date ? Carbon::parse($document->surat_perpanjangan_penahanan_date)->locale('id')->translatedFormat('d F Y') : '......';
            
            $sppCourtNo = $document->surat_perpanjangan_penahanan_court_number ?? '......';
            $sppCourtDate = $document->surat_perpanjangan_penahanan_court_date ? Carbon::parse($document->surat_perpanjangan_penahanan_court_date)->locale('id')->translatedFormat('d F Y') : '......';

            $detentionParagraph = "Berkaitan dengan hal tersebut, diberitahuan bahwa tersangka tersebut di atas ditahan di Rutan $rutan Cabang $cabang pada tanggal $startDate s.d. tanggal $endDate dengan Surat Perintah Penahanan Nomor: $sppNo tanggal $sppDate, Surat Perintah Perpanjangan Penahanan Nomor: $spppNo tanggal $spppDate dan Surat Perpanjangan Penahanan ke Pengadilan Nomor: $sppCourtNo tanggal $sppCourtDate";

            if ($status == 'DITANGGUHKAN') {
                $suspNo = $document->surat_penangguhan_penahanan_number ?? '......';
                $suspDate = $document->surat_penangguhan_penahanan_date ? Carbon::parse($document->surat_penangguhan_penahanan_date)->locale('id')->translatedFormat('d F Y') : '......';
                $detentionParagraph .= " serta ditangguhkan penahanannya berdasarkan Surat Perintah Penangguhan Penahanan Nomor: $suspNo tanggal $suspDate";
            }
        }

        $templateProcessor->setValue('detentionParagraph', $detentionParagraph);

        $bbStorage = $document->barang_bukti_storage ?? '......';
        $invName = $document->investigator_pangkat_nama ?? '......';
        $invHp = $document->investigator_hp ?? '......';

        $evidenceContactParagraph = "Barang-barang bukti yang tersebut dalam daftar barang bukti disimpan di $bbStorage . Untuk memudahkan dalam berkoordinasi dan berkomunikasi dapat menghubungi Penyidik/Penyidik Pembantu $invName, Hp. $invHp .";

        $templateProcessor->setValue('evidenceContactParagraph', $evidenceContactParagraph);

        $signatoryName = $signatory->full_name ?? '-';
        $signatoryRankName = $signatory->rank ?? '';
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        $templateProcessor->setValue('signatoryName', strtoupper($signatoryName));
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $tembusanArray = $document->tembusan ?? [];
        $blockCarbonCopies = [];
        foreach ($tembusanArray as $index => $value) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => ($index + 1),
                'carbon_copy_name' => $value,
            ];
        }
        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

        $filename = 'generate/' . Str::uuid() . ' - Surat Pengiriman Berkas Perkara (Tahap I) - Resor ' . ($accident->polres->full_name ?? '');
        $templateProcessor->saveAs($filename . '.docx');
        
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
    }

    /**
     * AJAX: Get Laws from a specific Sprindik
     */
    public function getSprindikLaws(Request $request)
    {
        $sprindikId = $request->query('sprindik_id');
        if (!$sprindikId) {
            return response()->json(['success' => false, 'message' => 'Sprindik ID required'], 400);
        }

        return response()->json([
            'success' => true,
            'pasal_string' => $this->formatSprindikLawsString($sprindikId)
        ]);
    }

    /**
     * Helper Method: Ekstrak dan format string pasal dari sprindik ID.
     */
    private function formatSprindikLawsString($sprindikId)
    {
        if (!$sprindikId) return '';

        $laws = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocumentLaw::where('surat_perintah_penyidikan_document_id', $sprindikId)
            ->with(['crimeType', 'crimeClass', 'crimeConstitution'])
            ->get();

        if ($laws->isEmpty()) return '';

        $formattedLaws = $laws->map(function ($law) {
            $chapterInfo = $law->constitution_chapter ?? '';
            $namaUU = $law->crimeConstitution ? $law->crimeConstitution->name : ($law->constitution ?? '');
            $description = $law->crimeConstitution ? $law->crimeConstitution->description : '';

            $verseText = $description;

            $verseNum = null;
            if (preg_match('/ayat\s*\(?(\d+)\)?/i', $chapterInfo, $matches)) {
                $verseNum = $matches[1];
            }

            if ($verseNum && $description) {
                // Lookahead memastikan kita berhenti SEBELUM ayat berikutnya yang cirinya start string, <br>, atau <p>
                if (preg_match('/(?:^|<br[^>]*>|<p>|[\r\n]+)\s*\(' . $verseNum . '\)\s*(.*?)(?=(?:<br[^>]*>|<p>|[\r\n]+)\s*\(\d+\)|$)/is', $description, $descMatches)) {
                    $verseText = $descMatches[1];
                }
            }

            $cleanText = strip_tags($verseText); // Hapus tag HTML
            $cleanText = preg_replace('/^\s*\(\d+\)\s*/', '', $cleanText); // Hapus pola nomor awal
            $cleanText = preg_replace('/\s+/', ' ', $cleanText); // Hapus spasi berlebih
            $cleanText = trim($cleanText);

            if (empty($cleanText) && $law->crimeType) {
                $cleanText = $law->crimeType->name;
            }

            $kalimat = '';
            if ($cleanText) {
                $kalimat .= 'dalam perkara dugaan tindak pidana ' . lcfirst($cleanText) . ', ';
            }
            $kalimat .= 'sebagaimana dimaksud dalam ' . trim($chapterInfo . ' ' . $namaUU);

            return $kalimat;
        })->filter()->values();

        // Jika ada lebih dari satu pasal, hubungkan dengan "dan" (bisa disesuaikan mjd "jo.")
        return implode("\n\ndan\n", $formattedLaws->toArray());
    }

}
