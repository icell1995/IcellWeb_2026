<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\ReportedPerson;

use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Models\Lib\DocumentClassification;
use App\Models\Lib\Location;

use App\Traits\DocsOfficersTraits;

class SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        foreach ($suratPerintahPenyidikanDocuments as $sprindik) {
            $pasalParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));
                }
            }
            $sprindik->pasal_formatted = implode(', ', $pasalParts);
        }

        $suratPerintahTugasDocuments = SuratPerintahTugasDocument::where('accident_id', $accidentId)
            ->whereHasMorph('related', get_class(new SuratPerintahPenyidikanDocument()))
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
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

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('sort')->get();
        $courts      = Court::where('is_active', true)->orderBy('sort')->get();
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        // Load & filter districts based on Polres regency name to avoid regency_id mismatch
        $regencyName = $accident->polres->polres_regency ?? '';
        $regencyCode = '';
        
        $regenciesPath = base_path('master_seeder/regencies-new1.json');
        if ($regencyName && file_exists($regenciesPath)) {
            $allRegencies = json_decode(file_get_contents($regenciesPath), true) ?? [];
            foreach ($allRegencies as $reg) {
                if (isset($reg['Nama']) && strtoupper($reg['Nama']) === strtoupper($regencyName)) {
                    $regencyCode = $reg['KodePuskarda'] ?? '';
                    break;
                }
            }
        }
        
        // Fallback to regency_id if regencyName search fails
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
                $districts = array_filter($allDistricts, function($d) use ($regencyCode) {
                    return isset($d['KodePuskarda']) && strpos($d['KodePuskarda'], $regencyCode . '.') === 0;
                });
            } else {
                $districts = $allDistricts;
            }
        }

        // Auto-detect district from IRSMS road_name format
        $roadName = $accident->road_name ?? '';
        $extractedDistrict = '';
        if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $roadName, $matches)) {
            // Bersihkan dari titik, koma, dan karakter aneh lainnya
            $extractedDistrict = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $matches[1]));
            $extractedDistrict = preg_replace('/\s+/', ' ', $extractedDistrict);
        }
        
        $defaultKodeWilayah = '';
        if ($extractedDistrict && !empty($districts)) {
            $extractedClean = strtoupper(str_replace(' ', '', $extractedDistrict));
            foreach ($districts as $d) {
                if (isset($d['Nama'])) {
                    $dNameClean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $d['Nama']));
                    if ($dNameClean === $extractedClean) {
                        $defaultKodeWilayah = $d['KodePuskarda'];
                        break;
                    }
                }
            }
        }

        return view('docs.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.create', compact(
            'accidentId',
            'accident',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahTugasDocuments',
            'authorizedSignatories',
            'suspects',
            'reportedPersons',
            'prosecutors',
            'courts',
            'documentClassifications',
            'districts',
            'defaultKodeWilayah'
        ));
    }

    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id') ?? $request->accident_id);
        $accident   = Accident::where('id', $accidentId)->first();

        $validator = $this->validateForm($request, $accident);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber                   = htmlspecialchars($request->documentNumber);
        $documentDate                     = htmlspecialchars($request->documentDate);
        $documentClassificationId         = htmlspecialchars($request->documentClassification);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $suratPerintahTugasDocumentId     = htmlspecialchars($request->suratPerintahTugasDocument);
        $isSuspectExists                  = ($request->isSuspectExists == 'true') ? true : false;
        $prosecutorId                     = htmlspecialchars($request->prosecutor);
        $courtId                          = htmlspecialchars($request->court);
        $appendix                         = $request->appendix;
        $kodeWilayah                      = $request->kode_wilayah;
        $signatoryId                      = htmlspecialchars($request->signatory);

        $accident = Accident::find($accidentId);
        $accidentDateObj = \Carbon\Carbon::parse($accident->accident_date);
        $waktuKejadian   = 'Sekitar pukul ' . \Carbon\Carbon::parse($accident->accident_time)->format('H:i') . ' WIB';
        $tanggalKejadian = intval($accidentDateObj->format('d'));
        $bulanKejadian   = intval($accidentDateObj->format('m'));
        $tahunKejadian   = intval($accidentDateObj->format('Y'));

        $carbonCopies = $request->carbonCopies ?? [];

        $sprindik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->find($suratPerintahPenyidikanDocumentId);
            
        $daftarUuPasal = [];
        $dugaanTindakPidanaList = '';
        
        if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $laws = $sprindik->suratPerintahPenyidikanDocumentLaws;
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
            
            $daftarUuPasal = $pasalParts;
            $dugaanTindakPidanaList = implode(' dan ', array_unique($dugaanParts));
        }

        $uraianSingkatPerkara = $request->uraianSingkatPerkara ?: $dugaanTindakPidanaList;
        $lokasiKejadian       = $request->lokasi_kejadian;
        $sumberDana           = $request->sumber_dana;
        $sumberInformasi      = $request->sumber_informasi;

        $suspects       = $request->suspects;
        $reportedPerson = $request->reportedPerson;

        $exists = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['documentNumber' => 'Nomor dokumen "' . $documentNumber . '" sudah ada sebelumnya. Gunakan nomor yang berbeda.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            
            $doc = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::create([
                'accident_id'                          => $accidentId,
                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                'surat_perintah_tugas_document_id'     => $suratPerintahTugasDocumentId,
                'document_number'                      => $documentNumber,
                'document_date'                        => $documentDate,
                'document_classification_id'           => $documentClassificationId,
                'is_suspect_exists'                    => $isSuspectExists,
                'prosecutor_id'                        => $prosecutorId,
                'court_id'                             => $courtId,
                'appendix'                             => $appendix,
                'carbon_copies'                        => $carbonCopies,
                // Field tambahan SPPT-TI (simpan di messages jika ada)
                'messages'                           => [
                    'uraian_singkat_perkara'=> $uraianSingkatPerkara,
                    'daftar_uu_pasal'   => $daftarUuPasal,
                    'lokasi_kejadian'   => $lokasiKejadian,
                    'kode_wilayah'      => $kodeWilayah,
                    'waktu_kejadian'    => $waktuKejadian,
                    'tahun_kejadian'    => $tahunKejadian,
                    'bulan_kejadian'    => $bulanKejadian,
                    'tanggal_kejadian'  => $tanggalKejadian,
                    'sumber_dana'       => $sumberDana,
                    'sumber_informasi'  => $sumberInformasi,
                    'source'            => 'PUSIKNAS_FORM',
                ],
            ]);
            $doc->created_by_user_id = \Illuminate\Support\Facades\Auth::id();
            $doc->updated_by_user_id = \Illuminate\Support\Facades\Auth::id();
            $doc->save();

            $docId = $doc->id;

            // Penandatangan
            $signatory = Officer::where('id', $signatoryId)->first();
            $doc->officers()->create([
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $docId,
                'register_number' => $signatory->register_number,
                'first_title'     => $signatory->first_title,
                'first_name'      => $signatory->first_name,
                'last_name'       => $signatory->last_name,
                'last_title'      => $signatory->last_title,
                'rank_id'         => $signatory->rank_id,
                'position_id'     => $signatory->position_id,
                'phone_number'    => $signatory->phone_number,
                'email'           => $signatory->email,
                'police_id'       => $signatory->police_id,
                'status'          => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class'           => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag'            => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method'   => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('flag', 'IMPORT'),
            ]);

            // Tersangka / Terlapor
            if ($isSuspectExists) {
                foreach ($suspects as $suspect) {
                    $doc->suspects()->attach($suspect);
                }
            } else {
                $doc->reportedPersons()->attach($reportedPerson);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::withRelated()->where('id', $id)->first();
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        return view('docs.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.show', compact(
            'accidentId',
            'accident',
            'document'
        ));
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::with([
            'officers',
            'suratPerintahPenyidikanDocument',
            'suspects',
        ])->where('id', $id)->first();
        if (!$document->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui atau sedang dalam proses persetujuan.');
        }

        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        foreach ($suratPerintahPenyidikanDocuments as $sprindik) {
            $pasalParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));
                }
            }
            $sprindik->pasal_formatted = implode(', ', $pasalParts);
        }

        $suratPerintahTugasDocuments = SuratPerintahTugasDocument::where('accident_id', $accidentId)
            ->whereHasMorph('related', 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument')
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();
        $prosecutors     = Prosecutor::where('is_active', true)->orderBy('sort')->get();
        $courts          = Court::where('is_active', true)->orderBy('sort')->get();
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        $selectedSuspects = $document->suspects()->get()->pluck('id')->toArray();

        // Load & filter districts based on Polres regency name to avoid regency_id mismatch
        $regencyName = $accident->polres->polres_regency ?? '';
        $regencyCode = '';
        
        $regenciesPath = base_path('master_seeder/regencies-new1.json');
        if ($regencyName && file_exists($regenciesPath)) {
            $allRegencies = json_decode(file_get_contents($regenciesPath), true) ?? [];
            foreach ($allRegencies as $reg) {
                if (isset($reg['Nama']) && strtoupper($reg['Nama']) === strtoupper($regencyName)) {
                    $regencyCode = $reg['KodePuskarda'] ?? '';
                    break;
                }
            }
        }
        
        // Fallback to regency_id if regencyName search fails
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
                $districts = array_filter($allDistricts, function($d) use ($regencyCode) {
                    return isset($d['KodePuskarda']) && strpos($d['KodePuskarda'], $regencyCode . '.') === 0;
                });
            } else {
                $districts = $allDistricts;
            }
        }

        // Auto-detect district from IRSMS road_name format
        $roadName = $accident->road_name ?? '';
        $extractedDistrict = '';
        if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $roadName, $matches)) {
            $extractedDistrict = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $matches[1]));
            $extractedDistrict = preg_replace('/\s+/', ' ', $extractedDistrict);
        }
        
        $defaultKodeWilayah = $document->messages['kode_wilayah'] ?? '';
        if (!$defaultKodeWilayah && $extractedDistrict && !empty($districts)) {
            $extractedClean = strtoupper(str_replace(' ', '', $extractedDistrict));
            foreach ($districts as $d) {
                if (isset($d['Nama'])) {
                    $dNameClean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $d['Nama']));
                    if ($dNameClean === $extractedClean) {
                        $defaultKodeWilayah = $d['KodePuskarda'];
                        break;
                    }
                }
            }
        }

        return view('docs.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.edit', compact(
            'accidentId',
            'accident',
            'document',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahTugasDocuments',
            'authorizedSignatories',
            'suspects',
            'reportedPersons',
            'prosecutors',
            'courts',
            'documentClassifications',
            'selectedSuspects',
            'districts',
            'defaultKodeWilayah'
        ));
    }

    public function update(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->query('accident_id') ?? $request->accident_id);
        $accident   = Accident::where('id', $accidentId)->first();
        $document   = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('id', $id)->firstOrFail();

        if (!$document->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui atau sedang dalam proses persetujuan.');
        }

        $validator = $this->validateForm($request, $accident);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $isSuspectExists = ($request->isSuspectExists == 'true') ? true : false;
        $daftarUuPasal   = $request->daftar_uu_pasal ?? [];

        DB::beginTransaction();
        try {
            $document->update([
                'document_number'                      => htmlspecialchars($request->documentNumber),
                'document_date'                        => htmlspecialchars($request->documentDate),
                'document_classification_id'           => htmlspecialchars($request->documentClassification),
                'surat_perintah_penyidikan_document_id' => htmlspecialchars($request->suratPerintahPenyidikanDocument),
                'surat_perintah_tugas_document_id'     => htmlspecialchars($request->suratPerintahTugasDocument),
                'is_suspect_exists'                    => $isSuspectExists,
                'prosecutor_id'                        => htmlspecialchars($request->prosecutor),
                'court_id'                             => htmlspecialchars($request->court),
                'appendix'                             => htmlspecialchars($request->appendix),
                'carbon_copies'                        => $request->carbonCopies,
                'messages'                           => [
                    'uraian_singkat_perkara'=> $request->uraianSingkatPerkara,
                    'daftar_uu_pasal'   => $daftarUuPasal,
                    'lokasi_kejadian'   => htmlspecialchars($request->lokasi_kejadian ?? ''),
                    'kode_wilayah'      => htmlspecialchars($request->kode_wilayah ?? ''),
                    'waktu_kejadian'    => htmlspecialchars($request->waktu_kejadian ?? ''),
                    'tahun_kejadian'    => intval($request->tahun_kejadian ?? 0),
                    'bulan_kejadian'    => $request->bulan_kejadian ? intval($request->bulan_kejadian) : null,
                    'tanggal_kejadian'  => $request->tanggal_kejadian ? intval($request->tanggal_kejadian) : null,
                    'sumber_dana'       => $request->sumber_dana,
                    'sumber_informasi'  => $request->sumber_informasi,
                    'source'            => 'PUSIKNAS_FORM',
                ],
            ]);

            $signatory = Officer::where('id', htmlspecialchars($request->signatory))->first();
            $document->officers()
                ->where('class', 'SIGNATORY')
                ->delete();
            $document->officers()->create([
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $document->id,
                'register_number' => $signatory->register_number,
                'first_title'     => $signatory->first_title,
                'first_name'      => $signatory->first_name,
                'last_name'       => $signatory->last_name,
                'last_title'      => $signatory->last_title,
                'rank_id'         => $signatory->rank_id,
                'position_id'     => $signatory->position_id,
                'phone_number'    => $signatory->phone_number,
                'email'           => $signatory->email,
                'police_id'       => $signatory->police_id,
                'status'          => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class'           => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag'            => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method'   => SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentOfficer::getEnumOption('flag', 'IMPORT'),
            ]);

            if ($isSuspectExists) {
                $document->suspects()->sync($request->suspects ?? []);
                $document->reportedPersons()->detach();
            } else {
                $document->suspects()->detach();
                $document->reportedPersons()->sync([$request->reportedPerson]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah data: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $document->suspects()->detach();
            $document->reportedPersons()->detach();
            $document->officers()->delete();
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function download($id)
    {
        $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::with([
            'officers',
            'suratPerintahPenyidikanDocument',
            'suspects',
            'reportedPersons',
            'documentClassification',
            'prosecutor.regency',
            'court',
            'suratPerintahTugasDocument.suratPerintahTugasDocumentOfficers'
        ])->where('id', $id)->first();

        if (!$suratPemberitahuanDimulainyaPenyidikanDocument) {
            $legacyDoc = \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::find($id);
            if ($legacyDoc) {
                return redirect()->route('doc.surat-pemberitahuan-dimulainya-penyidikan-document.download', [
                    'id' => $id,
                    'accident_id' => request()->query('accident_id'),
                    'document_category_id' => request()->query('document_category_id', '0204'),
                ]);
            }
            abort(404, 'Dokumen Surat Pemberitahuan Dimulainya Penyidikan tidak ditemukan.');
        }

        $accidentId = $suratPemberitahuanDimulainyaPenyidikanDocument->accident_id;
        $isSuspectExist = $suratPemberitahuanDimulainyaPenyidikanDocument->is_suspect_exists;
        
        $suspects = $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->get();
        $reportedPersons = $suratPemberitahuanDimulainyaPenyidikanDocument->reportedPersons()->get();

        $signatory = $suratPemberitahuanDimulainyaPenyidikanDocument->officers()->with(['position.positionCluster', 'rank'])->where('class', 'SIGNATORY')->first();
        if (!$signatory) {
            return redirect()->back()->with('error', 'Penandatangan belum diset.');
        }

        $accident = Accident::with(['polres.polda', 'police'])->where('id', $accidentId)->first();

        $tempQrCodePath = storage_path('images/qrcode-signature-' . $suratPemberitahuanDimulainyaPenyidikanDocument->id . '.png');
        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->merge(public_path('images/logo2x.png'), .2, true)
            ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=' . $suratPemberitahuanDimulainyaPenyidikanDocument->id, $tempQrCodePath);

        $signatoryPositionId = $signatory ? (is_array($signatory->position) ? ($signatory->position['id'] ?? null) : $signatory->position_id) : null;
        $signatoryPositionDetail = $signatoryPositionId
            ? \App\Models\Lib\Position::with('positionCluster')->find($signatoryPositionId)
            : null;

        $polresFullName = $accident->polres->full_name ?? '';
        $poldaFullName  = $accident->polres->polda->full_name ?? '';
        $signatoryHeadText     = 'a.n. KEPALA KEPOLISIAN RESOR ' . $polresFullName;
        $signatoryPositionName = '';
        if ($signatoryPositionDetail) {
            if ($signatoryPositionDetail->position_cluster_id == '1') {
                $signatoryHeadText     = 'KEPALA KEPOLISIAN RESOR ' . $polresFullName;
                $signatoryPositionName = '';
            } elseif ($signatoryPositionDetail->position_cluster_id == '9') {
                $signatoryHeadText     = 'a.n. DIREKTUR LALU LINTAS POLDA ' . $poldaFullName;
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? $signatoryPositionDetail->name;
            } else {
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? $signatoryPositionDetail->name;
            }
        }
        if (!$signatoryPositionName && $signatoryPositionDetail && $signatoryPositionDetail->position_cluster_id != '1') {
            $signatoryPositionName = 'KASAT LANTAS';
        }


        $hasParty = ($isSuspectExist && $suspects->isNotEmpty()) || (!$isSuspectExist && $reportedPersons->isNotEmpty());

        if ($hasParty) {
            $templateName = 'surat_pemberitahuan_dimulainya_penyidikan_2026.docx';
            // Cek apakah ada file temp_spdp_2026.docx yang sudah disinkronkan saat file utama terkunci oleh Word
            if (file_exists(public_path('word-template/temp_spdp_2026.docx'))) {
                if (@copy(public_path('word-template/temp_spdp_2026.docx'), public_path('word-template/surat_pemberitahuan_dimulainya_penyidikan_2026.docx'))) {
                    @unlink(public_path('word-template/temp_spdp_2026.docx'));
                } else {
                    $templateName = 'temp_spdp_2026.docx';
                }
            }
        } else {
            $templateName = 'surat_pemberitahuan_dimulainya_penyidikan_tanpa_tersangka_2026.docx';
        }

        $templatePath = public_path('word-template/' . $templateName);
        if (!file_exists($templatePath)) {
            $templatePath = base_path('public/word-template/' . $templateName);
        }
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $documentDate = \Carbon\Carbon::parse($suratPemberitahuanDimulainyaPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber = $suratPemberitahuanDimulainyaPenyidikanDocument->document_number;
        $appendix = $suratPemberitahuanDimulainyaPenyidikanDocument->appendix;

        $documentClassificationName = $suratPemberitahuanDimulainyaPenyidikanDocument->documentClassification->name ?? '';

        $accidentNumber = $accident->no_lp;
        $accidentDate = \Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');

        // LP Model A / B party clause logic (untuk SPDP Tanpa Tersangka / Poin 2)
        $pelaporKorbanText = '';
        $isLpModelA = str_contains(strtoupper($accidentNumber), '/A/') || str_contains(strtoupper($accidentNumber), '-A/');

        if ($isLpModelA) {
            // LP Model A: Penyidik/petugas polri yang menjadi pelapor
            $officerRank = $accident->rank_id ? trim($accident->rank_id) . ' ' : '';
            $officerName = trim(($accident->officer_first_name ?? '') . ' ' . ($accident->officer_last_name ?? ''));
            $officerFullName = trim($officerRank . $officerName);
            if (!empty($officerFullName)) {
                $pelaporKorbanText = ' atas nama pelapor ' . $officerFullName;
            }
        } else {
            // LP Model B: Pelapor masyarakat atau Korban
            $victimName = null;
            if ($accident->dors_id) {
                $victim = \App\Models\Stg\DorsVictim::where('dors_id', $accident->dors_id)
                    ->whereIn('status_korban', ['MD', 'LB', 'LR'])
                    ->first();
                if ($victim && !empty($victim->nama)) {
                    $victimName = trim($victim->nama);
                }
            }

            if (!$victimName && $accident->involvedPeoples) {
                $invVictim = $accident->involvedPeoples
                    ->whereIn('class', ['DRIVER', 'PASSENGER', 'PEDESTRIAN'])
                    ->first();
                if ($invVictim && !empty($invVictim->name)) {
                    $victimName = trim($invVictim->name);
                }
            }

            if ($victimName) {
                $pelaporKorbanText = ' atas nama korban ' . $victimName;
            } else {
                $reporter = \App\Models\ReportingPerson::where('accident_id', $accidentId)->first();
                if ($reporter && !empty($reporter->name)) {
                    $pelaporKorbanText = ' atas nama pelapor ' . trim($reporter->name);
                }
            }
        }

        $prosecutor = $suratPemberitahuanDimulainyaPenyidikanDocument->prosecutor;
        $prosecutorName = $prosecutor->name ?? $prosecutor->full_name ?? '';
        $prosecutorLocation = ucwords(strtolower($prosecutor->regency->name ?? ''));
        
        $court = $suratPemberitahuanDimulainyaPenyidikanDocument->court;
        $courtName = $court->name ?? '';

        // Suspect / Reported person logic (Tersangka vs Terlapor)
        $isSuspect = $isSuspectExist && $suspects->isNotEmpty();
        $partyTypeLabel = $isSuspect ? 'tersangka' : 'terlapor';
        $partyTypeLabelTitle = ucfirst($partyTypeLabel);
        $partyTypeLabelUpper = strtoupper($partyTypeLabel);

        $suspect = $isSuspect ? $suspects->first() : $reportedPersons->first();
        if ($suspect) {
            $relationsToLoad = [
                'gender', 'religion', 'job', 'country',
                'village', 'district', 'regency', 'province'
            ];
            if (method_exists($suspect, 'nationality')) {
                $relationsToLoad[] = 'nationality';
            }
            if (method_exists($suspect, 'suratKetetapanTentangPenetapanTersangkaDocument')) {
                $relationsToLoad[] = 'suratKetetapanTentangPenetapanTersangkaDocument';
            }
            $suspect->loadMissing($relationsToLoad);
        }

        $suspectProperties = $suspect ? ($suspect->properties ?? []) : [];
        $isUnknownBirthPlace = ($suspectProperties['is_unknown_birth_place'] ?? false) || ($suspect->is_unknown_birth_place ?? false);
        $isUnknownBirthDate  = ($suspectProperties['is_unknown_birth_date'] ?? false) || ($suspect->is_unknown_birth_date ?? false);
        $isUnknownGender     = ($suspectProperties['is_unknown_gender'] ?? false) || ($suspect->is_unknown_gender ?? false);
        $isUnknownAddress    = ($suspectProperties['is_unknown_address'] ?? false) || ($suspect->is_unknown_address ?? false);

        $suspectName = $suspect ? $suspect->name : '-';
        $suspectIdentityNo = $suspect ? ($suspect->identity_number ?? '-') : '-';

        $suspectBirthPlace = '-';
        if ($suspect && !empty($suspect->birth_place)) {
            $suspectBirthPlace = $isUnknownBirthPlace ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->birth_place));
        } elseif ($isUnknownBirthPlace) {
            $suspectBirthPlace = 'TIDAK DIKETAHUI';
        }

        $suspectBirthDate = '-';
        if ($suspect && !empty($suspect->birth_date)) {
            $suspectBirthDate = $isUnknownBirthDate ? 'TIDAK DIKETAHUI' : \Carbon\Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y');
        } elseif ($isUnknownBirthDate) {
            $suspectBirthDate = 'TIDAK DIKETAHUI';
        }

        $suspectGender = '-';
        if ($suspect && isset($suspect->gender->name)) {
            $suspectGender = $isUnknownGender ? 'TIDAK DIKETAHUI' : ucwords(strtolower($suspect->gender->name));
        } elseif ($isUnknownGender) {
            $suspectGender = 'TIDAK DIKETAHUI';
        }

        $suspectNationality = 'Indonesia';
        if ($suspect) {
            if (method_exists($suspect, 'nationality') && is_object($suspect->nationality)) {
                $suspectNationality = $suspect->nationality->name ?? 'Indonesia';
            } elseif (!empty($suspect->nationality) && !is_object($suspect->nationality)) {
                $suspectNationality = $suspect->nationality;
            } elseif ($suspect->country) {
                $rawCountry = $suspect->country->name ?? 'Indonesia';
                $suspectNationality = stripos($rawCountry, 'indone') !== false ? 'Indonesia' : ucwords(strtolower($rawCountry));
            } else {
                $suspectNationality = 'Indonesia';
            }
        }

        $suspectReligion = ($suspect && isset($suspect->religion->name)) ? ucwords(strtolower($suspect->religion->name)) : '-';
        $suspectJob = ($suspect && isset($suspect->job->name)) ? ucwords(strtolower($suspect->job->name)) : '-';

        $suspectFullAddress = '-';
        if ($suspect) {
            if ($isUnknownAddress) {
                $suspectFullAddress = 'TIDAK DIKETAHUI';
            } else {
                $village = $suspect->village->name ?? '';
                $district = $suspect->district->name ?? '';
                $regency = $suspect->regency->name ?? '';
                $province = $suspect->province->name ?? '';
                $addrParts = array_filter([$suspect->address, $village, $district, $regency, $province]);
                $suspectFullAddress = !empty($addrParts) ? ucwords(strtolower(implode(', ', $addrParts))) : '-';
            }
        }

        // SKPPT (Surat Ketetapan tentang Penetapan Tersangka)
        $skppt = $isSuspectExist && $suspect && method_exists($suspect, 'suratKetetapanTentangPenetapanTersangkaDocument')
            ? $suspect->suratKetetapanTentangPenetapanTersangkaDocument->first()
            : null;
        $skpptNumber = $skppt ? $skppt->document_number : '-';
        $skpptDate = $skppt ? \Carbon\Carbon::parse($skppt->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        // SP Penyidikan
        $sprindik = $suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument;
        $sprindikNumber = $sprindik ? $sprindik->document_number : '-';
        $sprindikDocumentDay = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('l') : '-';
        $sprindikDate = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->format('d') : '-';
        $sprindikMonth = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('F') : '-';
        $sprindikYear = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->format('Y') : '-';
        $sprindikFullDate = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        // SP Tugas (for Ketua Tim)
        $spt = $suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahTugasDocument;
        $ketuaTimName = '-';
        $ketuaTimPhone = '-';
        $sptNumber   = $spt ? $spt->document_number : '-';
        $sptFullDate = $spt ? \Carbon\Carbon::parse($spt->document_date)->locale('id')->translatedFormat('d F Y') : '-';
        if ($spt) {
            $ketuaTimOfficer = $spt->suratPerintahTugasDocumentOfficers()->where('class', 'LEADER')->first();
            if ($ketuaTimOfficer) {
                $ketuaTimName = \App\Helpers\PeopleNameHelper::getFullName($ketuaTimOfficer->first_title ?? '', $ketuaTimOfficer->first_name ?? '', $ketuaTimOfficer->last_name ?? '', $ketuaTimOfficer->last_title ?? '');
                $ketuaTimPhone = $ketuaTimOfficer->phone_number ?? '-';
            }
        }

        // Crime Constitution
        $crimeConstitutionText = $sprindik ? ($sprindik->pasal_formatted ?? '') : '';
        if (!$crimeConstitutionText && $sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws) {
            $crimeTexts = [];
            foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag == 'MAIN') {
                    $crimeTexts[] = ($law->constitution_chapter ?? '') . ' ' . ($law->crimeConstitution->name ?? '');
                } elseif ($law->flag == 'ADDITIONAL') {
                    $crimeTexts[] = $law->constitution ?? '';
                }
            }
            $crimeConstitutionText = implode(', ', array_filter($crimeTexts));
        }

        // Police Info
        $daerahPoliceFullName = strtoupper($accident->polres->polda->full_name ?? '');
        $resorPolice = $accident->polres;
        $resorPoliceAddress = $resorPolice ? ($resorPolice->address . ', ' . $resorPolice->polres_zipcode) : '';
        $resorPoliceFullName = $resorPolice ? ((in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name)) : '';
        $documentLocation = ucwords(strtolower($resorPolice->polres_regency ?? ($resorPolice->name ?? '')));

        $signatoryName = trim(implode(' ', array_filter([
            $signatory->first_title ?? '',
            $signatory->first_name ?? '',
            $signatory->last_name ?? '',
            $signatory->last_title ?? ''
        ])));
        $signatoryName = $signatoryName ?: '-';
        $signatoryRank = $signatory->rank ?? ($signatory->rank_id ? \App\Models\Lib\Rank::find($signatory->rank_id) : null);
        $signatoryRankName = $signatoryRank->full_name ?? ($signatoryRank->name ?? '');
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        $carbonCopies = $suratPemberitahuanDimulainyaPenyidikanDocument->carbon_copies ?? [];
        $no = 1;
        $blockCarbonCopies = [];
        foreach ($carbonCopies as $carbonCopy) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => $no,
                'carbon_copy_name' => $carbonCopy,
            ];
            $no++;
        }
        $templateProcessor->cloneBlock('block_carbon_copies', 0, true, false, $blockCarbonCopies);

        $templateProcessor->setValues([
            'daerahPoliceFullName' => $daerahPoliceFullName,
            'resorPoliceFullName' => $resorPoliceFullName,
            'resorPoliceAddress' => $resorPoliceAddress,
            'documentLocation' => $documentLocation,
            'documentDate' => $documentDate,
            'documentNumber' => $documentNumber,
            'documentClassificationName' => $documentClassificationName,
            'appendix' => $appendix,
            'prosecutorName' => $prosecutorName,
            'prosecutorLocation' => $prosecutorLocation,
            'accidentNumber' => $accidentNumber,
            'accidentDate' => $accidentDate,
            'suratPerintahPenyidikanDocumentNumber' => $sprindikNumber,
            'suratPerintahPenyidikanDocumentDocumentDate' => $sprindikFullDate,
            'SuratKetetapantentangPenetapanDocumentNumber' => $skpptNumber,
            'SuratKetetapantentangPenetapanDocumentDate' => $skpptDate,
            'SuratPerintahPenyidikanDay' => $sprindikDocumentDay,
            'SuratPerintahDate' => $sprindikDate,
            'SuratPerintahPenyidikanMonth' => $sprindikMonth,
            'SuratPerintahPenyidikanYear' => $sprindikYear,
            'SuratPerintahPenyidikanLawsDocument' => $crimeConstitutionText,
            'pelaporKorbanText' => $pelaporKorbanText,
            'partyTypeLabel' => $partyTypeLabel,
            'partyTypeLabelTitle' => $partyTypeLabelTitle,
            'partyTypeLabelUpper' => $partyTypeLabelUpper,
            'suspectName' => $suspectName,
            'suspectIdentityNo' => $suspectIdentityNo,
            'suspectBirthPlace' => $suspectBirthPlace,
            'suspectBirthDate' => $suspectBirthDate,
            'suspectGender' => $suspectGender,
            'suspectNationality' => $suspectNationality,
            'suspectReligion' => $suspectReligion,
            'suspectJob' => $suspectJob,
            'suspectFullAddress' => $suspectFullAddress,
            'KetuaTimPenyidik' => $ketuaTimName,
            'KetuaTimPenyidikPhoneNumber' => $ketuaTimPhone,
            'courtName' => $courtName,
            'signatoryHeadText' => $signatoryHeadText,
            'signatoryPositionName' => $signatoryPositionName,
            'signatoryPositionHeadText' => $signatoryPositionName,
            'signatoryName' => strtoupper($signatoryName),
            'signatoryRankName' => strtoupper($signatoryRankName),
            'signatoryRegisterNumber' => $signatoryRegisterNumber,
            'SuratPerintahTugasPenyidikanNumber' => $sptNumber,
            'SuratPerintahTugasPenyidikanDate'   => $sptFullDate,
        ]);

        $templateProcessor->setImageValue('QRCodeImage', [
            'path' => $tempQrCodePath,
            'width' => 111,
            'height' => 111,
        ]);

        $filename = 'generate/' . $suratPemberitahuanDimulainyaPenyidikanDocument->id . ' - SPDP Pusiknas - ' . ($accident->polres->full_name ?? '');
        $templateProcessor->saveAs(public_path($filename . '.docx'));
        return response()->download(public_path($filename . '.docx'))->deleteFileAfterSend(true);
    }

    public function validateRequestForm(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::where('id', $accidentId)->first();
        $validator  = $this->validateForm($request, $accident);

        if ($validator->fails()) {
            return response()->json([
                'code'    => '422',
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data valid, dokumen siap disimpan.',
        ]);
    }

    private function validateForm(Request $request, $accident = null)
    {
        return Validator::make($request->all(), [
            'documentNumber'                  => 'required|min:5|max:255|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*\/).+$/',
            'documentDate'                    => 'required',
            'documentClassification'          => 'required',
            'suratPerintahPenyidikanDocument' => 'required',
            'suratPerintahTugasDocument'      => 'required',
            'isSuspectExists'                 => 'required',
            'prosecutor'                      => 'required',
            'court'                           => 'required',
            'appendix'                        => 'required|numeric|max:999|min:1',
            'signatory'                       => 'required',
            'suspects'                        => 'required_if:isSuspectExists,true',
            'reportedPerson'                  => [
                Rule::requiredIf(function () use ($request) {
                    return $request->specialInfo != 'TABRAK_LARI' && $request->isSuspectExists != 'true';
                })
            ],
            'isLegacy'                        => [
                function ($attribute, $value, $fail) use ($accident) {
                    if (empty($value)) return;
                    $reportDate      = strtotime($accident->report_date);
                    $cutoff          = strtotime('2024-01-01');
                    $isWhitelisted   = $accident->police->is_whitelisted_document_legacy ?? false;
                    $startWhitelist  = strtotime($accident->police->start_date_whitelisted_document_legacy ?? '1900-01-01');
                    $endWhitelist    = strtotime($accident->police->end_date_whitelisted_document_legacy ?? '2100-01-01');
                    $isValid         = $reportDate < $cutoff || ($isWhitelisted && $startWhitelist <= $reportDate && $reportDate <= $endWhitelist);
                    if (!$isValid) {
                        $fail('Keterangan dokumen legacy tidak valid, tanggal laporan harus sebelum 2024 atau termasuk dalam rentang waktu yang diizinkan.');
                    }
                }
            ],
            'kode_wilayah'                    => 'required|string',
            'carbonCopies'                    => 'required',
            'carbonCopies.*'                  => 'required',
        ], [
            'documentNumber.required'                  => 'Mohon mengisi Nomor Dokumen.',
            'documentNumber.max'                       => 'No Dokumen maksimal 255 karakter.',
            'documentNumber.min'                       => 'No Dokumen harus lengkap.',
            'documentNumber.regex'                     => 'No Dokumen harus lengkap.',
            'documentDate.required'                    => 'Mohon mengisi Tanggal Surat.',
            'documentClassification.required'          => 'Mohon mengisi Klasifikasi Surat.',
            'suratPerintahPenyidikanDocument.required' => 'Mohon mengisi Surat Perintah Penyidikan.',
            'suratPerintahTugasDocument.required'      => 'Mohon mengisi Surat Perintah Tugas.',
            'isSuspectExists.required'                 => 'Mohon mengisi Apakah tersangka ada.',
            'prosecutor.required'                      => 'Mohon mengisi Kejaksaan.',
            'court.required'                           => 'Mohon mengisi Pengadilan.',
            'appendix.required'                        => 'Mohon mengisi Lampiran.',
            'appendix.numeric'                         => 'Lampiran harus berupa angka.',
            'appendix.max'                             => 'Lampiran maksimal 999.',
            'appendix.min'                             => 'Lampiran minimal 1.',
            'signatory.required'                       => 'Mohon mengisi Penandatangan.',
            'suspects.required_if'                     => 'Mohon mengisi Tersangka.',
            'reportedPerson.required'                  => 'Mohon mengisi Terlapor.',
            'kode_wilayah.required'                    => 'Mohon mengisi Kode Wilayah Kejadian.',
            'carbonCopies'                             => 'Mohon mengisi Tembusan.',
            'carbonCopies.*'                           => 'Mohon Jangan Kosongkan Isi Tembusan, Hapus Jika Memang Tidak Ada.',
        ]);
    }
}
