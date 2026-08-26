<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer;
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

class SpdpPusiknasDocumentController extends Controller
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


        return view('docs.spdp-pusiknas-document.create', compact(
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

        ));
    }

    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->accident_id);
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

        $exists = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Ada Sebelumnya.');
        }

        DB::beginTransaction();
        try {
            
            $doc = SuratPemberitahuanDimulainyaPenyidikanDocument::create([
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
                // Field tambahan SPPT-TI (simpan di kolom description / messages jika ada)
                'description'                          => $uraianSingkatPerkara,
                'messages'                             => [
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

            $docId = $doc->id;

            // Penandatangan
            $signatory = Officer::where('id', $signatoryId)->first();
            $doc->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()->create([
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
                'status'          => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class'           => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag'            => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method'   => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'IMPORT'),
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
        $document   = SuratPemberitahuanDimulainyaPenyidikanDocument::withRelated()->where('id', $id)->first();
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        return view('docs.spdp-pusiknas-document.show', compact(
            'accidentId',
            'accident',
            'document'
        ));
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = SuratPemberitahuanDimulainyaPenyidikanDocument::with([
            'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers',
            'suratPerintahPenyidikanDocument',
            'suspects',
        ])->where('id', $id)->first();
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)->get();

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

        return view('docs.spdp-pusiknas-document.edit', compact(
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

            'selectedSuspects'
        ));
    }

    public function update(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->accident_id);
        $accident   = Accident::where('id', $accidentId)->first();

        $validator = $this->validateForm($request, $accident);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $document = SuratPemberitahuanDimulainyaPenyidikanDocument::where('id', $id)->firstOrFail();

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
                'description'                          => $request->uraianSingkatPerkara,
                'messages'                             => [
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
            $document->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()
                ->where('class', 'SIGNATORY')
                ->delete();
            $document->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()->create([
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
                'status'          => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class'           => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag'            => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method'   => SuratPemberitahuanDimulainyaPenyidikanDocumentOfficer::getEnumOption('flag', 'IMPORT'),
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
        $document   = SuratPemberitahuanDimulainyaPenyidikanDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $document->suspects()->detach();
            $document->reportedPersons()->detach();
            $document->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers()->delete();
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
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
                'errors'  => $validator->errors()->all(),
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
            'documentNumber'                => 'required|string|max:255|min:3',
            'documentDate'                  => 'required|date_format:Y-m-d',
            'documentClassification'        => 'required',
            'suratPerintahPenyidikanDocument' => 'required',
            'suratPerintahTugasDocument'    => 'required',
            'isSuspectExists'               => 'required',
            'prosecutor'                    => 'required',
            'court'                         => 'required',
            'appendix'                      => 'required|numeric|min:1|max:999',
            'signatory'                     => 'required',
            'uraianSingkatPerkara'          => 'nullable|string',
            'lokasi_kejadian'               => 'nullable|string',
            'kode_wilayah'                  => 'required|string',
            'suspects'                      => 'required_if:isSuspectExists,true',
            'carbonCopies'                  => 'required|array|min:1',
            'carbonCopies.*'                => 'required|string',
        ], [
            'documentNumber.required'              => 'Mohon mengisi Nomor Dokumen.',
            'documentDate.required'                => 'Mohon mengisi Tanggal Surat.',
            'documentClassification.required'      => 'Mohon mengisi Klasifikasi Surat.',
            'suratPerintahPenyidikanDocument.required' => 'Mohon mengisi Surat Perintah Penyidikan.',
            'suratPerintahTugasDocument.required'  => 'Mohon mengisi Surat Perintah Tugas.',
            'isSuspectExists.required'             => 'Mohon memilih status Tersangka.',
            'prosecutor.required'                  => 'Mohon mengisi Kejaksaan.',
            'court.required'                       => 'Mohon mengisi Pengadilan.',
            'appendix.required'                    => 'Mohon mengisi jumlah Lampiran.',
            'signatory.required'                   => 'Mohon mengisi Penandatangan.',
            'kode_wilayah.required'                => 'Mohon mengisi Kode Wilayah Kejadian.',
            'suspects.required_if'                 => 'Mohon mengisi Tersangka.',
            'carbonCopies.required'                => 'Mohon mengisi Tembusan.',
            'carbonCopies.min'                     => 'Mohon mengisi minimal 1 Tembusan.',
            'carbonCopies.*.required'              => 'Tembusan tidak boleh kosong.',
        ]);
    }
}
