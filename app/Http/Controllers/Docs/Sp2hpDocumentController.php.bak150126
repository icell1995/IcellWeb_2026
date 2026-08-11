<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument\SuratPemberitahuanPerkembanganHasilPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanPerkembanganHasilPenyidikanDocument\SuratPemberitahuanPerkembanganHasilPenyidikanOfficer;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Accident;
use App\Models\Lib\Location;
use App\Models\Lib\IdentityType;
use App\Models\Lib\Gender;
use App\Models\Lib\Ethnic;
use App\Models\Lib\Job;
use App\Models\Lib\Religion;
use App\Models\Lib\Education;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Nationality;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\AccidentType;
use App\Models\Lib\VehicleType;
use App\Models\Lib\AccidentCause;
use App\Models\Lib\DrivingLicenseType;
use App\Models\ReportingPerson;
use App\Models\Officer;
use App\Models\Stg\DorsAccident;
use App\Models\Stg\DorsReportedPerson;
use App\Models\Stg\DorsEvidence;
use App\Models\Stg\DorsVictim;
use App\Traits\DocsOfficersTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\PeopleNameHelper;

class Sp2hpDocumentController extends Controller
{
    use DocsOfficersTraits;
    
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role_id != 1) {
                abort(403, 'Akses ditolak. Fitur SP2HP hanya tersedia untuk Administrator.');
            }
            return $next($request);
        });
    }
    
    /**
     * Get library data for form dropdowns
     */
    private function getLibData()
    {
        return [
            'identityTypes' => IdentityType::active()->orderBy('sort')->get(),
            'genders' => Gender::active()->orderBy('sort')->get(),
            'ethnics' => Ethnic::active()->orderBy('sort')->get(),
            'jobs' => Job::active()->orderBy('sort')->get(),
            'religions' => Religion::active()->orderBy('sort')->get(),
            'educations' => Education::active()->orderBy('sort')->get(),
            'maritalStatuses' => MaritalStatus::active()->orderBy('sort')->get(),
            'countries' => Location::active()->where('class', 'COUNTRY')->orderBy('sort')->get(),
            'nationalities' => Nationality::active()->orderBy('sort')->get(),
            'prosecutors' => Prosecutor::where('is_active', true)->orderBy('sort')->get(),
        ];
    }

    /**
     * Menampilkan list SP2HP dengan DataTables
     */
    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $accidentId = $request->accident_id;
            
            $data = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" 
                            data-original-title="Edit" class="edit btn btn-primary btn-sm editSp2hpReg">Edit</a>';
                    
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" 
                            data-original-title="Delete" class="btn btn-danger btn-sm deleteSp2hpReg">Delete</a>';
                    
                    if ($row->status === 'draft') {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" 
                                data-original-title="Submit" class="btn btn-success btn-sm submitSp2hpReg">Submit</a>';
                    }
                    
                    return $btn;
                })
                ->addColumn('nomor_surat_display', function($row) {
                    return $row->nomor_surat ?? '-';
                })
                ->addColumn('tersangka_nama_display', function($row) {
                    return $row->tersangka_nama ?? '-';
                })
                ->addColumn('status_badge', function($row) {
                    $badgeClass = 'badge-secondary';
                    if ($row->status === 'draft') {
                        $badgeClass = 'badge-warning';
                    } elseif ($row->status === 'submitted') {
                        $badgeClass = 'badge-info';
                    }
                    
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }
    }

    /**
     * Menampilkan form create/edit SP2HP (untuk AJAX)
     */
    public function show($id = null)
    {
        try {
            if ($id) {
                $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::with([
                    'accident', 
                    'reportingPerson', 
                    'createdByUser.officer.rank', 
                    'updatedByUser.officer.rank'
                ])->findOrFail($id);
                
                // Check if this is an AJAX request for JSON data
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json($sp2hp);
                }
                
                // Otherwise, return view for preview/print
                $accident = $sp2hp->accident;
                $libData = $this->getLibData();
                
                // Get all penerima SP2HP for this accident
                $allPenerima = ReportingPerson::where('accident_id', $accident->id)
                    ->where('class', 'SP2HP_PENERIMA')
                    ->where('is_active', true)
                    ->with(['identityType', 'gender'])
                    ->get();
                
                // Get all penyidik from officers table
                // Untuk A2-A7, ambil penyidik dari dokumen A1
                if (in_array($sp2hp->tipe_sp2hp, ['A2', 'A3', 'A4', 'A5', 'A6', 'A7'])) {
                    // Cari dokumen A1 untuk accident ini
                    $latestA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accident->id)
                        ->where('tipe_sp2hp', 'A1')
                        ->whereNull('deleted_at')
                        ->latest('created_at')
                        ->first();
                    
                    if ($latestA1) {
                        $allPenyidik = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::with(['rank', 'position', 'police'])
                            ->where('sp2hp_document_id', $latestA1->id)
                            ->where('class', 'INVESTIGATOR')
                            ->ordered()
                            ->get()
                            ->map(function($officer) {
                                return [
                                    'nama' => $officer->name,
                                    'pangkat' => $officer->rank ? $officer->rank->name : ($officer->rank_id ?? '-'),
                                    'nrp' => $officer->register_number,
                                    'telp' => $officer->phone_number,
                                    'unit' => $officer->police ? $officer->police->full_name : ($officer->police_id ?? '-'),
                                ];
                            })
                            ->toArray();
                    } else {
                        $allPenyidik = [];
                    }
                } else {
                    // Untuk A1, ambil langsung dari document ini
                    $allPenyidik = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::with(['rank', 'position', 'police'])
                        ->where('sp2hp_document_id', $sp2hp->id)
                        ->where('class', 'INVESTIGATOR')
                        ->ordered()
                        ->get()
                        ->map(function($officer) {
                            return [
                                'nama' => $officer->name,
                                'pangkat' => $officer->rank ? $officer->rank->name : ($officer->rank_id ?? '-'),
                                'nrp' => $officer->register_number,
                                'telp' => $officer->phone_number,
                                'unit' => $officer->police ? $officer->police->full_name : ($officer->police_id ?? '-'),
                            ];
                        })
                        ->toArray();
                }
                
                Log::info('SP2HP Show - Penyidik data', [
                    'sp2hp_id' => $sp2hp->id,
                    'penyidik_count' => count($allPenyidik),
                    'penyidik_data' => $allPenyidik
                ]);
                
                // Get signatory (pejabat penandatangan)
                $signatory = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::with(['rank', 'position', 'police'])
                    ->where('sp2hp_document_id', $sp2hp->id)
                    ->where('class', 'SIGNATORY')
                    ->first();
                
                $signatoryData = null;
                if ($signatory) {
                    $signatoryData = [
                        'nama' => $signatory->name,
                        'pangkat' => $signatory->rank ? $signatory->rank->name : ($signatory->rank_id ?? '-'),
                        'jabatan' => $signatory->position ? $signatory->position->name : ($signatory->position_id ?? '-'),
                        'nrp' => $signatory->register_number,
                        'telp' => $signatory->phone_number,
                        'email' => $signatory->email,
                        'unit' => $signatory->police ? $signatory->police->full_name : ($signatory->police_id ?? '-'),
                    ];
                }
                
                // Get tembusan (carbon copies) from type_specific_data
                $typeSpecificData = is_string($sp2hp->type_specific_data) 
                    ? json_decode($sp2hp->type_specific_data, true) 
                    : $sp2hp->type_specific_data;
                if (!is_array($typeSpecificData)) {
                    $typeSpecificData = [];
                }
                $tembusanList = $typeSpecificData['carbon_copies'] ?? [];
                
                // Get kendaraan detail
                $kendaraanDetail = null;
                if ($sp2hp->kendaraan_data && is_array($sp2hp->kendaraan_data)) {
                    $kendaraanDetail = [
                        'plat_nomor' => $sp2hp->kendaraan_data['plat_nomor'] ?? '-',
                        'jenis' => $sp2hp->kendaraan_data['jenis'] ?? '-',
                        'merk' => $sp2hp->kendaraan_data['merk'] ?? '-',
                        'warna' => $sp2hp->kendaraan_data['warna'] ?? '-',
                        'nomor_rangka' => $sp2hp->kendaraan_data['nomor_rangka'] ?? '-',
                        'nomor_mesin' => $sp2hp->kendaraan_data['nomor_mesin'] ?? '-',
                    ];
                }
                
                // Get rujukan based on tipe
                $rujukanSurat = null;
                if (in_array($sp2hp->tipe_sp2hp, ['A2', 'A3'])) {
                    // Untuk A2-A3: ambil dari A1
                    $rujukanSurat = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accident->id)
                        ->where('tipe_sp2hp', 'A1')
                        ->orderBy('created_at', 'desc')
                        ->first();
                } elseif (in_array($sp2hp->tipe_sp2hp, ['A5', 'A6', 'A7'])) {
                    // Untuk A5-A7: ambil dari A4
                    $rujukanSurat = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accident->id)
                        ->where('tipe_sp2hp', 'A4')
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
                
                return view('docs.surat-pemberitahuan-perkembangan-hasil-penyidikan.view', array_merge([
                    'sp2hp' => $sp2hp,
                    'accident' => $accident,
                    'accidentId' => $accident->id,
                    'allPenerima' => $allPenerima,
                    'allPenyidik' => $allPenyidik,
                    'signatory' => $signatoryData,
                    'tembusanList' => $tembusanList,
                    'kendaraanDetail' => $kendaraanDetail,
                    'rujukanSurat' => $rujukanSurat,
                ], $libData));
            }
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(null);
            }
            
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
            
            return redirect()->back()->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Download SP2HP document as Word
     */
    public function download($id)
    {
        try {
            // Get SP2HP document with relations
            $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::with([
                'accident.polres.polda',
                'accident.suspects',
                'reportingPerson',
                'createdByUser'
            ])->findOrFail($id);
            
            $accident = $sp2hp->accident;
            
            // Get penyidik data
            // Untuk A2-A7, ambil dari A1
            if (in_array($sp2hp->tipe_sp2hp, ['A2', 'A3', 'A4', 'A5', 'A6', 'A7'])) {
                $latestA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accident->id)
                    ->where('tipe_sp2hp', 'A1')
                    ->whereNull('deleted_at')
                    ->latest('created_at')
                    ->first();
                
                if ($latestA1) {
                    $allPenyidik = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $latestA1->id)
                        ->where('class', 'INVESTIGATOR')
                        ->ordered()
                        ->get();
                } else {
                    return redirect()->back()->with('error', 'Data A1 tidak ditemukan untuk mengambil data penyidik');
                }
            } else {
                // Untuk A1, ambil langsung
                $allPenyidik = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hp->id)
                    ->where('class', 'INVESTIGATOR')
                    ->ordered()
                    ->get();
            }
            
            // Get signatory (pejabat penandatangan) - ambil dari SP2HP document ini sendiri
            $signatory = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hp->id)
                ->where('class', 'SIGNATORY')
                ->first();
            
            if (!$signatory) {
                // Fallback ke penyidik pertama jika signatory tidak ada
                $signatory = $allPenyidik->first();
            }
            
            if (!$signatory) {
                return redirect()->back()->with('error', 'Data pejabat penandatangan tidak ditemukan untuk dokumen ini');
            }
            
            // Generate QR Code
            $tempQrCodePath = storage_path('images/qrcode-sp2hp-' . $sp2hp->id . '.png');
            QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->merge(public_path('images/logo2x.png'), .2, true)
                ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=' . $sp2hp->id, $tempQrCodePath);
            
            // Prepare template processor
            $templatePath = 'word-template/sp2hp_' . strtolower($sp2hp->tipe_sp2hp) . '.docx';
            
            // Check if template exists, fallback to generic template
            if (!file_exists($templatePath)) {
                $templatePath = 'word-template/sp2hp_generic.docx';
            }
            
            if (!file_exists($templatePath)) {
                return redirect()->back()->with('error', 'Template dokumen tidak ditemukan: ' . $templatePath);
            }
            
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            // Basic document info
            $documentDate = Carbon::parse($sp2hp->tanggal_surat)->locale('id')->translatedFormat('d F Y');
            $documentNumber = $sp2hp->nomor_surat ?? '-';
            $documentLocation = ucwords(strtolower($sp2hp->tempat_surat ?? $accident->polres->polres_province ?? ''));
            
            // Accident info
            $accidentNumber = $accident->no_lp ?? '-';
            $accidentDate = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-';
            $reportDate = $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-';
            
            // Police unit info
            $workUnitName = '';
            if (!empty($accident->police)) {
                if ($accident->police->class == 'DAERAH') {
                    $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
                } else if ($accident->police->class == 'RESOR') {
                    $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
                }
            }
            
            $resorPolice = $accident->polres;
            $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);
            $resorPoliceAddress = $resorPolice->address . ', ' . $resorPolice->polres_zipcode;
            
            // Signatory info
            $signatoryName = $signatory->name ?? '-';
            $signatoryRankName = strtoupper($signatory->rank_id ?? '-');
            $signatoryRegisterNumber = $signatory->register_number ?? '-';
            
            // Penerima info
            $penerima = $sp2hp->reportingPerson;
            $penerimaNama = $penerima->name ?? '-';
            $penerimaAlamat = $penerima->address ?? '-';
            
            // Pelapor info
            $pelaporNama = $sp2hp->pelapor_nama ?? '-';
            $pelaporAlamat = $sp2hp->pelapor_alamat ?? '-';
            
            // Type specific data
            $typeSpecificData = is_string($sp2hp->type_specific_data) 
                ? json_decode($sp2hp->type_specific_data, true) 
                : $sp2hp->type_specific_data;
            
            if (!is_array($typeSpecificData)) {
                $typeSpecificData = [];
            }
            
            // Prepare penyidik table
            $blockPenyidik = [];
            $no = 1;
            foreach ($allPenyidik as $penyidik) {
                $blockPenyidik[] = [
                    'penyidik_no' => $no++,
                    'penyidik_nama' => $penyidik->name ?? '-',
                    'penyidik_pangkat' => $penyidik->rank_id ?? '-',
                    'penyidik_nrp' => $penyidik->register_number ?? '-',
                    'penyidik_telp' => $penyidik->phone_number ?? '-',
                    'penyidik_unit' => $penyidik->police_id ?? '-',
                ];
            }
            
            // Prepare tersangka table (for A4-A7)
            $blockTersangka = [];
            if (in_array($sp2hp->tipe_sp2hp, ['A4', 'A5', 'A6', 'A7']) && $accident->suspects) {
                $no = 1;
                foreach ($accident->suspects as $suspect) {
                    $blockTersangka[] = [
                        'tersangka_no' => $no++,
                        'tersangka_nama' => $suspect->name ?? '-',
                        'tersangka_nik' => $suspect->identity_number ?? '-',
                        'tersangka_tempat_lahir' => $suspect->birth_place ?? '-',
                        'tersangka_tanggal_lahir' => $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-',
                        'tersangka_alamat' => $suspect->address ?? '-',
                    ];
                }
            }
            
            // Set QR Code
            $templateProcessor->setImageValue('QRCodeImage', [
                'path' => $tempQrCodePath,
                'width' => 111,
                'height' => 111,
            ]);
            
            // Set basic values
            $templateProcessor->setValue('documentDate', $documentDate);
            $templateProcessor->setValue('documentNumber', $documentNumber);
            $templateProcessor->setValue('documentLocation', $documentLocation);
            $templateProcessor->setValue('accidentNumber', $accidentNumber);
            $templateProcessor->setValue('accidentDate', $accidentDate);
            $templateProcessor->setValue('reportDate', $reportDate);
            $templateProcessor->setValue('workUnitName', $workUnitName);
            $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
            $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
            
            // Set penerima & pelapor
            $templateProcessor->setValue('penerimaNama', $penerimaNama);
            $templateProcessor->setValue('penerimaAlamat', $penerimaAlamat);
            $templateProcessor->setValue('pelaporNama', $pelaporNama);
            $templateProcessor->setValue('pelaporAlamat', $pelaporAlamat);
            
            // Set signatory
            $templateProcessor->setValue('signatoryName', $signatoryName);
            $templateProcessor->setValue('signatoryRankName', $signatoryRankName);
            $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);
            
            // Set pasal and barang bukti
            $templateProcessor->setValue('pasalDiduga', $sp2hp->pasal_diduga ?? '-');
            $templateProcessor->setValue('barangBukti', $sp2hp->barang_bukti ?? '-');
            $templateProcessor->setValue('catatan', $sp2hp->catatan ?? '-');
            
            // Set tipe-specific data
            foreach ($typeSpecificData as $key => $value) {
                $templateProcessor->setValue($key, $value ?? '-');
            }
            
            // Clone tables
            if (count($blockPenyidik) > 0) {
                $templateProcessor->cloneRowAndSetValues('penyidik_no', $blockPenyidik);
            }
            
            if (count($blockTersangka) > 0) {
                $templateProcessor->cloneRowAndSetValues('tersangka_no', $blockTersangka);
            }
            
            // Save and download
            $filename = 'generate/' . $sp2hp->id . ' - SP2HP ' . $sp2hp->tipe_sp2hp . ' - ' . $accident->polres->full_name;
            $templateProcessor->saveAs($filename . '.docx');
            
            return response()->download($filename . '.docx')->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error downloading SP2HP: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan SP2HP (Create/Update)
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Debug: Log semua data yang diterima
            Log::info('=== SP2HP STORE REQUEST ===');
            Log::info('Penerima Array: ' . json_encode($request->penerima));
            Log::info('Penyidik Array: ' . json_encode($request->penyidik));
            Log::info('Pelapor Nama: ' . $request->pelapor_nama);
            Log::info('Pelapor Alamat: ' . $request->pelapor_alamat);
            Log::info('===========================');;
            
            $validated = $request->validate([
                'accident_id' => 'required|uuid|exists:accidents,id',
                'nomor_lp' => 'nullable|string|max:255',
                'tanggal_lp' => 'nullable|string',
                'nomor_surat' => 'nullable|string|max:255',
                'tanggal_surat' => 'nullable|date_format:d-m-Y',
                'tempat_surat' => 'nullable|string|max:255',
                'tipe_sp2hp' => 'nullable|string|max:10',
                'tingkat_kasus' => 'nullable|string|in:RINGAN,SEDANG,BERAT',
                
                // Data Penerima SP2HP (array - multiple recipients)
                'penerima' => 'nullable|array',
                'penerima.*.jenis_identitas' => 'nullable|string',
                'penerima.*.nomor_identitas' => 'nullable|string|max:255',
                'penerima.*.nama' => 'required|string|max:255',
                'penerima.*.nama_alias' => 'nullable|string|max:255',
                'penerima.*.jenis_kelamin' => 'nullable|string',
                'penerima.*.tempat_lahir' => 'nullable|string|max:255',
                'penerima.*.tanggal_lahir' => 'nullable|string',
                'penerima.*.nama_ayah' => 'nullable|string|max:255',
                'penerima.*.nama_ibu' => 'nullable|string|max:255',
                'penerima.*.kewarganegaraan' => 'nullable|string',
                'penerima.*.suku' => 'nullable|string',
                'penerima.*.pekerjaan' => 'nullable|string',
                'penerima.*.agama' => 'nullable|string',
                'penerima.*.pendidikan' => 'nullable|string',
                'penerima.*.status_perkawinan' => 'nullable|string',
                'penerima.*.nomor_telepon' => 'nullable|max:255',
                'penerima.*.email' => 'nullable|email|max:255',
                'penerima.*.negara' => 'nullable|string',
                'penerima.*.provinsi' => 'nullable|string',
                'penerima.*.kota' => 'nullable|string',
                'penerima.*.kecamatan' => 'nullable|string',
                'penerima.*.kelurahan' => 'nullable|string',
                'penerima.*.alamat' => 'nullable|string',
                
                // Data Pelapor (will remain in SP2HP table)
                'pelapor_nama' => 'nullable|string|max:255',
                'pelapor_alamat' => 'nullable|string',
                
                // Data Penyidik (array - multiple investigators)
                'penyidik' => 'nullable|array',
                'penyidik.*.nrp' => 'nullable|string|max:20',
                'penyidik.*.pangkat' => 'nullable|string|max:100',
                'penyidik.*.nama' => 'nullable|string|max:255',
                'penyidik.*.telp' => 'nullable|string|max:20',
                'penyidik.*.unit' => 'nullable|string|max:100',
                
                'pasal_diduga' => 'nullable|string',
                'barang_bukti' => 'nullable|array',
                'barang_bukti.*' => 'nullable|string',
                'barang_bukti_lainnya' => 'nullable|string|max:255',
                'catatan' => 'nullable|string',
                'type_specific_data' => 'nullable|array',
                
                // Tembusan (Carbon Copies)
                'carbonCopies' => 'nullable|array',
                'carbonCopies.*' => 'nullable|string|max:255',
                
                // A2 Fields - Belum Dapat Ditingkatkan ke Penyidikan
                'a2_rujukan_a1' => 'nullable|string|max:255',
                'a2_fakta_lidik' => 'nullable|string',
                'a2_alasan' => 'nullable|string',
                
                // A3 Fields - Naik ke Penyidikan
                'a3_rujukan_a1' => 'nullable|string|max:255',
                'a3_tanggal_a1' => 'nullable|string',
                'a3_sprin_sidik' => 'nullable|string|max:255',
                'a3_tanggal_sprin' => 'nullable|string',
                'a3_nomor_spdp' => 'nullable|string|max:255',
                'a3_tanggal_spdp' => 'nullable|string',
                'a3_pasal_diduga' => 'nullable|string',
                
                // A4 Tindakan Yang Telah Dilakukan (checkbox + keterangan)
                'berkas' => 'nullable|array',
                'berkas.*' => 'nullable|string',
                'berkas_keterangan' => 'nullable|array',
                'berkas_keterangan.*' => 'nullable|string',
                'a4_hambatan' => 'nullable|string',
                'a4_rencana' => 'nullable|string',
                
                // A5 Fields - Penghentian Penyidikan (SP3)
                'a5_sprin_sidik' => 'nullable|string|max:255',
                'a5_sp2hp_terakhir' => 'nullable|string|max:255',
                'a5_alasan_sp3' => 'nullable|string|max:255',
                'a5_keterangan_sp3' => 'nullable|string',
                
                // A6 Fields - Pelimpahan Berkas Perkara Tahap 1
                'a6_sp2hp_terakhir' => 'nullable|string|max:255',
                'a6_nama_tersangka' => 'nullable|string|max:255',
                'a6_nomor_kirim_berkas' => 'nullable|string|max:255',
                'a6_tanggal_kirim' => 'nullable|string',
                'a6_tujuan_kejaksaan' => 'nullable|string|max:255',
                
                // A7 Fields - Pelimpahan Berkas Perkara Tahap 2
                'a7_nama_tersangka' => 'nullable|string|max:255',
                'a7_rujukan_tahap1' => 'nullable|string|max:255',
                'a7_nomor_p21' => 'nullable|string|max:255',
                'a7_tanggal_p21' => 'nullable|string',
                'a7_nomor_kirim_tahap2' => 'nullable|string|max:255',
                'a7_tanggal_serah_tahap2' => 'nullable|string',
                'a7_tujuan_kejaksaan' => 'nullable|string|max:255',
            ]);

            // Convert date formats dari d-m-Y ke Y-m-d
            if (isset($validated['tanggal_surat']) && $validated['tanggal_surat']) {
                try {
                    $validated['tanggal_surat'] = Carbon::createFromFormat('d-m-Y', $validated['tanggal_surat'])->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::warning('Failed to parse tanggal_surat: ' . $validated['tanggal_surat'] . ' - ' . $e->getMessage());
                    $validated['tanggal_surat'] = null;
                }
            }
            
            if (isset($validated['tanggal_lp']) && $validated['tanggal_lp']) {
                try {
                    $validated['tanggal_lp'] = Carbon::createFromFormat('d-m-Y', $validated['tanggal_lp'])->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::warning('Failed to parse tanggal_lp: ' . $validated['tanggal_lp'] . ' - ' . $e->getMessage());
                    $validated['tanggal_lp'] = null;
                }
            }

            // Save Multiple Penerima SP2HP to reporting_persons table
            $reportingPersonIds = [];
            if ($request->has('penerima') && is_array($request->penerima)) {
                foreach ($request->penerima as $penerimaData) {
                    if (!empty($penerimaData['nama'])) {
                        // Cek apakah data penerima sudah ada di database
                        $reportingPerson = null;
                        
                        // Prioritas 1: Cek berdasarkan nomor identitas (lebih unik)
                        if (!empty($penerimaData['nomor_identitas'])) {
                            $reportingPerson = ReportingPerson::where('identity_number', $penerimaData['nomor_identitas'])
                                ->where('class', 'SP2HP_PENERIMA')
                                ->first();
                        }
                        
                        // Prioritas 2: Cek berdasarkan nama + accident_id (jika nomor identitas tidak ada/tidak ketemu)
                        if (!$reportingPerson) {
                            $reportingPerson = ReportingPerson::where('name', $penerimaData['nama'])
                                ->where('accident_id', $request->accident_id)
                                ->where('class', 'SP2HP_PENERIMA')
                                ->first();
                        }
                        
                        // Data yang akan disimpan/diupdate
                        $reportingPersonData = [
                            'accident_id' => $request->accident_id,
                            'identity_type_id' => $penerimaData['jenis_identitas'] ?? null,
                            'identity_number' => $penerimaData['nomor_identitas'] ?? null,
                            'name' => $penerimaData['nama'],
                            'alias_name' => $penerimaData['nama_alias'] ?? null,
                            'gender_id' => $penerimaData['jenis_kelamin'] ?? null,
                            'birth_place' => $penerimaData['tempat_lahir'] ?? null,
                            'birth_date' => null,
                            'father_name' => $penerimaData['nama_ayah'] ?? null,
                            'is_unknown_father' => false,
                            'mother_name' => $penerimaData['nama_ibu'] ?? null,
                            'is_unknown_mother' => false,
                            'nationality_id' => $penerimaData['kewarganegaraan'] ?? null,
                            'ethnic_id' => $penerimaData['suku'] ?? null,
                            'job_id' => $penerimaData['pekerjaan'] ?? null,
                            'religion_id' => $penerimaData['agama'] ?? null,
                            'education_id' => $penerimaData['pendidikan'] ?? null,
                            'marital_status_id' => $penerimaData['status_perkawinan'] ?? null,
                            'phone_number' => $penerimaData['nomor_telepon'] ?? null,
                            'is_exists_phone_number' => !empty($penerimaData['nomor_telepon']),
                            'is_available_phone_number' => !empty($penerimaData['nomor_telepon']),
                            'email' => $penerimaData['email'] ?? null,
                            'is_exists_email' => !empty($penerimaData['email']),
                            'is_available_email' => !empty($penerimaData['email']),
                            'country_id' => $penerimaData['negara'] ?? null,
                            'province_id' => $penerimaData['provinsi'] ?? null,
                            'regency_id' => $penerimaData['kota'] ?? null,
                            'district_id' => $penerimaData['kecamatan'] ?? null,
                            'village_id' => $penerimaData['kelurahan'] ?? null,
                            'address' => $penerimaData['alamat'] ?? null,
                            'class' => 'SP2HP_PENERIMA',
                            'status' => 'active',
                            'is_active' => true,
                        ];
                        
                        // Convert birth_date format if exists
                        if (!empty($penerimaData['tanggal_lahir'])) {
                            try {
                                $reportingPersonData['birth_date'] = Carbon::createFromFormat('d-m-Y', $penerimaData['tanggal_lahir'])->format('Y-m-d');
                            } catch (\Exception $e) {
                                Log::warning('Failed to parse birth_date: ' . $penerimaData['tanggal_lahir'] . ' - ' . $e->getMessage());
                                $reportingPersonData['birth_date'] = null;
                            }
                        }
                        
                        if ($reportingPerson) {
                            // Update data yang sudah ada
                            $reportingPerson->update($reportingPersonData);
                            Log::info('Updated existing ReportingPerson: ' . $reportingPerson->name . ' (ID: ' . $reportingPerson->id . ')');
                        } else {
                            // Buat data baru
                            $reportingPerson = ReportingPerson::create($reportingPersonData);
                            Log::info('Created new ReportingPerson: ' . $reportingPerson->name . ' (ID: ' . $reportingPerson->id . ')');
                        }
                        
                        $reportingPersonIds[] = $reportingPerson->id;
                    }
                }
            }
            
            // Use first penerima as primary reporting_person_id
            $reportingPersonId = !empty($reportingPersonIds) ? $reportingPersonIds[0] : null;

            // Prepare SP2HP data (remove penerima and penyidik arrays as they need special handling)
            $sp2hpData = collect($validated)->reject(function ($value, $key) {
                return in_array($key, [
                    'penerima', 'penyidik', 'berkas', 'berkas_keterangan', 
                    'barang_bukti', 'barang_bukti_lainnya', 'carbonCopies',
                    'a2_rujukan_a1', 'a2_fakta_lidik', 'a2_alasan',
                    'a3_rujukan_a1', 'a3_tanggal_a1', 'a3_sprin_sidik', 'a3_tanggal_sprin', 'a3_nomor_spdp', 'a3_tanggal_spdp', 'a3_pasal_diduga',
                    'a4_hambatan', 'a4_rencana',
                    'a5_sprin_sidik', 'a5_sp2hp_terakhir', 'a5_alasan_sp3', 'a5_keterangan_sp3',
                    'a6_sp2hp_terakhir', 'a6_nama_tersangka', 'a6_nomor_kirim_berkas', 'a6_tanggal_kirim', 'a6_tujuan_kejaksaan',
                    'a7_nama_tersangka', 'a7_rujukan_tahap1', 'a7_nomor_p21', 'a7_tanggal_p21', 'a7_nomor_kirim_tahap2', 'a7_tanggal_serah_tahap2', 'a7_tujuan_kejaksaan'
                ]);
            })->toArray();
            
            // Process barang bukti (checkbox array to comma-separated string)
            $barangBuktiString = '';
            if ($request->has('barang_bukti') && is_array($request->barang_bukti)) {
                $barangBuktiNames = [];
                $barangBuktiMap = [
                    '1' => 'Kendaraan',
                    '2' => 'KTP',
                    '3' => 'SIM',
                    '4' => 'STNK'
                ];
                
                foreach ($request->barang_bukti as $bbId) {
                    if (isset($barangBuktiMap[$bbId])) {
                        $barangBuktiNames[] = $barangBuktiMap[$bbId];
                    }
                }
                
                // Add lainnya if exists
                if ($request->filled('barang_bukti_lainnya')) {
                    $barangBuktiNames[] = $request->barang_bukti_lainnya;
                }
                
                $barangBuktiString = implode(', ', $barangBuktiNames);
            } elseif ($request->filled('barang_bukti_lainnya')) {
                $barangBuktiString = $request->barang_bukti_lainnya;
            }
            
            $sp2hpData['barang_bukti'] = $barangBuktiString;
            
            // Initialize type_specific_data if not exists
            if (!isset($sp2hpData['type_specific_data'])) {
                $sp2hpData['type_specific_data'] = [];
            }
            
            // Process Tembusan (Carbon Copies)
            if ($request->has('carbonCopies') && is_array($request->carbonCopies)) {
                // Filter empty values
                $carbonCopiesFiltered = array_filter($request->carbonCopies, function($value) {
                    return !empty(trim($value));
                });
                
                // Re-index array and save
                $sp2hpData['type_specific_data']['carbon_copies'] = array_values($carbonCopiesFiltered);
                
                Log::info('Carbon copies saved', ['carbon_copies' => $sp2hpData['type_specific_data']['carbon_copies']]);
            }
            
            // Process A2 data into type_specific_data
            if ($request->tipe_sp2hp == 'A2') {
                $sp2hpData['type_specific_data']['a2_rujukan_a1'] = $request->a2_rujukan_a1;
                $sp2hpData['type_specific_data']['a2_fakta_lidik'] = $request->a2_fakta_lidik;
                $sp2hpData['type_specific_data']['a2_alasan'] = $request->a2_alasan;
            }
            
            // Process A3 data into type_specific_data
            if ($request->tipe_sp2hp == 'A3') {
                $sp2hpData['type_specific_data']['a3_rujukan_a1'] = $request->a3_rujukan_a1;
                $sp2hpData['type_specific_data']['a3_tanggal_a1'] = $request->a3_tanggal_a1;
                $sp2hpData['type_specific_data']['a3_sprin_sidik'] = $request->a3_sprin_sidik;
                $sp2hpData['type_specific_data']['a3_tanggal_sprin'] = $request->a3_tanggal_sprin;
                $sp2hpData['type_specific_data']['a3_nomor_spdp'] = $request->a3_nomor_spdp;
                $sp2hpData['type_specific_data']['a3_tanggal_spdp'] = $request->a3_tanggal_spdp;
                $sp2hpData['type_specific_data']['a3_pasal_diduga'] = $request->a3_pasal_diduga;
            }
            
            // Process A4 data into type_specific_data (hambatan & rencana)
            if ($request->tipe_sp2hp == 'A4') {
                $sp2hpData['type_specific_data']['a4_hambatan'] = $request->a4_hambatan;
                $sp2hpData['type_specific_data']['a4_rencana'] = $request->a4_rencana;
                
                // Save data tersangka A4 untuk digunakan di A5, A6, A7
                if ($request->filled('tersangka_nama')) {
                    $sp2hpData['type_specific_data']['tersangka_nama'] = $request->tersangka_nama;
                    $sp2hpData['type_specific_data']['tersangka_nik'] = $request->tersangka_nik;
                    $sp2hpData['type_specific_data']['tersangka_tempat_lahir'] = $request->tersangka_tempat_lahir;
                    $sp2hpData['type_specific_data']['tersangka_tanggal_lahir'] = $request->tersangka_tanggal_lahir;
                    $sp2hpData['type_specific_data']['tersangka_umur'] = $request->tersangka_umur;
                    $sp2hpData['type_specific_data']['tersangka_kebangsaan'] = $request->tersangka_kebangsaan;
                    $sp2hpData['type_specific_data']['tersangka_pekerjaan'] = $request->tersangka_pekerjaan;
                    $sp2hpData['type_specific_data']['tersangka_alamat'] = $request->tersangka_alamat;
                }
            }
            
            // Process A5 data into type_specific_data
            if ($request->tipe_sp2hp == 'A5') {
                $sp2hpData['type_specific_data']['a5_sprin_sidik'] = $request->a5_sprin_sidik;
                $sp2hpData['type_specific_data']['a5_sp2hp_terakhir'] = $request->a5_sp2hp_terakhir;
                $sp2hpData['type_specific_data']['a5_alasan_sp3'] = $request->a5_alasan_sp3;
                $sp2hpData['type_specific_data']['a5_keterangan_sp3'] = $request->a5_keterangan_sp3;
            }
            
            // Process A6 data into type_specific_data
            if ($request->tipe_sp2hp == 'A6') {
                $sp2hpData['type_specific_data']['a6_sp2hp_terakhir'] = $request->a6_sp2hp_terakhir;
                $sp2hpData['type_specific_data']['a6_nama_tersangka'] = $request->a6_nama_tersangka;
                $sp2hpData['type_specific_data']['a6_nomor_kirim_berkas'] = $request->a6_nomor_kirim_berkas;
                $sp2hpData['type_specific_data']['a6_tanggal_kirim'] = $request->a6_tanggal_kirim;
                $sp2hpData['type_specific_data']['a6_tujuan_kejaksaan_id'] = $request->a6_tujuan_kejaksaan;
                
                // Get prosecutor name
                if ($request->filled('a6_tujuan_kejaksaan')) {
                    $prosecutor = \App\Models\Lib\Prosecutor::find($request->a6_tujuan_kejaksaan);
                    $sp2hpData['type_specific_data']['a6_tujuan_kejaksaan_name'] = $prosecutor ? $prosecutor->name : null;
                }
            }
            
            // Process A7 data into type_specific_data
            if ($request->tipe_sp2hp == 'A7') {
                $sp2hpData['type_specific_data']['a7_nama_tersangka'] = $request->a7_nama_tersangka;
                $sp2hpData['type_specific_data']['a7_rujukan_tahap1'] = $request->a7_rujukan_tahap1;
                $sp2hpData['type_specific_data']['a7_nomor_p21'] = $request->a7_nomor_p21;
                $sp2hpData['type_specific_data']['a7_tanggal_p21'] = $request->a7_tanggal_p21;
                $sp2hpData['type_specific_data']['a7_nomor_kirim_tahap2'] = $request->a7_nomor_kirim_tahap2;
                $sp2hpData['type_specific_data']['a7_tanggal_serah_tahap2'] = $request->a7_tanggal_serah_tahap2;
                $sp2hpData['type_specific_data']['a7_tujuan_kejaksaan_id'] = $request->a7_tujuan_kejaksaan;
                
                // Get prosecutor name
                if ($request->filled('a7_tujuan_kejaksaan')) {
                    $prosecutor = \App\Models\Lib\Prosecutor::find($request->a7_tujuan_kejaksaan);
                    $sp2hpData['type_specific_data']['a7_tujuan_kejaksaan_name'] = $prosecutor ? $prosecutor->name : null;
                }
            }
            
            // Process berkas (A4 tindakan) + keterangan
            if ($request->has('berkas') && is_array($request->berkas)) {
                Log::info('Processing A4 berkas data', [
                    'berkas' => $request->berkas,
                    'berkas_keterangan' => $request->berkas_keterangan
                ]);
                
                $tindakanList = [];
                foreach ($request->berkas as $berkas) {
                    $tindakanList[] = [
                        'nama' => $berkas,
                        'keterangan' => $request->berkas_keterangan[$berkas] ?? null
                    ];
                }
                
                Log::info('A4 tindakan list created', ['tindakanList' => $tindakanList]);
                
                // Don't json_encode - let Laravel cast handle it
                $sp2hpData['a4_tindakan_list'] = $tindakanList;
            } else {
                Log::warning('A4 berkas data not found or not array', [
                    'has_berkas' => $request->has('berkas'),
                    'berkas_value' => $request->berkas,
                    'is_array' => is_array($request->berkas)
                ]);
            }
            
            // Convert type_specific_data to JSON if exists
            if (isset($sp2hpData['type_specific_data'])) {
                $sp2hpData['type_specific_data'] = json_encode($sp2hpData['type_specific_data']);
            }

            // Process kendaraan data jika ada
            if ($request->filled('kendaraan_select')) {
                $kendaraanData = [];
                
                // Ambil data dari form
                if ($request->filled('kendaraan_plat_nomor')) {
                    $kendaraanData['plat_nomor'] = $request->kendaraan_plat_nomor;
                }
                if ($request->filled('kendaraan_jenis')) {
                    $kendaraanData['jenis'] = $request->kendaraan_jenis;
                }
                
                // Atau ambil langsung dari database jika ada ID kendaraan
                if (!empty($kendaraanData)) {
                    $sp2hpData['kendaraan_data'] = json_encode($kendaraanData);
                    Log::info('Kendaraan data saved', ['kendaraan_data' => $kendaraanData]);
                }
            }

            $sp2hpData['created_by'] = Auth::id();
            $sp2hpData['updated_by'] = Auth::id();
            $sp2hpData['reporting_person_id'] = $reportingPersonId;

            if ($request->sp2hp_id) {
                $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::findOrFail($request->sp2hp_id);
                $sp2hp->update($sp2hpData);
                
                // REMOVED: Delete existing officers for this SP2HP document
                // SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hp->id)->delete();
                
                $message = 'SP2HP berhasil diperbarui';
            } else {
                $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::create($sp2hpData);
                $message = 'SP2HP berhasil ditambahkan';
            }

            // Save penyidik (officers) to separate table
            Log::info('About to save penyidik', [
                'has_penyidik' => $request->has('penyidik'),
                'is_array' => is_array($request->penyidik),
                'count' => is_array($request->penyidik) ? count($request->penyidik) : 0,
                'penyidik_data' => $request->penyidik,
                'tipe_sp2hp' => $request->tipe_sp2hp
            ]);
            
            // Hanya save penyidik dari form jika tipe A1
            if ($request->tipe_sp2hp == 'A1' && $request->has('penyidik') && is_array($request->penyidik)) {
                // 1. Identify which investigators to keep/update
                $incomingNrps = [];
                foreach ($request->penyidik as $p) {
                    if (!empty($p['nrp'])) {
                        $incomingNrps[] = $p['nrp'];
                    }
                }

                // 2. Delete investigators that are NOT in the new list
                if (!empty($incomingNrps)) {
                    SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hp->id)
                        ->where('class', 'INVESTIGATOR')
                        ->whereNotIn('register_number', $incomingNrps)
                        ->delete();
                } else {
                    // If no investigators sent, delete all? Or keep? Assuming if array is sent but empty/invalid, we might want to clear.
                    // But the outer if checks count > 0. So if count > 0, we have some.
                }

                // 3. Update or Create
                foreach ($request->penyidik as $index => $penyidikData) {
                    if (!empty($penyidikData['nama'])) {
                        $nrp = $penyidikData['nrp'] ?? null;
                        if (!$nrp) continue;
                        
                        // Cek apakah penyidik dengan NRP ini sudah ada untuk document ini
                        $existingPenyidik = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hp->id)
                            ->where('class', 'INVESTIGATOR')
                            ->where('register_number', $nrp)
                            ->first();
                        
                        $officerFromMaster = Officer::where('register_number', $nrp)->first();
                        
                        $officerData = [
                            'sp2hp_document_id' => $sp2hp->id,
                            'officer_id' => $officerFromMaster ? (string)$officerFromMaster->id : null,
                            'name' => $penyidikData['nama'] ?? null,
                            'rank_id' => $officerFromMaster ? (string)$officerFromMaster->rank_id : ($penyidikData['pangkat'] ?? null),
                            'position_id' => $officerFromMaster ? (string)$officerFromMaster->position_id : null,
                            'register_number' => $nrp,
                            'phone_number' => $penyidikData['telp'] ?? null,
                            'police_id' => $officerFromMaster ? (string)$officerFromMaster->police_id : ($penyidikData['unit'] ?? null),
                            'email' => $officerFromMaster ? $officerFromMaster->email : null,
                            'sort_order' => $index + 1,
                            'class' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('class', 'INVESTIGATOR'),
                            'status' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('status', 'PRESENT'),
                            'flag' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('flag', 'INTERNAL'),
                            'insert_method' => $officerFromMaster ? SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('insert_method', 'IMPORT') : SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('insert_method', 'MANUAL'),
                        ];

                        if ($existingPenyidik) {
                            // Update existing
                            $existingPenyidik->update($officerData);
                        } else {
                            // Create new
                            SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::create($officerData);
                        }
                    }
                }
                
                Log::info('Processed ' . count($request->penyidik) . ' penyidik(s) for A1 form input');
            } else if (in_array($request->tipe_sp2hp, ['A2', 'A3', 'A4', 'A5', 'A6', 'A7'])) {
                // Untuk tipe A2-A7, TIDAK perlu insert data penyidik lagi
                // Data penyidik akan diambil dari dokumen A1 saat ditampilkan di view
                Log::info('Tipe A2-A7 detected, penyidik will be retrieved from A1 when displaying (no insert needed)');
            } else {
                Log::warning('Penyidik data not saved - tipe is A1 but no penyidik input from form');
            }

            // SIGNATORY - Save pejabat penandatangan (HANYA untuk tipe A1)
            // Untuk tipe A2-A7, signatory akan diambil dari dokumen A1
            if ($request->tipe_sp2hp == 'A1' && $request->filled('signatory')) {
                $signatoryId = $request->signatory;
                $signatory = Officer::where('id', $signatoryId)->first();
                
                if ($signatory) {
                    // Cek apakah ada signatory existing (siapapun orangnya) untuk document ini
                    $existingSignatory = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hp->id)
                        ->where('class', 'SIGNATORY')
                        ->first();
                    
                    // Gabungkan first_name dan last_name menjadi name
                    $fullName = trim(($signatory->first_name ?? '') . ' ' . ($signatory->last_name ?? ''));
                    
                    $signatoryData = [
                        'sp2hp_document_id' => $sp2hp->id,
                        'officer_id' => (string)$signatory->id,
                        'register_number' => $signatory->register_number,
                        'name' => $fullName,
                        'rank_id' => (string)$signatory->rank_id, // Convert ke string
                        'position_id' => (string)$signatory->position_id, // Convert ke string
                        'phone_number' => $signatory->phone_number,
                        'email' => $signatory->email,
                        'police_id' => (string)$signatory->police_id, // Convert ke string
                        'status' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('status', 'PRESENT'),
                        'class' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('class', 'SIGNATORY'),
                        'flag' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('flag', 'INTERNAL'),
                        'insert_method' => SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::getEnumOption('insert_method', 'IMPORT'),
                        'sort_order' => 999, // Signatory always last
                    ];

                    if ($existingSignatory) {
                        $existingSignatory->update($signatoryData);
                        Log::info('Updated signatory for A1: ' . $signatory->register_number . ' - ' . $fullName);
                    } else {
                        SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::create($signatoryData);
                        Log::info('Stored signatory for A1: ' . $signatory->register_number . ' - ' . $fullName);
                    }
                }
            } else if (in_array($request->tipe_sp2hp, ['A2', 'A3', 'A4', 'A5', 'A6', 'A7'])) {
                Log::info('Tipe A2-A7: Signatory will be retrieved from A1 document when displaying (no insert needed)');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('view_produktivitas_accident', ['accident_id' => $request->accident_id]),
                'data' => $sp2hp
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sp2hpDocumentController store error: ', [$e->getMessage(), $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update SP2HP (menggunakan logic store dengan sp2hp_id)
     */
    public function update(Request $request, $id)
    {
        // Set sp2hp_id untuk update
        $request->merge(['sp2hp_id' => $id]);
        
        // Gunakan method store yang sudah handle update
        return $this->store($request);
    }

    /**
     * Menghapus SP2HP
     */
    public function destroy(Request $request)
    {
        try {
            $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::findOrFail($request->id);
            $sp2hp->delete();

            return response()->json([
                'success' => true,
                'message' => 'SP2HP berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus SP2HP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit SP2HP (ubah status dari draft ke submitted)
     */
    public function submit(Request $request)
    {
        try {
            $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::findOrFail($request->id);
            
            if ($sp2hp->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya SP2HP dengan status draft yang dapat disubmit'
                ], 400);
            }

            $sp2hp->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SP2HP berhasil disubmit',
                'data' => $sp2hp
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal submit SP2HP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export SP2HP ke PDF
     */
    public function exportPdf($id)
    {
        try {
            $sp2hp = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::findOrFail($id);
            
            // TODO: Implementasi export PDF sesuai format regulasi
            // Gunakan package seperti DOMPDF atau TCPDF
            
            return response()->json([
                'success' => false,
                'message' => 'Fitur export PDF sedang dalam pengembangan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export SP2HP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nomor surat otomatis
     */
    public function generateNomorSurat(Request $request)
    {
        try {
            $request->validate([
                'bulan' => 'required|numeric|between:1,12',
                'tahun' => 'required|numeric',
                'unit_kode' => 'required|string'
            ]);

            // Format: SP2HP / [Nomor Urut] / [Bulan Romawi] / [Unit Kode] / [Tahun]
            $bulanRomawi = [
                'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
            ];

            $bulan = $request->bulan;
            $tahun = $request->tahun;
            
            // Hitung nomor urut berdasarkan bulan dan tahun yang sama
            $nomorUrut = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::whereYear('tanggal_surat', $tahun)
                ->whereMonth('tanggal_surat', $bulan)
                ->count() + 1;

            $nomorSurat = sprintf(
                "SP2HP/%03d/%s/%s/%d",
                $nomorUrut,
                $bulanRomawi[$bulan - 1],
                $request->unit_kode,
                $tahun
            );

            return response()->json([
                'success' => true,
                'nomor_surat' => $nomorSurat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor surat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan form create SP2HP
     */
    public function create(Request $request)
    {
        $accidentId = $request->accident_id;
        
        // Load accident data dengan relasi
        $accident = Accident::with([
            'suspects',
            'reportedPersons',
            'caseVehicle',
            'police',
            'polres.polda'
        ])->findOrFail($accidentId);
        
        // Get old and new polres IDs for officer search
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        // Get officers (penyidik) from the same polres
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

        // Get authorized signatories (pejabat penandatangan)
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

        // Get courts for carbon copy
        $courts = \App\Models\Lib\Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        // Get vehicle list from IRSMS API (like SuratKetetapanTentangPenetapanTersangkaDocumentController)
        try {
            $vehicleListResponse = Http::timeout(10)
                ->withHeaders([
                    "Key" => "16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS",
                    "Content-Type" => "application/json"
                ])
                ->withBody(
                    json_encode([
                        'accident_id' => $accidentId
                    ]),
                    'application/json'
                )
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban')
                ->json();

            Log::info('Vehicle List Response: ', [$vehicleListResponse]);
            $vehicleListCollection = collect($vehicleListResponse['result'] ?? []);
        } catch (\Exception $e) {
            Log::error('Failed to fetch vehicle list from IRSMS API: ' . $e->getMessage());
            $vehicleListCollection = collect([]);
        }
        
        // Get library data for mapping
        $accidentTypes = AccidentType::where('is_active', true)->get();
        $accidentTypesCollection = collect($accidentTypes);

        $vehicleTypes = VehicleType::where('is_active', true)->get();
        $vehicleTypesCollection = collect($vehicleTypes);

        $accidentCauses = AccidentCause::where('is_active', true)->get();
        $accidentCausesCollection = collect($accidentCauses);

        $identityTypes = IdentityType::where('is_active', true)->orderBy('sort')->get();
        $identityTypesCollection = collect($identityTypes);

        $drivingLicenseTypes = DrivingLicenseType::where('is_active', true)->orderBy('sort')->get();
        $drivingLicenseTypesCollection = collect($drivingLicenseTypes);
        
        // Map vehicle data
        $vehicleList = $vehicleListCollection->map(function ($vehicle) use ($accidentTypesCollection, $vehicleTypesCollection, $accidentCausesCollection, $identityTypesCollection, $drivingLicenseTypesCollection) {
            $accidentTypeMatch = $accidentTypesCollection->firstWhere('irsms_id', $vehicle['jenis_kecelakaan']);
            if($accidentTypeMatch){
                $vehicle['accident_type_id'] = $accidentTypeMatch['id'];
                $vehicle['accident_type_name'] = $accidentTypeMatch['name'];
            }

            $vehicleTypeMatch = $vehicleTypesCollection->firstWhere('irsms_id', $vehicle['jenis_ranmor']);
            if($vehicleTypeMatch){
                $vehicle['vehicle_type_id'] = $vehicleTypeMatch['id'];
                $vehicle['vehicle_type_name'] = $vehicleTypeMatch['name'];
            }

            $accidentCauseMatch = $accidentCausesCollection->firstWhere('irsms_id', $vehicle['penyebab']);
            if($accidentCauseMatch){
                $vehicle['accident_cause_id'] = $accidentCauseMatch['id'];
                $vehicle['accident_cause_name'] = $accidentCauseMatch['name'];
            }

            $identityTypeMatch = $identityTypesCollection->firstWhere('irsms_id', $vehicle['tipe_identitas']);
            if($identityTypeMatch){
                $vehicle['identity_type_id'] = $identityTypeMatch['id'];
                $vehicle['identity_type_name'] = $identityTypeMatch['name'];
            }

            $drivingLicenseTypeMatch = $drivingLicenseTypesCollection->firstWhere('irsms_id', $vehicle['jenis_sim'] ?? '-');
            if($drivingLicenseTypeMatch){
                $vehicle['driving_license_type_id'] = $drivingLicenseTypeMatch['id'];
                $vehicle['driving_license_type_name'] = $drivingLicenseTypeMatch['name'];
            }

            return $vehicle;
        });

        // Get DORS victims if dors_id exists
        $dorsVictims = collect();
        if ($accident->dors_id) {
            $dorsAccident = DorsAccident::where('dors_id', $accident->dors_id)->first();
            if ($dorsAccident) {
                $dorsVictims = DorsVictim::where('dors_accident_id', $dorsAccident->id)->get();
            }
        }
        
        // Get library data
        $libData = $this->getLibData();
        
        // ============================================
        // GET EXISTING PENERIMA SP2HP (Auto-populate)
        // ============================================
        $existingPenerima = ReportingPerson::where('accident_id', $accidentId)
            ->where('class', 'SP2HP_PENERIMA')
            ->where('is_active', true)
            ->with(['identityType', 'gender', 'nationality', 'ethnic', 'job', 'religion', 'education', 'maritalStatus'])
            ->get();
        
        // ============================================
        // GET RUJUKAN A1 (Auto-populate for A2, A3, etc)
        // ============================================
        $rujukanA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
            ->where('tipe_sp2hp', 'A1')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorRujukanA1 = $rujukanA1 ? $rujukanA1->nomor_surat : '';
        $tanggalRujukanA1 = $rujukanA1 && $rujukanA1->tanggal_surat 
            ? Carbon::parse($rujukanA1->tanggal_surat)->format('d-m-Y') 
            : '';
        
        // ============================================
        // GET RUJUKAN A4 (Auto-populate for A5, A6, A7)
        // ============================================
        $rujukanA4 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
            ->where('tipe_sp2hp', 'A4')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorRujukanA4 = $rujukanA4 ? $rujukanA4->nomor_surat : '';
        $tanggalRujukanA4 = $rujukanA4 && $rujukanA4->tanggal_surat 
            ? Carbon::parse($rujukanA4->tanggal_surat)->format('d-m-Y') 
            : '';
        
        // ============================================
        // GET RUJUKAN A6 (for A7 - Nomor Kirim Berkas)
        // ============================================
        $rujukanA6 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
            ->where('tipe_sp2hp', 'A6')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorKirimBerkasA6 = null;
        if ($rujukanA6 && !empty($rujukanA6->type_specific_data)) {
            $dataA6 = is_string($rujukanA6->type_specific_data) 
                ? json_decode($rujukanA6->type_specific_data, true) 
                : $rujukanA6->type_specific_data;
            
            $nomorKirimBerkasA6 = $dataA6['a6_nomor_kirim_berkas'] ?? null;
        }
        
        // ============================================
        // GET DATA TERSANGKA FROM A4 (for display in A5, A6, A7)
        // ============================================
        $tersangkaFromA4 = null;
        if ($rujukanA4 && !empty($rujukanA4->type_specific_data)) {
            // Decode JSON jika masih berupa string
            $dataA4 = is_string($rujukanA4->type_specific_data) 
                ? json_decode($rujukanA4->type_specific_data, true) 
                : $rujukanA4->type_specific_data;
            
            $tersangkaFromA4 = $dataA4;
            
            Log::info('Tersangka dari A4 loaded', [
                'type_specific_data_type' => gettype($rujukanA4->type_specific_data),
                'decoded_data' => $dataA4,
                'tersangka_nama' => $dataA4['tersangka_nama'] ?? 'NOT FOUND'
            ]);
        } else {
            Log::warning('No tersangka data from A4', [
                'rujukanA4_exists' => $rujukanA4 ? 'yes' : 'no',
                'type_specific_data' => $rujukanA4 ? $rujukanA4->type_specific_data : null
            ]);
        }
        
        // ============================================
        // DETERMINE NOMOR SURAT & TANGGAL SURAT RUJUKAN
        // Logic: A1-A3 use A1, A4-A7 use A4
        // ============================================
        $nomorSuratRujukan = '';
        $tanggalSuratRujukan = '';
        
        // Untuk tipe A2-A3: gunakan A1
        if (in_array($request->tipe_sp2hp ?? '', ['A2', 'A3'])) {
            $nomorSuratRujukan = $nomorRujukanA1;
            $tanggalSuratRujukan = $tanggalRujukanA1;
        }
        // Untuk tipe A5-A7: gunakan A4
        elseif (in_array($request->tipe_sp2hp ?? '', ['A5', 'A6', 'A7'])) {
            $nomorSuratRujukan = $nomorRujukanA4;
            $tanggalSuratRujukan = $tanggalRujukanA4;
        }
        
        // ============================================
        // GET SPRIN SIDIK DATA (Auto-populate for A3)
        // ============================================
        $sprinSidik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
            ->where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorSprinSidik = $sprinSidik ? $sprinSidik->document_number : '';
        $tanggalSprinSidik = $sprinSidik && $sprinSidik->document_date 
            ? Carbon::parse($sprinSidik->document_date)->format('d-m-Y') 
            : '';
        
        // ============================================
        // GET SPDP DATA (Auto-populate for A3, A6, A7)
        // ============================================
        $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::with('prosecutor')
            ->where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorSpdp = $spdp ? $spdp->document_number : '';
        $tanggalSpdp = $spdp && $spdp->document_date 
            ? Carbon::parse($spdp->document_date)->format('d-m-Y') 
            : '';
        
        // Get prosecutor_id from SPDP for A6 & A7
        $spdpProsecutorId = $spdp ? $spdp->prosecutor_id : null;
        $spdpProsecutorName = ($spdp && $spdp->prosecutor) ? $spdp->prosecutor->full_name : null;
        
        // Get pasal from relationship suratPerintahPenyidikanDocumentLaws
        $pasalDiduga = '';
        if ($sprinSidik && $sprinSidik->suratPerintahPenyidikanDocumentLaws) {
            $pasalList = $sprinSidik->suratPerintahPenyidikanDocumentLaws
                ->map(function($law) {
                    $parts = [];
                    
                    // 1. Add constitution_chapter (pasal yang dipilih petugas dari dropdown)
                    if (!empty($law->constitution_chapter)) {
                        $parts[] = $law->constitution_chapter;
                    }
                    
                    // 2. Add constitution (input manual tambahan dari petugas)
                    if (!empty($law->constitution)) {
                        $parts[] = $law->constitution;
                    }
                    
                    // 3. Fallback: Add from crimeConstitution relationship if exists
                    if (empty($parts) && $law->crimeConstitution) {
                        if (!empty($law->crimeConstitution->chapter)) {
                            $parts[] = $law->crimeConstitution->chapter;
                        }
                        if (!empty($law->crimeConstitution->name)) {
                            $parts[] = $law->crimeConstitution->name;
                        }
                        if (!empty($law->crimeConstitution->description)) {
                            $parts[] = '(' . $law->crimeConstitution->description . ')';
                        }
                    }
                    
                    return !empty($parts) ? implode(' ', $parts) : null;
                })
                ->filter()
                ->toArray();
            $pasalDiduga = implode('; ', $pasalList);
        }
        
        // ============================================
        // GET PENYIDIK FROM A1 (for display in A2-A7)
        // ============================================
        $penyidikFromA1 = [];
        if ($rujukanA1) {
            $penyidikFromA1 = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $rujukanA1->id)
                ->where('class', 'INVESTIGATOR')
                ->ordered()
                ->get()
                ->map(function($officer) {
                    return [
                        'nama' => $officer->name,
                        'pangkat' => $officer->rank_id,
                        'nrp' => $officer->register_number,
                        'telp' => $officer->phone_number,
                        'unit' => $officer->police_id,
                    ];
                })
                ->toArray();
        }
        
        // ============================================
        // CHECK EXISTING SP2HP TYPES (for validation)
        // ============================================
        $existingSp2hpTypes = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->pluck('tipe_sp2hp')
            ->toArray();
        
        return view('docs.surat-pemberitahuan-perkembangan-hasil-penyidikan.create', array_merge([
            'accidentId' => $accidentId,
            'accident' => $accident,
            'officers' => $officers,
            'authorizedSignatories' => $authorizedSignatories,
            'courts' => $courts,
            'vehicleList' => $vehicleList,
            'dorsVictims' => $dorsVictims,
            'existingPenerima' => $existingPenerima,
            'penyidikFromA1' => $penyidikFromA1,
            'nomorRujukanA1' => $nomorRujukanA1,
            'tanggalRujukanA1' => $tanggalRujukanA1,
            'nomorRujukanA4' => $nomorRujukanA4,
            'tanggalRujukanA4' => $tanggalRujukanA4,
            'nomorKirimBerkasA6' => $nomorKirimBerkasA6,
            'tersangkaFromA4' => $tersangkaFromA4,
            'nomorSuratRujukan' => $nomorSuratRujukan,
            'tanggalSuratRujukan' => $tanggalSuratRujukan,
            'nomorSprinSidik' => $nomorSprinSidik,
            'tanggalSprinSidik' => $tanggalSprinSidik,
            'nomorSpdp' => $nomorSpdp,
            'tanggalSpdp' => $tanggalSpdp,
            'spdpProsecutorId' => $spdpProsecutorId,
            'spdpProsecutorName' => $spdpProsecutorName,
            'pasalDiduga' => $pasalDiduga,
            'existingSp2hpTypes' => $existingSp2hpTypes,
        ], $libData));
    }

    /**
     * Menampilkan form edit SP2HP
     */
    public function edit(Request $request, $id)
    {
        $accidentId = $request->accident_id;
        $sp2hpDocument = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::with(['accident'])->findOrFail($id);
        
        // Load accident data dengan relasi (same as create)
        $accident = Accident::with([
            'suspects',
            'reportedPersons',
            'caseVehicle',
            'police',
            'polres.polda'
        ])->findOrFail($accidentId);
        
        // Get old and new polres IDs for officer search
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        // Get officers (penyidik) from the same polres
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

        // Get authorized signatories (pejabat penandatangan)
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

        // Get courts for carbon copy
        $courts = \App\Models\Lib\Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        // Get vehicle list from IRSMS API
        try {
            $vehicleListResponse = Http::timeout(10)
                ->withHeaders([
                    "Key" => "16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS",
                    "Content-Type" => "application/json"
                ])
                ->withBody(
                    json_encode([
                        'accident_id' => $accidentId
                    ]),
                    'application/json'
                )
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban')
                ->json();

            Log::info('Vehicle List Response: ', [$vehicleListResponse]);
            $vehicleListCollection = collect($vehicleListResponse['result'] ?? []);
        } catch (\Exception $e) {
            Log::error('Failed to fetch vehicle list from IRSMS API: ' . $e->getMessage());
            $vehicleListCollection = collect([]);
        }
        
        // Get library data for mapping
        $accidentTypes = AccidentType::where('is_active', true)->get();
        $accidentTypesCollection = collect($accidentTypes);

        $vehicleTypes = VehicleType::where('is_active', true)->get();
        $vehicleTypesCollection = collect($vehicleTypes);

        $accidentCauses = AccidentCause::where('is_active', true)->get();
        $accidentCausesCollection = collect($accidentCauses);

        $identityTypes = IdentityType::where('is_active', true)->orderBy('sort')->get();
        $identityTypesCollection = collect($identityTypes);

        $drivingLicenseTypes = DrivingLicenseType::where('is_active', true)->orderBy('sort')->get();
        $drivingLicenseTypesCollection = collect($drivingLicenseTypes);
        
        // Map vehicle data
        $vehicleList = $vehicleListCollection->map(function ($vehicle) use ($accidentTypesCollection, $vehicleTypesCollection, $accidentCausesCollection, $identityTypesCollection, $drivingLicenseTypesCollection) {
            $accidentTypeMatch = $accidentTypesCollection->firstWhere('irsms_id', $vehicle['jenis_kecelakaan']);
            if($accidentTypeMatch){
                $vehicle['accident_type_id'] = $accidentTypeMatch['id'];
                $vehicle['accident_type_name'] = $accidentTypeMatch['name'];
            }

            $vehicleTypeMatch = $vehicleTypesCollection->firstWhere('irsms_id', $vehicle['jenis_ranmor']);
            if($vehicleTypeMatch){
                $vehicle['vehicle_type_id'] = $vehicleTypeMatch['id'];
                $vehicle['vehicle_type_name'] = $vehicleTypeMatch['name'];
            }

            $accidentCauseMatch = $accidentCausesCollection->firstWhere('irsms_id', $vehicle['penyebab']);
            if($accidentCauseMatch){
                $vehicle['accident_cause_id'] = $accidentCauseMatch['id'];
                $vehicle['accident_cause_name'] = $accidentCauseMatch['name'];
            }

            $identityTypeMatch = $identityTypesCollection->firstWhere('irsms_id', $vehicle['tipe_identitas']);
            if($identityTypeMatch){
                $vehicle['identity_type_id'] = $identityTypeMatch['id'];
                $vehicle['identity_type_name'] = $identityTypeMatch['name'];
            }

            $drivingLicenseTypeMatch = $drivingLicenseTypesCollection->firstWhere('irsms_id', $vehicle['jenis_sim'] ?? '-');
            if($drivingLicenseTypeMatch){
                $vehicle['driving_license_type_id'] = $drivingLicenseTypeMatch['id'];
                $vehicle['driving_license_type_name'] = $drivingLicenseTypeMatch['name'];
            }

            return $vehicle;
        });

        // Get DORS victims if dors_id exists
        $dorsVictims = collect();
        if ($accident->dors_id) {
            $dorsAccident = DorsAccident::where('dors_id', $accident->dors_id)->first();
            if ($dorsAccident) {
                $dorsVictims = DorsVictim::where('dors_accident_id', $dorsAccident->id)->get();
            }
        }
        
        // Get library data
        $libData = $this->getLibData();
        
        // Get existing penerima for this accident
        $existingPenerima = ReportingPerson::where('accident_id', $accidentId)
            ->where('class', 'SP2HP_PENERIMA')
            ->where('is_active', true)
            ->with(['identityType', 'gender', 'nationality', 'ethnic', 'job', 'religion', 'education', 'maritalStatus'])
            ->get();
        
        // Get existing penyidik from officers table
        $existingPenyidik = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hpDocument->id)
            ->where('class', 'INVESTIGATOR')
            ->ordered()
            ->get()
            ->map(function($officer) {
                return [
                    'nrp' => $officer->register_number,
                    'pangkat' => $officer->rank_id,
                    'nama' => $officer->name,
                    'telp' => $officer->phone_number,
                    'unit' => $officer->police_id,
                ];
            })
            ->toArray();
        
        // Get existing signatory
        $existingSignatory = SuratPemberitahuanPerkembanganHasilPenyidikanOfficer::where('sp2hp_document_id', $sp2hpDocument->id)
            ->where('class', 'SIGNATORY')
            ->first();
        
        $existingSignatoryId = $existingSignatory ? $existingSignatory->officer_id : null;
        
        // Get rujukan A1, sprin sidik, spdp (same as create)
        $rujukanA1 = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
            ->where('tipe_sp2hp', 'A1')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorRujukanA1 = $rujukanA1 ? $rujukanA1->nomor_surat : '';
        $tanggalRujukanA1 = $rujukanA1 && $rujukanA1->tanggal_surat 
            ? Carbon::parse($rujukanA1->tanggal_surat)->format('d-m-Y') 
            : '';
        
        $sprinSidik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
            ->where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorSprinSidik = $sprinSidik ? $sprinSidik->document_number : '';
        $tanggalSprinSidik = $sprinSidik && $sprinSidik->document_date 
            ? Carbon::parse($sprinSidik->document_date)->format('d-m-Y') 
            : '';
        
        $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::with('prosecutor')
            ->where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $nomorSpdp = $spdp ? $spdp->document_number : '';
        $tanggalSpdp = $spdp && $spdp->document_date 
            ? Carbon::parse($spdp->document_date)->format('d-m-Y') 
            : '';
        
        // Get prosecutor_id from SPDP for A6 & A7
        $spdpProsecutorId = $spdp ? $spdp->prosecutor_id : null;
        $spdpProsecutorName = ($spdp && $spdp->prosecutor) ? $spdp->prosecutor->full_name : null;
        
        // Get pasal from relationship
        $pasalDiduga = '';
        if ($sprinSidik && $sprinSidik->suratPerintahPenyidikanDocumentLaws) {
            $pasalList = $sprinSidik->suratPerintahPenyidikanDocumentLaws
                ->map(function($law) {
                    $parts = [];
                    
                    if (!empty($law->constitution_chapter)) {
                        $parts[] = $law->constitution_chapter;
                    }
                    
                    if (!empty($law->constitution)) {
                        $parts[] = $law->constitution;
                    }
                    
                    if (empty($parts) && $law->crimeConstitution) {
                        if (!empty($law->crimeConstitution->chapter)) {
                            $parts[] = $law->crimeConstitution->chapter;
                        }
                        if (!empty($law->crimeConstitution->name)) {
                            $parts[] = $law->crimeConstitution->name;
                        }
                        if (!empty($law->crimeConstitution->description)) {
                            $parts[] = '(' . $law->crimeConstitution->description . ')';
                        }
                    }
                    
                    return !empty($parts) ? implode(' ', $parts) : null;
                })
                ->filter()
                ->toArray();
            $pasalDiduga = implode('; ', $pasalList);
        }
        
        // Check existing SP2HP types for validation
        $existingSp2hpTypes = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::where('accident_id', $accidentId)
            ->whereNull('deleted_at')
            ->pluck('tipe_sp2hp')
            ->toArray();
        
        // Use same view as create, but with edit mode flag
        return view('docs.surat-pemberitahuan-perkembangan-hasil-penyidikan.create', array_merge([
            'isEdit' => true,
            'sp2hp' => $sp2hpDocument,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'officers' => $officers,
            'authorizedSignatories' => $authorizedSignatories,
            'courts' => $courts,
            'vehicleList' => $vehicleList,
            'dorsVictims' => $dorsVictims,
            'existingPenerima' => $existingPenerima,
            'existingPenyidik' => $existingPenyidik,
            'existingSignatoryId' => $existingSignatoryId,
            'nomorRujukanA1' => $nomorRujukanA1,
            'tanggalRujukanA1' => $tanggalRujukanA1,
            'nomorSprinSidik' => $nomorSprinSidik,
            'tanggalSprinSidik' => $tanggalSprinSidik,
            'nomorSpdp' => $nomorSpdp,
            'tanggalSpdp' => $tanggalSpdp,
            'spdpProsecutorId' => $spdpProsecutorId,
            'spdpProsecutorName' => $spdpProsecutorName,
            'pasalDiduga' => $pasalDiduga,
            'existingSp2hpTypes' => $existingSp2hpTypes,
        ], $libData));
    }

    /**
     * Menampilkan halaman download/show SP2HP
     */
    public function downloadShow(Request $request, $id)
    {
        $sp2hpDocument = SuratPemberitahuanPerkembanganHasilPenyidikanDocument::findOrFail($id);
        $accident = $sp2hpDocument->accident;
        
        return view('docs.surat-pemberitahuan-perkembangan-hasil-penyidikan.show', [
            'sp2hpDocument' => $sp2hpDocument,
            'accident' => $accident
        ]);
    }

    /**
     * Get locations (province, city, district) based on parent_id and class
     */
    public function getLocations(Request $request)
    {
        $class = $request->class;
        $parent_id = $request->parent_id;

        $locations = Location::where('is_active', true)
                        ->where('parent_id', $parent_id)
                        ->where('class', $class)
                        ->orderBy('sort')
                        ->get();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $locations
        ], 200);
    }
}
