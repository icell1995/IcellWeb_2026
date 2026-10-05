<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Ref;
use App\Models\Lib\Job;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Location;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\DocumentClassification;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\Tahap2Document\Tahap2Document;
use App\Models\Doc\Tahap2Document\Tahap2DocumentOfficer;
use App\Traits\DocsOfficersTraits;

class Tahap2DocumentController extends Controller
{
    use DocsOfficersTraits;

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::with(['polres.polda'])->where('id', $accidentId)->first();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 85, 86])
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 85, 86])
                    ->get()
            )->sortByDesc('created_at')->values();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('sort')->get();
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        
        $suratKetetapanTentangPenetapanTersangkaDocuments = \App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPerintahPenyidikanDocuments = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPerintahPenahananDocuments = collect(); // Mock for now

        $prisons = \App\Models\Lib\Prison::where('is_active', true)->orderBy('name')->get();

        $authorizedOfficers = \App\Models\Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

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

        // Load Master Data for Saksi Modal
        $refGender = Ref::where('grp_id', 'G01')->where('state', '1')->orderBy('sort')->get();
        $refAgama = Ref::where('grp_id', 'R01')->where('state', '1')->orderBy('sort')->get();
        $refPendidikan = Ref::where('grp_id', 'E01')->where('state', '1')->orderBy('sort')->get();
        $jobs = Job::orderBy('name')->get();
        $maritalStatuses = MaritalStatus::orderBy('name')->get();
        $countries = Location::where('is_active', true)->where('class', 'COUNTRY')->get();

        $tahap1Document = \App\Models\Doc\Tahap1Document\Tahap1Document::where('accident_id', $accidentId)
            ->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 85, 86])
            ->orderBy('created_at', 'desc')
            ->first();
            
        $witnesses = \App\Models\Witness::where('accident_id', $accidentId)->where('group', 'TAHAP_I')->get();
        $defaultSaksi = [];
        foreach($witnesses as $w) {
            $defaultSaksi[] = [
                'nama' => $w->name,
                'tempat_lahir' => $w->birth_place,
                'kode_jenis_kelamin' => $w->gender_id,
                'alamat' => $w->address,
                'kode_pekerjaan' => '',
                'kode_pekerjaan_text' => '',
                'kode_wilayah' => '',
                'kode_pendidikan' => '',
                'nama_ibu' => '',
                'kode_agama' => '',
                'kode_status_perkawinan' => '',
                'kode_warga_negara' => 'idn'
            ];
        }

        $defaultBarangBukti = [];
        if ($tahap1Document && $tahap1Document->barang_bukti) {
            $defaultBarangBukti = is_array($tahap1Document->barang_bukti) 
                ? $tahap1Document->barang_bukti 
                : json_decode($tahap1Document->barang_bukti, true);
        }

        return view('docs.tahap-2-pusiknas-document.create', compact('defaultSaksi', 'defaultBarangBukti', 
            'accidentId', 'accident', 'spdpDocuments', 'suspects',
            'authorizedSignatories', 'prosecutors', 'documentClassifications', 'districts', 'defaultKodeWilayah',
            'refGender', 'refAgama', 'refPendidikan', 'jobs', 'maritalStatuses', 'countries',
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahPenahananDocuments',
            'prisons',
            'authorizedOfficers'
        ));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->query('accident_id') ?? $request->accident_id);

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accident        = Accident::find($accidentId);
        $accidentDateObj = Carbon::parse($accident->accident_date);

        $documentNumber   = htmlspecialchars($request->documentNumber);
        $documentDate     = htmlspecialchars($request->documentDate);
        $noBerkasPerkara  = htmlspecialchars($request->noBerkasPerkara);
        $noSpdp           = htmlspecialchars($request->noSpdp);
        $tanggalTerimaP21 = $request->tanggalTerimaP21;
        $signatoryId      = htmlspecialchars($request->signatory);
        $prosecutorId     = htmlspecialchars($request->prosecutor ?? '');
        $classificationId = htmlspecialchars($request->documentClassification ?? '');
        $appendix         = $request->appendix ?? 0;
        $carbonCopies     = $request->carbonCopies ?? [];

        $uraianPerkara   = $request->uraianPerkara ?? '';
        $lokasiKejadian  = $request->lokasiKejadian ?? '';
        $kodeWilayah     = $request->kodeWilayah ?? '';
        $waktuKejadian   = $request->waktuKejadian ?? ('Sekitar pukul ' . Carbon::parse($accident->accident_time ?? now())->format('H:i') . ' WIB');
        $tahunKejadian   = $request->tahunKejadian ?? intval($accidentDateObj->format('Y'));
        $bulanKejadian   = $request->bulanKejadian ?? intval($accidentDateObj->format('m'));
        $tanggalKejadian = $request->tanggalKejadian ?? intval($accidentDateObj->format('d'));
        $suspects        = $request->suspects ?? [];

        $daftarSaksi       = $this->parseSaksi($request);
        $daftarBarangBukti = $this->parseBarangBukti($request);
        $daftarAhli        = $this->parseAhli($request);

        DB::beginTransaction();
        try {
            $doc = Tahap2Document::create([
                'accident_id'                => $accidentId,
                'document_number'            => $documentNumber,
                'document_date'              => $documentDate,
                'no_berkas_perkara'          => $noBerkasPerkara,
                'no_spdp'                    => $noSpdp,
                'tanggal_terima_p21'         => $tanggalTerimaP21 ?: null,
                'document_classification_id' => $classificationId ?: null,
                'prosecutor_id'              => $prosecutorId ?: null,
                'appendix'                   => $appendix,
                'carbon_copies'              => $carbonCopies,
                'tembusan'                   => $carbonCopies, // sinkronkan dengan kolom tembusan

                // Kolom Mindik — sejajar Tahap 1
                'klasifikasi'                            => $request->klasifikasi ?? null,
                'lampiran'                               => $request->lampiran ?? null,
                'surat_perintah_penyidikan_id'           => $request->surat_perintah_penyidikan_id ?: null,
                'surat_pemberitahuan_dimulainya_penyidikan_id' => $request->surat_pemberitahuan_dimulainya_penyidikan_id ?: null,
                'surat_ketetapan_penetapan_tersangka_id' => $request->surat_ketetapan_penetapan_tersangka_id ?: null,
                'berkas_perkara_number'                  => $request->berkas_perkara_number ?? null,
                'berkas_perkara_date'                    => $request->berkas_perkara_date ?: null,
                'berkas_perkara_rangkap'                 => intval($request->berkas_perkara_rangkap ?? 1),
                'pasal_disangkakan'                      => $request->pasal_disangkakan ?? null,
                'penahanan_status'                       => $request->penahanan_status ?? 'TIDAK_DITAHAN',
                'penahanan_rutan'                        => $request->penahanan_rutan ?? null,
                'penahanan_cabang'                       => $request->penahanan_cabang ?? null,
                'penahanan_start_date'                   => $request->penahanan_start_date ?: null,
                'penahanan_end_date'                     => $request->penahanan_end_date ?: null,
                'surat_perintah_penahanan_number'        => $request->surat_perintah_penahanan_number ?? null,
                'surat_perintah_penahanan_date'          => $request->surat_perintah_penahanan_date ?: null,
                'surat_perpanjangan_penahanan_number'    => $request->surat_perpanjangan_penahanan_number ?? null,
                'surat_perpanjangan_penahanan_date'      => $request->surat_perpanjangan_penahanan_date ?: null,
                'surat_perpanjangan_penahanan_court_number' => $request->surat_perpanjangan_penahanan_court_number ?? null,
                'surat_perpanjangan_penahanan_court_date'   => $request->surat_perpanjangan_penahanan_court_date ?: null,
                'surat_penangguhan_penahanan_number'     => $request->surat_penangguhan_penahanan_number ?? null,
                'surat_penangguhan_penahanan_date'       => $request->surat_penangguhan_penahanan_date ?: null,
                'barang_bukti_storage'                   => $request->barang_bukti_storage ?? null,
                'barang_bukti'                           => $daftarBarangBukti,
                'jumlah_bb'                              => collect($daftarBarangBukti)->sum('jumlah'),
                'investigator_pangkat_nama'              => $request->investigator_pangkat_nama ?? null,
                'investigator_hp'                        => $request->investigator_hp ?? null,

                // Payload SPPT-TI (messages) — hanya data yang memang masuk ke API Pusiknas
                'messages' => [
                    'signatory_id'           => $signatoryId,
                    'p21_number'             => $request->p21Number ?? null,
                    'uraian_singkat_perkara' => $uraianPerkara,
                    'lokasi_kejadian'        => $lokasiKejadian,
                    'kode_wilayah'           => $kodeWilayah,
                    'waktu_kejadian'         => $waktuKejadian,
                    'tahun_kejadian'         => intval($tahunKejadian),
                    'bulan_kejadian'         => intval($bulanKejadian),
                    'tanggal_kejadian'       => intval($tanggalKejadian),
                    'daftar_saksi'           => $daftarSaksi,
                    'daftar_barang_bukti'    => $daftarBarangBukti,
                    'daftar_ahli'            => $daftarAhli,
                    'sumber'                 => 'PUSIKNAS_FORM',
                ],
            ]);

            // Penandatangan
            $officer = Officer::find($signatoryId);
            if ($officer) {
                $doc->officers()->create([
                    'tahap_2_pusiknas_document_id' => $doc->id,
                    'register_number' => $officer->register_number,
                    'first_title'     => $officer->first_title,
                    'first_name'      => $officer->first_name,
                    'last_name'       => $officer->last_name,
                    'last_title'      => $officer->last_title,
                    'rank_id'         => $officer->rank_id,
                    'position_id'     => $officer->position_id,
                    'phone_number'    => $officer->phone_number,
                    'email'           => $officer->email,
                    'police_id'       => $officer->police_id,
                    'status'          => 'PRESENT',
                    'class'           => 'SIGNATORY',
                    'flag'            => 'INTERNAL',
                    'insert_method'   => 'IMPORT',
                    'sort'            => 0,
                ]);
            }

            if (!empty($suspects)) {
                $doc->suspects()->sync($suspects);
            }

            // Save Saksi Relational
            \App\Models\Witness::where('accident_id', $accidentId)->where('group', 'TAHAP_II')->delete();
            foreach ($daftarSaksi as $saksi) {
                \App\Models\Witness::create([
                    'id'            => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id'   => $accidentId,
                    'name'          => $saksi['nama'] ?? '-',
                    'address'       => $saksi['alamat'] ?? '-',
                    'flag'          => 'SAKSI',
                    'group'         => 'TAHAP_II',
                    'insert_method' => 'MANUAL',
                    'is_active'     => true,
                ]);
            }

            // Save Barang Bukti Relational
            \App\Models\DaftarBarangBukti::where('accident_id', $accidentId)->delete();
            foreach ($daftarBarangBukti as $bb) {
                \App\Models\DaftarBarangBukti::create([
                    'id'            => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id'   => $accidentId,
                    'nama_barang'   => $bb['nama'] ?? '-',
                    'jumlah_barang' => intval($bb['jumlah'] ?? 0),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = Tahap2Document::with(['officers', 'suspects', 'accident.polres.polda', 'prosecutor'])
            ->where('id', $id)->firstOrFail();
        $accident   = Accident::with(['polres.polda'])->where('id', $accidentId)->first();
        $messages   = is_string($document->messages) ? json_decode($document->messages, true) : ($document->messages ?? []);

        return view('docs.tahap-2-pusiknas-document.show', compact(
            'accidentId', 'accident', 'document', 'messages'
        ));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = Tahap2Document::with(['officers', 'suspects'])->where('id', $id)->firstOrFail();

        if (!$document->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui atau sedang dalam proses persetujuan.');
        }

        $accident   = Accident::with(['polres.polda'])->where('id', $accidentId)->first();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 85, 86])
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 85, 86])
                    ->get()
            )->sortByDesc('created_at')->values();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('sort')->get();
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        $districts        = $this->loadDistricts($accident);

        $extractedDistrict = '';
        if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $accident->road_name ?? '', $matches)) {
            $extractedDistrict = trim($matches[1]);
        }
        
        $messages = is_string($document->messages) ? json_decode($document->messages, true) : ($document->messages ?? []);

        // Sinkronkan field database dengan messages agar form edit terisi
        $messages['surat_perintah_penyidikan_id'] = $document->surat_perintah_penyidikan_id ?? $messages['surat_perintah_penyidikan_id'] ?? '';
        $messages['surat_ketetapan_penetapan_tersangka_id'] = $document->surat_ketetapan_penetapan_tersangka_id ?? $messages['surat_ketetapan_penetapan_tersangka_id'] ?? '';
        $messages['tanggal_terima_p21'] = $document->tanggal_terima_p21 ?? $messages['tanggal_terima_p21'] ?? '';
        $messages['pasal_disangkakan'] = $document->pasal_disangkakan ?? $messages['pasal_disangkakan'] ?? '';
        $messages['penahanan_status'] = $document->penahanan_status ?? $messages['penahanan_status'] ?? 'DITAHAN';
        $messages['penahanan_rutan'] = $document->penahanan_rutan ?? $messages['penahanan_rutan'] ?? '';
        $messages['penahanan_cabang'] = $document->penahanan_cabang ?? $messages['penahanan_cabang'] ?? '';
        $messages['penahanan_start_date'] = $document->penahanan_start_date ? \Carbon\Carbon::parse($document->penahanan_start_date)->format('Y-m-d') : ($messages['penahanan_start_date'] ?? '');
        $messages['penahanan_end_date'] = $document->penahanan_end_date ? \Carbon\Carbon::parse($document->penahanan_end_date)->format('Y-m-d') : ($messages['penahanan_end_date'] ?? '');
        $messages['surat_perintah_penahanan_number'] = $document->surat_perintah_penahanan_number ?? $messages['surat_perintah_penahanan_number'] ?? '';
        $messages['surat_perintah_penahanan_date'] = $document->surat_perintah_penahanan_date ? \Carbon\Carbon::parse($document->surat_perintah_penahanan_date)->format('Y-m-d') : ($messages['surat_perintah_penahanan_date'] ?? '');
        $messages['surat_perpanjangan_penahanan_number'] = $document->surat_perpanjangan_penahanan_number ?? $messages['surat_perpanjangan_penahanan_number'] ?? '';
        $messages['surat_perpanjangan_penahanan_date'] = $document->surat_perpanjangan_penahanan_date ? \Carbon\Carbon::parse($document->surat_perpanjangan_penahanan_date)->format('Y-m-d') : ($messages['surat_perpanjangan_penahanan_date'] ?? '');
        $messages['surat_perpanjangan_penahanan_court_number'] = $document->surat_perpanjangan_penahanan_court_number ?? $messages['surat_perpanjangan_penahanan_court_number'] ?? '';
        $messages['surat_perpanjangan_penahanan_court_date'] = $document->surat_perpanjangan_penahanan_court_date ? \Carbon\Carbon::parse($document->surat_perpanjangan_penahanan_court_date)->format('Y-m-d') : ($messages['surat_perpanjangan_penahanan_court_date'] ?? '');
        $messages['surat_penangguhan_penahanan_number'] = $document->surat_penangguhan_penahanan_number ?? $messages['surat_penangguhan_penahanan_number'] ?? '';
        $messages['surat_penangguhan_penahanan_date'] = $document->surat_penangguhan_penahanan_date ? \Carbon\Carbon::parse($document->surat_penangguhan_penahanan_date)->format('Y-m-d') : ($messages['surat_penangguhan_penahanan_date'] ?? '');
        $messages['barang_bukti_storage'] = $document->barang_bukti_storage ?? $messages['barang_bukti_storage'] ?? '';
        $messages['investigator_pangkat_nama'] = $document->investigator_pangkat_nama ?? $messages['investigator_pangkat_nama'] ?? '';
        $messages['investigator_hp'] = $document->investigator_hp ?? $messages['investigator_hp'] ?? '';
        if (empty($messages['daftar_barang_bukti']) && !empty($document->barang_bukti)) {
            $messages['daftar_barang_bukti'] = is_string($document->barang_bukti) ? json_decode($document->barang_bukti, true) : $document->barang_bukti;
        }

        $defaultKodeWilayah = $messages['kode_wilayah'] ?? '';
        if (!$defaultKodeWilayah && $extractedDistrict && !empty($districts)) {
            foreach ($districts as $d) {
                if (stripos($d['Nama'], $extractedDistrict) !== false) {
                    $defaultKodeWilayah = $d['KodePuskarda'] ?? '';
                    break;
                }
            }
        }

        $selectedSuspects = $document->suspects->pluck('id')->toArray();

        // Load Master Data for Saksi Modal
        $refGender = Ref::where('grp_id', 'G01')->where('state', '1')->orderBy('sort')->get();
        $refAgama = Ref::where('grp_id', 'R01')->where('state', '1')->orderBy('sort')->get();
        $refPendidikan = Ref::where('grp_id', 'E01')->where('state', '1')->orderBy('sort')->get();
        $jobs = Job::orderBy('name')->get();
        $maritalStatuses = MaritalStatus::orderBy('name')->get();
        $countries = Location::where('is_active', true)->where('class', 'COUNTRY')->get();

        
        $suratKetetapanTentangPenetapanTersangkaDocuments = \App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPerintahPenyidikanDocuments = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suratPerintahPenahananDocuments = collect(); // Mock for now

        $prisons = \App\Models\Lib\Prison::where('is_active', true)->orderBy('name')->get();

        $authorizedOfficers = \App\Models\Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        return view('docs.tahap-2-pusiknas-document.edit', compact(
            'accidentId', 'accident', 'document', 'messages',
            'spdpDocuments', 'suspects', 'selectedSuspects',
            'authorizedSignatories', 'prosecutors', 'documentClassifications', 'districts', 'defaultKodeWilayah',
            'refGender', 'refAgama', 'refPendidikan', 'jobs', 'maritalStatuses', 'countries',
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahPenahananDocuments',
            'prisons',
            'authorizedOfficers'
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->query('accident_id') ?? $request->accident_id);
        $document   = Tahap2Document::where('id', $id)->firstOrFail();

        if (!$document->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui atau sedang dalam proses persetujuan.');
        }

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accident        = Accident::find($accidentId);
        $accidentDateObj = Carbon::parse($accident->accident_date);

        $documentNumber   = htmlspecialchars($request->documentNumber);
        $documentDate     = htmlspecialchars($request->documentDate);
        $noBerkasPerkara  = htmlspecialchars($request->noBerkasPerkara);
        $noSpdp           = htmlspecialchars($request->noSpdp);
        $tanggalTerimaP21 = $request->tanggalTerimaP21;
        $signatoryId      = htmlspecialchars($request->signatory);
        $prosecutorId     = htmlspecialchars($request->prosecutor ?? '');
        $classificationId = htmlspecialchars($request->documentClassification ?? '');
        $appendix         = $request->appendix ?? 0;
        $carbonCopies     = $request->carbonCopies ?? [];

        $uraianPerkara   = $request->uraianPerkara ?? '';
        $lokasiKejadian  = $request->lokasiKejadian ?? '';
        $kodeWilayah     = $request->kodeWilayah ?? '';
        $waktuKejadian   = $request->waktuKejadian ?? '';
        $tahunKejadian   = $request->tahunKejadian ?? intval($accidentDateObj->format('Y'));
        $bulanKejadian   = $request->bulanKejadian ?? intval($accidentDateObj->format('m'));
        $tanggalKejadian = $request->tanggalKejadian ?? intval($accidentDateObj->format('d'));
        $suspects        = $request->suspects ?? [];

        $daftarSaksi       = $this->parseSaksi($request);
        $daftarBarangBukti = $this->parseBarangBukti($request);
        $daftarAhli        = $this->parseAhli($request);

        DB::beginTransaction();
        try {
            $document->update([
                'document_number'            => $documentNumber,
                'document_date'              => $documentDate,
                'no_berkas_perkara'          => $noBerkasPerkara,
                'no_spdp'                    => $noSpdp,
                'tanggal_terima_p21'         => $tanggalTerimaP21 ?: null,
                'document_classification_id' => $classificationId ?: null,
                'prosecutor_id'              => $prosecutorId ?: null,
                'appendix'                   => $appendix,
                'carbon_copies'              => $carbonCopies,
                'tembusan'                   => $carbonCopies,

                // Kolom Mindik — sejajar Tahap 1
                'klasifikasi'                            => $request->klasifikasi ?? null,
                'lampiran'                               => $request->lampiran ?? null,
                'surat_perintah_penyidikan_id'           => $request->surat_perintah_penyidikan_id ?: null,
                'surat_pemberitahuan_dimulainya_penyidikan_id' => $request->surat_pemberitahuan_dimulainya_penyidikan_id ?: null,
                'surat_ketetapan_penetapan_tersangka_id' => $request->surat_ketetapan_penetapan_tersangka_id ?: null,
                'berkas_perkara_number'                  => $request->berkas_perkara_number ?? null,
                'berkas_perkara_date'                    => $request->berkas_perkara_date ?: null,
                'berkas_perkara_rangkap'                 => intval($request->berkas_perkara_rangkap ?? 1),
                'pasal_disangkakan'                      => $request->pasal_disangkakan ?? null,
                'penahanan_status'                       => $request->penahanan_status ?? 'TIDAK_DITAHAN',
                'penahanan_rutan'                        => $request->penahanan_rutan ?? null,
                'penahanan_cabang'                       => $request->penahanan_cabang ?? null,
                'penahanan_start_date'                   => $request->penahanan_start_date ?: null,
                'penahanan_end_date'                     => $request->penahanan_end_date ?: null,
                'surat_perintah_penahanan_number'        => $request->surat_perintah_penahanan_number ?? null,
                'surat_perintah_penahanan_date'          => $request->surat_perintah_penahanan_date ?: null,
                'surat_perpanjangan_penahanan_number'    => $request->surat_perpanjangan_penahanan_number ?? null,
                'surat_perpanjangan_penahanan_date'      => $request->surat_perpanjangan_penahanan_date ?: null,
                'surat_perpanjangan_penahanan_court_number' => $request->surat_perpanjangan_penahanan_court_number ?? null,
                'surat_perpanjangan_penahanan_court_date'   => $request->surat_perpanjangan_penahanan_court_date ?: null,
                'surat_penangguhan_penahanan_number'     => $request->surat_penangguhan_penahanan_number ?? null,
                'surat_penangguhan_penahanan_date'       => $request->surat_penangguhan_penahanan_date ?: null,
                'barang_bukti_storage'                   => $request->barang_bukti_storage ?? null,
                'barang_bukti'                           => $daftarBarangBukti,
                'jumlah_bb'                              => collect($daftarBarangBukti)->sum('jumlah'),
                'investigator_pangkat_nama'              => $request->investigator_pangkat_nama ?? null,
                'investigator_hp'                        => $request->investigator_hp ?? null,

                // Payload SPPT-TI (messages)
                'messages' => [
                    'signatory_id'           => $signatoryId,
                    'p21_number'             => $request->p21Number ?? null,
                    'uraian_singkat_perkara' => $uraianPerkara,
                    'lokasi_kejadian'        => $lokasiKejadian,
                    'kode_wilayah'           => $kodeWilayah,
                    'waktu_kejadian'         => $waktuKejadian,
                    'tahun_kejadian'         => intval($tahunKejadian),
                    'bulan_kejadian'         => intval($bulanKejadian),
                    'tanggal_kejadian'       => intval($tanggalKejadian),
                    'daftar_saksi'           => $daftarSaksi,
                    'daftar_barang_bukti'    => $daftarBarangBukti,
                    'daftar_ahli'            => $daftarAhli,
                    'sumber'                 => 'PUSIKNAS_FORM',
                ],
            ]);

            DB::table('doc.tahap_2_pusiknas_document_officers')
                ->where('tahap_2_pusiknas_document_id', $document->id)
                ->where('class', 'SIGNATORY')
                ->delete();
            $officer = Officer::find($signatoryId);
            if ($officer) {
                $document->officers()->create([
                    'tahap_2_pusiknas_document_id' => $document->id,
                    'register_number' => $officer->register_number,
                    'first_title'     => $officer->first_title,
                    'first_name'      => $officer->first_name,
                    'last_name'       => $officer->last_name,
                    'last_title'      => $officer->last_title,
                    'rank_id'         => $officer->rank_id,
                    'position_id'     => $officer->position_id,
                    'phone_number'    => $officer->phone_number,
                    'email'           => $officer->email,
                    'police_id'       => $officer->police_id,
                    'status'          => 'PRESENT',
                    'class'           => 'SIGNATORY',
                    'flag'            => 'INTERNAL',
                    'insert_method'   => 'IMPORT',
                    'sort'            => 0,
                ]);
            }

            $document->suspects()->sync($suspects);

            // Save Saksi Relational
            \App\Models\Witness::where('accident_id', $accidentId)->where('group', 'TAHAP_II')->delete();
            foreach ($daftarSaksi as $saksi) {
                \App\Models\Witness::create([
                    'id'            => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id'   => $accidentId,
                    'name'          => $saksi['nama'] ?? '-',
                    'address'       => $saksi['alamat'] ?? '-',
                    'flag'          => 'SAKSI',
                    'group'         => 'TAHAP_II',
                    'insert_method' => 'MANUAL',
                    'is_active'     => true,
                ]);
            }

            // Save Barang Bukti Relational
            \App\Models\DaftarBarangBukti::where('accident_id', $accidentId)->delete();
            foreach ($daftarBarangBukti as $bb) {
                \App\Models\DaftarBarangBukti::create([
                    'id'            => (string) \Illuminate\Support\Str::uuid(),
                    'accident_id'   => $accidentId,
                    'nama_barang'   => $bb['nama'] ?? '-',
                    'jumlah_barang' => intval($bb['jumlah'] ?? 0),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengubah data: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = Tahap2Document::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD (Word)
    // ─────────────────────────────────────────────
    public function download($id)
    {
        $document = Tahap2Document::with([
            'officers',
            'suspects.gender',
            'suspects.job',
            'suspects.province',
            'suspects.regency',
            'suspects.district',
            'suspects.village',
            'suspects.country',
            'accident.polres.polda',
            'prosecutor.regency',
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $messages = is_string($document->messages) ? json_decode($document->messages, true) : ($document->messages ?? []);

        $signatory = $document->officers()->where('class', 'SIGNATORY')->first();
        if (!$signatory) {
            return redirect()->back()->with('error', 'Penandatangan belum diset.');
        }

        $resorPolice          = $accident->polres;
        $daerahPoliceFullName = strtoupper($accident->polres->polda->full_name ?? '');
        $resorPoliceFullName  = $resorPolice ? ((in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name)) : '';
        $resorPoliceAddress   = $resorPolice ? ($resorPolice->address . ', ' . $resorPolice->polres_zipcode) : '';
        $documentLocation     = ucwords(strtolower($resorPolice->polres_province ?? ''));

        $signatoryPositionId = $signatory ? (is_array($signatory->position) ? ($signatory->position['id'] ?? null) : $signatory->position_id) : null;
        $signatoryPositionDetail = $signatoryPositionId
            ? \App\Models\Lib\Position::with('positionCluster')->find($signatoryPositionId)
            : null;

        $signatoryHeadText     = 'a.n. KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? '');
        $signatoryPositionName = '';
        if ($signatoryPositionDetail) {
            if ($signatoryPositionDetail->position_cluster_id == '1') {
                $signatoryHeadText     = 'KEPALA KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? '');
                $signatoryPositionName = '';
            } elseif ($signatoryPositionDetail->position_cluster_id == '9') {
                $signatoryHeadText     = 'a.n. DIREKTUR LALU LINTAS POLDA ' . ($accident->polres->polda->full_name ?? '');
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? '';
            } else {
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? '';
            }
        }

        $documentDate               = Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber             = $document->document_number;
        $documentClassificationName = $document->document_classification_id ?? '-';
        $accidentNumber             = $accident->no_lp;
        $accidentDate               = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');

        $tanggalTerimaP21 = $document->tanggal_terima_p21
            ? Carbon::parse($document->tanggal_terima_p21)->locale('id')->translatedFormat('d F Y')
            : '-';

        $prosecutor         = $document->prosecutor;
        $prosecutorName     = $prosecutor ? strtoupper($prosecutor->name ?? '-') : '-';
        $prosecutorLocation = $prosecutor
            ? strtoupper(ucwords(strtolower($prosecutor->regency->name ?? '')))
            : strtoupper($documentLocation);

        $blockSuspects = [];
        foreach ($document->suspects as $suspect) {
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
                'suspectName'        => strtoupper($suspect->name),
                'suspectAge'         => $age,
                'suspectJobName'     => ucwords(strtolower($suspect->job->name ?? '-')),
                'suspectFullAddress' => $fullAddress,
            ];
        }
        
        $suspectsCount = count($blockSuspects);
        $suspectsCountText = $this->terbilang($suspectsCount);

        $firstSuspect = $document->suspects->first();
        $suspectName  = strtoupper($firstSuspect->name ?? '-');

        $berkasNumber  = $document->no_berkas_perkara ?? $document->berkas_perkara_number ?? '-';
        $berkasDate    = $document->berkas_perkara_date
            ? Carbon::parse($document->berkas_perkara_date)->locale('id')->translatedFormat('d F Y')
            : $accidentDate;
        $berkasRangkap = $document->berkas_perkara_rangkap ?? 1;

        // Relasi dokumen rujukan — baca dari kolom langsung
        $sprindikDoc    = $document->suratPerintahPenyidikan;
        $sprindikNumber = $sprindikDoc->document_number ?? '-';
        $sprindikDate   = ($sprindikDoc && $sprindikDoc->document_date)
            ? Carbon::parse($sprindikDoc->document_date)->locale('id')->translatedFormat('d F Y')
            : '-';

        $spdpNumber = $document->no_spdp ?? '-';
        $spdpDate   = '-';
        $spdpDoc    = $document->suratPemberitahuanDimulainyaPenyidikan
            ?? \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::where('document_number', $spdpNumber)->first();
        if ($spdpDoc && $spdpDoc->document_date) {
            $spdpDate = Carbon::parse($spdpDoc->document_date)->locale('id')->translatedFormat('d F Y');
        }

        $tapTersangkaDoc    = $document->suratKetetapanTentangPenetapanTersangka;
        $tapTersangkaNumber = $tapTersangkaDoc->document_number ?? '-';
        $tapTersangkaDate   = ($tapTersangkaDoc && $tapTersangkaDoc->document_date)
            ? Carbon::parse($tapTersangkaDoc->document_date)->locale('id')->translatedFormat('d F Y')
            : '-';

        $p21Number = $messages['p21_number'] ?? '-';
        $p21Date   = $tanggalTerimaP21;

        $references = [
            ['reference_iteration' => 'a.', 'reference_name' => 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'],
            ['reference_iteration' => 'b.', 'reference_name' => 'Pasal 3 dan Pasal 618 Undang-Undang Nomor 1 Tahun 2023 tentang Kitab Undang-Undang Hukum Pidana;'],
            ['reference_iteration' => 'c.', 'reference_name' => 'Pasal 8, Pasal 11, Pasal 60 ayat (3), Pasal 61, Pasal 62 dan Pasal 361 Undang-Undang Nomor 20 Tahun 2025 tentang Kitab Undang-Undang Hukum Acara Pidana;'],
            ['reference_iteration' => 'd.', 'reference_name' => 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan;'],
            ['reference_iteration' => 'e.', 'reference_name' => 'Laporan Polisi Nomor: ' . $accidentNumber . ', tanggal ' . $accidentDate . ';'],
            ['reference_iteration' => 'f.', 'reference_name' => 'Surat Perintah Penyidikan Nomor: ' . $sprindikNumber . ', tanggal ' . $sprindikDate . ';'],
            ['reference_iteration' => 'g.', 'reference_name' => 'Surat Pemberitahuan Dimulainya Penyidikan Nomor: ' . $spdpNumber . ', tanggal ' . $spdpDate . ';'],
            ['reference_iteration' => 'h.', 'reference_name' => 'Surat Ketetapan tentang Penetapan Tersangka Nomor: ' . $tapTersangkaNumber . ', tanggal ' . $tapTersangkaDate . ' atas nama ' . ($firstSuspect->name ?? '') . '.'],
            ['reference_iteration' => 'i.', 'reference_name' => 'Surat dari ' . $prosecutorName . ' Nomor: ' . $p21Number . ', tanggal ' . $p21Date . ', perihal pemberitahuan penyidikan sudah lengkap (P.21).'],
        ];

        // Pasal — baca dari kolom langsung, fallback dari sprindik
        $onTheFlyPasalString = $document->surat_perintah_penyidikan_id ? $this->formatSprindikLawsString($document->surat_perintah_penyidikan_id) : '';
        $finalPasalString    = $onTheFlyPasalString ?: ($document->pasal_disangkakan ?? '-');

        // Penahanan — baca dari kolom langsung
        $status             = $document->penahanan_status ?? 'TIDAK_DITAHAN';
        $detentionParagraph = '';
        if ($status == 'TIDAK_DITAHAN') {
            $detentionParagraph = 'Berkaitan dengan hal tersebut, diberitahukan bahwa tersangka tersebut tidak dilakukan penahanan';
        } else {
            $rutan        = $document->penahanan_rutan ?? '......';
            $cabang       = $document->penahanan_cabang ?? '......';
            $startDate    = $document->penahanan_start_date ? Carbon::parse($document->penahanan_start_date)->locale('id')->translatedFormat('d F Y') : '......';
            $endDate      = $document->penahanan_end_date ? Carbon::parse($document->penahanan_end_date)->locale('id')->translatedFormat('d F Y') : '......';
            $sppNo        = $document->surat_perintah_penahanan_number ?? '......';
            $sppDate      = $document->surat_perintah_penahanan_date ? Carbon::parse($document->surat_perintah_penahanan_date)->locale('id')->translatedFormat('d F Y') : '......';
            $spppNo       = $document->surat_perpanjangan_penahanan_number ?? '......';
            $spppDate     = $document->surat_perpanjangan_penahanan_date ? Carbon::parse($document->surat_perpanjangan_penahanan_date)->locale('id')->translatedFormat('d F Y') : '......';
            $sppCourtNo   = $document->surat_perpanjangan_penahanan_court_number ?? '......';
            $sppCourtDate = $document->surat_perpanjangan_penahanan_court_date ? Carbon::parse($document->surat_perpanjangan_penahanan_court_date)->locale('id')->translatedFormat('d F Y') : '......';
            $detentionParagraph = "Berkaitan dengan hal tersebut, diberitahuan bahwa tersangka tersebut di atas ditahan di Rutan $rutan Cabang $cabang pada tanggal $startDate s.d. tanggal $endDate dengan Surat Perintah Penahanan Nomor: $sppNo tanggal $sppDate, Surat Perintah Perpanjangan Penahanan Nomor: $spppNo tanggal $spppDate dan Surat Perpanjangan Penahanan ke Pengadilan Nomor: $sppCourtNo tanggal $sppCourtDate";
            if ($status == 'DITANGGUHKAN') {
                $suspNo             = $document->surat_penangguhan_penahanan_number ?? '......';
                $suspDate           = $document->surat_penangguhan_penahanan_date ? Carbon::parse($document->surat_penangguhan_penahanan_date)->locale('id')->translatedFormat('d F Y') : '......';
                $detentionParagraph .= " serta ditangguhkan penahanannya berdasarkan Surat Perintah Penangguhan Penahanan Nomor: $suspNo tanggal $suspDate";
            }
        }

        $bbStorage                = $document->barang_bukti_storage ?? '';
        $invName                  = $document->investigator_pangkat_nama ?? '......';
        $invHp                    = $document->investigator_hp ?? '......';
        
        $bbArray = is_string($document->barang_bukti) ? json_decode($document->barang_bukti, true) : ($document->barang_bukti ?? []);
        $bbListString = "";
        if (!empty($bbArray)) {
            $charIndex = 'a';
            foreach ($bbArray as $bb) {
                $jumlah = $bb['jumlah'] ?? '';
                $satuan = $bb['satuan'] ?? '';
                $nama = $bb['nama'] ?? '';
                $keterangan = $bb['keterangan'] ?? '';
                
                $bbStr = trim("$jumlah $satuan $nama $keterangan");
                if ($bbStr) {
                    $bbListString .= "</w:t><w:tab/><w:t>" . $charIndex . ". " . $bbStr . ";</w:t><w:tab/><w:br/><w:t>";
                    $charIndex++;
                }
            }
        }
        
        if ($bbListString) {
            // Include <w:tab/><w:br/> hacks to prevent Justified paragraph stretching in Word
            $evidenceContactParagraph = "Barang-barang bukti yang tersebut dalam daftar barang bukti berupa:</w:t><w:tab/><w:br/><w:t>" . 
                $bbListString . 
                "</w:t><w:tab/><w:br/><w:t>Agar KA mengirim turunan surat pelimpahan perkara beserta surat dakwaan dan salinan surat putusan pengadilan. Untuk memudahkan dalam berkoordinasi dan berkomunikasi dapat menghubungi Penyidik/ Penyidik pembantu $invName, Nomor Hp : $invHp";
        } else {
            $bbStorageStr = $bbStorage ? $bbStorage : '......';
            $evidenceContactParagraph = "Barang-barang bukti yang tersebut dalam daftar barang bukti disimpan di $bbStorageStr . Agar KA mengirim turunan surat pelimpahan perkara beserta surat dakwaan dan salinan surat putusan pengadilan. Untuk memudahkan dalam berkoordinasi dan berkomunikasi dapat menghubungi Penyidik/ Penyidik pembantu $invName, Nomor Hp : $invHp";
        }

        $signatoryName           = trim(implode(' ', array_filter([$signatory->first_title ?? '', $signatory->first_name ?? '', $signatory->last_name ?? '', $signatory->last_title ?? ''])));
        $signatoryName           = $signatoryName ?: '-';
        $signatoryRankName       = $signatory->rank->name ?? '';
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        $carbonCopies = $document->carbon_copies ?? [];
        if (is_string($carbonCopies)) $carbonCopies = json_decode($carbonCopies, true) ?? [];
        $blockCarbonCopies = [];
        foreach ($carbonCopies as $index => $copy) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => ($index + 1),
                'carbon_copy_name'      => $copy,
            ];
        }

        $templatePath = public_path('word-template/berkas_perkara_tahap_II.docx');
        if (!file_exists($templatePath)) {
            $templatePath = public_path('word-template/berkas_perkara_tahap_I.docx');
        }
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPositionName);
        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('suspectsCount', $suspectsCount);
        $templateProcessor->setValue('suspectsCountText', ucwords($suspectsCountText));

        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentClassificationName', $documentClassificationName);
        $templateProcessor->setValue('appendix', $document->appendix ?? '-');
        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('prosecutorLocation', $prosecutorLocation);
        $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);
        $templateProcessor->setValue('berkasNumber', $berkasNumber);
        $templateProcessor->setValue('berkasDate', $berkasDate);
        $templateProcessor->setValue('berkasRangkap', $berkasRangkap);
        $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        $templateProcessor->setValue('suspectName', $suspectName);
        $templateProcessor->setValue('pasalDisangkakan', $finalPasalString);
        $templateProcessor->setValue('detentionParagraph', $detentionParagraph);
        $templateProcessor->setValue('evidenceContactParagraph', $evidenceContactParagraph);
        $templateProcessor->setValue('signatoryName', strtoupper($signatoryName));
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);
        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

        $filename = 'generate/' . $document->id . ' - Surat Pengiriman Berkas Perkara (Tahap II) - Resor ' . ($accident->polres->full_name ?? '');
        $templateProcessor->saveAs(public_path($filename . '.docx'));

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response()->download(public_path($filename . '.docx'))->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────
    // VALIDATE FORM (AJAX)
    // ─────────────────────────────────────────────
    public function validateRequestForm(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return response()->json([
                'code'    => '422',
                'success' => false,
                'errors'  => $validator->errors()->all(),
            ], 422);
        }
        return response()->json(['success' => true, 'message' => 'Data valid, dokumen siap disimpan.']);
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────
    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'documentNumber'  => 'required|string|min:3|max:255',
            'documentDate'    => 'required|date_format:Y-m-d',
            'noBerkasPerkara' => 'required|string|min:3|max:255',
            'noSpdp'          => 'required|string',
            'signatory'       => 'required',
            'uraianPerkara'   => 'required|string|min:5',
            'lokasiKejadian'  => 'required|string|min:3',
            'waktuKejadian'   => 'required|string|min:3',
            'tahunKejadian'   => 'required|integer|min:1900|max:2100',
            'bulanKejadian'   => 'required|integer|min:1|max:12',
            // Penahanan
            'penahanan_status'                   => 'required|in:DITAHAN,DITANGGUHKAN,TIDAK_DITAHAN',
            'penahanan_rutan'                    => 'nullable|required_if:penahanan_status,DITAHAN|string|max:255',
            'penahanan_cabang'                   => 'nullable|string|max:255',
            'penahanan_start_date'               => 'nullable|required_if:penahanan_status,DITAHAN|date',
            'penahanan_end_date'                 => 'nullable|required_if:penahanan_status,DITAHAN|date|after_or_equal:penahanan_start_date',
            'surat_penangguhan_penahanan_number' => 'nullable|required_if:penahanan_status,DITANGGUHKAN|string|max:255',
            'surat_penangguhan_penahanan_date'   => 'nullable|required_if:penahanan_status,DITANGGUHKAN|date',
            // Tersangka
            'suspects'   => 'required|array|min:1',
            'suspects.*' => 'exists:suspects,id',
            // Opsional
            'surat_perpanjangan_penahanan_number'       => 'nullable|string|max:255',
            'surat_perpanjangan_penahanan_date'         => 'nullable|date',
            'surat_perpanjangan_penahanan_court_number' => 'nullable|string|max:255',
            'surat_perpanjangan_penahanan_court_date'   => 'nullable|date',
            'investigator_pangkat_nama' => 'nullable|string|max:255',
            'investigator_hp'           => 'nullable|string|max:255',
            'tanggalTerimaP21'          => 'nullable|date',
            'p21Number'                 => 'nullable|string|max:255',
        ], [
            'documentNumber.required'   => 'Mohon mengisi Nomor Surat Pengantar.',
            'documentDate.required'     => 'Mohon mengisi Tanggal Surat Pengantar.',
            'noBerkasPerkara.required'  => 'Mohon mengisi Nomor Berkas Perkara.',
            'noSpdp.required'           => 'Mohon memilih/mengisi Nomor SPDP.',
            'signatory.required'        => 'Mohon mengisi Penandatangan.',
            'uraianPerkara.required'    => 'Mohon mengisi Uraian Singkat Perkara.',
            'lokasiKejadian.required'   => 'Mohon mengisi Lokasi Kejadian.',
            'waktuKejadian.required'    => 'Mohon mengisi Waktu Kejadian.',
            'tahunKejadian.required'    => 'Mohon mengisi Tahun Kejadian.',
            'bulanKejadian.required'    => 'Mohon mengisi Bulan Kejadian.',
            'penahanan_status.required' => 'Mohon memilih Status Penahanan.',
            'penahanan_status.in'       => 'Status Penahanan tidak valid.',
            'penahanan_rutan.required_if'       => 'Nama Rutan wajib diisi jika status Ditahan.',
            'penahanan_start_date.required_if'  => 'Tanggal Mulai Penahanan wajib diisi jika status Ditahan.',
            'penahanan_end_date.required_if'    => 'Tanggal Selesai Penahanan wajib diisi jika status Ditahan.',
            'penahanan_end_date.after_or_equal' => 'Tanggal Selesai Penahanan harus setelah atau sama dengan Tanggal Mulai.',
            'surat_penangguhan_penahanan_number.required_if' => 'Nomor Surat Penangguhan wajib diisi jika status Ditangguhkan.',
            'surat_penangguhan_penahanan_date.required_if'   => 'Tanggal Surat Penangguhan wajib diisi jika status Ditangguhkan.',
            'suspects.required' => 'Mohon memilih minimal 1 Tersangka.',
            'suspects.min'      => 'Mohon memilih minimal 1 Tersangka.',
        ]);
    }

    private function parseSaksi(Request $request): array
    {
        return $request->input('daftar_saksi', []);
    }

    private function parseBarangBukti(Request $request): array
    {
        $names = $request->input('bb_nama', []);
        $result = [];
        foreach ($names as $i => $name) {
            if (empty($name)) continue;
            $result[] = [
                'nama'       => $name,
                'jumlah'     => $request->input("bb_jumlah.$i", '1'),
                'satuan'     => $request->input("bb_satuan.$i", 'unit'),
                'keterangan' => $request->input("bb_keterangan.$i", ''),
            ];
        }
        return $result;
    }

    private function parseAhli(Request $request): array
    {
        $names = $request->input('ahli_nama', []);
        $result = [];
        foreach ($names as $i => $name) {
            if (empty($name)) continue;
            $result[] = [
                'nama'     => $name,
                'keahlian' => $request->input("ahli_keahlian.$i", ''),
            ];
        }
        return $result;
    }

    private function loadDistricts(Accident $accident): array
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
        $allDistricts  = file_exists($districtsPath) ? json_decode(file_get_contents($districtsPath), true) ?? [] : [];
        if ($regencyCode) {
            return array_values(array_filter($allDistricts, fn($d) => isset($d['KodePuskarda']) && strpos($d['KodePuskarda'], $regencyCode . '.') === 0));
        }
        return $allDistricts;
    }

    
    // ==========================================
    // PORTED DARI TAHAP 1 UNTUK PASAL DAN AJAX
    // ==========================================
    public function getAJAXSprindikLaws(Request $request)
    {
        $sprindikId = $request->query('sprindik_id');
        if (!$sprindikId) {
            return response()->json(['pasal_string' => '']);
        }
        $pasalString = $this->formatSprindikLawsString($sprindikId);
        return response()->json(['pasal_string' => $pasalString]);
    }

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

    public function getLocations(Request $request)
    {
        $class = $request->class;
        $parent_id = $request->parent_id;

        $locations = Location::where('is_active', true)
                        ->where('parent_id', $parent_id)
                        ->where('class', $class)
                        ->orderBy('name', 'asc')
                        ->get();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $locations
        ], 200);
    }
    public function getAJAXKodeWilayah(Request $request)
    {
        $lokasiKejadian = $request->input('lokasiKejadian', '');
        
        // Extract Kecamatan name from Lokasi Kejadian
        // Assuming format like "KEC. HATONDUHAN" or "KEC HATONDUHAN"
        $kodeWilayah = '';
        if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $lokasiKejadian, $matches)) {
            $kecamatanName = trim($matches[1]);
            
            // Search for the district in the database
            $district = \App\Models\Geography\District::where('name', 'LIKE', '%' . $kecamatanName . '%')->first();
            
            if ($district) {
                $kodeWilayah = $district->id;
            }
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'kode_wilayah' => $kodeWilayah
        ], 200);
    }

    private function terbilang($angka) {
        $angka = abs($angka);
        $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($angka < 12) {
            $temp = " " . $baca[$angka];
        } else if ($angka < 20) {
            $temp = $this->terbilang($angka - 10) . " belas";
        } else if ($angka < 100) {
            $temp = $this->terbilang($angka / 10) . " puluh" . $this->terbilang($angka % 10);
        } else if ($angka < 200) {
            $temp = " seratus" . $this->terbilang($angka - 100);
        } else if ($angka < 1000) {
            $temp = $this->terbilang($angka / 100) . " ratus" . $this->terbilang($angka % 100);
        } else if ($angka < 2000) {
            $temp = " seribu" . $this->terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $temp = $this->terbilang($angka / 1000) . " ribu" . $this->terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $temp = $this->terbilang($angka / 1000000) . " juta" . $this->terbilang($angka % 1000000);
        }
        return trim($temp);
    }

    /**
     * Submit document for approval
     */
    public function submit($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $document = Tahap2Document::findOrFail($id);
            
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

            $document = Tahap2Document::findOrFail($id);
            
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

            $document->update([
                'status_id' => '5',
                'approved_at' => Carbon::now(),
                'released_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestampsLog,
                'ip_addresses' => $ipAddresses,
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
