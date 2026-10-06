<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\TemplateProcessor;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use ZipArchive;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Prison;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocument;
use App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer;
use App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentAttachment;

use App\Traits\DocsOfficersTraits;

class SuratPermohonanPerpanjanganPenahananKejaksaanDocumentController extends Controller
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
     * Tampilan form pembuatan Surat Permohonan Perpanjangan Penahanan Kejaksaan
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // 1. Dokumen SPDP
        $spdpDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSpdp = $spdpDocument->nomor ?? $spdpDocument->document_number ?? '-';
        $tanggalSpdp = $spdpDocument->tanggal ?? $spdpDocument->document_date ?? null;
        $kodeSatkerDefault = $accident->polres->satker_code ?? '006.09.05';

        // 2. Dokumen Surat Perintah Penyidikan
        $sprindikDocument = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSprindik = $sprindikDocument->nomor ?? $sprindikDocument->document_number ?? '-';
        $tanggalSprindik = $sprindikDocument->tanggal ?? $sprindikDocument->document_date ?? null;

        // Ambil uraian pasal dari Sprindik
        $pasalList = [];
        if ($sprindikDocument && $sprindikDocument->suratPerintahPenyidikanDocumentLaws) {
            foreach ($sprindikDocument->suratPerintahPenyidikanDocumentLaws as $law) {
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
        $defaultPasal = !empty($pasalList) ? implode(', ', $pasalList) : 'Pasal 310 ayat (4) UU RI No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        // 3. Dokumen Surat Ketetapan Penetapan Tersangka
        $sketTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSket = $sketTersangkaDocument->nomor ?? $sketTersangkaDocument->document_number ?? '-';
        $tanggalSket = $sketTersangkaDocument->tanggal ?? $sketTersangkaDocument->document_date ?? null;

        // 4. Dokumen Surat Perintah Penahanan (S-17)
        $suratPerintahPenahanan = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorS17 = $suratPerintahPenahanan->nomor ?? $suratPerintahPenahanan->document_number ?? '-';
        $tanggalS17 = $suratPerintahPenahanan->tanggal ?? $suratPerintahPenahanan->document_date ?? null;

        // Hitung masa penahanan lama & perpanjangan
        $tanggalAkhirPenahananLama = $suratPerintahPenahanan->tanggal_akhir ?? null;
        if (empty($tanggalAkhirPenahananLama) && !empty($suratPerintahPenahanan->tanggal_mulai)) {
            $tanggalAkhirPenahananLama = Carbon::parse($suratPerintahPenahanan->tanggal_mulai)->addDays(19)->format('Y-m-d');
        }

        // Tanggal mulai perpanjangan = 1 hari setelah akhir penahanan lama
        $tanggalMulaiPerpanjangan = $tanggalAkhirPenahananLama
            ? Carbon::parse($tanggalAkhirPenahananLama)->addDay()->format('Y-m-d')
            : date('Y-m-d');

        // Tanggal akhir perpanjangan (40 hari perpanjangan kejaksaan)
        $tanggalAkhirPerpanjangan = Carbon::parse($tanggalMulaiPerpanjangan)->addDays(39)->format('Y-m-d');

        $namaRutanDefault = $suratPerintahPenahanan->tempat_penahanan ?? ('Rutan Kepolisian Resor ' . ($accident->polres->name ?? ''));

        // 5. Tersangka perkara ini
        $suspects = Suspect::with(['gender', 'job', 'religion', 'education', 'maritalStatus', 'country', 'location'])
            ->where('accident_id', $accidentId)
            ->get();

        // 6. Kejaksaan (Prosecutors)
        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name', 'asc')->get();
        $defaultProsecutorId = $spdpDocument->prosecutor_id ?? ($sketTersangkaDocument->prosecutor_id ?? null);
        $defaultProsecutor = $defaultProsecutorId ? Prosecutor::find($defaultProsecutorId) : null;
        $defaultProsecutorLocation = $defaultProsecutor->address ?? ($accident->polres->name ?? '');

        // 7. Personel Internal & Pejabat Penandatangan
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

        // Default Satker Penyidik & TKP & Waktu
        $satkerPenyidikDefault = ($accident->polres->name ?? 'Kepolisian Resor') . ' Satuan Lalu Lintas';
        $tempatKejadianDefault = $accident->address_detail ?: ($accident->location ?: '-');
        $kurunWaktuDefault = '-';
        if ($accident->accident_date) {
            $kurunWaktuDefault = 'hari ' . Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l')
                . ', tanggal ' . Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y')
                . ($accident->accident_time ? ' sekira pukul ' . substr($accident->accident_time, 0, 5) . ' WIB' : '');
        }

        // Default Tembusan
        $defaultCarbonCopies = [
            'Ketua Pengadilan Negeri ' . ($accident->polres->name ?? 'setempat'),
            'Kepala Kepolisian Resor ' . ($accident->polres->name ?? '') . ' (sebagai laporan)',
            'Tersangka / Keluarga Tersangka',
        ];

        return view('docs.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.create', compact(
            'accidentId',
            'accident',
            'nomorSpdp',
            'tanggalSpdp',
            'kodeSatkerDefault',
            'sprindikDocument',
            'nomorSprindik',
            'tanggalSprindik',
            'sketTersangkaDocument',
            'nomorSket',
            'tanggalSket',
            'suratPerintahPenahanan',
            'nomorS17',
            'tanggalS17',
            'defaultPasal',
            'satkerPenyidikDefault',
            'tempatKejadianDefault',
            'kurunWaktuDefault',
            'tanggalAkhirPenahananLama',
            'tanggalMulaiPerpanjangan',
            'tanggalAkhirPerpanjangan',
            'namaRutanDefault',
            'suspects',
            'prosecutors',
            'defaultProsecutorId',
            'defaultProsecutorLocation',
            'internalOfficers',
            'authorizedSignatories',
            'defaultCarbonCopies'
        ));
    }

    /**
     * Simpan dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan
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

            $sphDoc = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')->first();

            // Data Kejaksaan
            $prosecutor = Prosecutor::find($request->prosecutor_id);
            $namaKejaksaan = $prosecutor ? $prosecutor->name : ($request->nama_kejaksaan ?? '-');
            $lokasiKejaksaan = $request->lokasi_kejaksaan ?: ($prosecutor->address ?? ($accident->polres->name ?? '-'));

            // Data Kontak Penyidik Penghubung
            $contactOfficer = Officer::with(['rank', 'position', 'police'])->find($request->contact_officer_id);
            $contactOfficerName = $contactOfficer
                ? PeopleNameHelper::getFullName($contactOfficer->first_title, $contactOfficer->first_name, $contactOfficer->last_name, $contactOfficer->last_title)
                : ($request->contact_officer_name ?? '-');
            $contactOfficerPhone = $request->contact_officer_phone ?? '-';

            // Data Signatory
            $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
            $signatoryName = $signatory
                ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)
                : '-';
            $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
            $signatoryNrp = $signatory->register_number ?? '-';
            $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');

            // Format Tembusan (Carbon Copies)
            $carbonCopies = [];
            if ($request->has('carbon_copies') && is_array($request->carbon_copies)) {
                $carbonCopies = array_values(array_filter($request->carbon_copies, fn($v) => !empty(trim($v))));
            }

            $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::create([
                'id'                                    => $docId,
                'accident_id'                           => $accidentId,
                'surat_perintah_penyidikan_document_id' => $sprindikDoc->id ?? null,
                'surat_ketetapan_penetapan_tersangka_id'=> $sketDoc->id ?? null,
                'surat_perintah_penahanan_document_id'  => $sphDoc->id ?? null,
                'prosecutor_id'                         => $request->prosecutor_id,
                'nomor'                                 => $request->nomor,
                'tanggal'                               => $request->tanggal,
                'klasifikasi'                           => $request->klasifikasi ?? 'BIASA',
                'lampiran'                              => $request->lampiran ?? '1 (satu) Berkas',
                'tempat_surat'                          => $request->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan'),
                'nama_kejaksaan'                        => $namaKejaksaan,
                'lokasi_kejaksaan'                      => $lokasiKejaksaan,
                'nomor_spdp'                            => $request->nomor_spdp,
                'tanggal_spdp'                          => $request->tanggal_spdp,
                'kode_satker_penerbit_spdp'             => $request->kode_satker_penerbit_spdp ?? ($accident->polres->satker_code ?? '006.09.05'),
                'nomor_sprindik'                        => $request->nomor_sprindik,
                'tanggal_sprindik'                      => $request->tanggal_sprindik,
                'nomor_penetapan_tersangka'             => $request->nomor_penetapan_tersangka,
                'tanggal_penetapan_tersangka'           => $request->tanggal_penetapan_tersangka,
                'nomor_surat_perintah_penahanan'        => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan'      => $request->tanggal_surat_perintah_penahanan,
                'satker_penyidik'                       => $request->satker_penyidik,
                'dugaan_tindak_pidana'                  => $request->dugaan_tindak_pidana,
                'pasal_diduga'                          => $request->pasal_diduga,
                'tempat_kejadian'                       => $request->tempat_kejadian,
                'kurun_waktu'                           => $request->kurun_waktu,
                'tanggal_akhir_penahanan_lama'          => $request->tanggal_akhir_penahanan_lama,
                'nama_rutan'                            => $request->nama_rutan,
                'jumlah_hari'                           => $request->jumlah_hari ?? 40,
                'tanggal_mulai_perpanjangan'            => $request->tanggal_mulai_perpanjangan,
                'tanggal_akhir_perpanjangan'            => $request->tanggal_akhir_perpanjangan,
                'contact_officer_id'                    => $request->contact_officer_id,
                'contact_officer_name'                  => $contactOfficerName,
                'contact_officer_phone'                 => $contactOfficerPhone,
                'carbon_copies'                         => $carbonCopies,
                'signatory_id'                          => $request->signatory,
                'signatory_head_text'                   => $request->signatory_head_text,
                'signatory_position'                    => $signatoryPosition,
                'signatory_name'                        => $signatoryName,
                'signatory_rank'                        => $signatoryRank,
                'signatory_nrp'                         => $signatoryNrp,
                'document_number'                       => $request->nomor,
                'document_date'                         => $request->tanggal,
                'status_id'                             => '2',
                'document_category_id'                  => '0605',
                'created_by_user_id'                    => Auth::id(),
                'updated_by_user_id'                    => Auth::id(),
                'messages'                              => [
                    'nama_kejaksaan' => $namaKejaksaan,
                    'rutan_name'     => $request->nama_rutan,
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

            // Simpan Petugas Penghubung (CONTACT)
            if ($contactOfficer) {
                SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $docId,
                    'officer_id'      => (string) $contactOfficer->id,
                    'first_name'      => $contactOfficer->first_name,
                    'last_name'       => $contactOfficer->last_name,
                    'first_title'     => $contactOfficer->first_title,
                    'last_title'      => $contactOfficer->last_title,
                    'register_number' => $contactOfficer->register_number,
                    'position_id'     => $contactOfficer->position_id,
                    'rank_id'         => $contactOfficer->rank_id,
                    'police_id'       => $contactOfficer->police_id,
                    'class'           => 'CONTACT',
                    'order_number'    => 1,
                ]);
            }

            // Simpan Pejabat Penandatangan (SIGNATORY)
            if ($signatory) {
                SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer::create([
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
                    'order_number'    => 2,
                ]);
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan form edit Surat Permohonan Perpanjangan Penahanan Kejaksaan
     */
    public function edit(string $id)
    {
        $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects',
            'prosecutor',
            'officers',
            'signatory',
            'contactOfficer',
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $accidentId = $accident->id;

        // Tersangka perkara ini
        $suspects = Suspect::with(['gender', 'job', 'religion', 'education', 'maritalStatus', 'country', 'location'])
            ->where('accident_id', $accidentId)
            ->get();

        // Kejaksaan (Prosecutors)
        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name', 'asc')->get();

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

        $selectedSuspectIds = $document->suspects->pluck('id')->toArray();
        $carbonCopies = $document->carbon_copies ?? [];

        return view('docs.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'suspects',
            'selectedSuspectIds',
            'prosecutors',
            'internalOfficers',
            'authorizedSignatories',
            'carbonCopies'
        ));
    }

    /**
     * Update dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan
     */
    public function update(Request $request, string $id)
    {
        $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::where('id', $accidentId)->firstOrFail();

        $this->validateForm($request)->validate();

        DB::beginTransaction();
        try {
            // Data Kejaksaan
            $prosecutor = Prosecutor::find($request->prosecutor_id);
            $namaKejaksaan = $prosecutor ? $prosecutor->name : ($request->nama_kejaksaan ?? '-');
            $lokasiKejaksaan = $request->lokasi_kejaksaan ?: ($prosecutor->address ?? ($accident->polres->name ?? '-'));

            // Data Kontak Penyidik Penghubung
            $contactOfficer = Officer::with(['rank', 'position', 'police'])->find($request->contact_officer_id);
            $contactOfficerName = $contactOfficer
                ? PeopleNameHelper::getFullName($contactOfficer->first_title, $contactOfficer->first_name, $contactOfficer->last_name, $contactOfficer->last_title)
                : ($request->contact_officer_name ?? '-');
            $contactOfficerPhone = $request->contact_officer_phone ?? '-';

            // Data Signatory
            $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
            $signatoryName = $signatory
                ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)
                : '-';
            $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
            $signatoryNrp = $signatory->register_number ?? '-';
            $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');

            // Format Tembusan (Carbon Copies)
            $carbonCopies = [];
            if ($request->has('carbon_copies') && is_array($request->carbon_copies)) {
                $carbonCopies = array_values(array_filter($request->carbon_copies, fn($v) => !empty(trim($v))));
            }

            $document->update([
                'prosecutor_id'                         => $request->prosecutor_id,
                'nomor'                                 => $request->nomor,
                'tanggal'                               => $request->tanggal,
                'klasifikasi'                           => $request->klasifikasi ?? 'BIASA',
                'lampiran'                              => $request->lampiran ?? '1 (satu) Berkas',
                'tempat_surat'                          => $request->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan'),
                'nama_kejaksaan'                        => $namaKejaksaan,
                'lokasi_kejaksaan'                      => $lokasiKejaksaan,
                'nomor_spdp'                            => $request->nomor_spdp,
                'tanggal_spdp'                          => $request->tanggal_spdp,
                'kode_satker_penerbit_spdp'             => $request->kode_satker_penerbit_spdp ?? ($accident->polres->satker_code ?? '006.09.05'),
                'nomor_sprindik'                        => $request->nomor_sprindik,
                'tanggal_sprindik'                      => $request->tanggal_sprindik,
                'nomor_penetapan_tersangka'             => $request->nomor_penetapan_tersangka,
                'tanggal_penetapan_tersangka'           => $request->tanggal_penetapan_tersangka,
                'nomor_surat_perintah_penahanan'        => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan'      => $request->tanggal_surat_perintah_penahanan,
                'satker_penyidik'                       => $request->satker_penyidik,
                'dugaan_tindak_pidana'                  => $request->dugaan_tindak_pidana,
                'pasal_diduga'                          => $request->pasal_diduga,
                'tempat_kejadian'                       => $request->tempat_kejadian,
                'kurun_waktu'                           => $request->kurun_waktu,
                'tanggal_akhir_penahanan_lama'          => $request->tanggal_akhir_penahanan_lama,
                'nama_rutan'                            => $request->nama_rutan,
                'jumlah_hari'                           => $request->jumlah_hari ?? 40,
                'tanggal_mulai_perpanjangan'            => $request->tanggal_mulai_perpanjangan,
                'tanggal_akhir_perpanjangan'            => $request->tanggal_akhir_perpanjangan,
                'contact_officer_id'                    => $request->contact_officer_id,
                'contact_officer_name'                  => $contactOfficerName,
                'contact_officer_phone'                 => $contactOfficerPhone,
                'carbon_copies'                         => $carbonCopies,
                'signatory_id'                          => $request->signatory,
                'signatory_head_text'                   => $request->signatory_head_text,
                'signatory_position'                    => $signatoryPosition,
                'signatory_name'                        => $signatoryName,
                'signatory_rank'                        => $signatoryRank,
                'signatory_nrp'                         => $signatoryNrp,
                'document_number'                       => $request->nomor,
                'document_date'                         => $request->tanggal,
                'updated_by_user_id'                    => Auth::id(),
                'messages'                              => [
                    'nama_kejaksaan' => $namaKejaksaan,
                    'rutan_name'     => $request->nama_rutan,
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
            SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer::where('doc_id', $document->id)->delete();

            // Simpan Petugas Penghubung (CONTACT)
            if ($contactOfficer) {
                SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $document->id,
                    'officer_id'      => (string) $contactOfficer->id,
                    'first_name'      => $contactOfficer->first_name,
                    'last_name'       => $contactOfficer->last_name,
                    'first_title'     => $contactOfficer->first_title,
                    'last_title'      => $contactOfficer->last_title,
                    'register_number' => $contactOfficer->register_number,
                    'position_id'     => $contactOfficer->position_id,
                    'rank_id'         => $contactOfficer->rank_id,
                    'police_id'       => $contactOfficer->police_id,
                    'class'           => 'CONTACT',
                    'order_number'    => 1,
                ]);
            }

            // Simpan Pejabat Penandatangan (SIGNATORY)
            if ($signatory) {
                SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer::create([
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
                    'order_number'    => 2,
                ]);
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Hapus dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan
     */
    public function delete(string $id)
    {
        $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::findOrFail($id);
        $accidentId = $document->accident_id;

        DB::beginTransaction();
        try {
            $document->suspects()->detach();
            SuratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficer::where('doc_id', $document->id)->delete();
            SuratPermohonanPerpanjanganPenahananKejaksaanDocumentAttachment::where('doc_id', $document->id)->delete();
            $document->delete();

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Download File Word Dokumen Surat Permohonan Perpanjangan Penahanan Kejaksaan
     */
    public function download(string $id)
    {
        $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects',
            'prosecutor',
            'signatory',
            'contactOfficer',
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $templatePath = public_path('word-template/surat_permohonan_perpanjangan_penahanan_kejaksaan.docx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template Word tidak ditemukan di server.');
        }

        $tempDocx = tempnam(sys_get_temp_dir(), 'spp_kejaksaan_') . '.docx';
        copy($templatePath, $tempDocx);

        // Pre-process Word XML: Clean runs and fix template typos
        $this->fixWordTemplateXml($tempDocx);

        $templateProcessor = new TemplateProcessor($tempDocx);

        // 1. Header / Kop Surat
        $polda = $accident->polres->polda->name ?? 'JAWA TIMUR';
        $resor = $accident->polres->name ?? 'RESOR PASURUAN';
        $alamat = $accident->polres->address ?? '';
        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($polda));
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($resor));
        $templateProcessor->setValue('resorPoliceAddress', $alamat);

        // Meta Dokumen
        $location = $document->tempat_surat ?: ($accident->polres->name ?? 'Pasuruan');
        $docDate = $document->tanggal ? $this->formatDateIndo($document->tanggal) : $this->formatDateIndo(now());
        $templateProcessor->setValue('documentLocation', $location);
        $templateProcessor->setValue('documentDate', $docDate);
        $templateProcessor->setValue('documentNumber', $document->nomor ?? $document->document_number ?? '-');
        $templateProcessor->setValue('documentClassificationName', strtoupper($document->klasifikasi ?? 'BIASA'));
        $templateProcessor->setValue('appendix', $document->lampiran ?? '1 (satu) Berkas');

        // Kejaksaan Tujuan
        $prosecutorName = $document->nama_kejaksaan ?: ($document->prosecutor->name ?? 'KEJAKSAAN NEGERI');
        $prosecutorLocation = $document->lokasi_kejaksaan ?: ($document->prosecutor->address ?? $location);
        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('prosecutorLocation', $prosecutorLocation);

        // 2. Poin 1 (Rujukan Dasar)
        $noLp = $accident->no_lp ?? '-';
        $tglLp = $accident->accident_date ? $this->formatDateIndo($accident->accident_date) : '-';
        $templateProcessor->setValue('accidentNumber', $noLp);
        $templateProcessor->setValue('accidentDate', $tglLp);

        $templateProcessor->setValue('sprindikDocumentNumber', $document->nomor_sprindik ?? '-');
        $templateProcessor->setValue('sprindikDocumentDate', $document->tanggal_sprindik ? $this->formatDateIndo($document->tanggal_sprindik) : '-');

        $templateProcessor->setValue('spdpDocumentNumber', $document->nomor_spdp ?? '-');
        $templateProcessor->setValue('spdpDocumentDate', $document->tanggal_spdp ? $this->formatDateIndo($document->tanggal_spdp) : '-');

        $templateProcessor->setValue('stap_tersangkaDocumentNumber', $document->nomor_penetapan_tersangka ?? '-');
        $templateProcessor->setValue('stap_tersangkaDocumentDate', $document->tanggal_penetapan_tersangka ? $this->formatDateIndo($document->tanggal_penetapan_tersangka) : '-');

        $templateProcessor->setValue('sphDocumentNumber', $document->nomor_surat_perintah_penahanan ?? '-');
        $templateProcessor->setValue('sphDocumentDate', $document->tanggal_surat_perintah_penahanan ? $this->formatDateIndo($document->tanggal_surat_perintah_penahanan) : '-');

        // Tersangka
        $suspect = $document->suspects->first();
        $suspectName = $suspect ? strtoupper($suspect->name) : '-';
        $templateProcessor->setValue('suspectName', $suspectName);

        // 3. Poin 2 (Perkara)
        $satkerPenyidik = $document->satker_penyidik ?: (($accident->polres->name ?? 'Kepolisian Resor') . ' Satuan Lalu Lintas');
        $dugaanPidana = $document->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat';
        $pasalDiduga = $document->pasal_diduga ?: 'Pasal 310 ayat (4) UU RI No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
        $tempatKejadian = $document->tempat_kejadian ?: ($accident->address_detail ?: ($accident->location ?: '-'));
        $kurunWaktu = $document->kurun_waktu ?: '-';

        $templateProcessor->setValue('satker_penyidik', $satkerPenyidik);
        $templateProcessor->setValue('dugaan_tindak_pidana', $dugaanPidana);
        $templateProcessor->setValue('pasal_diduga', $pasalDiduga);
        $templateProcessor->setValue('tempat_kejadian', $tempatKejadian);
        $templateProcessor->setValue('kurun_waktu', $kurunWaktu);

        // 4. Poin 3 (Masa Penahanan & Perpanjangan)
        $lastDatePenahanan = $document->tanggal_akhir_penahanan_lama ? $this->formatDateIndo($document->tanggal_akhir_penahanan_lama) : '-';
        $namaRutan = $document->nama_rutan ?: ('Rutan Kepolisian Resor ' . ($accident->polres->name ?? ''));
        $extStartDate = $document->tanggal_mulai_perpanjangan ? $this->formatDateIndo($document->tanggal_mulai_perpanjangan) : '-';
        $extEndDate = $document->tanggal_akhir_perpanjangan ? $this->formatDateIndo($document->tanggal_akhir_perpanjangan) : '-';

        $contactName = $document->contact_officer_name ?: ($document->contactOfficer ? PeopleNameHelper::getFullName($document->contactOfficer->first_title, $document->contactOfficer->first_name, $document->contactOfficer->last_name, $document->contactOfficer->last_title) : '-');
        $contactPhone = $document->contact_officer_phone ?: '-';

        $templateProcessor->setValue('lastDate_penahanan', $lastDatePenahanan);
        $templateProcessor->setValue('rutan_name', $namaRutan);
        $templateProcessor->setValue('extensionStartDate', $extStartDate);
        $templateProcessor->setValue('extensionEndDate', $extEndDate);
        $templateProcessor->setValue('contactOfficerName', $contactName);
        $templateProcessor->setValue('contactOfficerPhone', $contactPhone);

        // 5. Poin 4 & Tembusan (Carbon Copies)
        $carbonCopies = $document->carbon_copies;
        $blockCarbonCopies = [];
        if (!empty($carbonCopies) && is_array($carbonCopies)) {
            $iteration = 1;
            foreach ($carbonCopies as $cc) {
                if (!empty(trim($cc))) {
                    $blockCarbonCopies[] = [
                        'carbon_copy_iteration' => (string) $iteration++,
                        'carbon_copy_name'      => trim($cc),
                    ];
                }
            }
        }

        if (empty($blockCarbonCopies)) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => '-',
                'carbon_copy_name'      => '-',
            ];
        }

        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

        // 6. Signatory (Penandatangan)
        $signatoryHeadText = $document->signatory_head_text ?? ('a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->name ?? ''));
        $signatoryPosition = $document->signatory_position ?? 'KASAT LANTAS';
        $signatoryName = $document->signatory_name ?? '-';
        $signatoryRank = $document->signatory_rank ?? '-';
        $signatoryNrp = $document->signatory_nrp ?? '-';

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPosition);
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', $signatoryRank);
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryNrp);

        $outputFilename = 'SURAT_PERMOHONAN_PERPANJANGAN_PENAHANAN_KEJAKSAAN_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $document->nomor ?: 'DRAFT') . '.docx';

        $outputFullPath = tempnam(sys_get_temp_dir(), 'spp_kejaksaan_out_') . '.docx';
        $templateProcessor->saveAs($outputFullPath);

        @unlink($tempDocx);

        return response()->download($outputFullPath, $outputFilename)->deleteFileAfterSend(true);
    }

    /**
     * Tampilan Detail / Format JSON S-21 (SPPT-TI / Pusiknas)
     */
    public function show(string $id, Request $request)
    {
        $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            'accident.suratPerintahPenahananDocuments',
            'prosecutor',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.rank',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.position',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.police',
            'status',
            'attachment'
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $jsonPayload = self::buildJsonPayloadStatic($document);

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return view('docs.surat-permohonan-perpanjangan-penahanan-kejaksaan-document.show', compact('document', 'accident', 'jsonPayload'));
    }

    /**
     * Endpoint API murni JSON untuk integrasi atau pengujian
     */
    public function json(string $id, Request $request)
    {
        $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            'accident.suratPerintahPenahananDocuments',
            'prosecutor',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.rank',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.position',
            'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.police',
            'status',
            'attachment'
        ])->where('id', $id)->firstOrFail();

        $jsonPayload = self::buildJsonPayloadStatic($document);

        return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Helper membuat JSON S-21 persis dengan contoh user & spesifikasi SPPT-TI
     */
    public function buildJsonPayload(SuratPermohonanPerpanjanganPenahananKejaksaanDocument $document): array
    {
        return self::buildJsonPayloadStatic($document);
    }

    /**
     * Static helper untuk membuat JSON S-21 sesuai format baku yang dibutuhkan
     */
    public static function buildJsonPayloadStatic(SuratPermohonanPerpanjanganPenahananKejaksaanDocument $document): array
    {
        $accident = $document->accident;

        // 1. Identitas Dokumen (Tabel a)
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
            'nomor_spdp'                     => (string) ($nomorSpdp ?: ($document->exists ? '-' : 'SPDP/326/IX/2017')),
            'tanggal_spdp'                   => $tanggalSpdp ? Carbon::parse($tanggalSpdp)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-01-15'),
            'kode_satker_penerbit_spdp'      => (string) ($document->kode_satker_penerbit_spdp ?: ($accident->polres->satker_code ?? '006.09.05')),
            'nomor_surat_perintah_penahanan' => (string) ($nomorPenahanan ?: ($document->exists ? '-' : 'S17/3/12/2012.PolresJAKPUS')),
        ];

        // 2. Konten Dokumen - Pejabat Penandatangan (Tabel b: schema aparat_negara)
        $pejabatPenandatangan = [];
        $signatories = $document->suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers
            ? $document->suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers->where('class', 'SIGNATORY')
            : collect();

        if ($signatories->isEmpty() && $document->signatory_name) {
            $pejabatPenandatangan[] = [
                'nama'        => (string) $document->signatory_name,
                'nomor_induk' => (string) ($document->signatory_nrp ?: '-'),
                'jabatan'     => (string) ($document->signatory_position ?: '-'),
                'pangkat'     => (string) ($document->signatory_rank ?: '-'),
            ];
        } elseif ($signatories->isNotEmpty()) {
            foreach ($signatories as $officer) {
                $pejabatPenandatangan[] = [
                    'nama'        => PeopleNameHelper::getFullName($officer->first_title, $officer->first_name, $officer->last_name, $officer->last_title),
                    'nomor_induk' => (string) ($officer->register_number ?: '-'),
                    'jabatan'     => (string) ($officer->position->name ?? ($officer->position_id ?? '-')),
                    'pangkat'     => (string) ($officer->rank->name ?? ($officer->rank_id ?? '-')),
                ];
            }
        } elseif ($document->suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers && $document->suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers->isNotEmpty()) {
            $firstOfficer = $document->suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers->first();
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

        // 3. Konten Dokumen - Dokumen Digital
        $daftarDokumenDigital = [];
        $attachment = $document->attachment ?? ($document->suratPermohonanPerpanjanganPenahananKejaksaanDocumentAttachment ?? null);

        if ($attachment && !empty($attachment->name)) {
            $filePath = public_path('documents/attachments/' . $attachment->name);
            $base64 = File::exists($filePath) ? base64_encode(File::get($filePath)) : 'U3dhZ2dlciByb2Nrcw==';
            $item = [
                'kode_jenis_dokumen' => 's21',
                'mime_type'          => $attachment->mimetype ?? 'application/pdf',
                'file'               => $base64,
            ];
            if (!empty($attachment->url)) {
                $item['url'] = $attachment->url;
            }
            $daftarDokumenDigital[] = $item;
        }

        if (empty($daftarDokumenDigital)) {
            $daftarDokumenDigital = [
                [
                    'kode_jenis_dokumen' => 's21',
                    'mime_type'          => 'application/pdf',
                    'file'               => 'U3dhZ2dlciByb2Nrcw==',
                    'url'                => 'http://sign.kejaksaan.go.id/perkara/abc.pdf',
                ],
                [
                    'kode_jenis_dokumen' => 'sprindik',
                    'mime_type'          => 'application/pdf',
                    'file'               => 'U3dhZ2dlciByb2Nrcw==',
                ],
                [
                    'kode_jenis_dokumen' => 'lp',
                    'mime_type'          => 'application/pdf',
                    'file'               => 'U3dhZ2dlciByb2Nrcw==',
                ],
            ];
        }

        // 4. Konten Dokumen (Urutan field sesuai contoh JSON user)
        $kontenDokumen = [
            'kode_satker_tempat_penahanan'         => (string) ($document->kode_satker_tempat_penahanan ?: ($accident->polres->satker_code ?? '006.09.05')),
            'tanggal_mulai_perpanjangan_penahanan' => $document->tanggal_mulai_perpanjangan ? Carbon::parse($document->tanggal_mulai_perpanjangan)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-03-05'),
            'tanggal_akhir_perpanjangan_penahanan' => $document->tanggal_akhir_perpanjangan ? Carbon::parse($document->tanggal_akhir_perpanjangan)->format('Y-m-d') : ($document->exists ? date('Y-m-d') : '2019-04-15'),
            'pejabat_penandatangan'                => $pejabatPenandatangan,
            'daftar_dokumen_digital'               => $daftarDokumenDigital,
        ];

        return [
            'kode_jenis_dokumen' => 's21',
            'identitas_dokumen'  => $identitasDokumen,
            'konten_dokumen'     => $kontenDokumen,
        ];
    }

    /**
     * Fix XML issues in template: Run tags inside variable delimiters and typo in extensionEndDate
     */
    private function fixWordTemplateXml(string $filePath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            $xml = $zip->getFromName('word/document.xml');

            // 1. Merge runs split across ${...}
            $xml = preg_replace_callback('/\$\{([^\}]+)\}/s', function ($matches) {
                return '${' . strip_tags($matches[1]) . '}';
            }, $xml);

            // 2. Fix template typo: ${extensionEndDate, -> ${extensionEndDate}
            $xml = preg_replace('/(\$\{extensionEndDate)([\s,])/i', '$1}$2', $xml);

            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }
    }

    /**
     * Validasi form dokumen melalui AJAX
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
                'errors'  => 'Terjadi kesalahan pada sistem.',
                'code'    => 500,
            ], 500);
        }
    }

    /**
     * Validasi form permohonan perpanjangan penahanan
     */
    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'accident_id'                 => 'required|exists:accidents,id',
            'nomor'                       => 'required|string|max:255',
            'tanggal'                     => 'required|date',
            'prosecutor_id'               => 'required|string',
            'suspects'                    => 'required|array|min:1',
            'suspects.*'                  => 'required|exists:suspects,id',
            'signatory'                   => 'required|string',
            'contact_officer_id'          => 'required|string',
            'tanggal_mulai_perpanjangan'  => 'required|date',
            'tanggal_akhir_perpanjangan'  => 'required|date|after_or_equal:tanggal_mulai_perpanjangan',
            'nama_rutan'                  => 'required|string|max:255',
        ], [
            'nomor.required'                      => 'Nomor surat permohonan wajib diisi.',
            'tanggal.required'                    => 'Tanggal surat permohonan wajib diisi.',
            'prosecutor_id.required'              => 'Kejaksaan penerima wajib dipilih.',
            'suspects.required'                   => 'Minimal 1 (satu) tersangka harus dipilih.',
            'signatory.required'                  => 'Pejabat Penandatangan wajib dipilih.',
            'contact_officer_id.required'         => 'Penyidik/Penyidik Pembantu penghubung wajib dipilih.',
            'tanggal_mulai_perpanjangan.required' => 'Tanggal mulai perpanjangan penahanan wajib diisi.',
            'tanggal_akhir_perpanjangan.required' => 'Tanggal akhir perpanjangan penahanan wajib diisi.',
            'nama_rutan.required'                 => 'Nama tempat / Rutan penahanan wajib diisi.',
        ]);
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
}
