<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Models\Suspect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\DocsOfficersTraits;
use App\Helpers\PeopleNameHelper;
use App\Models\DaftarBarangBukti;

class SuratKetetapanPenghentianPenyidikanDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     if (Auth::check() && Auth::user()->role_id != 1) {
        //         abort(403, 'Akses ditolak. Fitur ini hanya tersedia untuk Administrator.');
        //     }
        //     return $next($request);
        // });
    }

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

        $suratPerintahPenyidikanDocuments = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $laporanHasilGelarPerkaraDocuments = \App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $alasanPenghentianOptions = [
            'TIDAK_CUKUP_BUKTI'         => 'Tidak Terdapat Cukup Alat Bukti',
            'BUKAN_TINDAK_PIDANA'       => 'Peristiwa Tersebut Bukan Merupakan Tindak Pidana',
            'DIHENTIKAN_DEMI_HUKUM'     => 'Penyidikan Dihentikan Demi Hukum',
            'PUTUSAN_PENGADILAN'        => 'Terdapat Putusan Pengadilan Yang Telah Memperoleh Kekuatan Hukum Tetap Terhadap Tersangka Atas Perkara Yang Sama',
            'KEDALUARSA'                => 'Kedaluarsa',
            'TERSANGKA_MENINGGAL'       => 'Tersangka Meninggal Dunia',
            'DITARIK_PENGADUAN'         => 'Ditariknya Pengaduan Pada Tindak Pidana Aduan',
            'RESTORATIF_JUSTICE'        => 'Tercapainya Penyelesaian Perkara Melalui Mekanisme Keadilan Restoratif',
            'BAYAR_DENDA_KATEGORI_II'   => 'Tersangka Membayar Maksimum Pidana Denda Atas Tindak Pidana Yang Hanya Diancam Dengan Pidana Denda Paling Banyak Kategori II',
            'BAYAR_DENDA_KATEGORI_IV'   => 'Tersangka Membayar Maksimum Pidana Denda Kategori IV Atas Tindak Pidana Yang Diancam Dengan Pidana Paling Lama 1 (satu) Tahun Atau Pidana Denda Paling Banyak Kategori III',
        ];

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

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

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', 'TERSANGKA')
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $document = null;

        $daftarBarangBukti = DaftarBarangBukti::where('accident_id', $accidentId)->get();

        $surat_penyitaan = collect();
        $officer = collect();

        return view('docs.surat-ketetapan-penghentian-penyidikan.create', compact(
            'accident',
            'accidentId',
            'suratPerintahPenyidikanDocuments',
            'laporanHasilGelarPerkaraDocuments',
            'suratPemberitahuanDimulainyaPenyidikanDocuments',
            'alasanPenghentianOptions',
            'prosecutors',
            'courts',
            'authorizedSignatories',
            'suspects',
            'document',
            'daftarBarangBukti',
            'surat_penyitaan',
            'officer'
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
                'surat_perintah_penyidikan_id' => 'nullable|uuid',
                'laporan_hasil_gelar_perkara_id' => 'nullable|uuid',
                'surat_pemberitahuan_dimulainya_penyidikan_id' => 'nullable|uuid',
                'document_number' => 'required|string|max:255',
                'document_date' => 'required|date',
                'effective_date' => 'required|date',
                'case_classification' => 'nullable|string|max:255',
                'prosecutor_id' => 'nullable|exists:prosecutors,id',
                'court_id' => 'nullable|exists:courts,id',
                'suspects' => 'required|array',
                'suspects.*' => 'exists:suspects,id',
                'nomor_serah_terima' => 'nullable|string|max:255',
                'tanggal_serah_terima' => 'nullable|date',
                'barang_bukti' => 'nullable|array',
                'jumlah_bb' => 'nullable|integer',
                'alasan_penghentian' => 'required|string|in:TIDAK_CUKUP_BUKTI,BUKAN_TINDAK_PIDANA,DIHENTIKAN_DEMI_HUKUM,PUTUSAN_PENGADILAN,KEDALUARSA,TERSANGKA_MENINGGAL,DITARIK_PENGADUAN,RESTORATIF_JUSTICE,BAYAR_DENDA_KATEGORI_II,BAYAR_DENDA_KATEGORI_IV',
                'menetapkan_alasan' => 'nullable|string',
                'signatory' => 'required|integer|exists:officers,id',
                // Restorative Justice fields - required only if alasan_penghentian is RESTORATIF_JUSTICE
                'rj_nomor_kesepakatan' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string|max:255',
                'rj_tanggal_kesepakatan' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|date',
                'rj_pihak_korban' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string',
                'rj_pihak_pelaku' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string',
                'rj_bentuk_ganti_rugi' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string',
                'rj_nilai_ganti_rugi' => 'nullable|string|max:255',
                'rj_keterangan_tambahan' => 'nullable|string',
                'rj_dokumen_pendukung.*' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            $signatoryOfficer = Officer::with(['rank', 'position', 'police'])->findOrFail($validated['signatory']);
            
            unset($validated['signatory']);
            
            if ($request->hasFile('rj_dokumen_pendukung')) {
                $uploadedFiles = [];
                foreach ($request->file('rj_dokumen_pendukung') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/skpp/restorative_justice', $filename, 'public');
                    $uploadedFiles[] = $path;
                }
                $validated['rj_dokumen_pendukung'] = $uploadedFiles;
            }

            $suspectsInput = $validated['suspects'];
            unset($validated['suspects']); // Remove before insert
            
            $validated['created_by'] = Auth::id();
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

            $document = SuratKetetapanPenghentianPenyidikanDocument::create($validated);

            if (!empty($suspectsInput)) {
                $document->suspect()->sync($suspectsInput);
            }

            $document->officers()->create([
                'sort' => 0,
                'register_number' => $signatoryOfficer->register_number,
                'first_title' => $signatoryOfficer->first_title,
                'first_name' => $signatoryOfficer->first_name,
                'last_name' => $signatoryOfficer->last_name,
                'last_title' => $signatoryOfficer->last_title,
                'rank' => $signatoryOfficer->rank ? [
                    'id' => $signatoryOfficer->rank->id,
                    'name' => $signatoryOfficer->rank->name,
                ] : null,
                'position' => $signatoryOfficer->position ? [
                    'id' => $signatoryOfficer->position->id,
                    'name' => $signatoryOfficer->position->name,
                ] : null,
                'headquarter_police' => $signatoryOfficer->police ? [
                    'id' => $signatoryOfficer->police->id,
                    'name' => $signatoryOfficer->police->name,
                ] : null,
                'status' => 'PRESENT',
                'class' => 'SIGNATORY',
                'flag' => 'INTERNAL',
                'insert_method' => 'MANUAL',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Ketetapan Penghentian Penyidikan berhasil disimpan',
                    'redirect' => route('view_produktivitas_accident', ['accident_id' => $validated['accident_id']])
                ]);
            }

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $validated['accident_id']])
                ->with('success', 'Surat Ketetapan Penghentian Penyidikan berhasil disimpan');

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
            Log::error('Error storing document: ' . $e->getMessage());
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
        $document = SuratKetetapanPenghentianPenyidikanDocument::with('accident')->findOrFail($id);
        
        if (!$document->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui');
        }

        $accident = $document->accident;
        $accidentId = $accident->id;

        $suratPerintahPenyidikanDocuments = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accident->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $laporanHasilGelarPerkaraDocuments = \App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument::where('accident_id', $accident->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPemberitahuanDimulainyaPenyidikanDocuments = \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accident->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $alasanPenghentianOptions = [
            'TIDAK_CUKUP_BUKTI'         => 'Tidak Terdapat Cukup Alat Bukti',
            'BUKAN_TINDAK_PIDANA'       => 'Peristiwa Tersebut Bukan Merupakan Tindak Pidana',
            'DIHENTIKAN_DEMI_HUKUM'     => 'Penyidikan Dihentikan Demi Hukum',
            'PUTUSAN_PENGADILAN'        => 'Terdapat Putusan Pengadilan Yang Telah Memperoleh Kekuatan Hukum Tetap Terhadap Tersangka Atas Perkara Yang Sama',
            'KEDALUARSA'                => 'Kedaluarsa',
            'TERSANGKA_MENINGGAL'       => 'Tersangka Meninggal Dunia',
            'DITARIK_PENGADUAN'         => 'Ditariknya Pengaduan Pada Tindak Pidana Aduan',
            'RESTORATIF_JUSTICE'        => 'Tercapainya Penyelesaian Perkara Melalui Mekanisme Keadilan Restoratif',
            'BAYAR_DENDA_KATEGORI_II'   => 'Tersangka Membayar Maksimum Pidana Denda Atas Tindak Pidana Yang Hanya Diancam Dengan Pidana Denda Paling Banyak Kategori II',
            'BAYAR_DENDA_KATEGORI_IV'   => 'Tersangka Membayar Maksimum Pidana Denda Kategori IV Atas Tindak Pidana Yang Diancam Dengan Pidana Paling Lama 1 (satu) Tahun Atau Pidana Denda Paling Banyak Kategori III',
        ];

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

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

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', 'TERSANGKA')
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $daftarBarangBukti = DaftarBarangBukti::where('accident_id', $accidentId)->get();

        $surat_penyitaan = collect();
        $officer = collect();

        return view('docs.surat-ketetapan-penghentian-penyidikan.edit', compact(
            'accident',
            'accidentId',
            'suratPerintahPenyidikanDocuments',
            'laporanHasilGelarPerkaraDocuments',
            'suratPemberitahuanDimulainyaPenyidikanDocuments',
            'alasanPenghentianOptions',
            'prosecutors',
            'courts',
            'authorizedSignatories',
            'suspects',
            'document',
            'daftarBarangBukti',
            'surat_penyitaan',
            'officer'
        ));
    }

    /**
     * Update the document
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $document = SuratKetetapanPenghentianPenyidikanDocument::findOrFail($id);
            
            if (!$document->isEditable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak dapat diedit karena sudah disetujui'
                ], 403);
            }

            $validated = $request->validate([
                'accident_id' => 'required|uuid|exists:accidents,id',
                'surat_perintah_penyidikan_id' => 'nullable|uuid',
                'laporan_hasil_gelar_perkara_id' => 'nullable|uuid',
                'surat_pemberitahuan_dimulainya_penyidikan_id' => 'nullable|uuid',
                'document_number' => 'required|string|max:255',
                'document_date' => 'required|date',
                'effective_date' => 'required|date',
                'case_classification' => 'nullable|string|max:255',
                'prosecutor_id' => 'nullable|exists:prosecutors,id',
                'court_id' => 'nullable|exists:courts,id',
                'suspects' => 'required|array',
                'suspects.*' => 'exists:suspects,id',
                'nomor_serah_terima' => 'nullable|string|max:255',
                'tanggal_serah_terima' => 'nullable|date',
                'barang_bukti' => 'nullable|array',
                'jumlah_bb' => 'nullable|integer',
                'alasan_penghentian' => 'required|string|in:TIDAK_CUKUP_BUKTI,BUKAN_TINDAK_PIDANA,DIHENTIKAN_DEMI_HUKUM,PUTUSAN_PENGADILAN,KEDALUARSA,TERSANGKA_MENINGGAL,DITARIK_PENGADUAN,RESTORATIF_JUSTICE,BAYAR_DENDA_KATEGORI_II,BAYAR_DENDA_KATEGORI_IV',
                'menetapkan_alasan' => 'nullable|string',
                'signatory' => 'required|integer|exists:officers,id',
                // Restorative Justice fields
                'rj_nomor_kesepakatan' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string|max:255',
                'rj_tanggal_kesepakatan' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|date',
                'rj_pihak_korban' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string',
                'rj_pihak_pelaku' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string',
                'rj_bentuk_ganti_rugi' => 'required_if:alasan_penghentian,RESTORATIF_JUSTICE|nullable|string',
                'rj_nilai_ganti_rugi' => 'nullable|string|max:255',
                'rj_keterangan_tambahan' => 'nullable|string',
                'rj_dokumen_pendukung.*' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            $signatoryOfficer = Officer::with(['rank', 'position', 'police'])->findOrFail($validated['signatory']);
            
            unset($validated['signatory']);
            
            // Handle file uploads for RJ documents
            if ($request->hasFile('rj_dokumen_pendukung')) {
                $uploadedFiles = $document->rj_dokumen_pendukung ?? [];
                foreach ($request->file('rj_dokumen_pendukung') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/skpp/restorative_justice', $filename, 'public');
                    $uploadedFiles[] = $path;
                }
                $validated['rj_dokumen_pendukung'] = $uploadedFiles;
            } else {
                unset($validated['rj_dokumen_pendukung']);
            }

            $suspectsInput = $validated['suspects'];
            unset($validated['suspects']);
            
            $validated['updated_by']         = Auth::id();
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

            $document->update($validated);

            if (!empty($suspectsInput)) {
                $document->suspect()->sync($suspectsInput);
            }

            $document->officers()->where('class', 'SIGNATORY')->delete();
            $document->officers()->create([
                'sort' => 0,
                'register_number' => $signatoryOfficer->register_number,
                'first_title' => $signatoryOfficer->first_title,
                'first_name' => $signatoryOfficer->first_name,
                'last_name' => $signatoryOfficer->last_name,
                'last_title' => $signatoryOfficer->last_title,
                'rank' => $signatoryOfficer->rank ? [
                    'id' => $signatoryOfficer->rank->id,
                    'name' => $signatoryOfficer->rank->name,
                ] : null,
                'position' => $signatoryOfficer->position ? [
                    'id' => $signatoryOfficer->position->id,
                    'name' => $signatoryOfficer->position->name,
                ] : null,
                'headquarter_police' => $signatoryOfficer->police ? [
                    'id' => $signatoryOfficer->police->id,
                    'name' => $signatoryOfficer->police->name,
                ] : null,
                'status' => 'PRESENT',
                'class' => 'SIGNATORY',
                'flag' => 'INTERNAL',
                'insert_method' => 'MANUAL',
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Ketetapan Penghentian Penyidikan berhasil diperbarui',
                    'redirect' => route('view_produktivitas_accident', ['accident_id' => $validated['accident_id']])
                ]);
            }

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $validated['accident_id']])
                ->with('success', 'Surat Ketetapan Penghentian Penyidikan berhasil diperbarui');

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
            Log::error('Error updating document: ' . $e->getMessage());
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
     * Display the specified document
     */
    public function show($id)
    {
        $document = SuratKetetapanPenghentianPenyidikanDocument::withRelated()->findOrFail($id);
        
        return view('docs.surat-ketetapan-penghentian-penyidikan.show', compact('document'));
    }

    /**
     * Remove the specified document
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $document = SuratKetetapanPenghentianPenyidikanDocument::findOrFail($id);
            
            if (!$document->isEditable()) {
                return redirect()->back()->with('error', 'Dokumen tidak dapat dihapus karena sudah disetujui');
            }

            $accidentId = $document->accident_id;
            $document->delete();

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Ketetapan Penghentian Penyidikan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting document: ' . $e->getMessage());
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

            $document = SuratKetetapanPenghentianPenyidikanDocument::findOrFail($id);
            
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
     * Download document as Word file
     */
    public function download($id)
    {
        $document = SuratKetetapanPenghentianPenyidikanDocument::with([
            'accident.polres.polda',
            'suratPerintahPenyidikan.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'laporanHasilGelarPerkara',
            'suratPemberitahuanDimulainyaPenyidikan',
            'status',
            'officers',
            'attachment',
            'suspect.gender',
            'suspect.job',
            'suspect.religion',
            'suspect.country',
            'suspect.province',
            'suspect.regency',
            'suspect.district',
            'suspect.village',
            'suspect.suratKetetapanTentangPenetapanTersangkaDocument',
            'prosecutor',
            'court'
        ])->findOrFail($id);

        $accident = $document->accident;
        
        $signatory = $document->officers->where('class', 'SIGNATORY')->first();

        $signatoryHeadText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name,
        ];

        $signatoryPositionId     = $signatory ? ($signatory->position['id'] ?? null) : null;
        $signatoryPositionDetail = $signatoryPositionId
            ? \App\Models\Lib\Position::with('positionCluster')->find($signatoryPositionId)
            : null;

        $signatoryPositionHeadText = [
            'NO_KAPOLRES'  => $signatoryPositionDetail?->positionCluster?->alias_name ?? '',
            'NO_DIRLANTAS' => $signatoryPositionDetail?->positionCluster?->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_ketetapan_penghentian_penyidikan.docx');

        if (isset($signatoryPositionDetail)) {
            if ($signatoryPositionDetail->position_cluster_id == '1') {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionHeadText', '');
            } elseif ($signatoryPositionDetail->position_cluster_id == '9') {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_DIRLANTAS']);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText['NO_DIRLANTAS']);
            } else {
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText['NO_KAPOLRES']);
            }
        } else {
            $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_KAPOLRES']);
            $templateProcessor->setValue('signatoryPositionHeadText', '');
        }

        $documentDate = Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber = $document->document_number;
        
        $accidentNumber = $accident->no_lp;
        $accidentDate = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');
        $reportDate = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y');
        $roadName = $accident->road_name;
        
        $daerahPolice = $accident->polres->polda;
        $daerahPoliceFullName = $daerahPolice->full_name;
        
        $resorPolice = $accident->polres;
        $resorPoliceAddress = $resorPolice->address . ', ' . $resorPolice->polres_zipcode;
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);
        $resorPoliceProvinceName = $resorPolice->polres_province;
        
        $documentLocation = $resorPoliceProvinceName;

        $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
        $signatoryRankName = $signatory->rank['name'] ?? '';
        $signatoryRegisterNumber = $signatory->register_number;

        $prosecutorName = $document->prosecutor ? $document->prosecutor->name : '';
        $courtName = $document->court ? $document->court->name : '';

        $suspect = $document->suspect->first();
        $suspectName = $suspect->name ?? '';
        $suspectIdentityNumber = $suspect->identity_number ?? '';
        $suspectNationality = $suspect->nationality ?? '';
        $suspectBirthPlace = $suspect->birth_place ?? '';
        $suspectBirthDate = (!empty($suspect->birth_date)) ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
        $suspectGenderName = $suspect->gender->name ?? '';
        $suspectJobName = $suspect->job->name ?? '';
        $suspectReligionName = $suspect->religion->name ?? '';

        $suspectVillageName = $suspect->village ? ', ' . $suspect->village->name : '';
        $suspectDistrictName = $suspect->district ? ', ' . $suspect->district->name : '';
        $suspectRegencyName = $suspect->regency ? ', ' . $suspect->regency->name : '';
        $suspectProvinceName = $suspect->province ? ', ' . $suspect->province->name : '';
        $suspectAddress = $suspect->address ?? '';
        $suspectFullAddress = $suspectAddress . $suspectVillageName . $suspectDistrictName . $suspectRegencyName . $suspectProvinceName;

        $spdpNumber = $document->suratPemberitahuanDimulainyaPenyidikan ? $document->suratPemberitahuanDimulainyaPenyidikan->document_number : '';
        $spdpDate = $document->suratPemberitahuanDimulainyaPenyidikan && $document->suratPemberitahuanDimulainyaPenyidikan->document_date ? Carbon::parse($document->suratPemberitahuanDimulainyaPenyidikan->document_date)->locale('id')->translatedFormat('d F Y') : '';

        $sTapTersangka = $suspect && $suspect->suratKetetapanTentangPenetapanTersangkaDocument->isNotEmpty() ? $suspect->suratKetetapanTentangPenetapanTersangkaDocument->first() : null;
        $sTapTersangkaNumber = $sTapTersangka ? $sTapTersangka->document_number : '';
        $sTapTersangkaDate = $sTapTersangka && $sTapTersangka->document_date ? Carbon::parse($sTapTersangka->document_date)->locale('id')->translatedFormat('d F Y') : '';

        $suratPerintahPenyidikan = $document->suratPerintahPenyidikan;
        $pasalList = '';
        $dugaanTindakPidanaList = '';
        
        if ($suratPerintahPenyidikan && $suratPerintahPenyidikan->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $laws = $suratPerintahPenyidikan->suratPerintahPenyidikanDocumentLaws;
            $pasalParts = [];
            $dugaanParts = [];

            foreach ($laws as $law) {
                $chapter = trim($law->constitution_chapter ?? '');
                $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));
                $description = $law->crimeConstitution ? $law->crimeConstitution->description : '';
                $verseText = $description;
                $verseNum = null;
                
                if (preg_match('/ayat\s*\(?(\d+)\)?/i', $chapter, $matches)) {
                    $verseNum = $matches[1];
                }

                if ($verseNum && $description) {
                    if (preg_match('/(?:^|<br[^>]*>|<p>|[\r\n]+)\s*\(' . $verseNum . '\)\s*(.*?)(?=(?:<br[^>]*>|<p>|[\r\n]+)\s*\(\d+\)|$)/is', $description, $descMatches)) {
                        $verseText = $descMatches[1];
                    }
                }

                $cleanText = strip_tags($verseText);
                $cleanText = preg_replace('/^\s*\(\d+\)\s*/', '', $cleanText);
                $cleanText = preg_replace('/\s+/', ' ', $cleanText);
                $cleanText = trim($cleanText);

                if (empty($cleanText) && $law->crimeType) {
                    $cleanText = $law->crimeType->name;
                }

                if (!empty($cleanText)) {
                    $dugaanParts[] = lcfirst($cleanText);
                }
            }
            
            $pasalList = implode(', ', $pasalParts);
            $dugaanTindakPidanaList = implode(' dan ', array_unique($dugaanParts));
        }
        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentLocation', $documentLocation);
       
        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('reportDate', $reportDate);
        $templateProcessor->setValue('roadName', $roadName);

        $templateProcessor->setValue('suspectName', $suspectName);
        $templateProcessor->setValue('suspectIdentityNumber', $suspectIdentityNumber);
        $templateProcessor->setValue('suspectNationality', $suspectNationality);
        $templateProcessor->setValue('suspectBirthPlace', $suspectBirthPlace);
        $templateProcessor->setValue('suspectBirthDate', $suspectBirthDate);
        $templateProcessor->setValue('suspectGenderName', $suspectGenderName);
        $templateProcessor->setValue('suspectJobName', $suspectJobName);
        $templateProcessor->setValue('suspectReligionName', $suspectReligionName);
        $templateProcessor->setValue('suspectFullAddress', $suspectFullAddress);
        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('courtName', $courtName);

        $suratPerintahPenyidikan = $document->suratPerintahPenyidikan;
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $suratPerintahPenyidikan ? $suratPerintahPenyidikan->document_number : '-');
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDocumentDate', $suratPerintahPenyidikan && $suratPerintahPenyidikan->document_date ? Carbon::parse($suratPerintahPenyidikan->document_date)->locale('id')->translatedFormat('d F Y') : '-');
        $laporanHasilGelarPerkara = $document->laporanHasilGelarPerkara;
        $templateProcessor->setValue('laporanHasilGelarPerkaraDocumentNumber', $laporanHasilGelarPerkara ? $laporanHasilGelarPerkara->document_number : '-');
        $templateProcessor->setValue('laporanHasilGelarPerkaraDocumentDate', $laporanHasilGelarPerkara && $laporanHasilGelarPerkara->document_date ? Carbon::parse($laporanHasilGelarPerkara->document_date)->locale('id')->translatedFormat('d F Y') : '-');
        
        $templateProcessor->setValue('spdpNumber', $spdpNumber);
        $templateProcessor->setValue('spdpDate', $spdpDate);
        $templateProcessor->setValue('suratPemberitahuanDimulainyaPenyidikanDocumentNumber', $spdpNumber);
        $templateProcessor->setValue('suratPemberitahuanDimulainyaPenyidikanDocumentDocumentDate', $spdpDate);

        $templateProcessor->setValue('suratKetetapanTentangPenetapanTersangkaDocumentNumber', $sTapTersangkaNumber);
        $templateProcessor->setValue('suratKetetapanTentangPenetapanTersangkaDocumentDocumentDate', $sTapTersangkaDate);
       
        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($daerahPoliceFullName));
       
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($resorPoliceFullName));
       
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $templateProcessor->setValue('caseClassification', $document->case_classification ?? '-');

        $templateProcessor->setValue('pasalList', $pasalList ?: '-');
        $templateProcessor->setValue('dugaanTindakPidana', $dugaanTindakPidanaList ?: '-');

        $alasanPenghentianMap = [
            'TIDAK_CUKUP_BUKTI'         => 'Tidak Terdapat Cukup Alat Bukti',
            'BUKAN_TINDAK_PIDANA'       => 'Peristiwa Tersebut Bukan Merupakan Tindak Pidana',
            'DIHENTIKAN_DEMI_HUKUM'     => 'Penyidikan Dihentikan Demi Hukum',
            'PUTUSAN_PENGADILAN'        => 'Terdapat Putusan Pengadilan Yang Telah Memperoleh Kekuatan Hukum Tetap Terhadap Tersangka Atas Perkara Yang Sama',
            'KEDALUARSA'                => 'Kedaluarsa',
            'TERSANGKA_MENINGGAL'       => 'Tersangka Meninggal Dunia',
            'DITARIK_PENGADUAN'         => 'Ditariknya Pengaduan Pada Tindak Pidana Aduan',
            'RESTORATIF_JUSTICE'        => 'Tercapainya Penyelesaian Perkara Melalui Mekanisme Keadilan Restoratif',
            'BAYAR_DENDA_KATEGORI_II'   => 'Tersangka Membayar Maksimum Pidana Denda Atas Tindak Pidana Yang Hanya Diancam Dengan Pidana Denda Paling Banyak Kategori II',
            'BAYAR_DENDA_KATEGORI_IV'   => 'Tersangka Membayar Maksimum Pidana Denda Kategori IV Atas Tindak Pidana Yang Diancam Dengan Pidana Paling Lama 1 (satu) Tahun Atau Pidana Denda Paling Banyak Kategori III',
            'LAINNYA'                   => 'Lainnya',
        ];

        $templateProcessor->setValue('alasanPenghentian', $alasanPenghentianMap[$document->alasan_penghentian] ?? $document->alasan_penghentian);
        $templateProcessor->setValue('effectiveDate', $document->effective_date ? Carbon::parse($document->effective_date)->locale('id')->translatedFormat('d F Y') : '-');

        if($document->alasan_penghentian == 'RESTORATIF_JUSTICE'){
            $templateProcessor->setValue('rjNomorKesepakatan', $document->rj_nomor_kesepakatan ?? '-');
            $templateProcessor->setValue('rjTanggalKesepakatan', $document->rj_tanggal_kesepakatan ? Carbon::parse($document->rj_tanggal_kesepakatan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('rjPihakKorban', $document->rj_pihak_korban ?? '-');
            $templateProcessor->setValue('rjPihakPelaku', $document->rj_pihak_pelaku ?? '-');
            $templateProcessor->setValue('rjBentukGantiRugi', $document->rj_bentuk_ganti_rugi ?? '-');
            $templateProcessor->setValue('rjNilaiGantiRugi', $document->rj_nilai_ganti_rugi ?? '-');
            $templateProcessor->setValue('rjKeteranganTambahan', $document->rj_keterangan_tambahan ?? '-');
        }

        $filename = 'generate/' . Str::uuid() . ' - Surat Ketetapan Penghentian Penyidikan - Resor ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    /**
     * Approve document
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $document = SuratKetetapanPenghentianPenyidikanDocument::findOrFail($id);
            
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

            $document->update([
                'status_id' => '5',
                'is_approved' => true,
                'approved_at' => Carbon::now(),
                'approved_by_user_id' => Auth::id(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestamps,
                'ip_addresses' => $ipAddresses,
                'released_at' => Carbon::now(),
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
}
