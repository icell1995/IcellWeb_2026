<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpWord\TemplateProcessor;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\UndanganGelarPerkaraDocument\UndanganGelarPerkaraDocument;
use App\Models\Doc\UndanganGelarPerkaraDocument\UndanganGelarPerkaraDocumentOfficer;

use App\Traits\DocsOfficersTraits;

class UndanganGelarPerkaraDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected DocService $docService;

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
     * Tampilan form pembuatan Undangan Gelar Perkara
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // 1. Dokumen Surat Perintah Penyidikan
        $sprindikDocument = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        // 2. Dokumen Surat Perintah Penyelidikan
        $sprinlidikDocument = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSprindik = $sprindikDocument->nomor ?? $sprindikDocument->document_number ?? null;
        $tanggalSprindik = $sprindikDocument->tanggal ?? $sprindikDocument->document_date ?? null;

        // 3. Tersangka & Korban
        $suspects = Suspect::where('accident_id', $accidentId)->get();

        // 4. Petugas internal
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->get();

        // 5. Pejabat Penandatangan
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->get();

        $today = date('Y-m-d');
        $hariIni = $this->getIndoDay($today);
        $jamIni = '09:00 WIB';
        $tempatDefault = 'Ruang Gelar Perkara Satlantas Polres ' . ($accident->polres->name ?? 'Pasuruan');

        // Ambil No LP berdasarkan accident_id
        $noLp = $accident->no_lp ?: ($accident->nomor_laporan_polisi ?: ($accident->accident_number ?: '-'));

        // Default Dasar Hukum
        $defaultDasar = "1. Pasal 7 ayat (1) huruf j, Pasal 11, Pasal 106, Pasal 108, Pasal 109 ayat (1) KUHAP;\n"
            . "2. Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;\n"
            . "3. Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;\n"
            . "4. Peraturan Kapolri Nomor 6 Tahun 2019 tentang Penyidikan Tindak Pidana;\n"
            . "5. Laporan Polisi Nomor: " . $noLp . ";\n"
            . ($nomorSprindik ? "6. Surat Perintah Penyidikan Nomor: " . $nomorSprindik . " Tanggal " . ($tanggalSprindik ? Carbon::parse($tanggalSprindik)->translatedFormat('d F Y') : '-') . ";" : '');

        // Default Kepada Yth / Pihak yang diundang
        $defaultTujuan = "1. Kasat Lantas Polres " . ($accident->polres->name ?? '') . "\n"
            . "2. Kasiwas Polres " . ($accident->polres->name ?? '') . "\n"
            . "3. Kasi Propam Polres " . ($accident->polres->name ?? '') . "\n"
            . "4. Kasikum Polres " . ($accident->polres->name ?? '') . "\n"
            . "5. Kanit Gakkum Satlantas Polres " . ($accident->polres->name ?? '') . "\n"
            . "6. Penyidik / Penyidik Pembantu Unit Gakkum";

        return view('docs.undangan-gelar-perkara-document.create', [
            'accident'               => $accident,
            'accidentId'             => $accidentId,
            'sprindikDocument'       => $sprindikDocument,
            'sprinlidikDocument'     => $sprinlidikDocument,
            'nomorSprindik'          => $nomorSprindik,
            'tanggalSprindik'        => $tanggalSprindik,
            'suspects'               => $suspects,
            'officers'               => $officers,
            'authorizedSignatories'  => $authorizedSignatories,
            'hariIni'                => $hariIni,
            'jamIni'                 => $jamIni,
            'tempatDefault'          => $tempatDefault,
            'defaultDasar'           => $defaultDasar,
            'defaultTujuan'          => $defaultTujuan,
        ]);
    }

    /**
     * Menyimpan Undangan Gelar Perkara
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nomor'               => 'nullable|string|max:255',
            'sifat'               => 'required|string|max:50',
            'lampiran'            => 'nullable|string|max:100',
            'perihal'             => 'required|string|max:255',
            'tanggal'             => 'required|date',
            'tempat_surat'        => 'nullable|string|max:255',
            'hari_pelaksanaan'    => 'required|string|max:50',
            'tanggal_pelaksanaan' => 'required|date',
            'jam_pelaksanaan'     => 'required|string|max:50',
            'tempat_pelaksanaan'  => 'required|string|max:255',
            'agenda'              => 'required|string',
            'tujuan_undangan'     => 'required|string',
            'signatory'           => 'required|string',
        ], [
            'tanggal.required'             => 'Tanggal surat wajib diisi.',
            'sifat.required'               => 'Sifat surat wajib dipilih.',
            'perihal.required'             => 'Perihal surat wajib diisi.',
            'hari_pelaksanaan.required'    => 'Hari pelaksanaan gelar perkara wajib diisi.',
            'tanggal_pelaksanaan.required' => 'Tanggal pelaksanaan gelar perkara wajib diisi.',
            'jam_pelaksanaan.required'     => 'Jam pelaksanaan gelar perkara wajib diisi.',
            'tempat_pelaksanaan.required'  => 'Tempat pelaksanaan gelar perkara wajib diisi.',
            'agenda.required'              => 'Agenda pelaksanaan wajib diisi.',
            'tujuan_undangan.required'     => 'Pihak yang diundang (Kepada Yth) wajib diisi.',
            'signatory.required'           => 'Pejabat Penandatangan wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $docId = (string) Uuid::generate();

            $sprindikDoc = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            $sprinlidikDoc = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            // Data Signatory
            $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
            $signatoryName = $signatory
                ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)
                : '-';
            $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
            $signatoryNrp = $signatory->register_number ?? '-';
            $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');

            $tembusan = [];
            if ($request->has('tembusan') && is_array($request->tembusan)) {
                $tembusan = array_values(array_filter($request->tembusan));
            }

            $document = UndanganGelarPerkaraDocument::create([
                'id'                                    => $docId,
                'accident_id'                           => $accidentId,
                'surat_perintah_penyelidikan_document_id'=> $sprinlidikDoc->id ?? null,
                'surat_perintah_penyidikan_document_id' => $sprindikDoc->id ?? null,
                'nomor'                                 => $request->nomor,
                'sifat'                                 => $request->sifat ?? 'Biasa',
                'lampiran'                              => $request->lampiran ?? '-',
                'perihal'                               => $request->perihal ?? 'Undangan Pelaksanaan Gelar Perkara',
                'tanggal'                               => $request->tanggal,
                'tempat_surat'                          => $request->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan'),
                'dasar_hukum'                           => $request->dasar_hukum,
                'nomor_lp'                              => $accident->no_lp ?: ($accident->nomor_laporan_polisi ?: ($accident->accident_number ?: null)),
                'tanggal_lp'                            => $accident->accident_date ?: ($accident->report_date ?: ($accident->tanggal_laporan_polisi ?: null)),
                'nomor_sprindik'                        => $sprindikDoc ? ($sprindikDoc->nomor ?? ($sprindikDoc->document_number ?? null)) : null,
                'tanggal_sprindik'                      => $sprindikDoc ? ($sprindikDoc->tanggal ?? ($sprindikDoc->document_date ?? null)) : null,
                'hari_pelaksanaan'                      => $request->hari_pelaksanaan,
                'tanggal_pelaksanaan'                   => $request->tanggal_pelaksanaan,
                'jam_pelaksanaan'                       => $request->jam_pelaksanaan,
                'tempat_pelaksanaan'                    => $request->tempat_pelaksanaan,
                'agenda'                                => $request->agenda,
                'tujuan_undangan'                       => $request->tujuan_undangan,
                'signatory_id'                          => $request->signatory,
                'signatory_head_text'                   => $request->signatory_head_text,
                'signatory_position'                    => $signatoryPosition,
                'signatory_name'                        => $signatoryName,
                'signatory_rank'                        => $signatoryRank,
                'signatory_nrp'                         => $signatoryNrp,
                'tembusan'                              => $tembusan,
                'catatan'                               => $request->catatan,
                'document_number'                       => $request->nomor,
                'document_date'                         => $request->tanggal,
                'status_id'                             => '2',
                'document_category_id'                  => '0701',
                'created_by_user_id'                    => Auth::id(),
                'updated_by_user_id'                    => Auth::id(),
            ]);

            // Simpan Signatory di Officers table
            if ($signatory) {
                UndanganGelarPerkaraDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $docId,
                    'officer_id'      => (string) $signatory->id,
                    'first_name'      => $signatory->first_name,
                    'last_name'       => $signatory->last_name,
                    'first_title'     => $signatory->first_title,
                    'last_title'      => $signatory->last_title,
                    'register_number' => $signatory->register_number,
                    'position_id'     => $signatory->position_id,
                    'rank_id'         => $signatory->rank_id,
                    'police_id'       => $signatory->police_id,
                    'class'           => 'SIGNATORY',
                    'order_number'    => 1,
                ]);
            }

            // Simpan Petugas yang Diundang (jika dipilih)
            if ($request->has('officer_invitees') && is_array($request->officer_invitees)) {
                $order = 2;
                foreach ($request->officer_invitees as $officerId) {
                    $invitee = Officer::find($officerId);
                    if ($invitee) {
                        UndanganGelarPerkaraDocumentOfficer::create([
                            'id'              => (string) Uuid::generate(),
                            'doc_id'          => $docId,
                            'officer_id'      => (string) $invitee->id,
                            'first_name'      => $invitee->first_name,
                            'last_name'       => $invitee->last_name,
                            'first_title'     => $invitee->first_title,
                            'last_title'      => $invitee->last_title,
                            'register_number' => $invitee->register_number,
                            'position_id'     => $invitee->position_id,
                            'rank_id'         => $invitee->rank_id,
                            'police_id'       => $invitee->police_id,
                            'class'           => 'INVITEE',
                            'order_number'    => $order++,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Undangan Gelar Perkara berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan form edit Undangan Gelar Perkara
     */
    public function edit($id)
    {
        $accidentId = request()->query('accident_id');
        $document = UndanganGelarPerkaraDocument::with(['officers', 'accident', 'signatoryOfficer'])->findOrFail($id);
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->findOrFail($accidentId);

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->get();

        $selectedInviteeIds = $document->officers->where('class', 'INVITEE')->pluck('officer_id')->toArray();

        return view('docs.undangan-gelar-perkara-document.edit', [
            'document'              => $document,
            'accident'              => $accident,
            'accidentId'            => $accident->id,
            'officers'              => $officers,
            'authorizedSignatories' => $authorizedSignatories,
            'selectedInviteeIds'    => $selectedInviteeIds,
        ]);
    }

    /**
     * Update Undangan Gelar Perkara
     */
    public function update(Request $request, $id)
    {
        $document = UndanganGelarPerkaraDocument::findOrFail($id);
        $accidentId = $document->accident_id ?? $request->accident_id;

        $validator = Validator::make($request->all(), [
            'nomor'               => 'nullable|string|max:255',
            'sifat'               => 'required|string|max:50',
            'lampiran'            => 'nullable|string|max:100',
            'perihal'             => 'required|string|max:255',
            'tanggal'             => 'required|date',
            'hari_pelaksanaan'    => 'required|string|max:50',
            'tanggal_pelaksanaan' => 'required|date',
            'jam_pelaksanaan'     => 'required|string|max:50',
            'tempat_pelaksanaan'  => 'required|string|max:255',
            'agenda'              => 'required|string',
            'tujuan_undangan'     => 'required|string',
            'signatory'           => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
            $signatoryName = $signatory
                ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)
                : $document->signatory_name;
            $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? $document->signatory_rank);
            $signatoryNrp = $signatory->register_number ?? $document->signatory_nrp;
            $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? $document->signatory_position);

            $tembusan = [];
            if ($request->has('tembusan') && is_array($request->tembusan)) {
                $tembusan = array_values(array_filter($request->tembusan));
            }

            $document->update([
                'nomor'               => $request->nomor,
                'sifat'               => $request->sifat ?? 'Biasa',
                'lampiran'            => $request->lampiran ?? '-',
                'perihal'             => $request->perihal ?? 'Undangan Pelaksanaan Gelar Perkara',
                'tanggal'             => $request->tanggal,
                'tempat_surat'        => $request->tempat_surat ?? $document->tempat_surat,
                'dasar_hukum'         => $request->dasar_hukum,
                'hari_pelaksanaan'    => $request->hari_pelaksanaan,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'jam_pelaksanaan'     => $request->jam_pelaksanaan,
                'tempat_pelaksanaan'  => $request->tempat_pelaksanaan,
                'agenda'              => $request->agenda,
                'tujuan_undangan'     => $request->tujuan_undangan,
                'signatory_id'        => $request->signatory,
                'signatory_head_text' => $request->signatory_head_text,
                'signatory_position'  => $signatoryPosition,
                'signatory_name'      => $signatoryName,
                'signatory_rank'      => $signatoryRank,
                'signatory_nrp'       => $signatoryNrp,
                'tembusan'            => $tembusan,
                'catatan'             => $request->catatan,
                'document_number'     => $request->nomor,
                'document_date'       => $request->tanggal,
                'updated_by_user_id'  => Auth::id(),
            ]);

            // Re-sync officers
            UndanganGelarPerkaraDocumentOfficer::where('doc_id', $document->id)->delete();

            if ($signatory) {
                UndanganGelarPerkaraDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $document->id,
                    'officer_id'      => (string) $signatory->id,
                    'first_name'      => $signatory->first_name,
                    'last_name'       => $signatory->last_name,
                    'first_title'     => $signatory->first_title,
                    'last_title'      => $signatory->last_title,
                    'register_number' => $signatory->register_number,
                    'position_id'     => $signatory->position_id,
                    'rank_id'         => $signatory->rank_id,
                    'police_id'       => $signatory->police_id,
                    'class'           => 'SIGNATORY',
                    'order_number'    => 1,
                ]);
            }

            if ($request->has('officer_invitees') && is_array($request->officer_invitees)) {
                $order = 2;
                foreach ($request->officer_invitees as $officerId) {
                    $invitee = Officer::find($officerId);
                    if ($invitee) {
                        UndanganGelarPerkaraDocumentOfficer::create([
                            'id'              => (string) Uuid::generate(),
                            'doc_id'          => $document->id,
                            'officer_id'      => (string) $invitee->id,
                            'first_name'      => $invitee->first_name,
                            'last_name'       => $invitee->last_name,
                            'first_title'     => $invitee->first_title,
                            'last_title'      => $invitee->last_title,
                            'register_number' => $invitee->register_number,
                            'position_id'     => $invitee->position_id,
                            'rank_id'         => $invitee->rank_id,
                            'police_id'       => $invitee->police_id,
                            'class'           => 'INVITEE',
                            'order_number'    => $order++,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Undangan Gelar Perkara berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan detail (view/cetak) Undangan Gelar Perkara
     */
    public function show($id)
    {
        $accidentId = request()->query('accident_id');
        $document = UndanganGelarPerkaraDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.police',
            'officers' => function ($query) {
                $query->with('rank', 'position', 'police');
            },
            'signatoryOfficer' => function ($query) {
                $query->with('rank', 'position', 'police', 'user');
            },
        ])->where('id', $id)->first();

        if (!$document) {
            $document = UndanganGelarPerkaraDocument::where('id', $id)->firstOrFail();
        }

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda', 'police'])->find($accidentId ?? $document->accident_id);
        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.undangan-gelar-perkara-document.show', array_merge([
            'document' => $document,
            'accident' => $accident,
        ], $data));
    }

    /**
     * Hapus Undangan Gelar Perkara
     */
    public function delete($id)
    {
        $accidentId = request()->query('accident_id');
        $document = UndanganGelarPerkaraDocument::findOrFail($id);
        $accidentId = $accidentId ?? $document->accident_id;

        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Undangan Gelar Perkara berhasil dihapus.');
    }

    /**
     * Unduh File Word Undangan Gelar Perkara
     */
    public function download($id)
    {
        $result = $this->generatePopulatedWordFile($id);
        return response()->download($result['filename'])->deleteFileAfterSend(true);
    }

    /**
     * Siapkan seluruh data yang konsisten untuk preview (HTML) dan dokumen Word
     */
    private function prepareDocumentData($document, $accident): array
    {
        $signatory = $document->signatoryOfficer ?? null;
        if (!$signatory && !empty($document->signatory_id)) {
            try {
                $signatory = Officer::with(['rank', 'position', 'police'])->find($document->signatory_id);
            } catch (\Throwable $e) {
                $signatory = null;
            }
        }
        if (!$signatory && $document->relationLoaded('officers') && $document->officers) {
            $signatory = $document->officers->where('class', 'SIGNATORY')->first();
        }

        // Dokumen terkait untuk rujukan
        try {
            $sprinlidikDoc = SuratPerintahPenyelidikanDocument::where('accident_id', $accident->id)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();
        } catch (\Throwable $e) {
            $sprinlidikDoc = null;
        }

        try {
            $sprindikDoc = SuratPerintahPenyidikanDocument::with([
                'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
                'suratPerintahPenyidikanDocumentLaws.crimeType'
            ])->where('accident_id', $accident->id)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();
        } catch (\Throwable $e) {
            $sprindikDoc = null;
        }

        try {
            $springasDoc = \App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument::where('accident_id', $accident->id)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();
        } catch (\Throwable $e) {
            $springasDoc = null;
        }

        $signatoryFullName = $signatory
            ? ($signatory->full_name ?? ($document->signatory_name ?? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)))
            : ($document->signatory_name ?? '................................................');
        $signatoryRankName = $signatory->rank->full_name ?? ($signatory->rank->name ?? ($document->signatory_rank ?? ''));
        $signatoryRegisterNumber = $signatory->register_number ?? ($document->signatory_nrp ?? '....................');
        $signatoryPositionName = strtoupper($signatory->position->name ?? ($document->signatory_position ?? 'KEPALA SATUAN LALU LINTAS'));
        $signatoryHeadText = $document->signatory_head_text ?? '';

        // Tanggal dan Nomor LP
        $accidentNumber = $document->nomor_lp ?: ($accident->no_lp ?: ($accident->nomor_laporan_polisi ?: ($accident->accident_number ?: '-')));
        $reportDate = $document->tanggal_lp
            ? Carbon::parse($document->tanggal_lp)->locale('id')->translatedFormat('d F Y')
            : ($accident && $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : ($accident && $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-'));

        // Pelaksanaan Gelar
        $hariPelaksanaan = $document->hari_pelaksanaan
            ?: ($document->tanggal_pelaksanaan ? Carbon::parse($document->tanggal_pelaksanaan)->locale('id')->translatedFormat('l') : '-');
        $tanggalPelaksanaan = $document->tanggal_pelaksanaan
            ? Carbon::parse($document->tanggal_pelaksanaan)->locale('id')->translatedFormat('d F Y')
            : '-';

        // Format multi-line untuk Word
        $formatMultiLineWord = function ($text) {
            if (empty($text)) {
                return '-';
            }
            $lines = preg_split('/\r\n|\r|\n/', trim($text));
            return implode('</w:t><w:br/><w:t>', array_map('htmlspecialchars', $lines));
        };

        // 1. Kopstuk
        $kopstuk_1 = 'KEPOLISIAN NEGARA REPUBLIK INDONESIA';
        $kopstuk_2 = strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR');
        $kopstuk_3 = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN'));
        $kopstuk_4 = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        // 2. Info Surat
        $nomorSurat = $document->document_number ?? ($document->nomor ?? 'B/Und-...../...../Satlantas');
        $klasifikasi = $document->sifat ?? 'Biasa';
        $lampiran = $document->lampiran ?? '-';
        $perihal = $document->perihal ?? 'Undangan Pelaksanaan Gelar Perkara';

        // 3. Dikeluarkan di & Tanggal
        $dikeluarkanDi = ucwords(strtolower($document->tempat_surat ?? ($accident->polres->polres_district ?? ($accident->polres->full_name ?? ($accident->polres->name ?? 'Pasuruan')))));
        $tanggalSurat = $document->document_date ? Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : ($document->tanggal ? Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : date('d F Y'));

        // 4. Tujuan Undangan
        $kepadaTujuanRaw = $document->tujuan_undangan ?? '-';
        $kepadaTujuanWord = $formatMultiLineWord($document->tujuan_undangan);
        $tempatTujuan = 'Tempat';

        // 5. Dasar Rujukan
        $dasarUuPidana = 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;';
        if ($sprindikDoc && $sprindikDoc->suratPerintahPenyidikanDocumentLaws && $sprindikDoc->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $firstLaw = $sprindikDoc->suratPerintahPenyidikanDocumentLaws->first();
            if ($firstLaw && $firstLaw->crimeConstitution && !empty($firstLaw->crimeConstitution->name)) {
                $dasarUuPidana = trim($firstLaw->crimeConstitution->name);
                if (!str_ends_with($dasarUuPidana, ';')) {
                    $dasarUuPidana .= ';';
                }
            }
        }

        $dasarLpNomor = $accidentNumber;
        $dasarLpTanggal = $reportDate;

        $dasarSpLidikNomor = $sprinlidikDoc->nomor ?? ($sprinlidikDoc->document_number ?? ($document->nomor_sprindik ?? ($sprindikDoc->nomor ?? ($sprindikDoc->document_number ?? '-'))));
        $dasarSpLidikTanggal = $sprinlidikDoc
            ? ($sprinlidikDoc->tanggal ? Carbon::parse($sprinlidikDoc->tanggal)->locale('id')->translatedFormat('d F Y') : ($sprinlidikDoc->document_date ? Carbon::parse($sprinlidikDoc->document_date)->locale('id')->translatedFormat('d F Y') : '-'))
            : ($sprindikDoc ? ($sprindikDoc->tanggal ? Carbon::parse($sprindikDoc->tanggal)->locale('id')->translatedFormat('d F Y') : ($sprindikDoc->document_date ? Carbon::parse($sprindikDoc->document_date)->locale('id')->translatedFormat('d F Y') : '-')) : '-');

        $dasarSpGasLidikNomor = $springasDoc->nomor ?? ($springasDoc->document_number ?? '-');
        $dasarSpGasLidikTanggal = $springasDoc
            ? ($springasDoc->tanggal ? Carbon::parse($springasDoc->tanggal)->locale('id')->translatedFormat('d F Y') : ($springasDoc->document_date ? Carbon::parse($springasDoc->document_date)->locale('id')->translatedFormat('d F Y') : '-'))
            : '-';

        // 6. Isi Poin 2
        $satker = 'Satuan Lalu Lintas ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres'));

        $tindakPidana = 'Kecelakaan Lalu Lintas';
        $pasalPidana = '310 ayat (4)';
        $uuNomor = '22';
        $uuTahun = '2009';
        $uuTentang = 'Lalu Lintas dan Angkutan Jalan';

        if ($sprindikDoc && $sprindikDoc->suratPerintahPenyidikanDocumentLaws && $sprindikDoc->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $tindakPidanaParts = [];
            $pasalParts = [];
            foreach ($sprindikDoc->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->crimeType && !empty($law->crimeType->name)) {
                    $tindakPidanaParts[] = trim($law->crimeType->name);
                }
                if (!empty($law->constitution_chapter)) {
                    $pasalParts[] = trim(str_ireplace('pasal', '', $law->constitution_chapter));
                }
            }
            if (!empty($tindakPidanaParts)) {
                $tindakPidana = implode(', ', array_unique($tindakPidanaParts));
            }
            if (!empty($pasalParts)) {
                $pasalPidana = implode(', ', array_unique($pasalParts));
            }
        }

        $tkp = $accident->road_name ?: ($accident->address ?: ($accident->tempat_kejadian ?: ($document->tempat_surat ?: 'wilayah hukum')));
        $hariKejadian = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : 'Senin';
        $tglKejadian = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-';
        $jamKejadian = $accident->accident_time ? ' sekira pukul ' . Carbon::parse($accident->accident_time)->format('H:i') . ' WIB' : '';
        $waktuKejadian = "hari {$hariKejadian} tanggal {$tglKejadian}{$jamKejadian}";

        $pelaporNama = 'Masyarakat';
        if (!empty($accident->reporter_name)) {
            $pelaporNama = $accident->reporter_name;
        } elseif (!empty($accident->officer_first_name)) {
            $pelaporNama = trim($accident->officer_first_name . ' ' . ($accident->officer_last_name ?? ''));
        }

        // 7. Poin 3 Pelaksanaan
        $gelarHariTanggal = $hariPelaksanaan . ', ' . $tanggalPelaksanaan;
        $gelarWaktu = $document->jam_pelaksanaan ?: '09:00 WIB';
        $gelarTempat = $document->tempat_pelaksanaan ?: ('Ruang Gelar Perkara Satlantas Polres ' . ($accident->polres->name ?? 'Pasuruan'));
        $gelarPimpinan = $document->signatory_position ?: 'KASAT LANTAS';

        // 8. Poin 4 Narahubung / Penyidik
        $investigator = null;
        if ($document->relationLoaded('officers') && $document->officers) {
            $investigator = $document->officers->where('class', 'INVITEE')->first()
                ?? $document->officers->where('class', 'LEADER')->first();
        }
        $investigator = $investigator ?? $signatory;

        $penyidikSebutan = 'Penyidik Pembantu';
        if ($investigator && $investigator->position && !empty($investigator->position->name)) {
            $penyidikSebutan = $investigator->position->name;
        }

        $penyidikNama = $investigator
            ? ($investigator->full_name ?? ($document->signatory_name ?? PeopleNameHelper::getFullName($investigator->first_title, $investigator->first_name, $investigator->last_name, $investigator->last_title)))
            : ($document->signatory_name ?? '-');

        $penyidikNohp = $investigator->phone_number ?? ($signatory->phone_number ?? ($investigator->user->phone_number ?? '-'));
        if (empty($penyidikNohp) || $penyidikNohp === '-') {
            $penyidikNohp = '081234567890';
        }

        // 9. Tembusan
        $tembusanList = is_array($document->tembusan) ? $document->tembusan : (json_decode($document->tembusan ?? '[]', true) ?: []);
        $cleanTembusan = function($text) {
            if (empty($text)) return '-';
            $text = trim($text);
            $text = preg_replace('/^\d+[\.\)]\s*/', '', $text);
            $text = rtrim($text, ';. ');
            return trim($text);
        };

        $tembusan1 = isset($tembusanList[0]) ? $cleanTembusan($tembusanList[0]) : ('Kapolres ' . ($accident->polres->name ?? 'Pasuruan') . ' (sebagai laporan)');
        $tembusan2 = isset($tembusanList[1]) ? $cleanTembusan($tembusanList[1]) : ('Wakapolres ' . ($accident->polres->name ?? 'Pasuruan'));
        $tembusan3 = isset($tembusanList[2]) ? $cleanTembusan($tembusanList[2]) : 'Pengawas Penyidikan / Arsip';

        // 10. Tanda Tangan Atasan
        $atasanJabatanRaw = $document->signatory_head_text
            ? (strtoupper($document->signatory_head_text) . "\n" . strtoupper($signatoryPositionName))
            : strtoupper($signatoryPositionName);
        $atasanJabatanWord = $document->signatory_head_text
            ? (strtoupper($document->signatory_head_text) . "</w:t><w:br/><w:t>" . strtoupper($signatoryPositionName))
            : strtoupper($signatoryPositionName);
        $atasanNama = strtoupper($signatoryFullName);
        $atasanPangkatNrp = strtoupper($signatoryRankName) . ' NRP ' . $signatoryRegisterNumber;

        return [
            'kopstuk_1'                 => $kopstuk_1,
            'kopstuk_2'                 => $kopstuk_2,
            'kopstuk_3'                 => $kopstuk_3,
            'kopstuk_4'                 => $kopstuk_4,
            'nomor_surat'               => $nomorSurat,
            'klasifikasi'               => $klasifikasi,
            'lampiran'                  => $lampiran,
            'perihal'                   => $perihal,
            'dikeluarkan_di'            => $dikeluarkanDi,
            'tanggal_surat'             => $tanggalSurat,
            'kepada_tujuan'             => $kepadaTujuanWord,
            'kepada_tujuan_raw'         => $kepadaTujuanRaw,
            'kepada_tujuan_word'        => $kepadaTujuanWord,
            'tempat_tujuan'             => $tempatTujuan,
            'dasar_uu_pidana'           => $dasarUuPidana,
            'dasar_lp_nomor'            => $dasarLpNomor,
            'dasar_lp_tanggal'          => $dasarLpTanggal,
            'dasar_sp_lidik_nomor'      => $dasarSpLidikNomor,
            'dasar_sp_lidik_tanggal'    => $dasarSpLidikTanggal,
            'dasar_sp_gas_lidik_nomor'  => $dasarSpGasLidikNomor,
            'dasar_sp_gas_lidik_tanggal'=> $dasarSpGasLidikTanggal,
            'satker'                    => $satker,
            'tindak_pidana'             => $tindakPidana,
            'pasal_pidana'              => $pasalPidana,
            'uu_nomor'                  => $uuNomor,
            'uu_tahun'                  => $uuTahun,
            'uu_tentang'                => $uuTentang,
            'tkp'                       => $tkp,
            'waktu_kejadian'            => $waktuKejadian,
            'pelapor_nama'              => $pelaporNama,
            'gelar_hari_tanggal'        => $gelarHariTanggal,
            'gelar_waktu'               => $gelarWaktu,
            'gelar_tempat'              => $gelarTempat,
            'gelar_pimpinan'            => $gelarPimpinan,
            'penyidik_sebutan'          => $penyidikSebutan,
            'penyidik_nama'             => $penyidikNama,
            'penyidik_nohp'             => $penyidikNohp,
            'tembusan_1'                => $tembusan1,
            'tembusan_2'                => $tembusan2,
            'tembusan_3'                => $tembusan3,
            'atasan_jabatan'            => $atasanJabatanWord,
            'atasan_jabatan_raw'        => $atasanJabatanRaw,
            'atasan_jabatan_word'       => $atasanJabatanWord,
            'atasan_nama'               => $atasanNama,
            'atasan_pangkat_nrp'        => $atasanPangkatNrp,
            // Fallback backward-compatibility
            'hari_pelaksanaan'          => $hariPelaksanaan,
            'tanggal_pelaksanaan'       => $tanggalPelaksanaan,
            'signatory_full_name'       => $signatoryFullName,
            'signatory_rank_name'       => $signatoryRankName,
            'signatory_register_number' => $signatoryRegisterNumber,
            'signatory_position_name'   => $signatoryPositionName,
            'signatory_head_text'       => $signatoryHeadText,
        ];
    }

    /**
     * Generate file Word dari template word-template/surat_undangan_gelar_perkara.docx
     * yang diisi data secara lengkap.
     */
    private function generatePopulatedWordFile($id): array
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = UndanganGelarPerkaraDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.police',
            'officers' => function ($query) {
                $query->with('rank', 'position', 'police');
            },
            'signatoryOfficer' => function ($query) {
                $query->with('rank', 'position', 'police', 'user');
            },
        ])->where('id', $id)->first();

        if (!$document) {
            $document = UndanganGelarPerkaraDocument::where('id', $id)->firstOrFail();
        }

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda', 'police'])->find($accidentId ?? $document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        // Load Word Template
        $templatePath = public_path('word-template/surat_undangan_gelar_perkara.docx');
        if (!file_exists($templatePath)) {
            $templatePath = base_path('public/word-template/surat_undangan_gelar_perkara.docx');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Set all 41 variables according to the user's template format
        $templateProcessor->setValue('kopstuk_1', $data['kopstuk_1']);
        $templateProcessor->setValue('kopstuk_2', $data['kopstuk_2']);
        $templateProcessor->setValue('kopstuk_3', $data['kopstuk_3']);
        $templateProcessor->setValue('kopstuk_4', $data['kopstuk_4']);

        $templateProcessor->setValue('nomor_surat', $data['nomor_surat']);
        $templateProcessor->setValue('klasifikasi', $data['klasifikasi']);
        $templateProcessor->setValue('lampiran', $data['lampiran']);
        $templateProcessor->setValue('perihal', $data['perihal']);

        $templateProcessor->setValue('dikeluarkan_di', $data['dikeluarkan_di']);
        $templateProcessor->setValue('tanggal_surat', $data['tanggal_surat']);

        $templateProcessor->setValue('kepada_tujuan', $data['kepada_tujuan_word']);
        $templateProcessor->setValue('tempat_tujuan', $data['tempat_tujuan']);

        $templateProcessor->setValue('dasar_uu_pidana', $data['dasar_uu_pidana']);
        $templateProcessor->setValue('dasar_lp_nomor', $data['dasar_lp_nomor']);
        $templateProcessor->setValue('dasar_lp_tanggal', $data['dasar_lp_tanggal']);
        $templateProcessor->setValue('dasar_sp_lidik_nomor', $data['dasar_sp_lidik_nomor']);
        $templateProcessor->setValue('dasar_sp_lidik_tanggal', $data['dasar_sp_lidik_tanggal']);
        $templateProcessor->setValue('dasar_sp_gas_lidik_nomor', $data['dasar_sp_gas_lidik_nomor']);
        $templateProcessor->setValue('dasar_sp_gas_lidik_tanggal', $data['dasar_sp_gas_lidik_tanggal']);

        $templateProcessor->setValue('satker', $data['satker']);
        $templateProcessor->setValue('tindak_pidana', $data['tindak_pidana']);
        $templateProcessor->setValue('pasal_pidana', $data['pasal_pidana']);
        $templateProcessor->setValue('uu_nomor', $data['uu_nomor']);
        $templateProcessor->setValue('uu_tahun', $data['uu_tahun']);
        $templateProcessor->setValue('uu_tentang', $data['uu_tentang']);
        $templateProcessor->setValue('tkp', $data['tkp']);
        $templateProcessor->setValue('waktu_kejadian', $data['waktu_kejadian']);
        $templateProcessor->setValue('pelapor_nama', $data['pelapor_nama']);

        $templateProcessor->setValue('gelar_hari_tanggal', $data['gelar_hari_tanggal']);
        $templateProcessor->setValue('gelar_waktu', $data['gelar_waktu']);
        $templateProcessor->setValue('gelar_tempat', $data['gelar_tempat']);
        $templateProcessor->setValue('gelar_pimpinan', $data['gelar_pimpinan']);

        $templateProcessor->setValue('penyidik_sebutan', $data['penyidik_sebutan']);
        $templateProcessor->setValue('penyidik_nama', $data['penyidik_nama']);
        $templateProcessor->setValue('penyidik_nohp', $data['penyidik_nohp']);

        $templateProcessor->setValue('tembusan_1', $data['tembusan_1']);
        $templateProcessor->setValue('tembusan_2', $data['tembusan_2']);
        $templateProcessor->setValue('tembusan_3', $data['tembusan_3']);

        $templateProcessor->setValue('atasan_jabatan', $data['atasan_jabatan_word']);
        $templateProcessor->setValue('atasan_nama', $data['atasan_nama']);
        $templateProcessor->setValue('atasan_pangkat_nrp', $data['atasan_pangkat_nrp']);

        // Fallback backward-compatibility variables
        $templateProcessor->setValue('daerahPoliceFullName', $data['kopstuk_2']);
        $templateProcessor->setValue('resorPoliceFullName', $data['kopstuk_3']);
        $templateProcessor->setValue('resorPoliceAddress', $data['kopstuk_4']);
        $templateProcessor->setValue('documentLocation', $data['dikeluarkan_di']);
        $templateProcessor->setValue('documentDate', $data['tanggal_surat']);
        $templateProcessor->setValue('documentNumber', $data['nomor_surat']);
        $templateProcessor->setValue('documentClassificationName', $data['klasifikasi']);
        $templateProcessor->setValue('appendix', $data['lampiran']);
        $templateProcessor->setValue('tujuanUndangan', $data['kepada_tujuan_word']);
        $templateProcessor->setValue('dasarHukum', $data['dasar_uu_pidana']);
        $templateProcessor->setValue('hariPelaksanaan', $data['hari_pelaksanaan']);
        $templateProcessor->setValue('tanggalPelaksanaan', $data['tanggal_pelaksanaan']);
        $templateProcessor->setValue('jamPelaksanaan', $data['gelar_waktu']);
        $templateProcessor->setValue('tempatPelaksanaan', $data['gelar_tempat']);
        $templateProcessor->setValue('agenda', $document->agenda ?? '-');
        $templateProcessor->setValue('signatoryHeadText', $data['signatory_head_text']);
        $templateProcessor->setValue('signatoryPositionName', $data['signatory_position_name']);
        $templateProcessor->setValue('signatoryName', $data['atasan_nama']);
        $templateProcessor->setValue('signatoryRankName', strtoupper($data['signatory_rank_name']));
        $templateProcessor->setValue('signatoryRegisterNumber', $data['signatory_register_number']);
        $templateProcessor->setValue('noLp', $data['dasar_lp_nomor']);
        $templateProcessor->setValue('nomorLp', $data['dasar_lp_nomor']);
        $templateProcessor->setValue('tglLp', $data['dasar_lp_tanggal']);
        $templateProcessor->setValue('satkerName', $accident->polres->full_name ?? ($accident->polres->name ?? 'Kepolisian'));

        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0777, true);
        }

        $cleanNomor = preg_replace('/[^A-Za-z0-9_\-]/', '_', $document->nomor ?: ($document->document_number ?: 'DRAFT'));
        $filename = public_path('generate/' . $document->id . ' - Undangan Gelar Perkara - ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres')) . '.docx');
        $templateProcessor->saveAs($filename);

        return [
            'document'    => $document,
            'accident'    => $accident,
            'filename'    => $filename,
            'clean_nomor' => $cleanNomor,
        ];
    }

    private function getIndoDay($date)
    {
        $day = date('D', strtotime($date));
        $dayList = [
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        ];
        return $dayList[$day] ?? 'Senin';
    }
}
