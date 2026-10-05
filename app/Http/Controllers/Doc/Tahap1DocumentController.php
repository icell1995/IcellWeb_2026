<?php

namespace App\Http\Controllers\Doc;

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
use App\Models\Lib\Gender;
use App\Models\Lib\Religion;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Education;
use App\Models\Lib\Job;
use App\Models\Lib\Nationality as Country;

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
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)->get()
            )->sortByDesc('created_at')->values();

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

        $prisons = \App\Models\Lib\Prison::where('is_active', true)->orderBy('name')->get();

        $authorizedOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        // Surat Perintah Penahanan yang sudah dibuat untuk accident ini
        $suratPerintahPenahananDocuments = collect(); // Mock untuk sementara karena tim lain belum selesai

        $genders = Gender::where('is_active', true)->get();
        $religions = Religion::where('is_active', true)->get();
        $maritalStatuses = MaritalStatus::where('is_active', true)->get();
        $educations = Education::where('is_active', true)->get();
        $jobs = Job::where('is_active', true)->get();
        $countries = \App\Models\Lib\Location::where('is_active', true)->where('class', 'COUNTRY')->get();

        
        $districts = $this->loadDistricts($accident);
        $extractedDistrict = '';
        if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $accident->road_name ?? '', $matches)) {
            $extractedDistrict = trim($matches[1]);
        }
        
        $defaultKodeWilayah = '';
        if ($extractedDistrict && !empty($districts)) {
            foreach ($districts as $d) {
                if (stripos($d['Nama'], $extractedDistrict) !== false) {
                    $defaultKodeWilayah = $d['KodePuskarda'] ?? '';
                    break;
                }
            }
        }

        return view('docs.tahap-1-pusiknas-document.create', compact(
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
            'authorizedOfficers',
            'suratPerintahPenahananDocuments',
            'genders',
            'religions',
            'maritalStatuses',
            'educations',
            'jobs',
            'countries'
        , 'districts', 'defaultKodeWilayah'));
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
                'surat_pemberitahuan_dimulainya_penyidikan_id' => 'required|uuid',
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
                'daftar_saksi' => 'nullable|array',
                'daftar_saksi.*.nama' => 'required|string|max:255',
                'daftar_saksi.*.tempat_lahir' => 'required|string|max:255',
                'daftar_saksi.*.kode_jenis_kelamin' => 'required|in:1,2',
                'daftar_saksi.*.alamat' => 'required|string',
                'daftar_barang_bukti' => 'nullable|array',
                'daftar_barang_bukti.*.nama' => 'required|string|max:255',
                'daftar_barang_bukti.*.jumlah' => 'required|numeric',
                'daftar_barang_bukti.*.satuan' => 'required|string|max:255',
                'daftar_barang_bukti.*.keterangan' => 'nullable|string',
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

            // Handle Saksi and Barang Bukti
            $daftarSaksi = $validated['daftar_saksi'] ?? [];
            unset($validated['daftar_saksi']);

            $daftarBarangBukti = $validated['daftar_barang_bukti'] ?? [];
            unset($validated['daftar_barang_bukti']);

            $validated['barang_bukti'] = $daftarBarangBukti;
            $validated['jumlah_bb'] = collect($daftarBarangBukti)->sum('jumlah');

            
            $validated['messages'] = [
                'uraian_singkat_perkara' => $request->input('uraianPerkara'),
                'lokasi_kejadian'        => $request->input('lokasiKejadian'),
                'kode_wilayah'           => $request->input('kodeWilayah'),
                'waktu_kejadian'         => $request->input('waktuKejadian'),
                'tahun_kejadian'         => intval($request->input('tahunKejadian')),
                'bulan_kejadian'         => intval($request->input('bulanKejadian')),
                'tanggal_kejadian'       => intval($request->input('tanggalKejadian')),
                'sumber'                 => 'PUSIKNAS_FORM',
            ];

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

            // Save Saksi
            \App\Models\Witness::where('accident_id', $validated['accident_id'])
                ->where('group', 'TAHAP_I')
                ->delete();

            foreach ($daftarSaksi as $saksi) {
                \App\Models\Witness::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id' => $validated['accident_id'],
                    'name' => $saksi['nama'],
                    'birth_place' => $saksi['tempat_lahir'],
                    'gender_id' => $saksi['kode_jenis_kelamin'],
                    'address' => $saksi['alamat'],
                    'flag' => 'SAKSI',
                    'group' => 'TAHAP_I',
                    'insert_method' => 'MANUAL',
                    'is_active' => true,
                ]);
            }

            // Save Barang Bukti Relational
            \App\Models\DaftarBarangBukti::where('accident_id', $validated['accident_id'])->delete();
            foreach ($daftarBarangBukti as $bb) {
                \App\Models\DaftarBarangBukti::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id' => $validated['accident_id'],
                    'nama_barang' => $bb['nama'] ?? '-',
                    'jumlah_barang' => intval($bb['jumlah'] ?? 0),
                ]);
            }

            // Save signatory
            $document->officers()->create([
                'register_number' => $signatoryOfficer->register_number,
                'first_title'     => $signatoryOfficer->first_title,
                'first_name'      => $signatoryOfficer->first_name,
                'last_name'       => $signatoryOfficer->last_name,
                'last_title'      => $signatoryOfficer->last_title,
                'rank_id'         => $signatoryOfficer->rank_id,
                'position_id'     => $signatoryOfficer->position_id,
                'phone_number'    => $signatoryOfficer->phone_number,
                'email'           => $signatoryOfficer->email,
                'police_id'       => $signatoryOfficer->police_id,
                'status'          => 'PRESENT',
                'class'           => 'SIGNATORY',
                'flag'            => 'INTERNAL',
                'insert_method'   => 'IMPORT',
                'sort'            => 0,
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
        $document = Tahap1Document::with(['accident', 'officers', 'suspects'])->findOrFail($id);
        
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
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)->get()
            )->sortByDesc('created_at')->values();

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

        $prisons = \App\Models\Lib\Prison::where('is_active', true)->orderBy('name')->get();

        $authorizedOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        // Surat Perintah Penahanan yang sudah dibuat untuk accident ini
        $suratPerintahPenahananDocuments = collect(); // Mock untuk sementara karena tim lain belum selesai

        
        $districts = $this->loadDistricts($accident);
        
        $genders = Gender::where('is_active', true)->get();
        $religions = Religion::where('is_active', true)->get();
        $maritalStatuses = MaritalStatus::where('is_active', true)->get();
        $educations = Education::where('is_active', true)->get();
        $jobs = Job::where('is_active', true)->get();
        $countries = \App\Models\Lib\Location::where('is_active', true)->where('class', 'COUNTRY')->get();

        $extractedDistrict = '';
        if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $accident->road_name ?? '', $matches)) {
            $extractedDistrict = trim($matches[1]);
        }
        
        $defaultKodeWilayah = $document->messages['kode_wilayah'] ?? '';
        if (!$defaultKodeWilayah && $extractedDistrict && !empty($districts)) {
            foreach ($districts as $d) {
                if (stripos($d['Nama'], $extractedDistrict) !== false) {
                    $defaultKodeWilayah = $d['KodePuskarda'] ?? '';
                    break;
                }
            }
        }

        return view('docs.tahap-1-pusiknas-document.edit', compact(
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
            'prisons',
            'authorizedOfficers',
            'suratPerintahPenahananDocuments'
        , 'districts', 'defaultKodeWilayah', 'genders', 'religions', 'maritalStatuses', 'educations', 'jobs', 'countries'));
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
                'surat_pemberitahuan_dimulainya_penyidikan_id' => 'required|uuid',
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
                'suspects.*' => 'exists:suspects,id',
                'daftar_saksi' => 'nullable|array',
                'daftar_saksi.*.nama' => 'required|string|max:255',
                'daftar_saksi.*.tempat_lahir' => 'required|string|max:255',
                'daftar_saksi.*.kode_jenis_kelamin' => 'required|in:1,2',
                'daftar_saksi.*.alamat' => 'required|string',
                'daftar_barang_bukti' => 'nullable|array',
                'daftar_barang_bukti.*.nama' => 'required|string|max:255',
                'daftar_barang_bukti.*.jumlah' => 'required|numeric',
                'daftar_barang_bukti.*.satuan' => 'required|string|max:255',
                'daftar_barang_bukti.*.keterangan' => 'nullable|string',
                'tembusan' => 'nullable|array',
            ]);

            $signatoryOfficer = Officer::with(['rank', 'position', 'police'])->findOrFail($validated['signatory']);
            unset($validated['signatory']);

            $suspectsInput = $validated['suspects'];
            unset($validated['suspects']);

            
            $messages = $document->messages ?? [];
            $messages['uraian_singkat_perkara'] = $request->input('uraianPerkara');
            $messages['lokasi_kejadian']        = $request->input('lokasiKejadian');
            $messages['kode_wilayah']           = $request->input('kodeWilayah');
            $messages['waktu_kejadian']         = $request->input('waktuKejadian');
            $messages['tahun_kejadian']         = intval($request->input('tahunKejadian'));
            $messages['bulan_kejadian']         = intval($request->input('bulanKejadian'));
            $messages['tanggal_kejadian']       = intval($request->input('tanggalKejadian'));
            $validated['messages'] = $messages;

            // Handle Saksi and Barang Bukti

            $daftarSaksi = $validated['daftar_saksi'] ?? [];
            unset($validated['daftar_saksi']);

            $daftarBarangBukti = $validated['daftar_barang_bukti'] ?? [];
            unset($validated['daftar_barang_bukti']);

            $validated['barang_bukti'] = $daftarBarangBukti;
            $validated['jumlah_bb'] = collect($daftarBarangBukti)->sum('jumlah');

            $validated['updated_by_user_id'] = Auth::id();

            // Append to Audit Trail
            // Gunakan getAttributeValue() agar membaca kolom JSON, bukan properti Eloquent $timestamps (boolean)
            $timestampsLog = is_array($document->getAttributeValue('timestamps')) ? $document->getAttributeValue('timestamps') : [];
            $timestampsLog[] = [
                'status_id' => $document->status_id,
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen diperbarui'
            ];
            $validated['timestamps'] = $timestampsLog;

            $ipAddresses = is_array($document->getAttributeValue('ip_addresses')) ? $document->getAttributeValue('ip_addresses') : [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }
            $validated['ip_addresses'] = $ipAddresses;

            // Tembusan defaults to empty if none provided
            if (empty($validated['tembusan'])) {
                $validated['tembusan'] = [];
            }

            $document->update($validated);

            if (isset($suspectsInput)) {
                $document->suspects()->sync($suspectsInput);
            }

            // Save Saksi
            \App\Models\Witness::where('accident_id', $document->accident_id)
                ->where('group', 'TAHAP_I')
                ->delete();

            foreach ($daftarSaksi as $saksi) {
                \App\Models\Witness::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id' => $document->accident_id,
                    'name' => $saksi['nama'],
                    'birth_place' => $saksi['tempat_lahir'],
                    'gender_id' => $saksi['kode_jenis_kelamin'],
                    'address' => $saksi['alamat'],
                    'flag' => 'SAKSI',
                    'group' => 'TAHAP_I',
                    'insert_method' => 'MANUAL',
                    'is_active' => true,
                ]);
            }

            // Save Barang Bukti Relational
            \App\Models\DaftarBarangBukti::where('accident_id', $document->accident_id)->delete();
            foreach ($daftarBarangBukti as $bb) {
                \App\Models\DaftarBarangBukti::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id' => $document->accident_id,
                    'nama_barang' => $bb['nama'] ?? '-',
                    'jumlah_barang' => intval($bb['jumlah'] ?? 0),
                ]);
            }

            // Update signatory
            $document->officers()->where('class', 'SIGNATORY')->delete();
            $document->officers()->create([
                'register_number' => $signatoryOfficer->register_number,
                'first_title'     => $signatoryOfficer->first_title,
                'first_name'      => $signatoryOfficer->first_name,
                'last_name'       => $signatoryOfficer->last_name,
                'last_title'      => $signatoryOfficer->last_title,
                'rank_id'         => $signatoryOfficer->rank_id,
                'position_id'     => $signatoryOfficer->position_id,
                'phone_number'    => $signatoryOfficer->phone_number,
                'email'           => $signatoryOfficer->email,
                'police_id'       => $signatoryOfficer->police_id,
                'status'          => 'PRESENT',
                'class'           => 'SIGNATORY',
                'flag'            => 'INTERNAL',
                'insert_method'   => 'IMPORT',
                'sort'            => 0,
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
    public function delete($id)
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
    public function submit($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $document = Tahap1Document::findOrFail($id);
            
            // Audit Trail - gunakan getAttributeValue() agar membaca kolom JSON, bukan properti Eloquent
            $timestampsLog = is_array($document->getAttributeValue('timestamps')) ? $document->getAttributeValue('timestamps') : [];
            $timestampsLog[] = [
                'status_id' => '3',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen diajukan untuk persetujuan'
            ];

            $ipAddresses = is_array($document->getAttributeValue('ip_addresses')) ? $document->getAttributeValue('ip_addresses') : [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }

            // Update status to waiting approval (status_id = 3 - MENUNGGU PERSETUJUAN)
            $document->update([
                'status_id' => '3',
                'submitted_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestampsLog,
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
    public function approve($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $document = Tahap1Document::findOrFail($id);
            
            // Audit Trail - gunakan getAttributeValue() agar membaca kolom JSON, bukan properti Eloquent
            $timestampsLog = is_array($document->getAttributeValue('timestamps')) ? $document->getAttributeValue('timestamps') : [];
            $timestampsLog[] = [
                'status_id' => '5',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen disetujui'
            ];

            $ipAddresses = is_array($document->getAttributeValue('ip_addresses')) ? $document->getAttributeValue('ip_addresses') : [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }

            // Update status to approved (status_id = 5 - DISETUJUI)
            $document->update([
                'status_id' => '5',
                'approved_at' => Carbon::now(),
                'released_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestampsLog,
                'ip_addresses' => $ipAddresses,
            ]);

            DB::commit();

            // Forward to Puskarda
            $this->forwardToPuskarda($document);

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

        $signatoryName = trim(implode(' ', array_filter([$signatory->first_title ?? '', $signatory->first_name ?? '', $signatory->last_name ?? '', $signatory->last_title ?? ''])));
        $signatoryName = $signatoryName ?: '-';
        $signatoryRankName = $signatory->rank->name ?? '';
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
        $laws = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocumentLaw::withRelated()
            ->where('surat_perintah_penyidikan_document_id', $sprindikId)
            ->get();


        $formattedLaws = $laws->map(function ($law) {
            $chapterInfo = $law->constitution_chapter ?? '';
            if (empty($chapterInfo) && $law->crimeConstitution) {
                $chapterInfo = $law->crimeConstitution->chapter ?? '';
            }

            $namaUU = $law->crimeConstitution ? $law->crimeConstitution->name : ($law->constitution ?? '');
            
            // Extract description and verse
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
            
            $undangUndang = trim($chapterInfo . ' ' . $namaUU);
            if ($undangUndang) {
                $kalimat .= 'sebagaimana dimaksud dalam ' . $undangUndang;
            }

            return trim($kalimat);
        })->filter()->values();

        // Jika ada lebih dari satu pasal, hubungkan dengan "dan" (bisa disesuaikan mjd "jo.")
        return implode("\n\ndan\n", $formattedLaws->toArray());
    }

    private function buildBpt1Payload(Tahap1Document $document)
    {
        $accident = $document->accident;
        $spdp = $document->suratPemberitahuanDimulainyaPenyidikan;

        // Kode wilayah
        $kodeWilayah = '31.74.49'; // Default
        if ($accident && $accident->polres && $accident->polres->emp_id) {
            $kodeWilayah = $accident->polres->emp_id;
        }

        $waktuKejadian = $accident && $accident->accident_date ? Carbon::parse($accident->accident_date) : null;

        // Suspects
        $suspects = [];
        foreach ($document->suspects as $s) {
            $suspects[] = [
                'nama' => $s->name,
                'tempat_lahir' => $s->birth_place,
                'tanggal_lahir' => $s->birth_date ? Carbon::parse($s->birth_date)->format('Y-m-d') : null,
                'kode_jenis_kelamin' => $s->gender_id == 1 ? '1' : ($s->gender_id == 2 ? '2' : null),
                'kebangsaan' => $s->nationality ?? 'Indonesia',
                'alamat' => $s->address,
                'kode_agama' => (string) $s->religion_id,
                'pekerjaan' => (string) $s->job_id,
                'pendidikan' => (string) $s->education_id,
                'nomor_identitas' => $s->identity_number
            ];
        }

        // Witnesses
        $witnesses = [];
        $saksiQuery = \App\Models\Witness::where('accident_id', $document->accident_id)->where('group', 'TAHAP_I')->get();
        foreach ($saksiQuery as $w) {
            $witnesses[] = [
                'nama' => $w->name,
                'tempat_lahir' => $w->birth_place,
                'kode_jenis_kelamin' => $w->gender_id == 1 ? '1' : ($w->gender_id == 2 ? '2' : null),
                'alamat' => $w->address
            ];
        }

        // Barang Bukti
        $barangBukti = [];
        $bbData = is_array($document->barang_bukti) ? $document->barang_bukti : [];
        foreach ($bbData as $bb) {
            $barangBukti[] = [
                'nama' => $bb['nama'] ?? '',
                'jumlah' => (int) ($bb['jumlah'] ?? 0),
                'satuan' => $bb['satuan'] ?? '',
                'keterangan' => $bb['keterangan'] ?? ''
            ];
        }

        return [
            'header' => [
                'kode_jenis_dokumen' => 'bpt1',
                'identitas_dokumen' => [
                    'nomor_surat_pengantar' => $document->document_number,
                    'tanggal_surat_pengantar' => $document->document_date ? $document->document_date->format('Y-m-d') : null,
                    'nomor_berkas_perkara' => $document->berkas_perkara_number,
                    'nomor_spdp' => $spdp ? $spdp->document_number : null
                ],
                'konten_dokumen' => [
                    'uraian_singkat_perkara' => $accident ? $accident->damage_lose_desc : '',
                    'tempat_kejadian_perkara' => [
                        'lokasi' => $accident ? $accident->road_name : '',
                        'kode_wilayah' => $kodeWilayah
                    ],
                    'waktu_kejadian' => $waktuKejadian ? "Kira-kira " . $waktuKejadian->translatedFormat('j F') . " antara jam " . $waktuKejadian->format('H:i') : null,
                    'tahun_kejadian' => $waktuKejadian ? (int) $waktuKejadian->format('Y') : null,
                    'bulan_kejadian' => $waktuKejadian ? (int) $waktuKejadian->format('n') : null,
                    'tanggal_kejadian' => $waktuKejadian ? (int) $waktuKejadian->format('j') : null,
                    'daftar_tersangka' => $suspects,
                    'daftar_saksi' => $witnesses,
                    'daftar_barang_bukti' => $barangBukti
                ]
            ]
        ];
    }

    private function forwardToPuskarda(Tahap1Document $document)
    {
        try {
            $payload = $this->buildBpt1Payload($document);
            $url = env('PUSKARDA_API_URL', 'https://10.34.17.4/puskarda-main/proses');
            
            \Illuminate\Support\Facades\Http::withoutVerifying()
                ->post($url, $payload);

            Log::info('Berhasil trigger forward BPT1 ke Puskarda for document_id: ' . $document->id);
        } catch (\Exception $e) {
            Log::error('Exception forwardToPuskarda: ' . $e->getMessage());
        }
    }

    public function getLocations(Request $request)
    {
        $parentId = $request->query('parent_id');
        $class = $request->query('class');

        $query = \App\Models\Lib\Location::where('is_active', true);

        if ($parentId) {
            $query->where('parent_id', $parentId);
        }

        if ($class) {
            $query->where('class', $class);
        }

        $locations = $query->orderBy('name')->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

        private function loadDistricts($accident)
    {
        $regencyName  = $accident->polres->polres_regency ?? '';
        $regencyCode  = '';
        $regenciesPath = base_path('master_seeder/regencies-new1.json');
        if ($regencyName && file_exists($regenciesPath)) {
            foreach (json_decode(file_get_contents($regenciesPath), true) ?? [] as $reg) {
                if (isset($reg['Nama']) && strtoupper($reg['Nama']) === strtoupper($regencyName)) {
                    $regencyCode = $reg['KodePuskarda'] ?? '';
                    break;
                }
            }
        }
        if (!$regencyCode) {
            $regencyId = $accident->polres->regency_id ?? '';
            if (strlen($regencyId) === 4) {
                $regencyCode = substr($regencyId, 0, 2) . '.' . substr($regencyId, 2, 2);
            }
        }
        $districtsPath = base_path('master_seeder/districts-new1.json');
        
        $districts = [];
        if (file_exists($districtsPath)) {
            $allDistricts = json_decode(file_get_contents($districtsPath), true) ?? [];
            if ($regencyCode) {
                $districts = array_filter($allDistricts, function ($d) use ($regencyCode) {
                    return isset($d['KodePuskarda']) && strpos($d['KodePuskarda'], $regencyCode . '.') === 0;
                });
            } else {
                $districts = $allDistricts;
            }
        }
        return $districts;
    }

}
