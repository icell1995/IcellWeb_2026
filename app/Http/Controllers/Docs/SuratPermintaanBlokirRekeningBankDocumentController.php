<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
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
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPermintaanBlokirRekeningBankDocument\SuratPermintaanBlokirRekeningBankDocument;
use App\Models\Doc\SuratPermintaanBlokirRekeningBankDocument\SuratPermintaanBlokirRekeningBankDocumentOfficer;

use App\Traits\DocsOfficersTraits;

class SuratPermintaanBlokirRekeningBankDocumentController extends Controller
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
     * Form pembuatan Surat Permintaan Pemblokiran Rekening Bank
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda', 'suspects.gender', 'suspects.job', 'suspects.religion'])
            ->where('id', $accidentId)->firstOrFail();

        // 1. Dokumen Surat Perintah Penyidikan
        $sprindikDocument = SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType'
        ])->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->first();

        $nomorSprindik = $sprindikDocument->nomor ?? ($sprindikDocument->document_number ?? null);
        $tanggalSprindik = $sprindikDocument->tanggal ?? ($sprindikDocument->document_date ?? null);

        // 2. Tersangka
        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accidentId)->get();
        $selectedSuspect = $suspects->first();

        // 3. Petugas internal
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->get();

        // 4. Pejabat Penandatangan (Kasat Lantas / KBO)
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
        $tempatDefault = 'Ruang Unit Gakkum Satlantas Polres ' . ($accident->polres->name ?? 'Pasuruan');

        // Nomor dan Tanggal LP
        $noLp = $accident->no_lp ?: ($accident->nomor_laporan_polisi ?: ($accident->accident_number ?: '-'));
        $tglLp = $accident->accident_date
            ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y')
            : ($accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-');

        // Defaults rujukan hukum
        $defaultDasarKuhp = 'Kitab Undang-Undang Hukum Pidana (KUHP);';
        $defaultDasarKuhap = 'Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d dan j, Pasal 11, Pasal 38 ayat (1), Pasal 39 ayat (1) dan Pasal 44 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana;';
        $defaultDasarPolri = 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;';
        $defaultDasarUuPidana = 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;';

        if ($sprindikDocument && $sprindikDocument->suratPerintahPenyidikanDocumentLaws && $sprindikDocument->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $firstLaw = $sprindikDocument->suratPerintahPenyidikanDocumentLaws->first();
            if ($firstLaw && $firstLaw->crimeConstitution && !empty($firstLaw->crimeConstitution->name)) {
                $defaultDasarUuPidana = trim($firstLaw->crimeConstitution->name);
                if (!str_ends_with($defaultDasarUuPidana, ';')) {
                    $defaultDasarUuPidana .= ';';
                }
            }
        }

        // Default pasal pidana & tindak pidana
        $tindakPidana = 'Kecelakaan Lalu Lintas';
        $pasalPidana = '310 ayat (4)';
        $uuNomor = '22';
        $uuTahun = '2009';
        $uuTentang = 'Lalu Lintas dan Angkutan Jalan';

        if ($sprindikDocument && $sprindikDocument->suratPerintahPenyidikanDocumentLaws && $sprindikDocument->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            $tindakPidanaParts = [];
            $pasalParts = [];
            foreach ($sprindikDocument->suratPerintahPenyidikanDocumentLaws as $law) {
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

        // TKP dan waktu kejadian
        $tkp = $accident->road_name ?: ($accident->address ?: ($accident->tempat_kejadian ?: 'wilayah hukum Polres ' . ($accident->polres->name ?? 'Pasuruan')));
        $hariKejadian = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : 'Senin';
        $tglKejadian = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-';
        $jamKejadian = $accident->accident_time ? ' sekira pukul ' . Carbon::parse($accident->accident_time)->format('H:i') . ' WIB' : '';
        $waktuKejadian = "hari {$hariKejadian} tanggal {$tglKejadian}{$jamKejadian}";

        // Default Bank Tujuan
        $defaultBankTujuan = "Direktur Utama PT. Bank Rakyat Indonesia (Persero) Tbk.\nUp. Pimpinan Kantor Cabang " . ($accident->polres->name ?? 'Pasuruan');
        $defaultTempatTujuan = ucwords(strtolower($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan')));

        // Default Objek Blokir
        $defaultObjekBlokir = 'Rekening Bank atas nama ' . ($selectedSuspect->name ?? 'Tersangka') . ' dengan nomor rekening: [Isi Nomor Rekening] pada Bank [Isi Nama Bank]';

        return view('docs.surat-permintaan-blokir-rekening-bank-document.create', [
            'accident'               => $accident,
            'accidentId'             => $accidentId,
            'sprindikDocument'       => $sprindikDocument,
            'nomorSprindik'          => $nomorSprindik,
            'tanggalSprindik'        => $tanggalSprindik ? Carbon::parse($tanggalSprindik)->locale('id')->translatedFormat('d F Y') : '-',
            'suspects'               => $suspects,
            'selectedSuspect'        => $selectedSuspect,
            'officers'               => $officers,
            'authorizedSignatories'  => $authorizedSignatories,
            'today'                  => $today,
            'hariIni'                => $hariIni,
            'jamIni'                 => $jamIni,
            'tempatDefault'          => $tempatDefault,
            'noLp'                   => $noLp,
            'tglLp'                  => $tglLp,
            'defaultDasarKuhp'       => $defaultDasarKuhp,
            'defaultDasarKuhap'      => $defaultDasarKuhap,
            'defaultDasarPolri'      => $defaultDasarPolri,
            'defaultDasarUuPidana'   => $defaultDasarUuPidana,
            'tindakPidana'           => $tindakPidana,
            'pasalPidana'            => $pasalPidana,
            'uuNomor'                => $uuNomor,
            'uuTahun'                => $uuTahun,
            'uuTentang'              => $uuTentang,
            'tkp'                    => $tkp,
            'waktuKejadian'          => $waktuKejadian,
            'defaultBankTujuan'      => $defaultBankTujuan,
            'defaultTempatTujuan'    => $defaultTempatTujuan,
            'defaultObjekBlokir'     => $defaultObjekBlokir,
        ]);
    }

    /**
     * Menyimpan Surat Permintaan Pemblokiran Rekening Bank
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id ?: $request->query('accident_id');
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nomor'               => 'nullable|string|max:255',
            'sifat'               => 'required|string|max:50',
            'lampiran'            => 'nullable|string|max:100',
            'perihal'             => 'required|string|max:255',
            'tanggal'             => 'required|date',
            'tempat_surat'        => 'nullable|string|max:255',
            'bank_tujuan'         => 'required|string',
            'tempat_tujuan'       => 'required|string|max:255',
            'objek_blokir'        => 'required|string',
            'blokir_hari_tanggal' => 'required|string|max:100',
            'blokir_waktu'        => 'required|string|max:50',
            'blokir_tempat'       => 'required|string|max:255',
            'signatory'           => 'required|string',
        ], [
            'tanggal.required'             => 'Tanggal surat wajib diisi.',
            'sifat.required'               => 'Sifat surat wajib dipilih.',
            'perihal.required'             => 'Perihal surat wajib diisi.',
            'bank_tujuan.required'         => 'Pihak Bank Tujuan (Kepada Yth) wajib diisi.',
            'tempat_tujuan.required'       => 'Tempat/Kota Bank Tujuan wajib diisi.',
            'objek_blokir.required'        => 'Objek pemblokiran (rekening bank) wajib diisi.',
            'blokir_hari_tanggal.required' => 'Hari dan tanggal pelaksanaan blokir wajib diisi.',
            'blokir_waktu.required'        => 'Waktu pelaksanaan blokir wajib diisi.',
            'blokir_tempat.required'       => 'Tempat pelaksanaan wajib diisi.',
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

            // Data Signatory
            $signatory = Officer::with(['rank', 'position', 'police'])->find($request->signatory);
            $signatoryName = $signatory
                ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)
                : '-';
            $signatoryRank = $signatory->rank->name ?? ($signatory->rank_id ?? '-');
            $signatoryNrp = $signatory->register_number ?? '-';
            $signatoryPosition = $signatory->position->name ?? ($signatory->position_id ?? 'KASAT LANTAS');

            // Data Penyidik / Narahubung
            $investigator = null;
            if (!empty($request->penyidik_id)) {
                $investigator = Officer::with(['rank', 'position', 'user'])->find($request->penyidik_id);
            }
            $penyidikNama = $request->penyidik_nama ?: ($investigator ? PeopleNameHelper::getFullName($investigator->first_title, $investigator->first_name, $investigator->last_name, $investigator->last_title) : '-');
            $penyidikNohp = $request->penyidik_nohp ?: ($investigator->phone_number ?? ($investigator->user->phone_number ?? '-'));
            $penyidikSebutan = $request->penyidik_sebutan ?: ($investigator->position->name ?? 'Penyidik Pembantu');

            // Data Tersangka
            $suspect = null;
            if (!empty($request->suspect_id)) {
                $suspect = Suspect::with(['gender', 'job', 'religion'])->find($request->suspect_id);
            }
            $tersangkaNama = $request->tersangka_nama ?: ($suspect->name ?? '-');
            $tersangkaNik = $request->tersangka_no_identitas ?: ($suspect->nik ?? ($suspect->sim_number ?? '-'));
            $tersangkaKewarganegaraan = $request->tersangka_kewarganegaraan ?: 'WNI';
            $tersangkaKelamin = $request->tersangka_jenis_kelamin ?: ($suspect->gender->name ?? 'Laki-laki');
            $tersangkaTtl = $request->tersangka_ttl ?: (($suspect && $suspect->birth_place) ? ($suspect->birth_place . ', ' . ($suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-')) : '-');
            $tersangkaPekerjaan = $request->tersangka_pekerjaan ?: ($suspect->job->name ?? 'Swasta');
            $tersangkaAgama = $request->tersangka_agama ?: ($suspect->religion->name ?? 'Islam');
            $tersangkaAlamat = $request->tersangka_alamat ?: ($suspect->address ?? '-');

            $document = SuratPermintaanBlokirRekeningBankDocument::create([
                'id'                                    => $docId,
                'accident_id'                           => $accidentId,
                'surat_perintah_penyidikan_document_id' => $sprindikDoc->id ?? null,
                'suspect_id'                            => $request->suspect_id ?? null,
                'nomor'                                 => $request->nomor,
                'sifat'                                 => $request->sifat ?? 'Biasa',
                'lampiran'                              => $request->lampiran ?? '-',
                'perihal'                               => $request->perihal ?? 'Permintaan Pemblokiran Rekening Bank',
                'tanggal'                               => $request->tanggal,
                'tempat_surat'                          => $request->tempat_surat ?? ($accident->polres->name ?? 'Pasuruan'),
                'bank_tujuan'                           => $request->bank_tujuan,
                'tempat_tujuan'                         => $request->tempat_tujuan ?? 'Tempat',
                'dasar_kuhp'                            => $request->dasar_kuhp,
                'dasar_kuhap'                           => $request->dasar_kuhap,
                'dasar_polri'                           => $request->dasar_polri,
                'dasar_uu_pidana'                       => $request->dasar_uu_pidana,
                'dasar_lp_nomor'                        => $request->dasar_lp_nomor,
                'dasar_lp_tanggal'                      => $request->dasar_lp_tanggal,
                'dasar_sp_sidik_nomor'                  => $request->dasar_sp_sidik_nomor,
                'dasar_sp_sidik_tanggal'                => $request->dasar_sp_sidik_tanggal,
                'satker'                                => $request->satker ?? ('Satuan Lalu Lintas Polres ' . ($accident->polres->name ?? 'Pasuruan')),
                'tindak_pidana'                         => $request->tindak_pidana ?? 'Kecelakaan Lalu Lintas',
                'pasal_pidana'                          => $request->pasal_pidana ?? '310 ayat (4)',
                'uu_nomor'                              => $request->uu_nomor ?? '22',
                'uu_tahun'                              => $request->uu_tahun ?? '2009',
                'uu_tentang'                            => $request->uu_tentang ?? 'Lalu Lintas dan Angkutan Jalan',
                'tkp'                                   => $request->tkp,
                'waktu_kejadian'                        => $request->waktu_kejadian,
                'tersangka_nama'                        => $tersangkaNama,
                'tersangka_no_identitas'                => $tersangkaNik,
                'tersangka_kewarganegaraan'             => $tersangkaKewarganegaraan,
                'tersangka_jenis_kelamin'               => $tersangkaKelamin,
                'tersangka_ttl'                         => $tersangkaTtl,
                'tersangka_pekerjaan'                   => $tersangkaPekerjaan,
                'tersangka_agama'                       => $tersangkaAgama,
                'tersangka_alamat'                      => $tersangkaAlamat,
                'objek_blokir'                          => $request->objek_blokir,
                'blokir_hari_tanggal'                   => $request->blokir_hari_tanggal,
                'blokir_waktu'                          => $request->blokir_waktu,
                'blokir_tempat'                         => $request->blokir_tempat,
                'penyidik_id'                           => $request->penyidik_id ?? null,
                'penyidik_sebutan'                      => $penyidikSebutan,
                'penyidik_nama'                         => $penyidikNama,
                'penyidik_nohp'                         => $penyidikNohp,
                'tembusan_1'                            => $request->tembusan_1,
                'tembusan_2'                            => $request->tembusan_2,
                'tembusan_3'                            => $request->tembusan_3,
                'tembusan_4'                            => $request->tembusan_4,
                'tembusan_5'                            => $request->tembusan_5,
                'signatory_id'                          => $request->signatory,
                'signatory_head_text'                   => $request->signatory_head_text,
                'signatory_position'                    => $signatoryPosition,
                'signatory_name'                        => $signatoryName,
                'signatory_rank'                        => $signatoryRank,
                'signatory_nrp'                         => $signatoryNrp,
                'catatan'                               => $request->catatan,
                'document_number'                       => $request->nomor,
                'document_date'                         => $request->tanggal,
                'status_id'                             => '2',
                'document_category_id'                  => '0707',
                'created_by_user_id'                    => Auth::id(),
                'updated_by_user_id'                    => Auth::id(),
            ]);

            // Simpan Signatory di Officers
            if ($signatory) {
                SuratPermintaanBlokirRekeningBankDocumentOfficer::create([
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
                    'created_by_user_id' => Auth::id(),
                ]);
            }

            // Simpan Investigator jika ada
            if ($investigator) {
                SuratPermintaanBlokirRekeningBankDocumentOfficer::create([
                    'id'              => (string) Uuid::generate(),
                    'doc_id'          => $docId,
                    'officer_id'      => (string) $investigator->id,
                    'first_name'      => $investigator->first_name,
                    'last_name'       => $investigator->last_name,
                    'first_title'     => $investigator->first_title,
                    'last_title'      => $investigator->last_title,
                    'register_number' => $investigator->register_number,
                    'position_id'     => $investigator->position_id,
                    'rank_id'         => $investigator->rank_id,
                    'police_id'       => $investigator->police_id,
                    'class'           => 'INVESTIGATOR',
                    'order_number'    => 2,
                    'created_by_user_id' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Permintaan Pemblokiran Rekening Bank berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Form edit Surat Permintaan Pemblokiran Rekening Bank
     */
    public function edit($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPermintaanBlokirRekeningBankDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'officers',
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->findOrFail($accidentId ?? $document->accident_id);

        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accident->id)->get();

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

        return view('docs.surat-permintaan-blokir-rekening-bank-document.edit', [
            'document'               => $document,
            'accident'               => $accident,
            'accidentId'             => $accident->id,
            'suspects'               => $suspects,
            'officers'               => $officers,
            'authorizedSignatories'  => $authorizedSignatories,
        ]);
    }

    /**
     * Memperbarui Surat Permintaan Pemblokiran Rekening Bank
     */
    public function update(Request $request, $id)
    {
        $accidentId = $request->accident_id ?: $request->query('accident_id');
        $document = SuratPermintaanBlokirRekeningBankDocument::findOrFail($id);
        $accidentId = $accidentId ?? $document->accident_id;

        $validator = Validator::make($request->all(), [
            'nomor'               => 'nullable|string|max:255',
            'sifat'               => 'required|string|max:50',
            'lampiran'            => 'nullable|string|max:100',
            'perihal'             => 'required|string|max:255',
            'tanggal'             => 'required|date',
            'tempat_surat'        => 'nullable|string|max:255',
            'bank_tujuan'         => 'required|string',
            'tempat_tujuan'       => 'required|string|max:255',
            'objek_blokir'        => 'required|string',
            'blokir_hari_tanggal' => 'required|string|max:100',
            'blokir_waktu'        => 'required|string|max:50',
            'blokir_tempat'       => 'required|string|max:255',
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

            $investigator = null;
            if (!empty($request->penyidik_id)) {
                $investigator = Officer::with(['rank', 'position', 'user'])->find($request->penyidik_id);
            }
            $penyidikNama = $request->penyidik_nama ?: ($investigator ? PeopleNameHelper::getFullName($investigator->first_title, $investigator->first_name, $investigator->last_name, $investigator->last_title) : $document->penyidik_nama);
            $penyidikNohp = $request->penyidik_nohp ?: ($investigator->phone_number ?? ($investigator->user->phone_number ?? $document->penyidik_nohp));
            $penyidikSebutan = $request->penyidik_sebutan ?: ($investigator->position->name ?? $document->penyidik_sebutan);

            $document->update([
                'suspect_id'          => $request->suspect_id ?? $document->suspect_id,
                'nomor'               => $request->nomor,
                'sifat'               => $request->sifat ?? 'Biasa',
                'lampiran'            => $request->lampiran ?? '-',
                'perihal'             => $request->perihal ?? 'Permintaan Pemblokiran Rekening Bank',
                'tanggal'             => $request->tanggal,
                'tempat_surat'        => $request->tempat_surat ?? $document->tempat_surat,
                'bank_tujuan'         => $request->bank_tujuan,
                'tempat_tujuan'       => $request->tempat_tujuan ?? 'Tempat',
                'dasar_kuhp'          => $request->dasar_kuhp,
                'dasar_kuhap'         => $request->dasar_kuhap,
                'dasar_polri'         => $request->dasar_polri,
                'dasar_uu_pidana'     => $request->dasar_uu_pidana,
                'dasar_lp_nomor'      => $request->dasar_lp_nomor,
                'dasar_lp_tanggal'    => $request->dasar_lp_tanggal,
                'dasar_sp_sidik_nomor'=> $request->dasar_sp_sidik_nomor,
                'dasar_sp_sidik_tanggal'=> $request->dasar_sp_sidik_tanggal,
                'satker'              => $request->satker ?? $document->satker,
                'tindak_pidana'       => $request->tindak_pidana ?? $document->tindak_pidana,
                'pasal_pidana'        => $request->pasal_pidana ?? $document->pasal_pidana,
                'uu_nomor'            => $request->uu_nomor ?? $document->uu_nomor,
                'uu_tahun'            => $request->uu_tahun ?? $document->uu_tahun,
                'uu_tentang'          => $request->uu_tentang ?? $document->uu_tentang,
                'tkp'                 => $request->tkp,
                'waktu_kejadian'      => $request->waktu_kejadian,
                'tersangka_nama'      => $request->tersangka_nama,
                'tersangka_no_identitas' => $request->tersangka_no_identitas,
                'tersangka_kewarganegaraan' => $request->tersangka_kewarganegaraan,
                'tersangka_jenis_kelamin' => $request->tersangka_jenis_kelamin,
                'tersangka_ttl'       => $request->tersangka_ttl,
                'tersangka_pekerjaan' => $request->tersangka_pekerjaan,
                'tersangka_agama'     => $request->tersangka_agama,
                'tersangka_alamat'    => $request->tersangka_alamat,
                'objek_blokir'        => $request->objek_blokir,
                'blokir_hari_tanggal' => $request->blokir_hari_tanggal,
                'blokir_waktu'        => $request->blokir_waktu,
                'blokir_tempat'       => $request->blokir_tempat,
                'penyidik_id'         => $request->penyidik_id ?? $document->penyidik_id,
                'penyidik_sebutan'    => $penyidikSebutan,
                'penyidik_nama'       => $penyidikNama,
                'penyidik_nohp'       => $penyidikNohp,
                'tembusan_1'          => $request->tembusan_1,
                'tembusan_2'          => $request->tembusan_2,
                'tembusan_3'          => $request->tembusan_3,
                'tembusan_4'          => $request->tembusan_4,
                'tembusan_5'          => $request->tembusan_5,
                'signatory_id'        => $request->signatory,
                'signatory_head_text' => $request->signatory_head_text,
                'signatory_position'  => $signatoryPosition,
                'signatory_name'      => $signatoryName,
                'signatory_rank'      => $signatoryRank,
                'signatory_nrp'       => $signatoryNrp,
                'catatan'             => $request->catatan,
                'document_number'     => $request->nomor,
                'document_date'       => $request->tanggal,
                'updated_by_user_id'  => Auth::id(),
            ]);

            // Re-sync signatory
            SuratPermintaanBlokirRekeningBankDocumentOfficer::where('doc_id', $document->id)
                ->where('class', 'SIGNATORY')->delete();

            if ($signatory) {
                SuratPermintaanBlokirRekeningBankDocumentOfficer::create([
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
                    'created_by_user_id' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Permintaan Pemblokiran Rekening Bank berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan detail (view/cetak) Surat Permintaan Pemblokiran Rekening Bank
     */
    public function show($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPermintaanBlokirRekeningBankDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.police',
            'officers',
            'signatoryOfficer',
            'investigatorOfficer',
        ])->where('id', $id)->first();

        if (!$document) {
            $document = SuratPermintaanBlokirRekeningBankDocument::where('id', $id)->firstOrFail();
        }

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda', 'police'])->find($accidentId ?? $document->accident_id);
        $data = $this->prepareDocumentData($document, $accident);

        return view('docs.surat-permintaan-blokir-rekening-bank-document.show', array_merge([
            'document' => $document,
            'accident' => $accident,
        ], $data));
    }

    /**
     * Hapus Surat Permintaan Pemblokiran Rekening Bank
     */
    public function delete($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratPermintaanBlokirRekeningBankDocument::findOrFail($id);
        $accidentId = $accidentId ?? $document->accident_id;

        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Permintaan Pemblokiran Rekening Bank berhasil dihapus.');
    }

    /**
     * Unduh File Word
     */
    public function download($id)
    {
        $result = $this->generatePopulatedWordFile($id);
        return response()->download($result['filename'])->deleteFileAfterSend(true);
    }

    /**
     * Siapkan seluruh data yang konsisten untuk preview (HTML) dan dokumen Word
     */
    public function prepareDocumentData($document, $accident): array
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

        $signatoryFullName = $signatory
            ? ($signatory->full_name ?? ($document->signatory_name ?? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)))
            : ($document->signatory_name ?? '................................................');
        $signatoryRankName = $signatory->rank->full_name ?? ($signatory->rank->name ?? ($document->signatory_rank ?? ''));
        $signatoryRegisterNumber = $signatory->register_number ?? ($document->signatory_nrp ?? '....................');
        $signatoryPositionName = strtoupper($signatory->position->name ?? ($document->signatory_position ?? 'KEPALA SATUAN LALU LINTAS'));
        $signatoryHeadText = $document->signatory_head_text ?? '';

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
        $kopstuk_2 = 'DAERAH ' . strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR');
        $kopstuk_3 = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->name ?? ($accident->polres->full_name ?? 'PASURUAN'));
        $kopstuk_4 = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));

        // 2. Info Surat
        $nomorSurat = $document->document_number ?? ($document->nomor ?? 'B/...../...../Satlantas');
        $klasifikasi = $document->sifat ?? 'Biasa';
        $lampiran = $document->lampiran ?? '-';
        $perihal = $document->perihal ?? 'Permintaan Pemblokiran Rekening Bank';

        // 3. Dikeluarkan di & Tanggal
        $dikeluarkanDi = ucwords(strtolower($document->tempat_surat ?? ($accident->polres->polres_district ?? ($accident->polres->name ?? 'Pasuruan'))));
        $tanggalSurat = $document->document_date ? Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : ($document->tanggal ? Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : date('d F Y'));

        // 4. Tujuan Bank
        $bankTujuanRaw = $document->bank_tujuan ?? '-';
        $bankTujuanWord = $formatMultiLineWord($document->bank_tujuan);
        $tempatTujuan = $document->tempat_tujuan ?? 'Tempat';

        // 5. Dasar Rujukan
        $dasarKuhp = $document->dasar_kuhp ?? 'Kitab Undang-Undang Hukum Pidana (KUHP);';
        $dasarKuhap = $document->dasar_kuhap ?? 'Pasal 5 ayat (1) huruf b angka 1, Pasal 7 ayat (1) huruf d dan j, Pasal 11, Pasal 38 ayat (1), Pasal 39 ayat (1) dan Pasal 44 Undang-Undang Nomor 8 Tahun 1981 tentang Hukum Acara Pidana;';
        $dasarPolri = $document->dasar_polri ?? 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;';
        $dasarUuPidana = $document->dasar_uu_pidana ?? 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;';
        $dasarLpNomor = $document->dasar_lp_nomor ?? ($accident->no_lp ?: ($accident->nomor_laporan_polisi ?: ($accident->accident_number ?: '-')));
        $dasarLpTanggal = $document->dasar_lp_tanggal ?? ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-');
        $dasarSpSidikNomor = $document->dasar_sp_sidik_nomor ?? '-';
        $dasarSpSidikTanggal = $document->dasar_sp_sidik_tanggal ?? '-';

        // 6. Isi Perkara
        $satker = $document->satker ?? ('Satuan Lalu Lintas ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres')));
        $tindakPidana = $document->tindak_pidana ?? 'Kecelakaan Lalu Lintas';
        $pasalPidana = $document->pasal_pidana ?? '310 ayat (4)';
        $uuNomor = $document->uu_nomor ?? '22';
        $uuTahun = $document->uu_tahun ?? '2009';
        $uuTentang = $document->uu_tentang ?? 'Lalu Lintas dan Angkutan Jalan';
        $tkp = $document->tkp ?? ($accident->road_name ?: ($accident->address ?: ($accident->tempat_kejadian ?: 'wilayah hukum')));
        $waktuKejadian = $document->waktu_kejadian ?? '-';

        // 7. Identitas Tersangka
        $tersangkaNama = $document->tersangka_nama ?? '-';
        $tersangkaNoIdentitas = $document->tersangka_no_identitas ?? '-';
        $tersangkaKewarganegaraan = $document->tersangka_kewarganegaraan ?? 'WNI';
        $tersangkaJenisKelamin = $document->tersangka_jenis_kelamin ?? '-';
        $tersangkaTtl = $document->tersangka_ttl ?? '-';
        $tersangkaPekerjaan = $document->tersangka_pekerjaan ?? '-';
        $tersangkaAgama = $document->tersangka_agama ?? '-';
        $tersangkaAlamat = $document->tersangka_alamat ?? '-';

        // 8. Objek & Waktu Blokir
        $objekBlokir = $document->objek_blokir ?? '-';
        $blokirHariTanggal = $document->blokir_hari_tanggal ?? '-';
        $blokirWaktu = $document->blokir_waktu ?? '09:00 WIB';
        $blokirTempat = $document->blokir_tempat ?? ('Ruang Unit Gakkum Satlantas Polres ' . ($accident->polres->name ?? 'Pasuruan'));

        // 9. Narahubung / Penyidik
        $penyidikSebutan = $document->penyidik_sebutan ?? 'Penyidik Pembantu';
        $penyidikNama = $document->penyidik_nama ?? '-';
        $penyidikNohp = $document->penyidik_nohp ?? '-';

        // 10. Tembusan
        $cleanTembusan = function($text) {
            if (empty($text)) return '-';
            $text = trim($text);
            $text = preg_replace('/^\d+[\.\)]\s*/', '', $text);
            $text = rtrim($text, ';. ');
            return trim($text);
        };

        $tembusan1 = $document->tembusan_1 ? $cleanTembusan($document->tembusan_1) : ('Kapolres ' . ($accident->polres->name ?? 'Pasuruan') . ' (sebagai laporan)');
        $tembusan2 = $document->tembusan_2 ? $cleanTembusan($document->tembusan_2) : ('Wakapolres ' . ($accident->polres->name ?? 'Pasuruan'));
        $tembusan3 = $document->tembusan_3 ? $cleanTembusan($document->tembusan_3) : ('Kabid Propam Polda ' . ($accident->polres->polda->name ?? 'Jatim'));
        $tembusan4 = $document->tembusan_4 ? $cleanTembusan($document->tembusan_4) : ('Kasiwas Polres ' . ($accident->polres->name ?? 'Pasuruan'));
        $tembusan5 = $document->tembusan_5 ? $cleanTembusan($document->tembusan_5) : 'Pengawas Penyidikan / Arsip';

        // 11. Tanda Tangan Atasan
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
            'bank_tujuan'               => $bankTujuanWord,
            'bank_tujuan_raw'           => $bankTujuanRaw,
            'tempat_tujuan'             => $tempatTujuan,
            'dasar_kuhp'                => $dasarKuhp,
            'dasar_kuhap'               => $dasarKuhap,
            'dasar_polri'               => $dasarPolri,
            'dasar_uu_pidana'           => $dasarUuPidana,
            'dasar_lp_nomor'            => $dasarLpNomor,
            'dasar_lp_tanggal'          => $dasarLpTanggal,
            'dasar_sp_sidik_nomor'      => $dasarSpSidikNomor,
            'dasar_sp_sidik_tanggal'    => $dasarSpSidikTanggal,
            'satker'                    => $satker,
            'tindak_pidana'             => $tindakPidana,
            'pasal_pidana'              => $pasalPidana,
            'uu_nomor'                  => $uuNomor,
            'uu_tahun'                  => $uuTahun,
            'uu_tentang'                => $uuTentang,
            'tkp'                       => $tkp,
            'waktu_kejadian'            => $waktuKejadian,
            'tersangka_nama'            => $tersangkaNama,
            'tersangka_no_identitas'    => $tersangkaNoIdentitas,
            'tersangka_kewarganegaraan' => $tersangkaKewarganegaraan,
            'tersangka_jenis_kelamin'   => $tersangkaJenisKelamin,
            'tersangka_ttl'             => $tersangkaTtl,
            'tersangka_pekerjaan'       => $tersangkaPekerjaan,
            'tersangka_agama'           => $tersangkaAgama,
            'tersangka_alamat'          => $tersangkaAlamat,
            'objek_blokir'              => $objekBlokir,
            'blokir_hari_tanggal'       => $blokirHariTanggal,
            'blokir_waktu'              => $blokirWaktu,
            'blokir_tempat'             => $blokirTempat,
            'penyidik_sebutan'          => $penyidikSebutan,
            'penyidik_nama'             => $penyidikNama,
            'penyidik_nohp'             => $penyidikNohp,
            'tembusan_1'                => $tembusan1,
            'tembusan_2'                => $tembusan2,
            'tembusan_3'                => $tembusan3,
            'tembusan_4'                => $tembusan4,
            'tembusan_5'                => $tembusan5,
            'atasan_jabatan'            => $atasanJabatanWord,
            'atasan_jabatan_raw'        => $atasanJabatanRaw,
            'atasan_nama'               => $atasanNama,
            'atasan_pangkat_nrp'        => $atasanPangkatNrp,
            'signatory_head_text'       => $signatoryHeadText,
        ];
    }

    /**
     * Generate file Word dari template word-template/surat_permintaan_blokir_rekening_bank.docx
     */
    private function generatePopulatedWordFile($id): array
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratPermintaanBlokirRekeningBankDocument::with([
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
            $document = SuratPermintaanBlokirRekeningBankDocument::where('id', $id)->firstOrFail();
        }

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda', 'police'])->find($accidentId ?? $document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        // Load Word Template
        $templatePath = public_path('word-template/surat_permintaan_blokir_rekening_bank.docx');
        if (!file_exists($templatePath)) {
            $templatePath = base_path('public/word-template/surat_permintaan_blokir_rekening_bank.docx');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Set all 51 variables
        $allVars = $templateProcessor->getVariables();
        foreach ($allVars as $var) {
            if (isset($data[$var])) {
                $templateProcessor->setValue($var, $data[$var]);
            }
        }

        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0777, true);
        }

        $cleanNomor = preg_replace('/[^A-Za-z0-9_\-]/', '_', $document->nomor ?: ($document->document_number ?: 'DRAFT'));
        $filename = public_path('generate/' . $document->id . ' - Surat Permintaan Pemblokiran Rekening Bank - ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres')) . '.docx');
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

