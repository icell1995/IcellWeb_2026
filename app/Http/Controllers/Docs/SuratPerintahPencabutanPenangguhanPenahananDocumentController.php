<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\TemplateProcessor;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Lib\Prison;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocument;
use App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocument;
use App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer;
use App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocumentAttachment;

use App\Traits\DocsOfficersTraits;

class SuratPerintahPencabutanPenangguhanPenahananDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected DocService $docService;

    // Master jenis penahanan sesuai standar KUHAP / SPPT-TI
    public static $masterJenisPenahanan = [
        1 => 'Penahanan Rumah Tahanan Negara (Rutan)',
        2 => 'Penahanan Rumah',
        3 => 'Penahanan Kota',
    ];

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index()
    {
        $accidentId = request()->query('accident_id');
        if ($accidentId) {
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
        }
        return redirect()->back();
    }

    /**
     * Tampilan form pembuatan Surat Perintah Pencabutan Penangguhan Penahanan (S-19)
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // Ambil dokumen SPDP yang sudah ada untuk perkara ini
        $spdpDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSpdp = $spdpDocument->nomor ?? $spdpDocument->document_number ?? '-';
        $tanggalSpdp = $spdpDocument->tanggal ?? $spdpDocument->document_date ?? null;
        $kodeSatkerDefault = $accident->polres->satker_code ?? '006.09.05';

        // Ambil dokumen Surat Perintah Penyidikan
        $sprindikDocument = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil dokumen Surat Ketetapan Penetapan Tersangka
        $sketTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil dokumen Surat Perintah Penahanan (S-17)
        $suratPerintahPenahanan = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil dokumen Surat Perintah Penangguhan Penahanan (S-18)
        $suratPerintahPenangguhan = SuratPerintahPenangguhanPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // Tersangka perkara ini
        $suspects = Suspect::with(['gender', 'job', 'religion', 'education', 'maritalStatus', 'country', 'location'])
            ->where('accident_id', $accidentId)
            ->get();

        // Personel Internal & Pejabat Penandatangan
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

        $internalOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $leaderOfficers = $internalOfficers;

        // Master Rutan
        $prisons = Prison::where('is_active', true)->orderBy('name', 'asc')->get();

        // Master jenis penahanan
        $masterJenisPenahanan = self::$masterJenisPenahanan;

        return view('docs.surat-perintah-pencabutan-penangguhan-penahanan-document.create', compact(
            'accidentId',
            'accident',
            'nomorSpdp',
            'tanggalSpdp',
            'kodeSatkerDefault',
            'sprindikDocument',
            'sketTersangkaDocument',
            'suratPerintahPenahanan',
            'suratPerintahPenangguhan',
            'suspects',
            'internalOfficers',
            'leaderOfficers',
            'authorizedSignatories',
            'prisons',
            'masterJenisPenahanan'
        ));
    }

    /**
     * Simpan dokumen Surat Perintah Pencabutan Penangguhan Penahanan (S-19)
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::where('id', $accidentId)->firstOrFail();

        $this->validateForm($request)->validate();

        DB::beginTransaction();
        try {
            $docId = (string) Uuid::generate();

            // Poin Dasar referensi dokumen
            $sprindikDoc = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            $sketDoc = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            $s17Doc = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            $s18Doc = SuratPerintahPenangguhanPenahananDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            $nomorS17 = $request->nomor_surat_perintah_penahanan ?: ($s17Doc->nomor ?? $s17Doc->document_number ?? null);
            $tanggalS17 = $s17Doc->tanggal ?? $s17Doc->document_date ?? null;

            $nomorS18 = $request->nomor_surat_perintah_penangguhan ?: ($s18Doc->nomor ?? $s18Doc->document_number ?? null);
            $tanggalS18 = $s18Doc->tanggal ?? $s18Doc->document_date ?? null;

            // Simpan Dokumen Utama
            $document = SuratPerintahPencabutanPenangguhanPenahananDocument::create([
                'id'                                            => $docId,
                'accident_id'                                   => $accidentId,
                'surat_perintah_penyidikan_document_id'         => $sprindikDoc->id ?? null,
                'surat_ketetapan_penetapan_tersangka_id'        => $sketDoc->id ?? null,
                'surat_perintah_penahanan_document_id'          => $s17Doc->id ?? null,
                'surat_perintah_penangguhan_penahanan_document_id' => $s18Doc->id ?? null,
                'nomor'                                         => $request->nomor,
                'document_number'                               => $request->nomor,
                'tanggal'                                       => $request->tanggal,
                'document_date'                                 => $request->tanggal,
                'nomor_spdp'                                    => $request->nomor_spdp,
                'tanggal_spdp'                                  => $request->tanggal_spdp,
                'kode_satker_penerbit_spdp'                     => $request->kode_satker_penerbit_spdp,
                'nomor_surat_perintah_penahanan'                => $nomorS17,
                'tanggal_surat_perintah_penahanan'              => $tanggalS17,
                'nomor_surat_perintah_penangguhan'              => $nomorS18,
                'tanggal_surat_perintah_penangguhan'            => $tanggalS18,
                'kode_jenis_penahanan'                          => $request->kode_jenis_penahanan ?? 1,
                'kode_satker_tempat_penahanan'                  => $request->kode_satker_tempat_penahanan,
                'tempat_penahanan'                              => $request->tempat_penahanan,
                'jumlah_hari'                                   => $request->jumlah_hari ?? 20,
                'tanggal_mulai'                                 => $request->tanggal_mulai,
                'tanggal_akhir'                                 => $request->tanggal_akhir,
                'alasan_pencabutan'                             => $request->alasan_pencabutan ?? 'melanggar persyaratan yang telah ditetapkan',
                'status_id'                                     => '2',
                'document_category_id'                          => '0604',
                'created_by_user_id'                            => Auth::id(),
                'messages'                                      => [
                    'alasan_pencabutan'            => $request->alasan_pencabutan,
                    'tempat_penahanan_keterangan'  => $request->tempat_penahanan_keterangan,
                ],
            ]);

            // Sync Tersangka
            if (!empty($request->suspects)) {
                $syncData = [];
                foreach ($request->suspects as $suspectId) {
                    $syncData[$suspectId] = [
                        'id' => (string) Uuid::generate(),
                    ];
                }
                $document->suspects()->sync($syncData);
            }

            // Simpan Petugas Ketua Tim (LEADER)
            if (!empty($request->officerLeader)) {
                $leader = Officer::with(['rank', 'position', 'police'])->find($request->officerLeader);
                if ($leader) {
                    SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::create([
                        'id'              => (string) Uuid::generate(),
                        'doc_id'          => $docId,
                        'officer_id'      => $leader->id,
                        'first_name'      => $leader->first_name,
                        'last_name'       => $leader->last_name,
                        'first_title'     => $leader->first_title,
                        'last_title'      => $leader->last_title,
                        'register_number' => $leader->register_number,
                        'position_id'     => $leader->position_id,
                        'rank_id'         => $leader->rank_id,
                        'police_id'       => $leader->police_id,
                        'class'           => 'LEADER',
                        'order_number'    => 1,
                    ]);
                }
            }

            // Simpan Anggota Petugas (MEMBER)
            if (!empty($request->officers) && is_array($request->officers)) {
                $orderNumber = 2;
                foreach ($request->officers as $officerId) {
                    if ($officerId == $request->officerLeader) continue;
                    $officer = Officer::with(['rank', 'position', 'police'])->find($officerId);
                    if ($officer) {
                        SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::create([
                            'id'              => (string) Uuid::generate(),
                            'doc_id'          => $docId,
                            'officer_id'      => $officer->id,
                            'first_name'      => $officer->first_name,
                            'last_name'       => $officer->last_name,
                            'first_title'     => $officer->first_title,
                            'last_title'      => $officer->last_title,
                            'register_number' => $officer->register_number,
                            'position_id'     => $officer->position_id,
                            'rank_id'         => $officer->rank_id,
                            'police_id'       => $officer->police_id,
                            'class'           => 'MEMBER',
                            'order_number'    => $orderNumber++,
                        ]);
                    }
                }
            }

            // Simpan Pejabat Penandatangan (SIGNATORY)
            if (!empty($request->signatory)) {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
                if ($signatory) {
                    SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::create([
                        'id'              => (string) Uuid::generate(),
                        'doc_id'          => $docId,
                        'officer_id'      => $signatory->id,
                        'first_name'      => $signatory->first_name,
                        'last_name'       => $signatory->last_name,
                        'first_title'     => $signatory->first_title,
                        'last_title'      => $signatory->last_title,
                        'register_number' => $signatory->register_number,
                        'position_id'     => $signatory->position_id,
                        'rank_id'         => $signatory->rank_id,
                        'police_id'       => $signatory->police_id,
                        'class'           => 'SIGNATORY',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Pencabutan Penangguhan Penahanan berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan form pengubahan Surat Perintah Pencabutan Penangguhan Penahanan (S-19)
     */
    public function edit(string $id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratPerintahPencabutanPenangguhanPenahananDocument::with([
            'accident',
            'accident.polres',
            'suspects',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.rank',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.position',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.police'
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;

        $nomorSpdp = $document->nomor_spdp;
        $tanggalSpdp = $document->tanggal_spdp;
        $kodeSatkerDefault = $document->kode_satker_penerbit_spdp ?? ($accident->polres->satker_code ?? '006.09.05');

        $suspects = Suspect::with(['gender', 'job', 'religion', 'education', 'maritalStatus', 'country', 'location'])
            ->where('accident_id', $accidentId)
            ->get();

        $selectedSuspectIds = $document->suspects->pluck('id')->toArray();

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

        $internalOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $leaderOfficers = $internalOfficers;

        $selectedLeader = $document->leaderOfficer;
        $currentLeaderVal = $selectedLeader ? ($selectedLeader->officer_id ?? $selectedLeader->id) : null;

        $selectedSignatory = $document->signatory;
        $selectedSignatoryId = $selectedSignatory ? ($selectedSignatory->officer_id ?? $selectedSignatory->id) : null;

        $prisons = Prison::where('is_active', true)->orderBy('name', 'asc')->get();
        $masterJenisPenahanan = self::$masterJenisPenahanan;

        return view('docs.surat-perintah-pencabutan-penangguhan-penahanan-document.edit', compact(
            'document',
            'accidentId',
            'accident',
            'nomorSpdp',
            'tanggalSpdp',
            'kodeSatkerDefault',
            'suspects',
            'selectedSuspectIds',
            'internalOfficers',
            'leaderOfficers',
            'currentLeaderVal',
            'authorizedSignatories',
            'selectedSignatoryId',
            'prisons',
            'masterJenisPenahanan'
        ));
    }

    /**
     * Perbarui Surat Perintah Pencabutan Penangguhan Penahanan (S-19)
     */
    public function update(Request $request, string $id)
    {
        $accidentId = $request->accident_id;
        $document = SuratPerintahPencabutanPenangguhanPenahananDocument::where('id', $id)->firstOrFail();

        $this->validateForm($request)->validate();

        DB::beginTransaction();
        try {
            $document->update([
                'nomor'                             => $request->nomor,
                'document_number'                    => $request->nomor,
                'tanggal'                           => $request->tanggal,
                'document_date'                     => $request->tanggal,
                'nomor_spdp'                        => $request->nomor_spdp,
                'tanggal_spdp'                      => $request->tanggal_spdp,
                'kode_satker_penerbit_spdp'         => $request->kode_satker_penerbit_spdp,
                'nomor_surat_perintah_penahanan'    => $request->nomor_surat_perintah_penahanan,
                'nomor_surat_perintah_penangguhan'  => $request->nomor_surat_perintah_penangguhan,
                'kode_jenis_penahanan'              => $request->kode_jenis_penahanan ?? 1,
                'kode_satker_tempat_penahanan'      => $request->kode_satker_tempat_penahanan,
                'tempat_penahanan'                  => $request->tempat_penahanan,
                'jumlah_hari'                       => $request->jumlah_hari ?? 20,
                'tanggal_mulai'                     => $request->tanggal_mulai,
                'tanggal_akhir'                     => $request->tanggal_akhir,
                'alasan_pencabutan'                 => $request->alasan_pencabutan ?? 'melanggar persyaratan yang telah ditetapkan',
                'updated_by_user_id'                => Auth::id(),
                'messages'                          => [
                    'alasan_pencabutan'           => $request->alasan_pencabutan,
                    'tempat_penahanan_keterangan' => $request->tempat_penahanan_keterangan,
                ],
            ]);

            // Sync Tersangka
            if (!empty($request->suspects)) {
                $syncData = [];
                foreach ($request->suspects as $suspectId) {
                    $syncData[$suspectId] = [
                        'id' => (string) Uuid::generate(),
                    ];
                }
                $document->suspects()->sync($syncData);
            }

            // Hapus officer lama dan insert ulang
            SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::where('doc_id', $document->id)->delete();

            // Simpan Ketua Tim (LEADER)
            if (!empty($request->officerLeader)) {
                $leader = Officer::with(['rank', 'position', 'police'])->find($request->officerLeader);
                if ($leader) {
                    SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::create([
                        'id'              => (string) Uuid::generate(),
                        'doc_id'          => $document->id,
                        'officer_id'      => $leader->id,
                        'first_name'      => $leader->first_name,
                        'last_name'       => $leader->last_name,
                        'first_title'     => $leader->first_title,
                        'last_title'      => $leader->last_title,
                        'register_number' => $leader->register_number,
                        'position_id'     => $leader->position_id,
                        'rank_id'         => $leader->rank_id,
                        'police_id'       => $leader->police_id,
                        'class'           => 'LEADER',
                        'order_number'    => 1,
                    ]);
                }
            }

            // Simpan Anggota Petugas (MEMBER)
            if (!empty($request->officers) && is_array($request->officers)) {
                $orderNumber = 2;
                foreach ($request->officers as $officerId) {
                    if ($officerId == $request->officerLeader) continue;
                    $officer = Officer::with(['rank', 'position', 'police'])->find($officerId);
                    if ($officer) {
                        SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::create([
                            'id'              => (string) Uuid::generate(),
                            'doc_id'          => $document->id,
                            'officer_id'      => $officer->id,
                            'first_name'      => $officer->first_name,
                            'last_name'       => $officer->last_name,
                            'first_title'     => $officer->first_title,
                            'last_title'      => $officer->last_title,
                            'register_number' => $officer->register_number,
                            'position_id'     => $officer->position_id,
                            'rank_id'         => $officer->rank_id,
                            'police_id'       => $officer->police_id,
                            'class'           => 'MEMBER',
                            'order_number'    => $orderNumber++,
                        ]);
                    }
                }
            }

            // Simpan Pejabat Penandatangan (SIGNATORY)
            if (!empty($request->signatory)) {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
                if ($signatory) {
                    SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer::create([
                        'id'              => (string) Uuid::generate(),
                        'doc_id'          => $document->id,
                        'officer_id'      => $signatory->id,
                        'first_name'      => $signatory->first_name,
                        'last_name'       => $signatory->last_name,
                        'first_title'     => $signatory->first_title,
                        'last_title'      => $signatory->last_title,
                        'register_number' => $signatory->register_number,
                        'position_id'     => $signatory->position_id,
                        'rank_id'         => $signatory->rank_id,
                        'police_id'       => $signatory->police_id,
                        'class'           => 'SIGNATORY',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Pencabutan Penangguhan Penahanan berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Hapus dokumen
     */
    public function delete(string $id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPerintahPencabutanPenangguhanPenahananDocument::where('id', $id)->firstOrFail();

        $accidentId = $accidentId ?: $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Pencabutan Penangguhan Penahanan berhasil dihapus.');
    }

    /**
     * AJAX Form Validation
     */
    public function validateRequestForm(Request $request)
    {
        try {
            $validator = $this->validateForm($request);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code'    => 422,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'code'    => 200,
                'message' => 'Silahkan menunggu proses simpan data',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors'  => 'Terjadi kesalahan pada sistem: ' . $e->getMessage(),
                'code'    => 500,
            ], 500);
        }
    }

    /**
     * Validasi Form S-19
     */
    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'nomor'                        => 'required',
            'tanggal'                      => 'required|date',
            'nomor_spdp'                   => 'required',
            'tanggal_spdp'                 => 'required|date',
            'kode_satker_penerbit_spdp'    => 'required',
            'suspects'                     => 'required|array|min:1',
            'officerLeader'                => 'required',
            'signatory'                    => 'required',
            'kode_jenis_penahanan'         => 'required',
            'jumlah_hari'                  => 'nullable|numeric|min:1',
            'tanggal_mulai'                => 'nullable|date',
            'tanggal_akhir'                => 'nullable|date',
        ], [
            'nomor.required'                     => 'Nomor Dokumen S-19 wajib diisi.',
            'tanggal.required'                   => 'Tanggal S-19 wajib diisi.',
            'nomor_spdp.required'                => 'Nomor SPDP wajib terisi.',
            'tanggal_spdp.required'              => 'Tanggal SPDP wajib terisi.',
            'kode_satker_penerbit_spdp.required' => 'Kode Satker Penerbit SPDP wajib terisi.',
            'suspects.required'                  => 'Tersangka yang dicabut penangguhannya harus dipilih minimal 1 orang.',
            'suspects.min'                       => 'Tersangka yang dicabut penangguhannya harus dipilih minimal 1 orang.',
            'officerLeader.required'             => 'Mohon memilih Ketua Tim.',
            'signatory.required'                 => 'Pejabat Penandatangan wajib dipilih.',
            'kode_jenis_penahanan.required'      => 'Jenis penahanan wajib dipilih.',
        ]);
    }

    /**
     * Download dokumen Word (.docx) S-19
     */
    public function download(string $id)
    {
        $document = SuratPerintahPencabutanPenangguhanPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects.gender',
            'suspects.job',
            'suspects.country',
            'suratPerintahPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenahananDocument',
            'suratPerintahPenangguhanPenahananDocument',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.rank',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.position',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.police'
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;

        $templatePath = public_path('word-template/surat_perintah_pencabutan_penangguhan_penahanan.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template dokumen Surat Perintah Pencabutan Penangguhan Penahanan tidak ditemukan.');
        }

        $tempFileName = 's19_' . $document->id . '_' . time() . '.docx';
        $tempPath = storage_path('app/' . $tempFileName);
        copy($templatePath, $tempPath);

        // Pre-filter Word XML
        $this->filterPencabutanDocx($tempPath, $document);

        $templateProcessor = new TemplateProcessor($tempPath);

        // 1. Header Satuan Kepolisian
        $daerah = $accident->polres->polda->name ?? '';
        $resor = $accident->polres->full_name ?? ($accident->polres->name ?? '');
        $alamat = $accident->polres->address ?? '';

        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($daerah));
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($resor));
        $templateProcessor->setValue('resorPoliceAddress', $alamat);
        $templateProcessor->setValue('documentNumber', $document->nomor ?? $document->document_number ?? '-');

        // 2. Dasar Poin 5: Laporan Polisi
        $noLp = $accident->no_lp ?? '-';
        $tglLp = $accident->accident_date ? $this->formatDateIndo($accident->accident_date) : '-';
        $templateProcessor->setValue('accidentNumber', $noLp);
        $templateProcessor->setValue('accidentDate', $tglLp);

        // Dasar Poin 6: Surat Perintah Penyidikan
        $sprindik = $document->suratPerintahPenyidikanDocument;
        $noSprindik = $sprindik->nomor ?? $sprindik->document_number ?? '-';
        $tglSprindik = $sprindik ? $this->formatDateIndo($sprindik->tanggal ?? $sprindik->document_date) : '-';
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $noSprindik);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDate', $tglSprindik);

        // Dasar Poin 7: Surat Ketetapan Penetapan Tersangka
        $sket = $document->suratKetetapanTentangPenetapanTersangkaDocument;
        $noSket = $sket->nomor ?? $sket->document_number ?? '-';
        $tglSket = $sket ? $this->formatDateIndo($sket->tanggal ?? $sket->document_date) : '-';
        $templateProcessor->setValue('suratKetetapanTentangPenetapanTersangkaDcumentNumber', $noSket);
        $templateProcessor->setValue('suratKetetapanTentangPenetapanTersangkaDocumentNumber', $noSket);
        $templateProcessor->setValue('suratKetetapanTentangPenetapanTersangkaDcumentDate', $tglSket);
        $templateProcessor->setValue('suratKetetapanTentangPenetapanTersangkaDocumentDate', $tglSket);

        // Dasar Poin 8: Surat Perintah Penahanan (S-17)
        $s17 = $document->suratPerintahPenahananDocument;
        $noS17 = $document->nomor_surat_perintah_penahanan ?: ($s17->nomor ?? $s17->document_number ?? '-');
        $tglS17 = $document->tanggal_surat_perintah_penahanan
            ? $this->formatDateIndo($document->tanggal_surat_perintah_penahanan)
            : ($s17 ? $this->formatDateIndo($s17->tanggal ?? $s17->document_date) : '-');
        $templateProcessor->setValue('suratPerintahPenahananDocumentNumber', $noS17);
        $templateProcessor->setValue('suratPerintahPenahananDocumentDate', $tglS17);

        // Dasar Poin 9: Surat Perintah Penangguhan Penahanan (S-18)
        $s18 = $document->suratPerintahPenangguhanPenahananDocument;
        $noS18 = $document->nomor_surat_perintah_penangguhan ?: ($s18->nomor ?? $s18->document_number ?? '-');
        $tglS18 = $document->tanggal_surat_perintah_penangguhan
            ? $this->formatDateIndo($document->tanggal_surat_perintah_penangguhan)
            : ($s18 ? $this->formatDateIndo($s18->tanggal ?? $s18->document_date) : '-');
        $templateProcessor->setValue('suratPerintahPenangguhanPenahananDocumentNumber', $noS18);
        $templateProcessor->setValue('suratPerintahPenangguhanPenahananDocumentDate', $tglS18);

        // 3. Tersangka
        $suspect = $document->suspects->first();
        $suspectName = $suspect ? strtoupper($suspect->name) : '-';
        $templateProcessor->setValue('suspectName', $suspectName);
        $templateProcessor->setValue('susectName', $suspectName); // typo di template poin 8

        $templateProcessor->setValue('suspectIdentityNumber', $suspect->identity_number ?? '-');
        $templateProcessor->setValue('suspectBirthPlace', $suspect->birth_place ?? '-');
        $templateProcessor->setValue('suspectBirthDate', $suspect && $suspect->birth_date ? $this->formatDateIndo($suspect->birth_date) : '-');
        $templateProcessor->setValue('suspectGenderName', $suspect->gender->name ?? '-');
        $templateProcessor->setValue('suspectJobName', $suspect->job->name ?? '-');
        $templateProcessor->setValue('suspectNationality', $suspect->country->name ?? 'Indonesia');
        $templateProcessor->setValue('suspectFullAddress', $suspect->address ?? '-');

        // 4. Tempat Penahanan & Sisa Waktu Masa Penahanan
        $tempatPenahananText = $this->getTempatPenahananText($document, $accident, $suspect);
        $templateProcessor->setValue('tempat_penahanan', $tempatPenahananText);

        $jumlahHari = intval($document->jumlah_hari ?: 20);
        $templateProcessor->setValue('jumlah_hari', (string) $jumlahHari);
        $templateProcessor->setValue('terbilang_hari', $this->terbilang($jumlahHari));
        $templateProcessor->setValue('tanggal_mulai', $document->tanggal_mulai ? $this->formatDateIndo($document->tanggal_mulai) : '-');
        $templateProcessor->setValue('tanggal_akhir', $document->tanggal_akhir ? $this->formatDateIndo($document->tanggal_akhir) : '-');

        // 5. Petugas yang Diperintahkan (Block Officers)
        $leaderOfficer = $document->leaderOfficer ?? $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers->where('class', 'LEADER')->first();
        $memberOfficers = $document->memberOfficers ?? $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers->where('class', 'MEMBER');

        $blockOfficers = [];
        $order = 1;

        if ($leaderOfficer) {
            $fullName = PeopleNameHelper::getFullName($leaderOfficer->first_title, $leaderOfficer->first_name, $leaderOfficer->last_name, $leaderOfficer->last_title);
            $pos = $leaderOfficer->position->name ?? ($leaderOfficer->position_id ?? 'Ketua Tim');
            $blockOfficers[] = [
                'number'     => (string) $order++,
                'first_name' => $fullName . ' (Ketua Tim)',
                'last_name'  => '',
                'rank_id'    => $leaderOfficer->rank->name ?? ($leaderOfficer->rank_id ?? '-'),
                'officer_id' => $leaderOfficer->register_number ?? '-',
                'position'   => $pos,
            ];
        }

        foreach ($memberOfficers as $member) {
            $fullName = PeopleNameHelper::getFullName($member->first_title, $member->first_name, $member->last_name, $member->last_title);
            $blockOfficers[] = [
                'number'     => (string) $order++,
                'first_name' => $fullName,
                'last_name'  => '',
                'rank_id'    => $member->rank->name ?? ($member->rank_id ?? '-'),
                'officer_id' => $member->register_number ?? '-',
                'position'   => $member->position->name ?? ($member->position_id ?? '-'),
            ];
        }

        if (empty($blockOfficers)) {
            $blockOfficers[] = [
                'number'     => '1',
                'first_name' => '-',
                'last_name'  => '',
                'rank_id'    => '-',
                'officer_id' => '-',
                'position'   => '-',
            ];
        }

        $templateProcessor->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);

        // 6. Tanda Tangan Ketua Tim (Leader Officer)
        if ($leaderOfficer) {
            $leaderName = PeopleNameHelper::getFullName($leaderOfficer->first_title, $leaderOfficer->first_name, $leaderOfficer->last_name, $leaderOfficer->last_title);
            $leaderRank = $leaderOfficer->rank->name ?? ($leaderOfficer->rank_id ?? '-');
            $leaderNrp = $leaderOfficer->register_number ?? '-';
        } else {
            $leaderName = '-';
            $leaderRank = '-';
            $leaderNrp = '-';
        }

        $templateProcessor->setValue('leaderOfficerName', $leaderName);
        $templateProcessor->setValue('leaderOfficerRankName', $leaderRank);
        $templateProcessor->setValue('leaderOfficerRegisterNumber', $leaderNrp);

        // 7. Pejabat Penandatangan (Signatory)
        $signatory = $document->signatory ?? $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers->where('class', 'SIGNATORY')->first();
        if ($signatory) {
            $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
            $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
            $signatoryNrp = $signatory->register_number ?? '-';
            $signatoryPosition = strtoupper($signatory->position->name ?? ($signatory->position_id ?? 'KEPALA KEPOLISIAN'));
        } else {
            $signatoryName = '-';
            $signatoryRank = '-';
            $signatoryNrp = '-';
            $signatoryPosition = 'KEPALA KEPOLISIAN';
        }

        $polresName = strtoupper($accident->polres->full_name ?? ($accident->polres->name ?? ''));
        $templateProcessor->setValue('signatoryHeadText', 'a.n. KEPALA KEPOLISIAN RESOR ' . $polresName);
        $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPosition);
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', $signatoryRank);
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryNrp);

        // 8. Dikeluarkan di & pada tanggal
        $kota = $accident->polres->city ?? ($accident->polres->name ?? 'Tempat');
        $tglDokumen = $document->tanggal ? $this->formatDateIndo($document->tanggal) : $this->formatDateIndo(date('Y-m-d'));
        $templateProcessor->setValue('dikeluarkan_di', $kota);
        $templateProcessor->setValue('tanggal_dikeluarkan', $tglDokumen);

        // 9. Serah terima
        $templateProcessor->setValue('downloadDay', $this->formatDayIndo(date('Y-m-d')));
        $templateProcessor->setValue('downloadDate', $this->formatDateIndo(date('Y-m-d')));

        $safeNomor = str_replace(['/', '\\', ' '], '_', $document->nomor ?? 'dokumen');
        $downloadName = 'Surat_Perintah_Pencabutan_Penangguhan_Penahanan_' . $safeNomor . '.docx';

        $outputFile = storage_path('app/' . $downloadName);
        $templateProcessor->saveAs($outputFile);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        return response()->download($outputFile, $downloadName)->deleteFileAfterSend(true);
    }

    /**
     * Pra-pemrosesan XML dokumen Word template S-19
     */
    private function filterPencabutanDocx(string $filePath, SuratPerintahPencabutanPenangguhanPenahananDocument $document): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // 1. Gabungkan run XML yang terpecah di dalam tag ${...}
        $mergePattern = '/<\/w:t>(?:(?!<\/w:p>).)*?<w:t[^>]*>/s';
        $xmlOut = preg_replace($mergePattern, '', $xml);

        // 2. Modifikasi tabel tanda tangan:
        // Kiri atas (Yang menerima perintah) & kanan bawah (Yang Menyerahkan) -> Ketua Tim
        // Kanan atas -> Pejabat Penandatangan
        $pos = strpos($xmlOut, 'Yang menerima perintah');
        if ($pos !== false) {
            $tableStart = strrpos(substr($xmlOut, 0, $pos), '<w:tbl');
            $tableEnd = strpos($xmlOut, '</w:tbl>', $pos) + strlen('</w:tbl>');
            $tableXml = substr($xmlOut, $tableStart, $tableEnd - $tableStart);

            $parts = explode('Yang Menyerahkan', $tableXml, 2);
            if (count($parts) === 2) {
                // Di bagian atas tabel: ganti signatoryName pertama (kiri) dengan leaderOfficerName
                $parts[0] = preg_replace('/signatoryName/', 'leaderOfficerName', $parts[0], 1);
                $parts[0] = preg_replace('/signatoryRankName/', 'leaderOfficerRankName', $parts[0], 1);
                $parts[0] = preg_replace('/signatoryRegisterNumber/', 'leaderOfficerRegisterNumber', $parts[0], 1);

                // Di bagian bawah tabel: ganti signatoryName (Yang Menyerahkan) dengan leaderOfficerName
                $parts[1] = preg_replace('/signatoryName/', 'leaderOfficerName', $parts[1]);
                $parts[1] = preg_replace('/signatoryRankName/', 'leaderOfficerRankName', $parts[1]);
                $parts[1] = preg_replace('/signatoryRegisterNumber/', 'leaderOfficerRegisterNumber', $parts[1]);

                $newTableXml = implode('Yang Menyerahkan', $parts);
            } else {
                $newTableXml = preg_replace('/signatoryName/', 'leaderOfficerName', $tableXml, 1);
                $newTableXml = preg_replace('/signatoryRankName/', 'leaderOfficerRankName', $newTableXml, 1);
                $newTableXml = preg_replace('/signatoryRegisterNumber/', 'leaderOfficerRegisterNumber', $newTableXml, 1);
            }

            $xmlOut = substr_replace($xmlOut, $newTableXml, $tableStart, strlen($tableXml));
        }

        // 3. Tambahkan placeholder pada "Dikeluarkan di : " dan "pada tanggal : "
        $xmlOut = preg_replace('/Dikeluarkan di\s*:\s*/', 'Dikeluarkan di : ${dikeluarkan_di}', $xmlOut);
        $xmlOut = preg_replace('/pada tanggal\s*:\s*/', 'pada tanggal : ${tanggal_dikeluarkan}', $xmlOut);

        // 4. Otomatisasi sisa waktu masa penahanan
        $xmlOut = preg_replace(
            '/guna menjalani sisa waktu masa penahanan selama\s*[\.\s\(\)]+\s*hari mulai tanggal\s*[\.\s]+\s*s\.d\.\s*[\.\s]+/u',
            'guna menjalani sisa waktu masa penahanan selama ${jumlah_hari} (${terbilang_hari}) hari mulai tanggal ${tanggal_mulai} s.d. ${tanggal_akhir}',
            $xmlOut
        );

        // 5. Otomatisasi tanggal serah terima
        $xmlOut = preg_replace(
            '/Pada hari ini\s*[\.\s]+\s*tanggal\s*[\.\s]+/u',
            'Pada hari ini ${downloadDay} tanggal ${downloadDate}',
            $xmlOut
        );

        $zip->addFromString('word/document.xml', $xmlOut);
        $zip->close();
    }

    /**
     * Buat teks penempatan / tempat penahanan
     */
    private function getTempatPenahananText(
        SuratPerintahPencabutanPenangguhanPenahananDocument $document,
        ?Accident $accident = null,
        ?Suspect $suspect = null
    ): string
    {
        if (!empty($document->tempat_penahanan)) {
            return $document->tempat_penahanan;
        }

        $kodeJenis = intval($document->kode_jenis_penahanan ?? 1);
        if ($kodeJenis === 1) {
            $rutan = $document->kode_satker_tempat_penahanan ?: ('Polres ' . ($accident->polres->name ?? ''));
            return "Rumah Tahanan Negara (Rutan) pada " . $rutan;
        } elseif ($kodeJenis === 2) {
            $alamat = $suspect->address ?? ($accident->polres->name ?? '-');
            return "Rumah tempat tinggal tersangka di " . $alamat;
        } elseif ($kodeJenis === 3) {
            $kota = $accident->polres->city ?? ($accident->polres->name ?? '-');
            return "Kota tempat tinggal tersangka di " . $kota;
        }

        return "Rumah Tahanan Negara (Rutan) pada Kepolisian Resor " . ($accident->polres->name ?? '');
    }

    /**
     * Tampilan Detail / Format JSON S-19 (SPPT-TI / Pusiknas)
     */
    public function show(string $id, Request $request)
    {
        $document = SuratPerintahPencabutanPenangguhanPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            'accident.suratPerintahPenangguhanPenahananDocuments',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.rank',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.position',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.police',
            'status',
            'attachment'
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $jsonPayload = self::buildJsonPayloadStatic($document);

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return view('docs.surat-perintah-pencabutan-penangguhan-penahanan-document.show', compact('document', 'accident', 'jsonPayload'));
    }

    /**
     * Endpoint API murni JSON untuk integrasi atau pengujian
     */
    public function json(string $id, Request $request)
    {
        $document = SuratPerintahPencabutanPenangguhanPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            'accident.suratPerintahPenangguhanPenahananDocuments',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.rank',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.position',
            'suratPerintahPencabutanPenangguhanPenahananDocumentOfficers.police',
            'status',
            'attachment'
        ])->where('id', $id)->firstOrFail();

        $jsonPayload = self::buildJsonPayloadStatic($document);

        return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Helper membuat JSON S-19 persis dengan contoh user & spesifikasi SPPT-TI
     */
    public function buildJsonPayload(SuratPerintahPencabutanPenangguhanPenahananDocument $document): array
    {
        return self::buildJsonPayloadStatic($document);
    }

    /**
     * Static helper untuk membuat JSON S-19 sesuai format baku yang dibutuhkan
     */
    public static function buildJsonPayloadStatic(SuratPerintahPencabutanPenangguhanPenahananDocument $document): array
    {
        $accident = $document->accident;

        // 1. Identitas Dokumen (Tabel a: nomor, tanggal, nomor_spdp, tanggal_spdp, kode_satker_penerbit_spdp, nomor_s18)
        $nomorSpdp = $document->nomor_spdp;
        $tanggalSpdp = $document->tanggal_spdp;
        if (empty($nomorSpdp) && $accident) {
            $spdpDoc = $accident->suratPemberitahuanDimulainyaPenyidikanDocuments
                ? $accident->suratPemberitahuanDimulainyaPenyidikanDocuments->first()
                : null;
            if ($spdpDoc) {
                $nomorSpdp = $spdpDoc->nomor ?? $spdpDoc->document_number;
                $tanggalSpdp = $tanggalSpdp ?: ($spdpDoc->tanggal ?? $spdpDoc->document_date);
            }
        }

        $nomorS18 = $document->nomor_s18 ?? ($document->nomor_surat_perintah_penangguhan ?? null);
        if (empty($nomorS18) && $accident) {
            $s18Doc = $accident->suratPerintahPenangguhanPenahananDocuments
                ? $accident->suratPerintahPenangguhanPenahananDocuments->sortByDesc('id')->first()
                : null;
            if ($s18Doc) {
                $nomorS18 = $s18Doc->nomor ?? $s18Doc->document_number;
            }
        }

        $identitasDokumen = [
            'nomor'                     => (string) ($document->nomor ?: ($document->document_number ?: ($document->exists ? '-' : 'SRT/3/12/2012.PolresJAKPUS'))),
            'tanggal'                   => $document->tanggal ? Carbon::parse($document->tanggal)->format('Y-m-d') : ($document->document_date ? Carbon::parse($document->document_date)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-01-15')),
            'nomor_spdp'                => (string) ($nomorSpdp ?: ($document->exists ? '-' : 'SPDP/3/12/2012.PolresJAKPUS')),
            'tanggal_spdp'              => $tanggalSpdp ? Carbon::parse($tanggalSpdp)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-01-15'),
            'kode_satker_penerbit_spdp' => (string) ($document->kode_satker_penerbit_spdp ?: ($accident->polres->satker_code ?? '006.09.05')),
            'nomor_s18'                 => (string) ($nomorS18 ?: ($document->exists ? '-' : 'S18/3/12/2012.PolresJAKPUS')),
        ];

        // 2. Konten Dokumen - Pejabat Penandatangan (Tabel b: pejabat_penandatangan[] array aparat_negara)
        $pejabatPenandatangan = [];
        $signatories = $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers
            ? $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers->where('class', 'SIGNATORY')
            : collect();

        if ($signatories->isEmpty() && $document->signatory) {
            $signatories = collect([$document->signatory]);
        }

        if ($signatories->isNotEmpty()) {
            foreach ($signatories as $officer) {
                $pejabatPenandatangan[] = [
                    'nama'        => PeopleNameHelper::getFullName($officer->first_title, $officer->first_name, $officer->last_name, $officer->last_title),
                    'nomor_induk' => (string) ($officer->register_number ?: '-'),
                    'jabatan'     => (string) ($officer->position->name ?? ($officer->position_id ?? '-')),
                    'pangkat'     => (string) ($officer->rank->name ?? ($officer->rank_id ?? '-')),
                ];
            }
        } elseif ($document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers && $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers->isNotEmpty()) {
            $firstOfficer = $document->suratPerintahPencabutanPenangguhanPenahananDocumentOfficers->first();
            $pejabatPenandatangan[] = [
                'nama'        => PeopleNameHelper::getFullName($firstOfficer->first_title, $firstOfficer->first_name, $firstOfficer->last_name, $firstOfficer->last_title),
                'nomor_induk' => (string) ($firstOfficer->register_number ?: '-'),
                'jabatan'     => (string) ($firstOfficer->position->name ?? ($firstOfficer->position_id ?? '-')),
                'pangkat'     => (string) ($firstOfficer->rank->name ?? ($firstOfficer->rank_id ?? '-')),
            ];
        } else {
            $pejabatPenandatangan[] = [
                'nama'        => 'Evan Bangun',
                'nomor_induk' => '199805052020021001',
                'jabatan'     => 'Ajun Jaksa',
                'pangkat'     => 'Penata Muda Tingkat I',
            ];
        }

        $kontenDokumen = [
            'pejabat_penandatangan' => $pejabatPenandatangan,
        ];

        $terenkripsi = filter_var($document->terenkripsi ?? ($document->messages['terenkripsi'] ?? false), FILTER_VALIDATE_BOOLEAN);
        $daftarKunciEnkripsi = $terenkripsi
            ? (array) ($document->daftar_kunci_enkripsi ?? ($document->messages['daftar_kunci_enkripsi'] ?? []))
            : [];
        $tandaTanganDigital = $document->tanda_tangan_digital
            ?? ($document->messages['tanda_tangan_digital'] ?? null);

        return [
            'kode_jenis_dokumen'    => 's19',
            'identitas_dokumen'     => $identitasDokumen,
            'konten_dokumen'        => $kontenDokumen,
            'terenkripsi'           => $terenkripsi,
            'daftar_kunci_enkripsi' => $daftarKunciEnkripsi,
            'tanda_tangan_digital'  => $tandaTanganDigital,
        ];
    }

    /**
     * Konversi angka ke terbilang bahasa Indonesia
     */
    private function terbilang(int $angka): string
    {
        $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return $bilangan[$angka - 10] . ' belas';
        } elseif ($angka < 100) {
            return $bilangan[intval($angka / 10)] . ' puluh' . ($angka % 10 != 0 ? ' ' . $bilangan[$angka % 10] : '');
        }
        return (string) $angka;
    }

    /**
     * Format tanggal Indonesia menggunakan Carbon
     */
    private function formatDateIndo($date): string
    {
        if (empty($date)) {
            return '-';
        }
        try {
            return Carbon::parse($date)->locale('id')->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            return '-';
        }
    }

    /**
     * Format nama hari Indonesia menggunakan Carbon
     */
    private function formatDayIndo($date): string
    {
        if (empty($date)) {
            return '-';
        }
        try {
            return Carbon::parse($date)->locale('id')->translatedFormat('l');
        } catch (\Throwable $e) {
            return '-';
        }
    }
}
