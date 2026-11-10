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
use App\Models\ReportedPerson;
use App\Models\Doc\SuratKuasaDocument\SuratKuasaDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;

use App\Traits\DocsOfficersTraits;

class SuratKuasaDocumentController extends Controller
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
     * Tampilan form pembuatan Surat Kuasa Penasehat Hukum
     */
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // 1. Ambil pihak-pihak yang terlibat untuk memudahkan autofill Pemberi Kuasa
        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accidentId)->get();
        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();
        $witnesses = DB::table('daftar_saksi')->where('accident_id', $accidentId)->get();

        // 2. Petugas internal untuk opsi "Mengetahui" (Penyidik / Kanit)
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

        $today = date('Y-m-d');
        $namaPolres = $accident->polres->name ?? 'Pasuruan';
        $tempatDefault = ucwords(strtolower(trim(str_ireplace(['polres', 'resor'], '', $namaPolres))));
        if (empty($tempatDefault)) {
            $tempatDefault = 'Pasuruan';
        }

        // 3. Dokumen Rujukan: LP, SP Sidik, SP Gas Sidik
        $noLp = $accident->nomor_laporan_polisi ?: ($accident->no_lp ?: ($accident->accident_number ?: '-'));
        $tanggalLp = $accident->report_date
            ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y')
            : ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-');

        try {
            $sprindikDocument = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (\Throwable $e) {
            $sprindikDocument = null;
        }

        $noSpSidik = $sprindikDocument->nomor ?? ($sprindikDocument->document_number ?? '-');
        $tanggalSpSidik = $sprindikDocument && $sprindikDocument->tanggal
            ? Carbon::parse($sprindikDocument->tanggal)->locale('id')->translatedFormat('d F Y')
            : ($sprindikDocument && $sprindikDocument->document_date ? Carbon::parse($sprindikDocument->document_date)->locale('id')->translatedFormat('d F Y') : '-');

        try {
            $springasDoc = SuratPerintahTugasDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (\Throwable $e) {
            $springasDoc = null;
        }

        $noSpGasSidik = $springasDoc->nomor ?? ($springasDoc->document_number ?? '-');
        $tanggalSpGasSidik = $springasDoc && $springasDoc->tanggal
            ? Carbon::parse($springasDoc->tanggal)->locale('id')->translatedFormat('d F Y')
            : ($springasDoc && $springasDoc->document_date ? Carbon::parse($springasDoc->document_date)->locale('id')->translatedFormat('d F Y') : '-');

        // Suspect pertama jika ada
        $firstSuspect = $suspects->first();
        $defaultPemberiTtl = '';
        if ($firstSuspect) {
            $pTempat = $firstSuspect->birth_place ?: '';
            $pTgl = $firstSuspect->birth_date ? Carbon::parse($firstSuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '';
            $defaultPemberiTtl = trim(($pTempat ? $pTempat . ', ' : '') . $pTgl, ', ');
        }

        $tkpDefault = $accident->tkp ?: ($accident->place_of_accident ?: ($accident->location ?: '-'));
        $waktuKejadianDefault = ($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '')
            . ($accident->accident_time ? ' sekira pukul ' . $accident->accident_time . ' WIB' : '');
        if (empty($waktuKejadianDefault)) {
            $waktuKejadianDefault = '-';
        }

        return view('docs.surat-kuasa-document.create', [
            'accident'               => $accident,
            'accidentId'             => $accidentId,
            'suspects'               => $suspects,
            'firstSuspect'           => $firstSuspect,
            'reportedPersons'        => $reportedPersons,
            'witnesses'              => $witnesses,
            'officers'               => $officers,
            'authorizedSignatories'  => $authorizedSignatories,
            'today'                  => $today,
            'tempatDefault'          => $tempatDefault,
            'noLp'                   => $noLp,
            'tanggalLp'              => $tanggalLp,
            'noSpSidik'              => $noSpSidik,
            'tanggalSpSidik'         => $tanggalSpSidik,
            'noSpGasSidik'           => $noSpGasSidik,
            'tanggalSpGasSidik'      => $tanggalSpGasSidik,
            'tkpDefault'             => $tkpDefault,
            'waktuKejadianDefault'   => $waktuKejadianDefault,
            'defaultPemberiTtl'      => $defaultPemberiTtl,
        ]);
    }

    /**
     * Menyimpan Surat Kuasa Penasehat Hukum
     */
    public function store(Request $request)
    {
        $accidentId = $request->accident_id;
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'pemberi_nama'    => 'required|string|max:255',
            'penerima_nama'   => 'required|string|max:255',
        ], [
            'pemberi_nama.required'  => 'Nama Pemberi Kuasa wajib diisi.',
            'penerima_nama.required' => 'Nama Penerima Kuasa wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $docId = (string) Uuid::generate();

            // Tanggal Surat & Dikeluarkan Di
            $dikeluarkanDi = $request->dikeluarkan_di ?: ($request->tempat_surat ?: ($accident->polres->name ?? 'Pasuruan'));
            $tanggalSurat = $request->tanggal_surat ?: ($request->tanggal ? Carbon::parse($request->tanggal)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y'));
            $rawDate = $request->tanggal ?: date('Y-m-d');

            // Data Mengetahui Officer (opsional)
            $mengetahuiName = null;
            $mengetahuiRank = null;
            $mengetahuiNrp = null;
            $mengetahuiPosition = null;

            if ($request->filled('mengetahui_signatory_id')) {
                $mengetahuiSignatory = Officer::with(['rank', 'position'])->find($request->mengetahui_signatory_id);
                if ($mengetahuiSignatory) {
                    $mengetahuiName = PeopleNameHelper::getFullName(
                        $mengetahuiSignatory->first_title,
                        $mengetahuiSignatory->first_name,
                        $mengetahuiSignatory->last_name,
                        $mengetahuiSignatory->last_title
                    );
                    $mengetahuiRank = $mengetahuiSignatory->rank->name ?? ($mengetahuiSignatory->rank_id ?? '-');
                    $mengetahuiNrp = $mengetahuiSignatory->register_number ?? '-';
                    $mengetahuiPosition = $mengetahuiSignatory->position->name ?? ($mengetahuiSignatory->position_id ?? 'PENYIDIK');
                }
            }

            SuratKuasaDocument::create([
                'id'                            => $docId,
                'accident_id'                   => $accidentId,
                'nomor'                         => $request->nomor,
                'tanggal'                       => $rawDate,
                'tempat_surat'                  => $dikeluarkanDi,
                'judul_surat'                   => $request->judul_surat ?? 'SURAT KUASA PENASEHAT HUKUM',

                // 1. Identitas Pemberi Kuasa (Exact Word Template)
                'pemberi_nama'                  => $request->pemberi_nama,
                'pemberi_no_identitas'          => $request->pemberi_no_identitas ?: '-',
                'pemberi_kewarganegaraan'       => $request->pemberi_kewarganegaraan ?: 'Indonesia',
                'pemberi_jenis_kelamin'         => $request->pemberi_jenis_kelamin ?: '-',
                'pemberi_ttl'                   => $request->pemberi_ttl ?: '-',
                'pemberi_umur'                  => $request->pemberi_umur ?: '-',
                'pemberi_pekerjaan'             => $request->pemberi_pekerjaan ?: '-',
                'pemberi_agama'                 => $request->pemberi_agama ?: '-',
                'pemberi_alamat'                => $request->pemberi_alamat ?: '-',
                'pemberi_nohp'                  => $request->pemberi_nohp ?: '-',
                'pemberi_telp'                  => $request->pemberi_telp ?: '-',
                'pemberi_email'                 => $request->pemberi_email ?: '-',
                'pemberi_medsos_platform'       => $request->pemberi_medsos_platform ?: '-',
                'pemberi_medsos_akun'           => $request->pemberi_medsos_akun ?: '-',

                // Kolom lama untuk backward compatibility
                'pemberi_kuasa_nama'            => $request->pemberi_nama,
                'pemberi_kuasa_nik'             => $request->pemberi_no_identitas,
                'pemberi_kuasa_umur'            => $request->pemberi_umur,
                'pemberi_kuasa_jenis_kelamin'   => $request->pemberi_jenis_kelamin,
                'pemberi_kuasa_agama'           => $request->pemberi_agama,
                'pemberi_kuasa_pekerjaan'       => $request->pemberi_pekerjaan,
                'pemberi_kuasa_kewarganegaraan' => $request->pemberi_kewarganegaraan ?: 'Indonesia',
                'pemberi_kuasa_alamat'          => $request->pemberi_alamat,
                'pemberi_kuasa_telepon'         => $request->pemberi_nohp,
                'pemberi_kuasa_kedudukan'       => 'Tersangka',

                // 2. Identitas Penerima Kuasa (Exact Word Template)
                'penerima_nama'                 => $request->penerima_nama,
                'penerima_no_identitas'         => $request->penerima_no_identitas ?: '-',
                'penerima_kewarganegaraan'      => $request->penerima_kewarganegaraan ?: 'Indonesia',
                'penerima_jenis_kelamin'        => $request->penerima_jenis_kelamin ?: '-',
                'penerima_ttl'                  => $request->penerima_ttl ?: '-',
                'penerima_umur'                 => $request->penerima_umur ?: '-',
                'penerima_pekerjaan'            => $request->penerima_pekerjaan ?: 'Advokat / Penasihat Hukum',
                'penerima_agama'                => $request->penerima_agama ?: '-',
                'penerima_alamat'               => $request->penerima_alamat ?: '-',
                'penerima_nohp'                 => $request->penerima_nohp ?: '-',
                'penerima_telp'                 => $request->penerima_telp ?: '-',
                'penerima_email'                => $request->penerima_email ?: '-',
                'penerima_medsos_platform'      => $request->penerima_medsos_platform ?: '-',
                'penerima_medsos_akun'          => $request->penerima_medsos_akun ?: '-',

                // Kolom lama
                'penerima_kuasa_nama'           => $request->penerima_nama,
                'penerima_kuasa_nik'            => $request->penerima_no_identitas,
                'penerima_kuasa_umur'           => $request->penerima_umur,
                'penerima_kuasa_jenis_kelamin'  => $request->penerima_jenis_kelamin,
                'penerima_kuasa_agama'          => $request->penerima_agama,
                'penerima_kuasa_pekerjaan'      => $request->penerima_pekerjaan ?: 'Advokat / Penasihat Hukum',
                'penerima_kuasa_alamat'         => $request->penerima_alamat,
                'penerima_kuasa_telepon'        => $request->penerima_nohp,
                'penerima_kuasa_kewarganegaraan'=> $request->penerima_kewarganegaraan ?: 'Indonesia',

                // 3. Khusus & Rujukan
                'tindak_pidana'                 => $request->tindak_pidana ?: 'Kecelakaan Lalu Lintas',
                'pasal_pidana'                  => $request->pasal_pidana ?: '310 ayat (4)',
                'uu_nomor'                      => $request->uu_nomor ?: '22',
                'uu_tahun'                      => $request->uu_tahun ?: '2009',
                'uu_tentang'                    => $request->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan',
                'tkp'                           => $request->tkp ?: '-',
                'waktu_kejadian'                => $request->waktu_kejadian ?: '-',

                'dasar_lp_nomor'                => $request->dasar_lp_nomor ?: '-',
                'dasar_lp_tanggal'              => $request->dasar_lp_tanggal ?: '-',
                'dasar_sp_sidik_nomor'          => $request->dasar_sp_sidik_nomor ?: '-',
                'dasar_sp_sidik_tanggal'        => $request->dasar_sp_sidik_tanggal ?: '-',
                'dasar_sp_gas_sidik_nomor'      => $request->dasar_sp_gas_sidik_nomor ?: '-',
                'dasar_sp_gas_sidik_tanggal'    => $request->dasar_sp_gas_sidik_tanggal ?: '-',

                'surat_penetapan_nomor'         => $request->surat_penetapan_nomor ?: '-',
                'surat_penetapan_tanggal'       => $request->surat_penetapan_tanggal ?: '-',

                // 4. Tanggal & Tempat
                'dikeluarkan_di'                => $dikeluarkanDi,
                'tanggal_surat'                 => $tanggalSurat,

                // Mengetahui
                'mengetahui_signatory_id'       => $request->mengetahui_signatory_id,
                'mengetahui_nama'               => $mengetahuiName,
                'mengetahui_pangkat'            => $mengetahuiRank,
                'mengetahui_nrp'                => $mengetahuiNrp,
                'mengetahui_jabatan'            => $mengetahuiPosition,

                'document_number'               => $request->nomor,
                'document_date'                 => $rawDate,
                'status_id'                     => '2',
                'document_category_id'          => '0703',
                'created_by_user_id'            => Auth::id(),
                'updated_by_user_id'            => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Kuasa Penasehat Hukum berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal membuat dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan form edit Surat Kuasa Penasehat Hukum
     */
    public function edit($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratKuasaDocument::with(['accident', 'mengetahuiOfficer'])->findOrFail($id);
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->findOrFail($accidentId);

        $suspects = Suspect::with(['gender', 'job', 'religion'])->where('accident_id', $accident->id)->get();
        $reportedPersons = ReportedPerson::where('accident_id', $accident->id)->get();
        $witnesses = DB::table('daftar_saksi')->where('accident_id', $accident->id)->get();

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

        $today = date('Y-m-d');
        $namaPolres = $accident->polres->name ?? 'Pasuruan';
        $tempatDefault = ucwords(strtolower(trim(str_ireplace(['polres', 'resor'], '', $namaPolres))));
        if (empty($tempatDefault)) {
            $tempatDefault = 'Pasuruan';
        }

        return view('docs.surat-kuasa-document.edit', [
            'document'               => $document,
            'accident'               => $accident,
            'accidentId'             => $accident->id,
            'suspects'               => $suspects,
            'reportedPersons'        => $reportedPersons,
            'witnesses'              => $witnesses,
            'officers'               => $officers,
            'authorizedSignatories'  => $authorizedSignatories,
            'today'                  => $today,
            'tempatDefault'          => $tempatDefault,
        ]);
    }

    /**
     * Memperbarui Surat Kuasa Penasehat Hukum
     */
    public function update(Request $request, $id)
    {
        $accidentId = $request->accident_id;
        $document = SuratKuasaDocument::findOrFail($id);
        $accidentId = $accidentId ?? $document->accident_id;

        $validator = Validator::make($request->all(), [
            'pemberi_nama'    => 'required|string|max:255',
            'penerima_nama'   => 'required|string|max:255',
        ], [
            'pemberi_nama.required'  => 'Nama Pemberi Kuasa wajib diisi.',
            'penerima_nama.required' => 'Nama Penerima Kuasa wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $dikeluarkanDi = $request->dikeluarkan_di ?: ($request->tempat_surat ?: ($document->dikeluarkan_di ?: 'Pasuruan'));
            $tanggalSurat = $request->tanggal_surat ?: ($request->tanggal ? Carbon::parse($request->tanggal)->locale('id')->translatedFormat('d F Y') : ($document->tanggal_surat ?: Carbon::now()->locale('id')->translatedFormat('d F Y')));
            $rawDate = $request->tanggal ?: ($document->tanggal ? Carbon::parse($document->tanggal)->format('Y-m-d') : date('Y-m-d'));

            $mengetahuiName = $document->mengetahui_nama;
            $mengetahuiRank = $document->mengetahui_pangkat;
            $mengetahuiNrp = $document->mengetahui_nrp;
            $mengetahuiPosition = $document->mengetahui_jabatan;

            if ($request->filled('mengetahui_signatory_id')) {
                $mengetahuiSignatory = Officer::with(['rank', 'position'])->find($request->mengetahui_signatory_id);
                if ($mengetahuiSignatory) {
                    $mengetahuiName = PeopleNameHelper::getFullName(
                        $mengetahuiSignatory->first_title,
                        $mengetahuiSignatory->first_name,
                        $mengetahuiSignatory->last_name,
                        $mengetahuiSignatory->last_title
                    );
                    $mengetahuiRank = $mengetahuiSignatory->rank->name ?? ($mengetahuiSignatory->rank_id ?? '-');
                    $mengetahuiNrp = $mengetahuiSignatory->register_number ?? '-';
                    $mengetahuiPosition = $mengetahuiSignatory->position->name ?? ($mengetahuiSignatory->position_id ?? 'PENYIDIK');
                }
            } elseif ($request->has('mengetahui_signatory_id') && empty($request->mengetahui_signatory_id)) {
                $mengetahuiName = null;
                $mengetahuiRank = null;
                $mengetahuiNrp = null;
                $mengetahuiPosition = null;
            }

            $document->update([
                'nomor'                         => $request->nomor,
                'tanggal'                       => $rawDate,
                'tempat_surat'                  => $dikeluarkanDi,
                'judul_surat'                   => $request->judul_surat ?? ($document->judul_surat ?: 'SURAT KUASA PENASEHAT HUKUM'),

                // 1. Identitas Pemberi Kuasa
                'pemberi_nama'                  => $request->pemberi_nama,
                'pemberi_no_identitas'          => $request->pemberi_no_identitas ?: '-',
                'pemberi_kewarganegaraan'       => $request->pemberi_kewarganegaraan ?: 'Indonesia',
                'pemberi_jenis_kelamin'         => $request->pemberi_jenis_kelamin ?: '-',
                'pemberi_ttl'                   => $request->pemberi_ttl ?: '-',
                'pemberi_umur'                  => $request->pemberi_umur ?: '-',
                'pemberi_pekerjaan'             => $request->pemberi_pekerjaan ?: '-',
                'pemberi_agama'                 => $request->pemberi_agama ?: '-',
                'pemberi_alamat'                => $request->pemberi_alamat ?: '-',
                'pemberi_nohp'                  => $request->pemberi_nohp ?: '-',
                'pemberi_telp'                  => $request->pemberi_telp ?: '-',
                'pemberi_email'                 => $request->pemberi_email ?: '-',
                'pemberi_medsos_platform'       => $request->pemberi_medsos_platform ?: '-',
                'pemberi_medsos_akun'           => $request->pemberi_medsos_akun ?: '-',

                // Kolom lama
                'pemberi_kuasa_nama'            => $request->pemberi_nama,
                'pemberi_kuasa_nik'             => $request->pemberi_no_identitas,
                'pemberi_kuasa_umur'            => $request->pemberi_umur,
                'pemberi_kuasa_jenis_kelamin'   => $request->pemberi_jenis_kelamin,
                'pemberi_kuasa_agama'           => $request->pemberi_agama,
                'pemberi_kuasa_pekerjaan'       => $request->pemberi_pekerjaan,
                'pemberi_kuasa_kewarganegaraan' => $request->pemberi_kewarganegaraan ?: 'Indonesia',
                'pemberi_kuasa_alamat'          => $request->pemberi_alamat,
                'pemberi_kuasa_telepon'         => $request->pemberi_nohp,

                // 2. Identitas Penerima Kuasa
                'penerima_nama'                 => $request->penerima_nama,
                'penerima_no_identitas'         => $request->penerima_no_identitas ?: '-',
                'penerima_kewarganegaraan'      => $request->penerima_kewarganegaraan ?: 'Indonesia',
                'penerima_jenis_kelamin'        => $request->penerima_jenis_kelamin ?: '-',
                'penerima_ttl'                  => $request->penerima_ttl ?: '-',
                'penerima_umur'                 => $request->penerima_umur ?: '-',
                'penerima_pekerjaan'            => $request->penerima_pekerjaan ?: 'Advokat / Penasihat Hukum',
                'penerima_agama'                => $request->penerima_agama ?: '-',
                'penerima_alamat'               => $request->penerima_alamat ?: '-',
                'penerima_nohp'                 => $request->penerima_nohp ?: '-',
                'penerima_telp'                 => $request->penerima_telp ?: '-',
                'penerima_email'                => $request->penerima_email ?: '-',
                'penerima_medsos_platform'      => $request->penerima_medsos_platform ?: '-',
                'penerima_medsos_akun'          => $request->penerima_medsos_akun ?: '-',

                // Kolom lama
                'penerima_kuasa_nama'           => $request->penerima_nama,
                'penerima_kuasa_nik'            => $request->penerima_no_identitas,
                'penerima_kuasa_umur'           => $request->penerima_umur,
                'penerima_kuasa_jenis_kelamin'  => $request->penerima_jenis_kelamin,
                'penerima_kuasa_agama'          => $request->penerima_agama,
                'penerima_kuasa_pekerjaan'      => $request->penerima_pekerjaan ?: 'Advokat / Penasihat Hukum',
                'penerima_kuasa_alamat'         => $request->penerima_alamat,
                'penerima_kuasa_telepon'        => $request->penerima_nohp,
                'penerima_kuasa_kewarganegaraan'=> $request->penerima_kewarganegaraan ?: 'Indonesia',

                // 3. Khusus & Rujukan
                'tindak_pidana'                 => $request->tindak_pidana ?: $document->tindak_pidana,
                'pasal_pidana'                  => $request->pasal_pidana ?: $document->pasal_pidana,
                'uu_nomor'                      => $request->uu_nomor ?: $document->uu_nomor,
                'uu_tahun'                      => $request->uu_tahun ?: $document->uu_tahun,
                'uu_tentang'                    => $request->uu_tentang ?: $document->uu_tentang,
                'tkp'                           => $request->tkp ?: $document->tkp,
                'waktu_kejadian'                => $request->waktu_kejadian ?: $document->waktu_kejadian,

                'dasar_lp_nomor'                => $request->dasar_lp_nomor ?: $document->dasar_lp_nomor,
                'dasar_lp_tanggal'              => $request->dasar_lp_tanggal ?: $document->dasar_lp_tanggal,
                'dasar_sp_sidik_nomor'          => $request->dasar_sp_sidik_nomor ?: $document->dasar_sp_sidik_nomor,
                'dasar_sp_sidik_tanggal'        => $request->dasar_sp_sidik_tanggal ?: $document->dasar_sp_sidik_tanggal,
                'dasar_sp_gas_sidik_nomor'      => $request->dasar_sp_gas_sidik_nomor ?: $document->dasar_sp_gas_sidik_nomor,
                'dasar_sp_gas_sidik_tanggal'    => $request->dasar_sp_gas_sidik_tanggal ?: $document->dasar_sp_gas_sidik_tanggal,

                'surat_penetapan_nomor'         => $request->surat_penetapan_nomor ?: $document->surat_penetapan_nomor,
                'surat_penetapan_tanggal'       => $request->surat_penetapan_tanggal ?: $document->surat_penetapan_tanggal,

                // 4. Tanggal & Tempat
                'dikeluarkan_di'                => $dikeluarkanDi,
                'tanggal_surat'                 => $tanggalSurat,

                // Mengetahui
                'mengetahui_signatory_id'       => $request->mengetahui_signatory_id,
                'mengetahui_nama'               => $mengetahuiName,
                'mengetahui_pangkat'            => $mengetahuiRank,
                'mengetahui_nrp'                => $mengetahuiNrp,
                'mengetahui_jabatan'            => $mengetahuiPosition,

                'document_number'               => $request->nomor,
                'document_date'                 => $rawDate,
                'updated_by_user_id'            => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Kuasa Penasehat Hukum berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui dokumen: ' . $th->getMessage());
        }
    }

    /**
     * Tampilan detail (view/cetak) Surat Kuasa Penasehat Hukum
     */
    public function show($id)
    {
        $document = SuratKuasaDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'mengetahuiOfficer',
        ])->findOrFail($id);

        $data = $this->prepareDocumentData($document, $document->accident);

        return view('docs.surat-kuasa-document.show', array_merge([
            'document' => $document,
            'accident' => $document->accident,
        ], $data));
    }

    /**
     * Hapus Surat Kuasa Penasehat Hukum
     */
    public function delete($id)
    {
        $accidentId = request()->query('accident_id');
        $document = SuratKuasaDocument::findOrFail($id);
        $accidentId = $accidentId ?? $document->accident_id;

        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Kuasa Penasehat Hukum berhasil dihapus.');
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
     * Siapkan seluruh data yang konsisten untuk preview (HTML) dan dokumen Word (45 variabel)
     */
    public function prepareDocumentData($document, $accident): array
    {
        // 1. Pemberi Kuasa
        $pemberiNama = $document->pemberi_nama ?: ($document->pemberi_kuasa_nama ?: '-');
        $pemberiNoIdentitas = $document->pemberi_no_identitas ?: ($document->pemberi_kuasa_nik ?: '-');
        $pemberiKewarganegaraan = $document->pemberi_kewarganegaraan ?: ($document->pemberi_kuasa_kewarganegaraan ?: 'Indonesia');
        $pemberiJenisKelamin = $document->pemberi_jenis_kelamin ?: ($document->pemberi_kuasa_jenis_kelamin ?: '-');

        $pemberiTtl = $document->pemberi_ttl;
        if (empty($pemberiTtl) || $pemberiTtl === '-') {
            $tempat = $document->pemberi_kuasa_tempat_lahir ?: '';
            $tgl = $document->pemberi_kuasa_tanggal_lahir ? Carbon::parse($document->pemberi_kuasa_tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '';
            $pemberiTtl = trim(($tempat ? $tempat . ', ' : '') . $tgl, ', ') ?: '-';
        }

        $pemberiUmur = $document->pemberi_umur ?: ($document->pemberi_kuasa_umur ?: '-');
        $pemberiPekerjaan = $document->pemberi_pekerjaan ?: ($document->pemberi_kuasa_pekerjaan ?: '-');
        $pemberiAgama = $document->pemberi_agama ?: ($document->pemberi_kuasa_agama ?: '-');
        $pemberiAlamat = $document->pemberi_alamat ?: ($document->pemberi_kuasa_alamat ?: '-');
        $pemberiNohp = $document->pemberi_nohp ?: ($document->pemberi_kuasa_telepon ?: '-');
        $pemberiTelp = $document->pemberi_telp ?: '-';
        $pemberiEmail = $document->pemberi_email ?: '-';
        $pemberiMedsosPlatform = $document->pemberi_medsos_platform ?: '-';
        $pemberiMedsosAkun = $document->pemberi_medsos_akun ?: '-';

        // 2. Penerima Kuasa
        $penerimaNama = $document->penerima_nama ?: ($document->penerima_kuasa_nama ?: '-');
        $penerimaNoIdentitas = $document->penerima_no_identitas ?: ($document->penerima_kuasa_nik ?: '-');
        $penerimaKewarganegaraan = $document->penerima_kewarganegaraan ?: ($document->penerima_kuasa_kewarganegaraan ?: 'Indonesia');
        $penerimaJenisKelamin = $document->penerima_jenis_kelamin ?: ($document->penerima_kuasa_jenis_kelamin ?: '-');

        $penerimaTtl = $document->penerima_ttl;
        if (empty($penerimaTtl) || $penerimaTtl === '-') {
            $tempat = $document->penerima_kuasa_tempat_lahir ?: '';
            $tgl = $document->penerima_kuasa_tanggal_lahir ? Carbon::parse($document->penerima_kuasa_tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '';
            $penerimaTtl = trim(($tempat ? $tempat . ', ' : '') . $tgl, ', ') ?: '-';
        }

        $penerimaUmur = $document->penerima_umur ?: ($document->penerima_kuasa_umur ?: '-');
        $penerimaPekerjaan = $document->penerima_pekerjaan ?: ($document->penerima_kuasa_pekerjaan ?: 'Advokat / Penasihat Hukum');
        $penerimaAgama = $document->penerima_agama ?: ($document->penerima_kuasa_agama ?: '-');
        $penerimaAlamat = $document->penerima_alamat ?: ($document->penerima_kuasa_alamat ?: '-');
        $penerimaNohp = $document->penerima_nohp ?: ($document->penerima_kuasa_telepon ?: '-');
        $penerimaTelp = $document->penerima_telp ?: '-';
        $penerimaEmail = $document->penerima_email ?: '-';
        $penerimaMedsosPlatform = $document->penerima_medsos_platform ?: '-';
        $penerimaMedsosAkun = $document->penerima_medsos_akun ?: '-';

        // 3. Khusus & Rujukan
        $tindakPidana = $document->tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalPidana = $document->pasal_pidana ?: '310 ayat (4)';
        $uuNomor = $document->uu_nomor ?: '22';
        $uuTahun = $document->uu_tahun ?: '2009';
        $uuTentang = $document->uu_tentang ?: 'Lalu Lintas dan Angkutan Jalan';
        $tkp = $document->tkp ?: ($accident->tkp ?: ($accident->place_of_accident ?: '-'));
        $waktuKejadian = $document->waktu_kejadian ?: (($accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '') . ($accident->accident_time ? ' sekira pukul ' . $accident->accident_time . ' WIB' : '-'));

        $dasarLpNomor = $document->dasar_lp_nomor ?: ($accident->nomor_laporan_polisi ?: ($accident->no_lp ?: '-'));
        $dasarLpTanggal = $document->dasar_lp_tanggal ?: ($accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-');
        $dasarSpSidikNomor = $document->dasar_sp_sidik_nomor ?: '-';
        $dasarSpSidikTanggal = $document->dasar_sp_sidik_tanggal ?: '-';
        $dasarSpGasSidikNomor = $document->dasar_sp_gas_sidik_nomor ?: '-';
        $dasarSpGasSidikTanggal = $document->dasar_sp_gas_sidik_tanggal ?: '-';

        $suratPenetapanNomor = $document->surat_penetapan_nomor ?: '-';
        $suratPenetapanTanggal = $document->surat_penetapan_tanggal ?: '-';

        // 4. Tempat & Tanggal Penandatanganan
        $dikeluarkanDi = $document->dikeluarkan_di ?: ($document->tempat_surat ?: ($accident->polres->name ?? 'Pasuruan'));
        $tanggalSurat = $document->tanggal_surat ?: ($document->tanggal ? Carbon::parse($document->tanggal)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y'));

        return [
            // 14 Pemberi
            'pemberi_nama'              => $pemberiNama,
            'pemberi_no_identitas'      => $pemberiNoIdentitas,
            'pemberi_kewarganegaraan'   => $pemberiKewarganegaraan,
            'pemberi_jenis_kelamin'     => $pemberiJenisKelamin,
            'pemberi_ttl'               => $pemberiTtl,
            'pemberi_umur'              => $pemberiUmur,
            'pemberi_pekerjaan'         => $pemberiPekerjaan,
            'pemberi_agama'             => $pemberiAgama,
            'pemberi_alamat'            => $pemberiAlamat,
            'pemberi_nohp'              => $pemberiNohp,
            'pemberi_telp'              => $pemberiTelp,
            'pemberi_email'             => $pemberiEmail,
            'pemberi_medsos_platform'   => $pemberiMedsosPlatform,
            'pemberi_medsos_akun'       => $pemberiMedsosAkun,

            // 14 Penerima
            'penerima_nama'             => $penerimaNama,
            'penerima_no_identitas'     => $penerimaNoIdentitas,
            'penerima_kewarganegaraan'  => $penerimaKewarganegaraan,
            'penerima_jenis_kelamin'    => $penerimaJenisKelamin,
            'penerima_ttl'              => $penerimaTtl,
            'penerima_umur'             => $penerimaUmur,
            'penerima_pekerjaan'        => $penerimaPekerjaan,
            'penerima_agama'            => $penerimaAgama,
            'penerima_alamat'           => $penerimaAlamat,
            'penerima_nohp'             => $penerimaNohp,
            'penerima_telp'             => $penerimaTelp,
            'penerima_email'            => $penerimaEmail,
            'penerima_medsos_platform'  => $penerimaMedsosPlatform,
            'penerima_medsos_akun'      => $penerimaMedsosAkun,

            // 7 Khusus Perkara
            'tindak_pidana'             => $tindakPidana,
            'pasal_pidana'              => $pasalPidana,
            'uu_nomor'                  => $uuNomor,
            'uu_tahun'                  => $uuTahun,
            'uu_tentang'                => $uuTentang,
            'tkp'                       => $tkp,
            'waktu_kejadian'            => $waktuKejadian,

            // 6 Rujukan
            'dasar_lp_nomor'            => $dasarLpNomor,
            'dasar_lp_tanggal'          => $dasarLpTanggal,
            'dasar_sp_sidik_nomor'      => $dasarSpSidikNomor,
            'dasar_sp_sidik_tanggal'    => $dasarSpSidikTanggal,
            'dasar_sp_gas_sidik_nomor'  => $dasarSpGasSidikNomor,
            'dasar_sp_gas_sidik_tanggal'=> $dasarSpGasSidikTanggal,

            // 2 Penetapan
            'surat_penetapan_nomor'     => $suratPenetapanNomor,
            'surat_penetapan_tanggal'   => $suratPenetapanTanggal,

            // 2 Tempat & Tanggal
            'dikeluarkan_di'            => $dikeluarkanDi,
            'tanggal_surat'             => $tanggalSurat,
        ];
    }

    /**
     * Generate file Word dari template word-template/surat_kuasa_penasehat_hukum.docx
     */
    private function generatePopulatedWordFile($id): array
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratKuasaDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
        ])->where('id', $id)->first();

        if (!$document) {
            $document = SuratKuasaDocument::where('id', $id)->firstOrFail();
        }

        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->find($accidentId ?? $document->accident_id);

        $data = $this->prepareDocumentData($document, $accident);

        // Load Word Template
        $templatePath = public_path('word-template/surat_kuasa_penasehat_hukum.docx');
        if (!file_exists($templatePath)) {
            $templatePath = base_path('public/word-template/surat_kuasa_penasehat_hukum.docx');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Set all 45 variables
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
        $filename = public_path('generate/' . $document->id . ' - Surat Kuasa Penasehat Hukum - ' . ($accident->polres->full_name ?? ($accident->polres->name ?? 'Polres')) . '.docx');
        $templateProcessor->saveAs($filename);

        return [
            'document'    => $document,
            'accident'    => $accident,
            'filename'    => $filename,
            'clean_nomor' => $cleanNomor,
        ];
    }
}
