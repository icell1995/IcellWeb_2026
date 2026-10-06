<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocument;
use App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocumentOfficer;
use App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocumentAttachment;

use App\Traits\DocsOfficersTraits;

class SuratPerintahPenangguhanPenahananDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected DocService $docService;

    // Master jenis jaminan sesuai standar SPPT-TI (kode_lainnya.json: 1 = Jaminan Uang, 2 = Jaminan Orang)
    public static $masterJenisJaminan = [
        1 => 'Jaminan Uang',
        2 => 'Jaminan Orang',
    ];

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Tampilan form pembuatan Surat Perintah Penangguhan Penahanan (S-18)
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // Ambil dokumen SPDP yang sudah ada untuk perkara ini
        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        // Ambil dokumen Sprindik
        $sprindikDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        // Ambil dokumen Penetapan Tersangka
        $penetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        // Ambil dokumen Surat Perintah Penahanan (S-17)
        $penahananDocuments = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        // Daftar Tersangka di perkara ini
        $suspects = Suspect::with(['gender', 'education', 'job', 'religion', 'maritalStatus', 'country', 'location'])
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        // Daftar Pejabat Penandatangan (Signatory)
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

        // Daftar Petugas yang Diperintahkan (Internal Members)
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

        // Master jenis jaminan
        $masterJenisJaminan = self::$masterJenisJaminan;

        // Kode Satker polres penerbit sesuai polres pada nomor LP
        $polresPenerbit = Police::find($accident->polres_id);
        $kodeSatkerDefault = $polresPenerbit->satker_code ?? ($accident->polres->satker_code ?? '006.09.05');

        // SPDP info (Nomor & Tanggal SPDP dibuat readonly)
        $spdpLatest = $spdpDocuments->sortByDesc('id')->first();
        $nomorSpdp = $spdpLatest->document_number ?? ($spdpLatest->nomor ?? '');
        $tanggalSpdp = $spdpLatest->document_date ?? ($spdpLatest->tanggal ?? '');

        // S-17 info (Nomor & Tanggal Surat Perintah Penahanan dibuat readonly)
        $s17Latest = $penahananDocuments->sortByDesc('id')->first();
        $nomorSuratPerintahPenahanan = $s17Latest->nomor ?? ($s17Latest->document_number ?? '');
        $tanggalSuratPerintahPenahanan = $s17Latest->tanggal ?? ($s17Latest->document_date ?? '');

        // Pasal disangkakan otomatis diambil dari Surat Perintah Penyidikan
        $sprindikLatest = $sprindikDocuments->sortByDesc('id')->first();
        $pasalList = [];
        if ($sprindikLatest && $sprindikLatest->suratPerintahPenyidikanDocumentLaws) {
            foreach ($sprindikLatest->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag == 'MAIN') {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $cName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalList[] = trim($chapter . ' ' . $cName);
                } else {
                    $pasalList[] = trim($law->constitution ?? '');
                }
            }
        }
        $pasalList = array_values(array_filter($pasalList));
        if (empty($pasalList)) {
            $pasalList = ['Pasal 109 ayat (1) KUHAP'];
        }

        $leaderOfficers = $internalOfficers;

        return view('docs.surat-perintah-penangguhan-penahanan-document.create', compact(
            'accidentId',
            'accident',
            'spdpDocuments',
            'sprindikDocuments',
            'penetapanTersangkaDocuments',
            'penahananDocuments',
            'suspects',
            'authorizedSignatories',
            'internalOfficers',
            'leaderOfficers',
            'masterJenisJaminan',
            'kodeSatkerDefault',
            'nomorSpdp',
            'tanggalSpdp',
            'nomorSuratPerintahPenahanan',
            'tanggalSuratPerintahPenahanan',
            'pasalList'
        ));
    }

    /**
     * Simpan dokumen Surat Perintah Penangguhan Penahanan
     */
    public function store(Request $request)
    {
        $this->validateForm($request)->validate();

        $accidentId = $request->accident_id;
        $accident = Accident::findOrFail($accidentId);

        DB::beginTransaction();
        try {
            $sprindikLatest = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sketLatest = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)->latest()->first();
            $s17Latest = SuratPerintahPenahananDocument::where('accident_id', $accidentId)->latest()->first();

            $document = new SuratPerintahPenangguhanPenahananDocument();
            $document->accident_id = $accidentId;
            $document->surat_perintah_penyidikan_document_id = $sprindikLatest ? $sprindikLatest->id : null;
            $document->surat_ketetapan_penetapan_tersangka_id = $sketLatest ? $sketLatest->id : null;
            $document->surat_perintah_penahanan_document_id = $s17Latest ? $s17Latest->id : null;

            // Identitas Dokumen
            $document->nomor = $request->nomor;
            $document->tanggal = $request->tanggal;
            $document->document_number = $request->nomor;
            $document->document_date = $request->tanggal;
            $document->nomor_spdp = $request->nomor_spdp;
            $document->tanggal_spdp = $request->tanggal_spdp;
            $document->kode_satker_penerbit_spdp = $request->kode_satker_penerbit_spdp;
            $document->nomor_surat_perintah_penahanan = $request->nomor_surat_perintah_penahanan;
            $document->tanggal_surat_permohonan = $request->tanggal_surat_permohonan;

            // Poin 9 & 10 (bila ada) terpisah
            $tidakAdaPoin9 = $request->has('tidak_ada_surat_perpanjangan_penahanan') || $request->boolean('tidak_ada_surat_perpanjangan_penahanan');
            $tidakAdaPoin10 = $request->has('tidak_ada_sprin_perpanjangan_penahanan') || $request->boolean('tidak_ada_sprin_perpanjangan_penahanan');

            if ($tidakAdaPoin9) {
                $document->has_surat_perpanjangan_penahanan = false;
                $document->nomor_surat_perpanjangan_penahanan = null;
                $document->tanggal_surat_perpanjangan_penahanan = null;
            } else {
                $hasInputPoin9 = !empty($request->nomor_surat_perpanjangan_penahanan);
                $document->has_surat_perpanjangan_penahanan = $hasInputPoin9 || !$tidakAdaPoin9;
                $document->nomor_surat_perpanjangan_penahanan = $request->nomor_surat_perpanjangan_penahanan;
                $document->tanggal_surat_perpanjangan_penahanan = $request->tanggal_surat_perpanjangan_penahanan;
            }

            if ($tidakAdaPoin10) {
                $document->has_sprin_perpanjangan_penahanan = false;
                $document->nomor_sprin_perpanjangan_penahanan = null;
                $document->tanggal_sprin_perpanjangan_penahanan = null;
            } else {
                $hasInputPoin10 = !empty($request->nomor_sprin_perpanjangan_penahanan);
                $document->has_sprin_perpanjangan_penahanan = $hasInputPoin10 || !$tidakAdaPoin10;
                $document->nomor_sprin_perpanjangan_penahanan = $request->nomor_sprin_perpanjangan_penahanan;
                $document->tanggal_sprin_perpanjangan_penahanan = $request->tanggal_sprin_perpanjangan_penahanan;
            }

            $document->has_perpanjangan_penahanan = $document->has_surat_perpanjangan_penahanan || $document->has_sprin_perpanjangan_penahanan;

            // Konten Jaminan
            $document->jenis_jaminan = $request->jenis_jaminan ? intval($request->jenis_jaminan) : null;
            $document->besaran_uang_jaminan = $request->besaran_uang_jaminan ? floatval(str_replace(['.', ','], ['', '.'], $request->besaran_uang_jaminan)) : null;
            $document->nomor_identitas_penjamin = $request->nomor_identitas_penjamin;
            $document->nama_penjamin = $request->nama_penjamin;
            $document->alamat_penjamin = $request->alamat_penjamin;
            $document->lokasi_penyimpanan_jaminan = $request->lokasi_penyimpanan_jaminan;

            $document->status_id = '2'; // Status DRAF
            $document->document_category_id = '0603';
            $document->created_by_user_id = Auth::id();

            // Simpan daftar pasal dan SKET jaminan/tangguhan ke messages
            $pasalList = $request->input('pasal_disangkakan', []);
            if (empty($pasalList)) {
                $pasalList = ['Pasal 109 ayat (1) KUHAP'];
            }
            $document->messages = [
                'daftar_uu_pasal' => $pasalList,
                'nomor_sket_uang_jaminan' => $request->nomor_sket_uang_jaminan,
                'tanggal_sket_uang_jaminan' => $request->tanggal_sket_uang_jaminan,
                'nomor_sket_uang_tangguhan' => $request->nomor_sket_uang_tangguhan,
                'tanggal_sket_uang_tangguhan' => $request->tanggal_sket_uang_tangguhan,
            ];

            $document->save();

            // Simpan pivot Tersangka
            if ($request->has('suspects')) {
                $document->suspects()->sync($request->suspects);
            }

            // Simpan Pejabat Penandatangan Resmi (Signatory)
            if ($request->has('signatory')) {
                $signatoryOfficer = Officer::find($request->signatory);
                if ($signatoryOfficer) {
                    $docOfficer = new SuratPerintahPenangguhanPenahananDocumentOfficer();
                    $docOfficer->surat_perintah_penangguhan_penahanan_document_id = $document->id;
                    $docOfficer->sort = 1;
                    $docOfficer->register_number = $signatoryOfficer->register_number;
                    $docOfficer->first_title = $signatoryOfficer->first_title;
                    $docOfficer->first_name = $signatoryOfficer->first_name;
                    $docOfficer->last_name = $signatoryOfficer->last_name;
                    $docOfficer->last_title = $signatoryOfficer->last_title;
                    $docOfficer->police_id = $signatoryOfficer->police_id;
                    $docOfficer->rank_id = $signatoryOfficer->rank_id;
                    $docOfficer->position_id = $signatoryOfficer->position_id;
                    $docOfficer->class = 'SIGNATORY';
                    $docOfficer->flag = 'INTERNAL';
                    $docOfficer->status = 'PRESENT';
                    $docOfficer->save();
                }
            }

            // Simpan Ketua Tim (Leader Officer)
            if ($request->officerLeader) {
                $leaderOfficer = Officer::with(['rank', 'position'])->where('id', $request->officerLeader)
                    ->orWhere('register_number', $request->officerLeader)
                    ->first();
                if ($leaderOfficer) {
                    $docOfficer = new SuratPerintahPenangguhanPenahananDocumentOfficer();
                    $docOfficer->surat_perintah_penangguhan_penahanan_document_id = $document->id;
                    $docOfficer->sort = 1;
                    $docOfficer->register_number = $leaderOfficer->register_number;
                    $docOfficer->first_title = $leaderOfficer->first_title;
                    $docOfficer->first_name = $leaderOfficer->first_name;
                    $docOfficer->last_name = $leaderOfficer->last_name;
                    $docOfficer->last_title = $leaderOfficer->last_title;
                    $docOfficer->phone_number = $leaderOfficer->phone_number;
                    $docOfficer->email = $leaderOfficer->email;
                    $docOfficer->police_id = $leaderOfficer->police_id;
                    $docOfficer->rank_id = $leaderOfficer->rank_id;
                    $docOfficer->position_id = $leaderOfficer->position_id;
                    $docOfficer->class = 'LEADER';
                    $docOfficer->flag = 'INTERNAL';
                    $docOfficer->status = 'PRESENT';
                    $docOfficer->save();
                }
            }

            // Simpan Petugas yang Diperintahkan (Members)
            if ($request->has('officers') && is_array($request->officers)) {
                $sort = 2;
                foreach ($request->officers as $officerId) {
                    $memberOfficer = Officer::find($officerId);
                    if ($memberOfficer) {
                        $docOfficer = new SuratPerintahPenangguhanPenahananDocumentOfficer();
                        $docOfficer->surat_perintah_penangguhan_penahanan_document_id = $document->id;
                        $docOfficer->sort = $sort++;
                        $docOfficer->register_number = $memberOfficer->register_number;
                        $docOfficer->first_title = $memberOfficer->first_title;
                        $docOfficer->first_name = $memberOfficer->first_name;
                        $docOfficer->last_name = $memberOfficer->last_name;
                        $docOfficer->last_title = $memberOfficer->last_title;
                        $docOfficer->police_id = $memberOfficer->police_id;
                        $docOfficer->rank_id = $memberOfficer->rank_id;
                        $docOfficer->position_id = $memberOfficer->position_id;
                        $docOfficer->class = 'MEMBER';
                        $docOfficer->flag = 'INTERNAL';
                        $docOfficer->status = 'PRESENT';
                        $docOfficer->save();
                    }
                }
            }

            // Generate payload SPP-TI format JSON
            $document->refresh();
            $document->payload = $this->generateJsonPayload($document);
            $document->save();

            DB::commit();

            return redirect()->route('view_produktivitas_accident', [
                'accident_id' => $accidentId,
            ])->with('success', 'Surat Perintah Penangguhan Penahanan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan form edit Surat Perintah Penangguhan Penahanan
     */
    public function edit(string $id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $document = SuratPerintahPenangguhanPenahananDocument::with([
            'suspects',
            'suratPerintahPenangguhanPenahananDocumentOfficers',
            'signatory',
            'memberOfficers'
        ])->where('id', $id)->firstOrFail();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $sprindikDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $penetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $penahananDocuments = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suspects = Suspect::with(['gender', 'education', 'job', 'religion', 'maritalStatus', 'country', 'location'])
            ->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
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

        $masterJenisJaminan = self::$masterJenisJaminan;

        $polresPenerbit = Police::find($accident->polres_id);
        $kodeSatkerDefault = $document->kode_satker_penerbit_spdp ?: ($polresPenerbit->satker_code ?? ($accident->polres->satker_code ?? '006.09.05'));

        $spdpLatest = $spdpDocuments->sortByDesc('id')->first();
        $nomorSpdp = $document->nomor_spdp ?: ($spdpLatest->document_number ?? ($spdpLatest->nomor ?? ''));
        $tanggalSpdp = $document->tanggal_spdp ?: ($spdpLatest->document_date ?? ($spdpLatest->tanggal ?? ''));

        $s17Latest = $penahananDocuments->sortByDesc('id')->first();
        $nomorSuratPerintahPenahanan = $document->nomor_surat_perintah_penahanan ?: ($s17Latest->nomor ?? ($s17Latest->document_number ?? ''));
        $tanggalSuratPerintahPenahanan = $s17Latest->tanggal ?? ($s17Latest->document_date ?? '');

        $pasalList = $document->messages['daftar_uu_pasal'] ?? [];
        if (empty($pasalList)) {
            $sprindikLatest = $sprindikDocuments->sortByDesc('id')->first();
            if ($sprindikLatest && $sprindikLatest->suratPerintahPenyidikanDocumentLaws) {
                foreach ($sprindikLatest->suratPerintahPenyidikanDocumentLaws as $law) {
                    if ($law->flag == 'MAIN') {
                        $chapter = trim($law->constitution_chapter ?? '');
                        $cName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                        $pasalList[] = trim($chapter . ' ' . $cName);
                    } else {
                        $pasalList[] = trim($law->constitution ?? '');
                    }
                }
            }
            $pasalList = array_values(array_filter($pasalList));
            if (empty($pasalList)) {
                $pasalList = ['Pasal 109 ayat (1) KUHAP'];
            }
        }

        $leaderOfficers = $internalOfficers;
        $currentLeader = $document->suratPerintahPenangguhanPenahananDocumentOfficers->where('class', 'LEADER')->first();
        $currentLeaderVal = $currentLeader ? (Officer::where('register_number', $currentLeader->register_number)->value('id') ?? $currentLeader->register_number) : '';

        $selectedSuspectIds = $document->suspects->pluck('id')->toArray();
        $selectedSignatoryId = $document->signatory ? Officer::where('register_number', $document->signatory->register_number)->value('id') : null;
        $selectedMemberIds = [];
        foreach ($document->memberOfficers as $m) {
            $offId = Officer::where('register_number', $m->register_number)->value('id');
            if ($offId) $selectedMemberIds[] = $offId;
        }

        $messages = $document->messages ?? [];
        $nomorSketUangJaminan = $messages['nomor_sket_uang_jaminan'] ?? '';
        $tanggalSketUangJaminan = $messages['tanggal_sket_uang_jaminan'] ?? '';
        $tidakAdaSketUangJaminan = empty($nomorSketUangJaminan);

        $nomorSketUangTangguhan = $messages['nomor_sket_uang_tangguhan'] ?? '';
        $tanggalSketUangTangguhan = $messages['tanggal_sket_uang_tangguhan'] ?? '';
        $tidakAdaSketUangTangguhan = empty($nomorSketUangTangguhan);

        return view('docs.surat-perintah-penangguhan-penahanan-document.edit', compact(
            'id',
            'document',
            'accidentId',
            'accident',
            'spdpDocuments',
            'sprindikDocuments',
            'penetapanTersangkaDocuments',
            'penahananDocuments',
            'suspects',
            'authorizedSignatories',
            'internalOfficers',
            'leaderOfficers',
            'currentLeaderVal',
            'masterJenisJaminan',
            'kodeSatkerDefault',
            'nomorSpdp',
            'tanggalSpdp',
            'nomorSuratPerintahPenahanan',
            'tanggalSuratPerintahPenahanan',
            'pasalList',
            'selectedSuspectIds',
            'selectedSignatoryId',
            'selectedMemberIds',
            'nomorSketUangJaminan',
            'tanggalSketUangJaminan',
            'tidakAdaSketUangJaminan',
            'nomorSketUangTangguhan',
            'tanggalSketUangTangguhan',
            'tidakAdaSketUangTangguhan'
        ));
    }

    /**
     * Update dokumen Surat Perintah Penangguhan Penahanan
     */
    public function update(Request $request, string $id)
    {
        $this->validateForm($request)->validate();

        $accidentId = $request->accident_id;
        $accident = Accident::findOrFail($accidentId);

        $document = SuratPerintahPenangguhanPenahananDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $document->nomor = $request->nomor;
            $document->tanggal = $request->tanggal;
            $document->document_number = $request->nomor;
            $document->document_date = $request->tanggal;
            $document->nomor_spdp = $request->nomor_spdp;
            $document->tanggal_spdp = $request->tanggal_spdp;
            $document->kode_satker_penerbit_spdp = $request->kode_satker_penerbit_spdp;
            $document->nomor_surat_perintah_penahanan = $request->nomor_surat_perintah_penahanan;
            $document->tanggal_surat_permohonan = $request->tanggal_surat_permohonan;

            // Poin 9 & 10 (bila ada) terpisah
            $tidakAdaPoin9 = $request->has('tidak_ada_surat_perpanjangan_penahanan') || $request->boolean('tidak_ada_surat_perpanjangan_penahanan');
            $tidakAdaPoin10 = $request->has('tidak_ada_sprin_perpanjangan_penahanan') || $request->boolean('tidak_ada_sprin_perpanjangan_penahanan');

            if ($tidakAdaPoin9) {
                $document->has_surat_perpanjangan_penahanan = false;
                $document->nomor_surat_perpanjangan_penahanan = null;
                $document->tanggal_surat_perpanjangan_penahanan = null;
            } else {
                $hasInputPoin9 = !empty($request->nomor_surat_perpanjangan_penahanan);
                $document->has_surat_perpanjangan_penahanan = $hasInputPoin9 || !$tidakAdaPoin9;
                $document->nomor_surat_perpanjangan_penahanan = $request->nomor_surat_perpanjangan_penahanan;
                $document->tanggal_surat_perpanjangan_penahanan = $request->tanggal_surat_perpanjangan_penahanan;
            }

            if ($tidakAdaPoin10) {
                $document->has_sprin_perpanjangan_penahanan = false;
                $document->nomor_sprin_perpanjangan_penahanan = null;
                $document->tanggal_sprin_perpanjangan_penahanan = null;
            } else {
                $hasInputPoin10 = !empty($request->nomor_sprin_perpanjangan_penahanan);
                $document->has_sprin_perpanjangan_penahanan = $hasInputPoin10 || !$tidakAdaPoin10;
                $document->nomor_sprin_perpanjangan_penahanan = $request->nomor_sprin_perpanjangan_penahanan;
                $document->tanggal_sprin_perpanjangan_penahanan = $request->tanggal_sprin_perpanjangan_penahanan;
            }

            $document->has_perpanjangan_penahanan = $document->has_surat_perpanjangan_penahanan || $document->has_sprin_perpanjangan_penahanan;

            $document->jenis_jaminan = $request->jenis_jaminan ? intval($request->jenis_jaminan) : null;
            $document->besaran_uang_jaminan = $request->besaran_uang_jaminan ? floatval(str_replace(['.', ','], ['', '.'], $request->besaran_uang_jaminan)) : null;
            $document->nomor_identitas_penjamin = $request->nomor_identitas_penjamin;
            $document->nama_penjamin = $request->nama_penjamin;
            $document->alamat_penjamin = $request->alamat_penjamin;
            $document->lokasi_penyimpanan_jaminan = $request->lokasi_penyimpanan_jaminan;

            $document->updated_by_user_id = Auth::id();

            $pasalList = $request->input('pasal_disangkakan', []);
            if (empty($pasalList)) {
                $pasalList = ['Pasal 109 ayat (1) KUHAP'];
            }
            $messages = $document->messages ?? [];
            $messages['daftar_uu_pasal'] = $pasalList;
            $messages['nomor_sket_uang_jaminan'] = $request->nomor_sket_uang_jaminan;
            $messages['tanggal_sket_uang_jaminan'] = $request->tanggal_sket_uang_jaminan;
            $messages['nomor_sket_uang_tangguhan'] = $request->nomor_sket_uang_tangguhan;
            $messages['tanggal_sket_uang_tangguhan'] = $request->tanggal_sket_uang_tangguhan;
            $document->messages = $messages;

            $document->save();

            // Sync suspects
            if ($request->has('suspects')) {
                $document->suspects()->sync($request->suspects);
            }

            // Sync officers
            SuratPerintahPenangguhanPenahananDocumentOfficer::where('surat_perintah_penangguhan_penahanan_document_id', $document->id)->delete();

            if ($request->has('signatory')) {
                $signatoryOfficer = Officer::find($request->signatory);
                if ($signatoryOfficer) {
                    $docOfficer = new SuratPerintahPenangguhanPenahananDocumentOfficer();
                    $docOfficer->surat_perintah_penangguhan_penahanan_document_id = $document->id;
                    $docOfficer->sort = 1;
                    $docOfficer->register_number = $signatoryOfficer->register_number;
                    $docOfficer->first_title = $signatoryOfficer->first_title;
                    $docOfficer->first_name = $signatoryOfficer->first_name;
                    $docOfficer->last_name = $signatoryOfficer->last_name;
                    $docOfficer->last_title = $signatoryOfficer->last_title;
                    $docOfficer->police_id = $signatoryOfficer->police_id;
                    $docOfficer->rank_id = $signatoryOfficer->rank_id;
                    $docOfficer->position_id = $signatoryOfficer->position_id;
                    $docOfficer->class = 'SIGNATORY';
                    $docOfficer->flag = 'INTERNAL';
                    $docOfficer->status = 'PRESENT';
                    $docOfficer->save();
                }
            }

            // Simpan Ketua Tim (Leader Officer)
            if ($request->officerLeader) {
                $leaderOfficer = Officer::with(['rank', 'position'])->where('id', $request->officerLeader)
                    ->orWhere('register_number', $request->officerLeader)
                    ->first();
                if ($leaderOfficer) {
                    $docOfficer = new SuratPerintahPenangguhanPenahananDocumentOfficer();
                    $docOfficer->surat_perintah_penangguhan_penahanan_document_id = $document->id;
                    $docOfficer->sort = 1;
                    $docOfficer->register_number = $leaderOfficer->register_number;
                    $docOfficer->first_title = $leaderOfficer->first_title;
                    $docOfficer->first_name = $leaderOfficer->first_name;
                    $docOfficer->last_name = $leaderOfficer->last_name;
                    $docOfficer->last_title = $leaderOfficer->last_title;
                    $docOfficer->phone_number = $leaderOfficer->phone_number;
                    $docOfficer->email = $leaderOfficer->email;
                    $docOfficer->police_id = $leaderOfficer->police_id;
                    $docOfficer->rank_id = $leaderOfficer->rank_id;
                    $docOfficer->position_id = $leaderOfficer->position_id;
                    $docOfficer->class = 'LEADER';
                    $docOfficer->flag = 'INTERNAL';
                    $docOfficer->status = 'PRESENT';
                    $docOfficer->save();
                }
            }

            if ($request->has('officers') && is_array($request->officers)) {
                $sort = 2;
                foreach ($request->officers as $officerId) {
                    $memberOfficer = Officer::find($officerId);
                    if ($memberOfficer) {
                        $docOfficer = new SuratPerintahPenangguhanPenahananDocumentOfficer();
                        $docOfficer->surat_perintah_penangguhan_penahanan_document_id = $document->id;
                        $docOfficer->sort = $sort++;
                        $docOfficer->register_number = $memberOfficer->register_number;
                        $docOfficer->first_title = $memberOfficer->first_title;
                        $docOfficer->first_name = $memberOfficer->first_name;
                        $docOfficer->last_name = $memberOfficer->last_name;
                        $docOfficer->last_title = $memberOfficer->last_title;
                        $docOfficer->police_id = $memberOfficer->police_id;
                        $docOfficer->rank_id = $memberOfficer->rank_id;
                        $docOfficer->position_id = $memberOfficer->position_id;
                        $docOfficer->class = 'MEMBER';
                        $docOfficer->flag = 'INTERNAL';
                        $docOfficer->status = 'PRESENT';
                        $docOfficer->save();
                    }
                }
            }

            $document->refresh();
            $document->payload = $this->generateJsonPayload($document);
            $document->save();

            DB::commit();

            return redirect()->route('view_produktivitas_accident', [
                'accident_id' => $accidentId,
            ])->with('success', 'Surat Perintah Penangguhan Penahanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Hapus dokumen
     */
    public function delete(string $id)
    {
        $document = SuratPerintahPenangguhanPenahananDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', [
            'accident_id' => $accidentId,
        ])->with('success', 'Surat Perintah Penangguhan Penahanan berhasil dihapus.');
    }

    /**
     * Detail Dokumen & Preview JSON
     */
    public function show(string $id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPerintahPenangguhanPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            'suspects.gender',
            'suspects.education',
            'suspects.job',
            'suspects.religion',
            'suspects.maritalStatus',
            'suspects.country',
            'suspects.location',
            'suratPerintahPenangguhanPenahananDocumentOfficers.rank',
            'suratPerintahPenangguhanPenahananDocumentOfficers.position',
            'documentCategory'
        ])->findOrFail($id);

        $accident = $document->accident;
        $jsonPayload = self::buildJsonPayloadStatic($document);

        if (request()->wantsJson() || request()->has('json')) {
            return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return view('docs.surat-perintah-penangguhan-penahanan-document.show', compact('document', 'accident', 'jsonPayload'));
    }

    /**
     * Endpoint API murni JSON untuk integrasi atau pengujian
     */
    public function json(string $id, Request $request)
    {
        $document = SuratPerintahPenangguhanPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            'suspects.gender',
            'suspects.education',
            'suspects.job',
            'suspects.religion',
            'suspects.maritalStatus',
            'suspects.country',
            'suspects.location',
            'suratPerintahPenangguhanPenahananDocumentOfficers.rank',
            'suratPerintahPenangguhanPenahananDocumentOfficers.position',
            'documentCategory'
        ])->where('id', $id)->firstOrFail();

        $jsonPayload = self::buildJsonPayloadStatic($document);

        return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Download dokumen Word (.docx) hasil generate template
     */
    public function download(string $id)
    {
        $document = SuratPerintahPenangguhanPenahananDocument::with([
            'suspects.gender',
            'suspects.education',
            'suspects.job',
            'suspects.religion',
            'suspects.maritalStatus',
            'suspects.country',
            'suspects.location',
            'suratPerintahPenangguhanPenahananDocumentOfficers.position',
            'suratPerintahPenangguhanPenahananDocumentOfficers.rank',
            'accident.polres.polda',
            'accident.police',
            'suratPerintahPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenahananDocument'
        ])->findOrFail($id);

        $accident = $document->accident;
        $templatePath = public_path('word-template/surat_perintah_penangguhan_penahanan.docx');

        if (!File::exists($templatePath)) {
            return redirect()->back()->with('error', 'Template dokumen tidak ditemukan.');
        }

        // Buat file template sementara untuk diproses (menghapus poin 9 & 10 bila unchecked dan renumber)
        $tempFilteredTemplate = storage_path('app/temp_filtered_s18_' . uniqid() . '.docx');
        $this->filterPenangguhanDocx($templatePath, $tempFilteredTemplate, $document);

        $templateProcessor = new TemplateProcessor($tempFilteredTemplate);

        // Header / Kop Kepolisian
        $poldaName = $accident->polres->polda->name ?? '';
        $polresName = $accident->polres->full_name ?? ($accident->polres->name ?? '');
        $polresAddress = $accident->polres->address ?? '';

        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($poldaName));
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($polresName));
        $templateProcessor->setValue('resorPoliceAddress', $polresAddress);

        // Nomor Dokumen
        $docNumber = $document->nomor ?? $document->document_number ?? '-';
        $templateProcessor->setValue('documentNumber', $docNumber);

        // Laporan Polisi & Tanggal Kejadian (Diperbaiki: menggunakan kolom no_lp dan accident_date dari model Accident)
        $accidentNumber = $accident->no_lp ?? '-';
        $accidentDate = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-';
        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);

        // Sprindik (Poin Dasar 6)
        $sprindikNumber = '-';
        $sprindikDate = '-';
        $sprindikDoc = $document->suratPerintahPenyidikanDocument ?: SuratPerintahPenyidikanDocument::where('accident_id', $accident->id)->latest()->first();
        if ($sprindikDoc) {
            $sprindikNumber = $sprindikDoc->nomor ?? ($sprindikDoc->document_number ?? '-');
            $sprindikDate = $sprindikDoc->tanggal ? Carbon::parse($sprindikDoc->tanggal)->locale('id')->translatedFormat('d F Y') : '-';
        }
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $sprindikNumber);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDate', $sprindikDate);

        // Penetapan Tersangka (Poin Dasar 7)
        $sketNumber = '-';
        $sketDate = '-';
        $sketDoc = $document->suratKetetapanTentangPenetapanTersangkaDocument ?: SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accident->id)->latest()->first();
        if ($sketDoc) {
            $sketNumber = $sketDoc->nomor ?? ($sketDoc->document_number ?? '-');
            $sketDate = $sketDoc->tanggal ? Carbon::parse($sketDoc->tanggal)->locale('id')->translatedFormat('d F Y') : '-';
        }
        $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentNumber', $sketNumber);
        $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentDate', $sketDate);

        // Surat Perintah Penahanan (S-17) (Poin Dasar 8)
        $s17Number = $document->nomor_surat_perintah_penahanan ?? '-';
        $s17Date = '-';
        $s17Doc = $document->suratPerintahPenahananDocument ?: SuratPerintahPenahananDocument::where('accident_id', $accident->id)->latest()->first();
        if ($s17Doc) {
            if ($s17Number === '-' || empty($s17Number)) {
                $s17Number = $s17Doc->nomor ?? ($s17Doc->document_number ?? '-');
            }
            $s17Date = $s17Doc->tanggal ? Carbon::parse($s17Doc->tanggal)->locale('id')->translatedFormat('d F Y') : '-';
        }
        $templateProcessor->setValue('suratPerintahPenahananDocumentNumber', $s17Number);
        $templateProcessor->setValue('suratPerintahPenahananDocumentDate', $s17Date);

        // Surat Perpanjangan Penahanan (Poin Dasar 9 - bila ada)
        $noPerpanjangan = $document->nomor_surat_perpanjangan_penahanan ?: '-';
        $tglPerpanjangan = $document->tanggal_surat_perpanjangan_penahanan ? Carbon::parse($document->tanggal_surat_perpanjangan_penahanan)->locale('id')->translatedFormat('d F Y') : '-';
        $templateProcessor->setValue('suratPerpanjanganPenahananDocumentNumber', $noPerpanjangan);
        $templateProcessor->setValue('suratPerpanjanganPenahananDocumentDate', $tglPerpanjangan);

        // Surat Perintah Perpanjangan Penahanan (Poin Dasar 10 - bila ada)
        $noSprinPerpanjangan = $document->nomor_sprin_perpanjangan_penahanan ?: '-';
        $tglSprinPerpanjangan = $document->tanggal_sprin_perpanjangan_penahanan ? Carbon::parse($document->tanggal_sprin_perpanjangan_penahanan)->locale('id')->translatedFormat('d F Y') : '-';
        $templateProcessor->setValue('suratPerintahPerpanjanganPenahananDocumentNumber', $noSprinPerpanjangan);
        $templateProcessor->setValue('suratPerintahPerpanjanganPenahananDocumentDate', $tglSprinPerpanjangan);

        // Surat Permohonan Penangguhan Penahanan (Poin Dasar 11)
        $tglPermohonan = $document->tanggal_surat_permohonan ? Carbon::parse($document->tanggal_surat_permohonan)->locale('id')->translatedFormat('d F Y') : '-';
        $templateProcessor->setValue('suratPermohonanPenangguhanPenahananDocumentDate', $tglPermohonan);

        // Poin Dasar 12: SKET Penetapan Uang Jaminan
        $noSketJaminan = $document->messages['nomor_sket_uang_jaminan'] ?? '-';
        $tglSketJaminanRaw = $document->messages['tanggal_sket_uang_jaminan'] ?? null;
        $tglSketJaminan = $tglSketJaminanRaw ? Carbon::parse($tglSketJaminanRaw)->locale('id')->translatedFormat('d F Y') : '-';
        $templateProcessor->setValue('suratKetetapanTentangPenetapanUangJaminanPenangguhanPenahananDocumentNumber', $noSketJaminan);
        $templateProcessor->setValue('suratKetetapanTentangPenetapanUangJaminanPenangguhanPenahananDocumentDate', $tglSketJaminan);

        // Poin Dasar 13: SKET Penetapan Uang Tangguhan
        $noSketTangguhan = $document->messages['nomor_sket_uang_tangguhan'] ?? '-';
        $tglSketTangguhanRaw = $document->messages['tanggal_sket_uang_tangguhan'] ?? null;
        $tglSketTangguhan = $tglSketTangguhanRaw ? Carbon::parse($tglSketTangguhanRaw)->locale('id')->translatedFormat('d F Y') : '-';
        $templateProcessor->setValue('suratKetetapanTentangPenetapanUangTangguhanPenangguhanPenahananDocumentNumber', $noSketTangguhan);
        $templateProcessor->setValue('suratKetetapanTentangPenetapanUangTangguhanPenangguhanPenahananDocumentDate', $tglSketTangguhan);

        // Petugas yang Diperintahkan (Ketua Tim di urutan 1, disusul Anggota)
        $leaderOfficer = $document->suratPerintahPenangguhanPenahananDocumentOfficers
            ->where('class', 'LEADER')
            ->first();

        $memberOfficers = $document->suratPerintahPenangguhanPenahananDocumentOfficers
            ->where('class', 'MEMBER')
            ->sortBy('sort');

        $officers = collect();
        if ($leaderOfficer) {
            $officers->push($leaderOfficer);
        }
        foreach ($memberOfficers as $officer) {
            $officers->push($officer);
        }

        $blockOfficers = [];
        $no = 1;
        foreach ($officers as $officer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name' => ($officer->first_title ? $officer->first_title . ' ' : '') . $officer->first_name,
                'last_name' => ($officer->last_title ? ', ' . $officer->last_title : ($officer->last_name ? ' ' . $officer->last_name : '')),
                'rank_name' => $officer->rank->name ?? '',
                'rank_id' => $officer->rank->name ?? '',
                'officer_id' => $officer->register_number ?? '',
                'position' => $officer->position->name ?? '',
            ];
            $no++;
        }

        if (!empty($blockOfficers)) {
            $templateProcessor->cloneBlock('block_officers', count($blockOfficers), true, false, $blockOfficers);
        } else {
            $templateProcessor->cloneBlock('block_officers', 1, true, false, [[
                'number' => 1,
                'first_name' => '-',
                'last_name' => '',
                'rank_name' => '-',
                'rank_id' => '-',
                'officer_id' => '-',
                'position' => '-',
            ]]);
        }

        // Tersangka (Diperbaiki: menggunakan kolom identity_number)
        $suspect = $document->suspects->first();
        $suspectName = $suspect ? ($suspect->name ?? '-') : '-';
        $templateProcessor->setValue('nama tersangka', $suspectName);
        $templateProcessor->setValue('suspectName', $suspectName);

        if ($suspect) {
            $templateProcessor->setValue('suspectIdentityNumber', $suspect->identity_number ?? '-');
            $templateProcessor->setValue('suspectBirthPlace', $suspect->birth_place ?? '-');
            $templateProcessor->setValue('suspectBirthDate', $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('suspectGenderName', $suspect->gender->name ?? '-');
            $templateProcessor->setValue('suspectJobName', $suspect->job->name ?? '-');
            $templateProcessor->setValue('suspectNationality', $suspect->country->name ?? ($suspect->nationality ?? 'Indonesia'));
            $templateProcessor->setValue('suspectFullAddress', $suspect->address ?? '-');
        } else {
            $templateProcessor->setValue('suspectIdentityNumber', '-');
            $templateProcessor->setValue('suspectBirthPlace', '-');
            $templateProcessor->setValue('suspectBirthDate', '-');
            $templateProcessor->setValue('suspectGenderName', '-');
            $templateProcessor->setValue('suspectJobName', '-');
            $templateProcessor->setValue('suspectNationality', '-');
            $templateProcessor->setValue('suspectFullAddress', '-');
        }

        // Ketua Tim (Ttd sebelah kiri dan ttd Yang Menyerahkan)
        $leaderOfficerName = '-';
        $leaderOfficerRankName = '-';
        $leaderOfficerRegisterNumber = '-';
        if ($leaderOfficer) {
            $leaderOfficerName = PeopleNameHelper::getFullName($leaderOfficer->first_title, $leaderOfficer->first_name, $leaderOfficer->last_name, $leaderOfficer->last_title);
            $leaderOfficerRankName = strtoupper($leaderOfficer->rank->name ?? '');
            $leaderOfficerRegisterNumber = $leaderOfficer->register_number ?? '-';
        }
        $templateProcessor->setValue('leaderOfficerName', $leaderOfficerName);
        $templateProcessor->setValue('leaderOfficerRankName', $leaderOfficerRankName);
        $templateProcessor->setValue('leaderOfficerRegisterNumber', $leaderOfficerRegisterNumber);

        // Pejabat Penandatangan Resmi (Kanan Atas)
        $signatory = $document->suratPerintahPenangguhanPenahananDocumentOfficers->where('class', 'SIGNATORY')->first();
        $signatoryName = '-';
        $signatoryRankName = '-';
        $signatoryRegisterNumber = '-';
        $signatoryPosition = '-';
        $signatoryHeadText = 'KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? '');
        $signatoryPositionHeadText = '';

        if ($signatory) {
            $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
            $signatoryRankName = strtoupper($signatory->rank->name ?? '');
            $signatoryRegisterNumber = $signatory->register_number ?? '-';
            $signatoryPosition = $signatory->position->name ?? '';

            if (isset($signatory->position)) {
                if ($signatory->position->position_cluster_id == '1') {
                    $signatoryHeadText = 'KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? '');
                    $signatoryPositionHeadText = '';
                } else {
                    $signatoryHeadText = 'a.n. KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? '');
                    $signatoryPositionHeadText = strtoupper($signatoryPosition);
                }
            }
        }

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText);
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', $signatoryRankName);
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $tempFileName = 'Surat_Perintah_Penangguhan_Penahanan_' . str_replace(['/', '\\', ' '], '_', $document->document_number ?? $document->id) . '.docx';
        $tempPath = storage_path('app/' . $tempFileName);
        $templateProcessor->saveAs($tempPath);

        // Hapus template sementara setelah proses selesai
        if (file_exists($tempFilteredTemplate)) {
            @unlink($tempFilteredTemplate);
        }

        return response()->download($tempPath, $tempFileName)->deleteFileAfterSend(true);
    }

    /**
     * Filter Word Template Penangguhan Penahanan:
     * 1. Unifikasi tag variabel yang terpecah (<w:r> split runs) pada Word document.xml.
     * 2. Pengaturan Tanda Tangan:
     *    - Tanda tangan kiri (Yang menerima perintah) diisi nama & pangkat Ketua Tim.
     *    - Tanda tangan kanan atas diisi Pejabat Penandatangan Resmi.
     *    - Tanda tangan kanan bawah (Yang Menyerahkan) diisi nama & pangkat Ketua Tim.
     * 3. Kondisi Poin 9, 10, 12, 13 pada Dasar:
     *    - Poin 9 (Surat Perpanjangan Penahanan): jika tidak ada / kosong, baris dihapus.
     *    - Poin 10 (Surat Perintah Perpanjangan Penahanan): jika tidak ada / kosong, baris dihapus.
     *    - Poin 11 (Surat Permohonan Penangguhan Penahanan): selalu ada, nomor disesuaikan.
     *    - Poin 12 (SKET Penetapan Uang Jaminan): jika tidak ada / kosong, baris dihapus.
     *    - Poin 13 (SKET Penetapan Uang Tangguhan): jika tidak ada / kosong, baris dihapus.
     *    - Penomoran poin-poin setelah Poin 8 disesuaikan secara berurutan dinamis.
     * 4. Rincian jaminan uang / orang pada bagian "Untuk:".
     */
    private function filterPenangguhanDocx(string $sourcePath, string $targetPath, SuratPerintahPenangguhanPenahananDocument $document)
    {
        copy($sourcePath, $targetPath);

        $zip = new \ZipArchive();
        if ($zip->open($targetPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // 1. Unifikasi tag variabel ${...} yang terpecah di berbagai elemen w:r run Word
        $mergePattern = '/<\/w:t>(?:(?!<\/w:p>).)*?<w:t[^>]*>/s';
        $cleanedXml = preg_replace_callback('/<w:p(?:\s[^>]*)?>.*?<\/w:p>/s', function($pMatches) use ($mergePattern) {
            $p = $pMatches[0];
            if (strpos($p, '$') === false) return $p;
            return preg_replace_callback('/\$[^{}]*\{[^{}]*\}/s', function($m) use ($mergePattern) {
                return preg_replace($mergePattern, '', $m[0]);
            }, $p);
        }, $xml);

        // 2. Modifikasi penempatan placeholder tanda tangan:
        // Bagian atas (kiri: Ketua Tim, kanan: Signatory Resmi)
        // Bagian bawah (kiri: Tersangka, kanan: Ketua Tim yang Menyerahkan)
        $parts = explode('Menyerahkan', $cleanedXml, 2);
        if (count($parts) === 2) {
            // Pada bagian atas, gantikan kemunculan PERTAMA (kolom kiri: Yang menerima perintah) dengan Ketua Tim
            $parts[0] = preg_replace('/\$\{signatoryName\}/', '${leaderOfficerName}', $parts[0], 1);
            $parts[0] = preg_replace('/\$\{signatoryRankName\}/', '${leaderOfficerRankName}', $parts[0], 1);
            $parts[0] = preg_replace('/\$\{signatoryRegisterNumber\}/', '${leaderOfficerRegisterNumber}', $parts[0], 1);

            // Pada bagian bawah (setelah teks Menyerahkan), kolom kanan (Yang Menyerahkan) diisi oleh Ketua Tim
            $parts[1] = str_replace('${signatoryName}', '${leaderOfficerName}', $parts[1]);
            $parts[1] = str_replace('${signatoryRankName}', '${leaderOfficerRankName}', $parts[1]);
            $parts[1] = str_replace('${signatoryRegisterNumber}', '${leaderOfficerRegisterNumber}', $parts[1]);

            $cleanedXml = implode('Menyerahkan', $parts);
        }

        // 3. Modifikasi DOM untuk Poin Dasar 9, 10, 11, 12, 13
        $dom = new \DOMDocument();
        $dom->loadXML($cleanedXml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        // Pengecekan ada/tidaknya Poin 9 dan Poin 10
        $hasPoin9 = empty($document->messages['tidak_ada_surat_perpanjangan_penahanan']) && !empty($document->nomor_surat_perpanjangan_penahanan);
        $hasPoin10 = empty($document->messages['tidak_ada_sprin_perpanjangan_penahanan']) && !empty($document->nomor_sprin_perpanjangan_penahanan);

        // Pengecekan ada/tidaknya Poin 12 dan Poin 13
        $tidakAda12 = !empty($document->messages['tidak_ada_sket_uang_jaminan']);
        $hasPoin12 = !$tidakAda12 && !empty($document->messages['nomor_sket_uang_jaminan']);

        $tidakAda13 = !empty($document->messages['tidak_ada_sket_uang_tangguhan']);
        $hasPoin13 = !$tidakAda13 && !empty($document->messages['nomor_sket_uang_tangguhan']);

        // Kalkulasi dinamis nomor urut baris setelah Poin 8
        $currentNumber = 8;
        $numPoin9 = $hasPoin9 ? ++$currentNumber . '.' : null;
        $numPoin10 = $hasPoin10 ? ++$currentNumber . '.' : null;
        $numPoin11 = ++$currentNumber . '.';
        $numPoin12 = $hasPoin12 ? ++$currentNumber . '.' : null;
        $numPoin13 = $hasPoin13 ? ++$currentNumber . '.' : null;

        $rows = $xpath->query('//w:tr');
        $rowsToRemove = [];

        foreach ($rows as $tr) {
            $text = $tr->textContent;

            // Baris 9: Surat Perpanjangan Penahanan (bila ada)
            if (strpos($text, 'Surat Perpanjangan Penahanan Nomor:') !== false) {
                if (!$hasPoin9) {
                    $rowsToRemove[] = $tr;
                } else {
                    $this->changeRowNumberCell($xpath, $tr, $numPoin9);
                    $this->cleanBilaAdaText($tr);
                }
            }

            // Baris 10: Surat Perintah Perpanjangan Penahanan (bila ada)
            if (strpos($text, 'Surat Perintah Perpanjangan Penahanan Nomor:') !== false) {
                if (!$hasPoin10) {
                    $rowsToRemove[] = $tr;
                } else {
                    $this->changeRowNumberCell($xpath, $tr, $numPoin10);
                    $this->cleanBilaAdaText($tr);
                }
            }

            // Baris 11: Surat Permohonan Penangguhan Penahanan
            if (strpos($text, 'Surat Permohonan Penangguhan Penahanan') !== false) {
                $this->changeRowNumberCell($xpath, $tr, $numPoin11);
            }

            // Baris 12: Surat Ketetapan Penetapan Uang Jaminan Penangguhan Penahanan
            if (strpos($text, 'Penetapan Uang Jaminan Penangguhan Penahanan') !== false) {
                if (!$hasPoin12) {
                    $rowsToRemove[] = $tr;
                } else {
                    $this->changeRowNumberCell($xpath, $tr, $numPoin12);
                    if (!$hasPoin13) {
                        // Jika poin 13 tidak ada, ubah akhiran '; atau' menjadi ';'
                        $textCell = $xpath->query('.//w:tc[4]', $tr)->item(0);
                        if ($textCell instanceof \DOMElement) {
                            $tNodes = $xpath->query('.//w:t', $textCell);
                            foreach ($tNodes as $tNode) {
                                if (strpos($tNode->nodeValue, 'atau') !== false) {
                                    $tNode->nodeValue = str_replace('; atau', ';', $tNode->nodeValue);
                                }
                            }
                        }
                    }
                }
            }

            // Baris 13: Surat Ketetapan Penetapan Uang Tangguhan Penangguhan Penahanan
            if (strpos($text, 'Penetapan Uang Tangguhan Penangguhan Penahanan') !== false) {
                if (!$hasPoin13) {
                    $rowsToRemove[] = $tr;
                } else {
                    $this->changeRowNumberCell($xpath, $tr, $numPoin13);
                }
            }

            // Bagian Jaminan di poin "Untuk:" (baris jaminan uang dan jaminan orang)
            $this->processJaminanRows($dom, $xpath, $tr, $document);
        }

        // Hapus baris yang tidak digunakan
        foreach ($rowsToRemove as $r) {
            $r->parentNode->removeChild($r);
        }

        // Isi Dikeluarkan di & pada tanggal di tabel tanda tangan bila kosong
        $tables = $dom->getElementsByTagName('tbl');
        if ($tables->length >= 5) {
            $tblSig = $tables->item(4);
            $sigRows = $tblSig->getElementsByTagName('tr');
            if ($sigRows->length > 1) {
                $r0 = $sigRows->item(0);
                $r1 = $sigRows->item(1);
                $polresCity = $document->accident->polres->city ?? ($document->accident->polres->full_name ?? ($document->accident->polres->name ?? ''));
                $docDate = $document->tanggal ? Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y');

                if ($r0) {
                    $cells0 = $r0->getElementsByTagName('tc');
                    if ($cells0->length >= 6) {
                        $cLast = $cells0->item(5);
                        if (trim($cLast->textContent) === '') {
                            $this->setCellText($dom, $cLast, $polresCity);
                        }
                    }
                }
                if ($r1) {
                    $cells1 = $r1->getElementsByTagName('tc');
                    if ($cells1->length >= 6) {
                        $cLast = $cells1->item(5);
                        if (trim($cLast->textContent) === '') {
                            $this->setCellText($dom, $cLast, $docDate);
                        }
                    }
                }
            }
        }

        $zip->addFromString('word/document.xml', $dom->saveXML());
        $zip->close();
    }

    /**
     * Hapus tulisan (bila ada) pada w:tr jika poin perpanjangan disertakan
     */
    private function cleanBilaAdaText(\DOMElement $row)
    {
        $tNodes = $row->getElementsByTagName('t');
        foreach ($tNodes as $t) {
            if (strpos($t->nodeValue, '(bila ada)') !== false) {
                $t->nodeValue = str_replace([' (bila ada)', '(bila ada)'], '', $t->nodeValue);
            }
        }
    }

    /**
     * Set teks bersih pada elemen cell w:tc dengan mempertahankan styling w:pPr
     */
    private function setCellText(\DOMDocument $dom, \DOMElement $tc, string $newText)
    {
        $p = $tc->getElementsByTagName('p')->item(0);
        if (!$p) return;

        // Hapus semua node anak selain w:pPr
        $nodesToRemove = [];
        foreach ($p->childNodes as $child) {
            if ($child->nodeName !== 'w:pPr') {
                $nodesToRemove[] = $child;
            }
        }
        foreach ($nodesToRemove as $n) {
            $p->removeChild($n);
        }

        // Buat elemen w:r dengan w:rPr font Arial Narrow
        $r = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
        $rPr = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:rPr');
        $rFonts = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:rFonts');
        $rFonts->setAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:ascii', 'Arial Narrow');
        $rFonts->setAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:hAnsi', 'Arial Narrow');
        $rPr->appendChild($rFonts);
        $r->appendChild($rPr);

        $t = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t');
        $t->nodeValue = $newText;
        $r->appendChild($t);
        $p->appendChild($r);
    }

    /**
     * Ubah angka nomor pada kolom nomor tabel
     */
    private function changeRowNumberCell(\DOMXPath $xpath, \DOMElement $row, string $newNumber)
    {
        $numCell = $xpath->query('.//w:tc[3]', $row);
        if ($numCell->length > 0) {
            $tNodes = $xpath->query('.//w:t', $numCell->item(0));
            if ($tNodes->length > 0) {
                $tNodes->item(0)->nodeValue = $newNumber;
                for ($k = 1; $k < $tNodes->length; $k++) {
                    $tNodes->item($k)->nodeValue = '';
                }
            }
        }
    }

    /**
     * Modifikasi teks jaminan pada poin Untuk (Rp. ... dan Panitera Pengadilan ...)
     */
    private function processJaminanRows(\DOMDocument $dom, \DOMXPath $xpath, \DOMElement $row, SuratPerintahPenangguhanPenahananDocument $document)
    {
        $text = $row->textContent;
        $jenisJaminan = $document->jenis_jaminan;
        $besaranFormatted = $document->besaran_uang_jaminan ? number_format($document->besaran_uang_jaminan, 0, ',', '.') : '-';
        $lokasiPenyimpanan = $document->lokasi_penyimpanan_jaminan ?: 'Panitera Pengadilan Negeri ..........';

        // Poin 3: jumlah jaminan uang
        if (strpos($text, 'jumlah: Rp. ........;') !== false || (strpos($text, 'jumlah') !== false && strpos($text, 'Rp. ........;') !== false)) {
            if ($document->besaran_uang_jaminan) {
                $lastCell = $xpath->query('.//w:tc[last()]', $row)->item(0);
                if ($lastCell) {
                    $this->setCellText($dom, $lastCell, "Rp. {$besaranFormatted};");
                }
            }
        }

        // Poin 3: disimpan di lokasi jaminan uang
        if (strpos($text, 'disimpan di: Panitera Pengadilan Negri') !== false || (strpos($text, 'disimpan di') !== false && strpos($text, 'Panitera Pengadilan Negri') !== false)) {
            if ($document->lokasi_penyimpanan_jaminan) {
                $lastCell = $xpath->query('.//w:tc[last()]', $row)->item(0);
                if ($lastCell) {
                    $this->setCellText($dom, $lastCell, "{$lokasiPenyimpanan}; atau");
                }
            }
        }

        // Poin 4: identitas penjamin orang
        if (strpos($text, 'jaminan orang .... (identitas lengkap penjamin)') !== false) {
            if ($jenisJaminan == 2 && $document->nama_penjamin) {
                $penjaminDesc = "untuk penangguhan penahanan dengan jaminan orang {$document->nama_penjamin} (No. Identitas: " . ($document->nomor_identitas_penjamin ?: '-') . ", Alamat: " . ($document->alamat_penjamin ?: '-') . "), dengan uang tanggungan (Pasal 110 KUHAP) yang telah ditetapkan oleh Atasan Penyidik dalam bentuk mata uang rupiah:";
                $descCell = $xpath->query('.//w:tc[last()]', $row)->item(0);
                if ($descCell) {
                    $this->setCellText($dom, $descCell, $penjaminDesc);
                }
            }
        }

        // Poin 4: jumlah uang tanggungan
        if (strpos($text, 'jumlah: Rp. ......;') !== false || (strpos($text, 'jumlah') !== false && strpos($text, 'Rp. ......;') !== false)) {
            if ($document->besaran_uang_jaminan) {
                $lastCell = $xpath->query('.//w:tc[last()]', $row)->item(0);
                if ($lastCell) {
                    $this->setCellText($dom, $lastCell, "Rp. {$besaranFormatted};");
                }
            }
        }

        // Poin 4: disimpan di lokasi jaminan orang
        if (strpos($text, 'disimpan di: Panitera Pengadilan Negeri ..........;') !== false || (strpos($text, 'disimpan di') !== false && strpos($text, 'Panitera Pengadilan Negeri ..........;') !== false)) {
            if ($document->lokasi_penyimpanan_jaminan) {
                $lastCell = $xpath->query('.//w:tc[last()]', $row)->item(0);
                if ($lastCell) {
                    $this->setCellText($dom, $lastCell, "{$lokasiPenyimpanan};");
                }
            }
        }
    }

    /**
     * Helper membuat JSON S-18 persis dengan contoh user & spesifikasi SPPT-TI
     */
    public function buildJsonPayload(SuratPerintahPenangguhanPenahananDocument $document): array
    {
        return self::buildJsonPayloadStatic($document);
    }

    /**
     * Alias backward-compatible untuk generateJsonPayload
     */
    public function generateJsonPayload(SuratPerintahPenangguhanPenahananDocument $document): array
    {
        return self::buildJsonPayloadStatic($document);
    }

    /**
     * Static helper untuk membuat JSON S-18 sesuai format baku yang dibutuhkan
     */
    public static function buildJsonPayloadStatic(SuratPerintahPenangguhanPenahananDocument $document): array
    {
        $accident = $document->accident;

        // 1. Identitas Dokumen (S-18)
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

        $nomorPenahanan = $document->nomor_surat_perintah_penahanan;
        if (empty($nomorPenahanan) && $accident) {
            $s17Doc = $accident->suratPerintahPenahananDocuments
                ? $accident->suratPerintahPenahananDocuments->sortByDesc('id')->first()
                : null;
            if ($s17Doc) {
                $nomorPenahanan = $s17Doc->nomor ?? $s17Doc->document_number;
            }
        }

        $identitasDokumen = [
            'nomor'                          => (string) ($document->nomor ?: ($document->document_number ?: ($document->exists ? '-' : 'SRT/3/12/2012.PolresJAKPUS'))),
            'tanggal'                        => $document->tanggal ? Carbon::parse($document->tanggal)->format('Y-m-d') : ($document->document_date ? Carbon::parse($document->document_date)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-01-15')),
            'nomor_spdp'                     => (string) ($nomorSpdp ?: ($document->exists ? '-' : 'SPDP/3/12/2012.PolresJAKPUS')),
            'tanggal_spdp'                   => $tanggalSpdp ? Carbon::parse($tanggalSpdp)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-01-15'),
            'kode_satker_penerbit_spdp'      => (string) ($document->kode_satker_penerbit_spdp ?: ($accident->polres->satker_code ?? '006.09.05')),
            'nomor_surat_perintah_penahanan' => (string) ($nomorPenahanan ?: ($document->exists ? '-' : 'S17/3/12/2012.PolresJAKPUS')),
            'tanggal_surat_permohonan'       => $document->tanggal_surat_permohonan ? Carbon::parse($document->tanggal_surat_permohonan)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-01-15'),
        ];

        // 2. Konten Dokumen - Tersangka (S-18 wajib lengkap sama dengan S-17)
        $daftarTersangka = [];
        $daftarUuPasalDoc = $document->messages['daftar_uu_pasal'] ?? ['Pasal 109 ayat (1) KUHAP'];
        if (!is_array($daftarUuPasalDoc)) {
            $daftarUuPasalDoc = [$daftarUuPasalDoc];
        }
        $daftarUuPasalDoc = array_values(array_filter($daftarUuPasalDoc));
        if (empty($daftarUuPasalDoc)) {
            $daftarUuPasalDoc = ['Pasal 109 ayat (1) KUHAP'];
        }

        if ($document->suspects && $document->suspects->isNotEmpty()) {
            foreach ($document->suspects as $suspect) {
                $age = $suspect->age_at_crime ?? ($suspect->age ?? ($suspect->birth_date ? Carbon::parse($suspect->birth_date)->age : 21));

                $countryCode = 'idn';
                if ($suspect->country) {
                    $countryName = strtolower($suspect->country->name ?? '');
                    $countryIso = strtolower($suspect->country->iso_code ?? ($suspect->country->alpha_code ?? ''));
                    $countryEmp = (string) ($suspect->country->emp_id ?? '');
                    if (str_contains($countryName, 'indonesia') || in_array($countryIso, ['id', 'idn']) || in_array($countryEmp, ['101', 'idn'])) {
                        $countryCode = 'idn';
                    } else {
                        $countryCode = $countryIso ?: ($countryEmp ?: 'idn');
                    }
                } elseif (!empty($suspect->nationality)) {
                    $nat = strtolower($suspect->nationality);
                    $countryCode = (str_contains($nat, 'indonesia') || in_array($nat, ['id', 'idn', 'wni'])) ? 'idn' : $nat;
                }

                $daftarTersangka[] = [
                    'nama'                    => (string) ($suspect->name ?: '-'),
                    'tempat_lahir'            => (string) ($suspect->birth_place ?: '-'),
                    'kode_jenis_kelamin'      => intval($suspect->gender->pusiknas_id ?? $suspect->gender->emp_id ?? $suspect->gender_id ?? 1),
                    'alamat'                  => (string) ($suspect->address ?: '-'),
                    'kode_wilayah'            => (string) ($suspect->location->emp_id ?? ($suspect->location_id ?? ($accident->polres->satker_code ?? '11.01.17'))),
                    'kode_pendidikan'         => intval($suspect->education->pusiknas_id ?? $suspect->education->emp_id ?? 1),
                    'kode_pekerjaan'          => intval($suspect->job->pusiknas_id ?? $suspect->job->emp_id ?? 87),
                    'nama_ibu'                => (string) ($suspect->mother_name ?: '-'),
                    'kode_agama'              => intval($suspect->religion->pusiknas_id ?? $suspect->religion->emp_id ?? 1),
                    'kode_status_perkawinan'  => intval($suspect->maritalStatus->pusiknas_id ?? $suspect->maritalStatus->emp_id ?? 1),
                    'kode_warga_negara'       => $countryCode,
                    'umur_saat_tindak_pidana' => intval($age ?: 21),
                    'daftar_uu_pasal'         => $daftarUuPasalDoc,
                ];
            }
        }

        if (empty($daftarTersangka)) {
            $daftarTersangka[] = [
                'nama'                    => 'Samuel Sambiroto',
                'tempat_lahir'            => 'Jakarta',
                'kode_jenis_kelamin'      => 1,
                'alamat'                  => 'Jl. Jalan-jalan No 1',
                'kode_wilayah'            => '11.01.17',
                'kode_pendidikan'         => 1,
                'kode_pekerjaan'          => 87,
                'nama_ibu'                => 'Samuela',
                'kode_agama'              => 1,
                'kode_status_perkawinan'  => 1,
                'kode_warga_negara'       => 'idn',
                'umur_saat_tindak_pidana' => 21,
                'daftar_uu_pasal'         => $daftarUuPasalDoc,
            ];
        }

        // 3. Jaminan & Penjamin (Sesuai spesifikasi Gambar Tabel b & Contoh JSON)
        $isMock = !$document->exists || (
            $document->nomor === 'SRT/3/12/2012.PolresJAKPUS' &&
            empty($document->nama_penjamin)
        );

        $jenisJaminan = ($document->jenis_jaminan !== null && $document->jenis_jaminan !== '')
            ? intval($document->jenis_jaminan)
            : ($isMock ? 1 : null);

        $besaranUangJaminan = null;
        if ($document->besaran_uang_jaminan !== null && $document->besaran_uang_jaminan !== '') {
            $besaranUangJaminan = is_numeric($document->besaran_uang_jaminan)
                ? ($document->besaran_uang_jaminan == (int)$document->besaran_uang_jaminan ? (int)$document->besaran_uang_jaminan : (float)$document->besaran_uang_jaminan)
                : null;
        } elseif ($isMock) {
            $besaranUangJaminan = 50000000;
        }

        $nomorIdentitasPenjamin = ($document->nomor_identitas_penjamin !== null && $document->nomor_identitas_penjamin !== '')
            ? (string) $document->nomor_identitas_penjamin
            : ($isMock ? '-' : null);

        $namaPenjamin = ($document->nama_penjamin !== null && $document->nama_penjamin !== '')
            ? (string) $document->nama_penjamin
            : ($isMock ? 'PT Asuransi ABC' : null);

        $alamatPenjamin = ($document->alamat_penjamin !== null && $document->alamat_penjamin !== '')
            ? (string) $document->alamat_penjamin
            : ($isMock ? 'Jl. Jalan-jalan No 1' : null);

        $lokasiPenyimpananJaminan = ($document->lokasi_penyimpanan_jaminan !== null && $document->lokasi_penyimpanan_jaminan !== '')
            ? (string) $document->lokasi_penyimpanan_jaminan
            : ($isMock ? 'Jl. Jalan-jalan No 1' : null);

        // 4. Pejabat Penandatangan (Hanya SIGNATORY sesuai format)
        $pejabatPenandatangan = [];
        $signatories = $document->suratPerintahPenangguhanPenahananDocumentOfficers
            ? $document->suratPerintahPenangguhanPenahananDocumentOfficers->where('class', 'SIGNATORY')
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
        } elseif ($document->suratPerintahPenangguhanPenahananDocumentOfficers && $document->suratPerintahPenangguhanPenahananDocumentOfficers->isNotEmpty()) {
            $firstOfficer = $document->suratPerintahPenangguhanPenahananDocumentOfficers->first();
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

        // 5. Konten Dokumen (Urutan field sesuai contoh JSON user)
        $kontenDokumen = [
            'tersangka'                  => $daftarTersangka,
            'jenis_jaminan'              => $jenisJaminan,
            'besaran_uang_jaminan'       => $besaranUangJaminan,
            'nomor_identitas_penjamin'   => $nomorIdentitasPenjamin,
            'nama_penjamin'              => $namaPenjamin,
            'alamat_penjamin'            => $alamatPenjamin,
            'lokasi_penyimpanan_jaminan' => $lokasiPenyimpananJaminan,
            'pejabat_penandatangan'      => $pejabatPenandatangan,
        ];

        $terenkripsi = filter_var($document->terenkripsi ?? ($document->messages['terenkripsi'] ?? false), FILTER_VALIDATE_BOOLEAN);
        $daftarKunciEnkripsi = $terenkripsi
            ? (array) ($document->daftar_kunci_enkripsi ?? ($document->messages['daftar_kunci_enkripsi'] ?? []))
            : [];
        $tandaTanganDigital = $document->tanda_tangan_digital
            ?? ($document->messages['tanda_tangan_digital'] ?? null);

        return [
            'kode_jenis_dokumen'    => 's18',
            'identitas_dokumen'     => $identitasDokumen,
            'konten_dokumen'        => $kontenDokumen,
            'terenkripsi'           => $terenkripsi,
            'daftar_kunci_enkripsi' => $daftarKunciEnkripsi,
            'tanda_tangan_digital'  => $tandaTanganDigital,
        ];
    }

    /**
     * AJAX form validation
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
     * Aturan validasi form
     */
    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'accident_id'                    => 'required',
            'nomor'                          => 'required|string|max:255',
            'tanggal'                        => 'required|date_format:Y-m-d',
            'nomor_spdp'                     => 'required|string|max:255',
            'tanggal_spdp'                   => 'required|date_format:Y-m-d',
            'kode_satker_penerbit_spdp'      => 'required|string|max:50',
            'nomor_surat_perintah_penahanan' => 'required|string|max:255',
            'tanggal_surat_permohonan'       => 'required|date_format:Y-m-d',
            'suspects'                       => 'required|array|min:1',
            'officerLeader'                  => 'required',
            'signatory'                      => 'required',
            'jenis_jaminan'                  => 'nullable|integer|in:1,2',
            'besaran_uang_jaminan'           => 'nullable|numeric',
            'nomor_identitas_penjamin'       => 'nullable|string|max:255',
            'nama_penjamin'                  => 'nullable|string|max:255',
            'alamat_penjamin'                => 'nullable|string',
            'lokasi_penyimpanan_jaminan'     => 'nullable|string|max:255',
            'nomor_surat_perpanjangan_penahanan' => 'nullable|string|max:255',
            'tanggal_surat_perpanjangan_penahanan' => 'nullable|date_format:Y-m-d',
            'nomor_sprin_perpanjangan_penahanan' => 'nullable|string|max:255',
            'tanggal_sprin_perpanjangan_penahanan' => 'nullable|date_format:Y-m-d',
            'nomor_sket_uang_jaminan'        => 'nullable|string|max:255',
            'tanggal_sket_uang_jaminan'      => 'nullable|date_format:Y-m-d',
            'nomor_sket_uang_tangguhan'      => 'nullable|string|max:255',
            'tanggal_sket_uang_tangguhan'    => 'nullable|date_format:Y-m-d',
        ], [
            'nomor.required'                          => 'Mohon mengisi Nomor Surat Perintah Penangguhan Penahanan (S-18).',
            'tanggal.required'                        => 'Mohon mengisi Tanggal Surat Perintah Penangguhan Penahanan.',
            'nomor_spdp.required'                     => 'Mohon mengisi Nomor SPDP.',
            'tanggal_spdp.required'                   => 'Mohon mengisi Tanggal SPDP.',
            'kode_satker_penerbit_spdp.required'      => 'Mohon mengisi Kode Satker Penerbit SPDP.',
            'nomor_surat_perintah_penahanan.required' => 'Mohon mengisi Nomor Surat Perintah Penahanan (S-17).',
            'tanggal_surat_permohonan.required'       => 'Mohon mengisi Tanggal Surat Permohonan Penangguhan.',
            'suspects.required'                       => 'Mohon memilih minimal 1 Tersangka.',
            'suspects.min'                            => 'Mohon memilih minimal 1 Tersangka.',
            'officerLeader.required'                  => 'Mohon memilih Ketua Tim.',
            'signatory.required'                      => 'Mohon memilih Pejabat Penandatangan.',
        ]);
    }
}
