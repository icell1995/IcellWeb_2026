<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

use App\Helpers\PeopleNameHelper;
use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\Police;
use App\Models\Lib\Prison;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentOfficer;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocumentAttachment;

use App\Traits\DocsOfficersTraits;

class SuratPerintahPenahananDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected $docService;

    // Master jenis penahanan sesuai standar KUHAP / SPPT-TI
    public static $masterJenisPenahanan = [
        1 => 'Penahanan Rumah Tahanan Negara (Rutan)',
        2 => 'Penahanan Rumah',
        3 => 'Penahanan Kota',
    ];

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Tampilan form pembuatan Surat Perintah Penahanan (S-17)
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

        // Master jenis penahanan
        $masterJenisPenahanan = self::$masterJenisPenahanan;

        // Daftar Rutan / Lapas dari lib.prisons
        $prisons = Prison::where('is_active', true)->orderBy('name')->get();

        // Kode Satker polres penerbit sesuai polres pada nomor LP
        $polresPenerbit = Police::find($accident->polres_id);
        $kodeSatkerDefault = $polresPenerbit->satker_code ?? ($accident->polres->satker_code ?? '006.09.05');

        // SPDP info (Nomor & Tanggal SPDP dibuat readonly)
        $spdpLatest = $spdpDocuments->sortByDesc('id')->first();
        $nomorSpdp = $spdpLatest->document_number ?? '';
        $tanggalSpdp = $spdpLatest->document_date ?? '';

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
            $pasalList = ['Pasal 310 UU No. 22 Tahun 2009'];
        }

        return view('docs.surat-perintah-penahanan-document.create', compact(
            'accidentId',
            'accident',
            'spdpDocuments',
            'sprindikDocuments',
            'penetapanTersangkaDocuments',
            'suspects',
            'authorizedSignatories',
            'internalOfficers',
            'masterJenisPenahanan',
            'prisons',
            'kodeSatkerDefault',
            'nomorSpdp',
            'tanggalSpdp',
            'pasalList'
        ));
    }

    /**
     * Simpan dokumen Surat Perintah Penahanan
     */
    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->accident_id);
        $accident = Accident::with(['polres'])->where('id', $accidentId)->firstOrFail();

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Identitas Dokumen (Gambar 1)
        $nomor = htmlspecialchars($request->nomor ?? $request->document_number ?? '');
        $tanggal = htmlspecialchars($request->tanggal ?? $request->document_date ?? date('Y-m-d'));

        // SPDP & Satker otomatis dari sistem
        $spdpLatest = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();
        $nomorSpdp = htmlspecialchars($request->nomor_spdp ?: ($spdpLatest->document_number ?? ''));
        $tanggalSpdp = htmlspecialchars($request->tanggal_spdp ?: ($spdpLatest->document_date ?? ''));
        $kodeSatkerPenerbitSpdp = htmlspecialchars($accident->polres->satker_code ?? ($request->kode_satker_penerbit_spdp ?: '006.09.05'));
        $nomorSuratPerintahPenangkapan = $request->nomor_surat_perintah_penangkapan ? htmlspecialchars($request->nomor_surat_perintah_penangkapan) : null;

        // Konten Dokumen (Gambar 2)
        $kodeJenisPenahanan = intval($request->kode_jenis_penahanan);
        $kodeSatkerTempatPenahanan = $request->kode_satker_tempat_penahanan ? htmlspecialchars($request->kode_satker_tempat_penahanan) : null;
        $tanggalMulai = htmlspecialchars($request->tanggal_mulai);
        $tanggalAkhir = htmlspecialchars($request->tanggal_akhir);

        $suspectIds = $request->suspects ?? [];
        $signatoryId = htmlspecialchars($request->signatory);

        $jenisPenahananText = self::$masterJenisPenahanan[$kodeJenisPenahanan] ?? 'Penahanan Rumah Tahanan Negara';
        $lokasiPenahanan = htmlspecialchars($request->lokasi_penahanan ?? $request->tempat_penahanan_nama ?? '');
        $cabangPenahanan = htmlspecialchars($request->cabang_penahanan ?? '');

        // Relasi dokumen terkait jika dipilih
        $sprindikId = $request->surat_perintah_penyidikan_document_id ?: null;
        $penetapanTersangkaId = $request->surat_ketetapan_penetapan_tersangka_id ?: null;

        // Cek duplikasi nomor jika diisi
        if (!empty($nomor)) {
            $exists = SuratPerintahPenahananDocument::where('accident_id', $accidentId)
                ->where('nomor', 'ILIKE', $nomor)
                ->exists();
            if ($exists) {
                return redirect()->back()->with('error', 'Surat Perintah Penahanan nomor ' . $nomor . ' sudah ada.')->withInput();
            }
        }

        // Ambil daftar UU / Pasal langsung dari proses backend (Surat Perintah Penyidikan)
        $daftarUuPasal = [];
        $sprindik = null;
        if ($sprindikId) {
            $sprindik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')->find($sprindikId);
        }
        if (!$sprindik) {
            $sprindik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
                ->where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->latest()
                ->first();
        }
        if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws) {
            foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag == 'MAIN') {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $daftarUuPasal[] = trim($chapter . ' ' . $constName);
                } else {
                    $daftarUuPasal[] = trim($law->constitution ?? '');
                }
            }
        }
        $daftarUuPasal = array_values(array_filter($daftarUuPasal));
        if (empty($daftarUuPasal)) {
            $daftarUuPasal = ['Pasal 310 UU No. 22 Tahun 2009'];
        }

        DB::beginTransaction();
        try {
            $doc = SuratPerintahPenahananDocument::create([
                'accident_id'                           => $accidentId,
                'surat_perintah_penyidikan_document_id' => $sprindikId,
                'surat_ketetapan_penetapan_tersangka_id'=> $penetapanTersangkaId,

                // Identitas Dokumen (Gambar 1)
                'nomor'                                 => $nomor ?: null,
                'document_number'                       => $nomor ?: null,
                'tanggal'                               => $tanggal,
                'document_date'                         => $tanggal,
                'nomor_spdp'                            => $nomorSpdp,
                'tanggal_spdp'                          => $tanggalSpdp,
                'kode_satker_penerbit_spdp'             => $kodeSatkerPenerbitSpdp,
                'nomor_surat_perintah_penangkapan'      => $nomorSuratPerintahPenangkapan,

                // Konten Dokumen (Gambar 2)
                'kode_jenis_penahanan'                  => $kodeJenisPenahanan,
                'jenis_penahanan'                       => $jenisPenahananText,
                'kode_satker_tempat_penahanan'          => $kodeSatkerTempatPenahanan,
                'lokasi_penahanan'                      => $lokasiPenahanan,
                'cabang_penahanan'                      => $cabangPenahanan,
                'tanggal_mulai'                         => $tanggalMulai,
                'start_date'                            => $tanggalMulai,
                'tanggal_akhir'                         => $tanggalAkhir,
                'end_date'                              => $tanggalAkhir,

                // Workflow status & audit
                'status_id'                             => '2', // CREATED
                'document_category_id'                  => '0601',
                'is_active'                             => true,
                'created_by_user_id'                    => Auth::id(),
                'ip_addresses'                          => [
                    'created_ip' => $request->ip(),
                ],
                'messages'                              => [
                    'daftar_uu_pasal' => $daftarUuPasal,
                    'tempat_penahanan_nama' => $lokasiPenahanan,
                ],
            ]);

            // Simpan Pejabat Penandatangan
            $signatory = Officer::with(['rank', 'position'])->find($signatoryId);
            if ($signatory) {
                $doc->suratPerintahPenahananDocumentOfficers()->create([
                    'surat_perintah_penahanan_document_id' => $doc->id,
                    'sort'                                 => 1,
                    'register_number'                      => $signatory->register_number,
                    'first_title'                          => $signatory->first_title,
                    'first_name'                           => $signatory->first_name,
                    'last_name'                            => $signatory->last_name,
                    'last_title'                           => $signatory->last_title,
                    'phone_number'                         => $signatory->phone_number,
                    'email'                                => $signatory->email,
                    'police_id'                            => $signatory->police_id,
                    'rank_id'                              => $signatory->rank_id,
                    'position_id'                          => $signatory->position_id,
                    'status'                               => 'PRESENT',
                    'class'                                => 'SIGNATORY',
                    'flag'                                 => 'INTERNAL',
                    'insert_method'                        => 'MANUAL',
                ]);
            }

            // Simpan Petugas yang Diperintahkan (Member Officers)
            $internalOfficers = $request->internalOfficers ?? [];
            foreach ($internalOfficers as $sortIdx => $regNum) {
                $officer = Officer::where('register_number', $regNum)->first();
                if ($officer) {
                    $doc->suratPerintahPenahananDocumentOfficers()->create([
                        'surat_perintah_penahanan_document_id' => $doc->id,
                        'sort'                                 => $sortIdx + 2,
                        'register_number'                      => $officer->register_number,
                        'first_title'                          => $officer->first_title,
                        'first_name'                           => $officer->first_name,
                        'last_name'                            => $officer->last_name,
                        'last_title'                           => $officer->last_title,
                        'phone_number'                         => $officer->phone_number,
                        'email'                                => $officer->email,
                        'police_id'                            => $officer->police_id,
                        'rank_id'                              => $officer->rank_id,
                        'position_id'                          => $officer->position_id,
                        'status'                               => 'PRESENT',
                        'class'                                => 'MEMBER',
                        'flag'                                 => 'INTERNAL',
                        'insert_method'                        => 'IMPORT',
                    ]);
                }
            }

            // Sync Tersangka
            $doc->suspects()->sync($suspectIds);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penahanan berhasil dibuat.');
    }

    /**
     * Tampilan form edit Surat Perintah Penahanan
     */
    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratPerintahPenahananDocument::with([
            'suspects',
            'signatory',
            'memberOfficers.rank',
            'memberOfficers.position',
            'memberOfficers.police'
        ])->where('id', $id)->firstOrFail();
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

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

        $masterJenisPenahanan = self::$masterJenisPenahanan;
        $prisons = Prison::where('is_active', true)->orderBy('name')->get();

        // SPDP & Satker
        $spdpLatest = $spdpDocuments->sortByDesc('id')->first();
        $nomorSpdp = $document->nomor_spdp ?: ($spdpLatest->document_number ?? '');
        $tanggalSpdp = $document->tanggal_spdp ?: ($spdpLatest->document_date ?? '');
        $polresPenerbit = Police::find($accident->polres_id);
        $kodeSatkerDefault = $document->kode_satker_penerbit_spdp ?: ($polresPenerbit->satker_code ?? ($accident->polres->satker_code ?? '006.09.05'));

        // Pasal disangkakan
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
        }
        $pasalList = array_values(array_filter($pasalList));

        return view('docs.surat-perintah-penahanan-document.edit', compact(
            'accidentId',
            'accident',
            'document',
            'spdpDocuments',
            'sprindikDocuments',
            'penetapanTersangkaDocuments',
            'suspects',
            'authorizedSignatories',
            'internalOfficers',
            'masterJenisPenahanan',
            'prisons',
            'kodeSatkerDefault',
            'nomorSpdp',
            'tanggalSpdp',
            'pasalList'
        ));
    }

    /**
     * Update Surat Perintah Penahanan
     */
    public function update(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->accident_id);
        $accident = Accident::with(['polres'])->where('id', $accidentId)->firstOrFail();
        $document = SuratPerintahPenahananDocument::where('id', $id)->firstOrFail();

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $nomor = htmlspecialchars($request->nomor ?? $request->document_number ?? '');
        $tanggal = htmlspecialchars($request->tanggal ?? $request->document_date ?? date('Y-m-d'));

        $spdpLatest = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();
        $nomorSpdp = htmlspecialchars($request->nomor_spdp ?: ($document->nomor_spdp ?: ($spdpLatest->document_number ?? '')));
        $tanggalSpdp = htmlspecialchars($request->tanggal_spdp ?: ($document->tanggal_spdp ?: ($spdpLatest->document_date ?? '')));
        $kodeSatkerPenerbitSpdp = htmlspecialchars($accident->polres->satker_code ?? ($request->kode_satker_penerbit_spdp ?: ($document->kode_satker_penerbit_spdp ?: '006.09.05')));
        $nomorSuratPerintahPenangkapan = $request->nomor_surat_perintah_penangkapan ? htmlspecialchars($request->nomor_surat_perintah_penangkapan) : null;

        $kodeJenisPenahanan = intval($request->kode_jenis_penahanan);
        $kodeSatkerTempatPenahanan = $request->kode_satker_tempat_penahanan ? htmlspecialchars($request->kode_satker_tempat_penahanan) : null;
        $tanggalMulai = htmlspecialchars($request->tanggal_mulai);
        $tanggalAkhir = htmlspecialchars($request->tanggal_akhir);

        $suspectIds = $request->suspects ?? [];
        $signatoryId = htmlspecialchars($request->signatory);

        $jenisPenahananText = self::$masterJenisPenahanan[$kodeJenisPenahanan] ?? 'Penahanan Rumah Tahanan Negara';
        $lokasiPenahanan = htmlspecialchars($request->lokasi_penahanan ?? $request->tempat_penahanan_nama ?? '');
        $cabangPenahanan = htmlspecialchars($request->cabang_penahanan ?? '');

        // Relasi sprindik
        $sprindikId = $request->surat_perintah_penyidikan_document_id ?: $document->surat_perintah_penyidikan_document_id;
        $sprindik = null;
        if ($sprindikId) {
            $sprindik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')->find($sprindikId);
        }
        if (!$sprindik) {
            $sprindik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
                ->where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->latest()
                ->first();
        }

        $daftarUuPasal = [];
        if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws) {
            foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag == 'MAIN') {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $daftarUuPasal[] = trim($chapter . ' ' . $constName);
                } else {
                    $daftarUuPasal[] = trim($law->constitution ?? '');
                }
            }
        }
        $daftarUuPasal = array_values(array_filter($daftarUuPasal));
        if (empty($daftarUuPasal)) {
            $daftarUuPasal = $document->messages['daftar_uu_pasal'] ?? ['Pasal 310 UU No. 22 Tahun 2009'];
        }

        DB::beginTransaction();
        try {
            $messages = $document->messages ?? [];
            $messages['daftar_uu_pasal'] = $daftarUuPasal;
            $messages['tempat_penahanan_nama'] = $lokasiPenahanan;

            $document->update([
                'nomor'                                 => $nomor ?: $document->nomor,
                'document_number'                       => $nomor ?: $document->document_number,
                'tanggal'                               => $tanggal,
                'document_date'                         => $tanggal,
                'nomor_spdp'                            => $nomorSpdp,
                'tanggal_spdp'                          => $tanggalSpdp,
                'kode_satker_penerbit_spdp'             => $kodeSatkerPenerbitSpdp,
                'nomor_surat_perintah_penangkapan'      => $nomorSuratPerintahPenangkapan,
                'kode_jenis_penahanan'                  => $kodeJenisPenahanan,
                'jenis_penahanan'                       => $jenisPenahananText,
                'kode_satker_tempat_penahanan'          => $kodeSatkerTempatPenahanan,
                'lokasi_penahanan'                      => $lokasiPenahanan,
                'cabang_penahanan'                      => $cabangPenahanan,
                'tanggal_mulai'                         => $tanggalMulai,
                'start_date'                            => $tanggalMulai,
                'tanggal_akhir'                         => $tanggalAkhir,
                'end_date'                              => $tanggalAkhir,
                'updated_by_user_id'                    => Auth::id(),
                'messages'                              => $messages,
            ]);

            // Update Signatory
            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $document->suratPerintahPenahananDocumentOfficers()->where('class', 'SIGNATORY')->delete();
                $document->suratPerintahPenahananDocumentOfficers()->create([
                    'surat_perintah_penahanan_document_id' => $document->id,
                    'sort'                                 => 1,
                    'register_number'                      => $signatory->register_number,
                    'first_title'                          => $signatory->first_title,
                    'first_name'                           => $signatory->first_name,
                    'last_name'                            => $signatory->last_name,
                    'last_title'                           => $signatory->last_title,
                    'phone_number'                         => $signatory->phone_number,
                    'email'                                => $signatory->email,
                    'police_id'                            => $signatory->police_id,
                    'rank_id'                              => $signatory->rank_id,
                    'position_id'                          => $signatory->position_id,
                    'status'                               => 'PRESENT',
                    'class'                                => 'SIGNATORY',
                    'flag'                                 => 'INTERNAL',
                    'insert_method'                        => 'MANUAL',
                ]);
            }

            // Update Petugas yang Diperintahkan (Member Officers)
            $document->suratPerintahPenahananDocumentOfficers()->where('class', 'MEMBER')->delete();
            $internalOfficers = $request->internalOfficers ?? [];
            foreach ($internalOfficers as $sortIdx => $regNum) {
                $officer = Officer::where('register_number', $regNum)->first();
                if ($officer) {
                    $document->suratPerintahPenahananDocumentOfficers()->create([
                        'surat_perintah_penahanan_document_id' => $document->id,
                        'sort'                                 => $sortIdx + 2,
                        'register_number'                      => $officer->register_number,
                        'first_title'                          => $officer->first_title,
                        'first_name'                           => $officer->first_name,
                        'last_name'                            => $officer->last_name,
                        'last_title'                           => $officer->last_title,
                        'phone_number'                         => $officer->phone_number,
                        'email'                                => $officer->email,
                        'police_id'                            => $officer->police_id,
                        'rank_id'                              => $officer->rank_id,
                        'position_id'                          => $officer->position_id,
                        'status'                               => 'PRESENT',
                        'class'                                => 'MEMBER',
                        'flag'                                 => 'INTERNAL',
                        'insert_method'                        => 'IMPORT',
                    ]);
                }
            }

            $document->suspects()->sync($suspectIds);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penahanan berhasil diperbarui.');
    }

    /**
     * Hapus Dokumen
     */
    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratPerintahPenahananDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $document->suspects()->detach();
            $document->suratPerintahPenahananDocumentOfficers()->delete();
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Penahanan berhasil dihapus.');
    }

    /**
     * Unduh template Word S-17
     */
    public function download($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratPerintahPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects.gender',
            'suspects.education',
            'suspects.job',
            'suspects.religion',
            'suspects.maritalStatus',
            'suspects.country',
            'signatory.rank',
            'signatory.position',
            'suratPerintahPenahananDocumentOfficers.rank',
            'suratPerintahPenahananDocumentOfficers.position',
            'suratPerintahPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument'
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $signatory = $document->signatory;
        $firstSuspect = $document->suspects->first();
        $sprindik = $document->suratPerintahPenyidikanDocument;
        $spt = $document->suratKetetapanTentangPenetapanTersangkaDocument;

        $templatePath = public_path('word-template/surat_perintah_penahanan.docx');
        if (!File::exists($templatePath)) {
            return redirect()->back()->with('error', 'Template dokumen tidak ditemukan.');
        }

        // Terapkan filter opsi penahanan (Poin 8): hanya menampilkan jenis penahanan yang dipilih
        $tempFilteredTemplate = storage_path('app/temp_penahanan_tpl_' . $document->id . '_' . time() . '.docx');
        $this->filterPenahananDocx(
            $templatePath,
            $tempFilteredTemplate,
            $document->kode_jenis_penahanan,
            $document->lokasi_penahanan,
            $document->kode_satker_tempat_penahanan
        );

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($tempFilteredTemplate);

        // Header / Kop Kepolisian
        $poldaFullName = $accident->polres->polda->full_name ?? ($accident->polres->polda->name ?? 'METRO JAYA');
        $resorPoliceFullName = 'KEPOLISIAN RESOR ' . ($accident->polres->full_name ?? $accident->polres->name ?? '');
        $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_district ?? '')));

        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($poldaFullName));
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($resorPoliceFullName));
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);

        // Dokumen & Perkara
        $templateProcessor->setValue('documentNumber', $document->document_number ?? $document->nomor ?? '-');
        $templateProcessor->setValue('accidentNumber', $accident->no_lp ?? '-');
        $templateProcessor->setValue('accidentDate', $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-');

        // Sprindik & SPT
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $sprindik ? $sprindik->document_number : ($document->messages['nomor_sprindik'] ?? '-'));
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDate', ($sprindik && $sprindik->document_date) ? Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentNumber', $spt ? $spt->document_number : ($document->messages['nomor_spt'] ?? '-'));
        $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentDate', ($spt && $spt->document_date) ? Carbon::parse($spt->document_date)->locale('id')->translatedFormat('d F Y') : '-');

        // Petugas yang Diperintahkan (Poin 6: clone block block_officers)
        $memberOfficers = $document->suratPerintahPenahananDocumentOfficers
            ->where('class', 'MEMBER')
            ->sortBy('sort');

        $blockOfficers = [];
        $no = 1;
        foreach ($memberOfficers as $officer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name' => ($officer->first_title ? $officer->first_title . ' ' : '') . $officer->first_name,
                'last_name' => ($officer->last_title ? ', ' . $officer->last_title : ($officer->last_name ? ' ' . $officer->last_name : '')),
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
                'rank_id' => '-',
                'officer_id' => '-',
                'position' => '-',
            ]]);
        }

        // Data Tersangka
        if ($firstSuspect) {
            $templateProcessor->setValue('suspectName', $firstSuspect->name);
            $templateProcessor->setValue('suspectIdentityNumber', $firstSuspect->identity_number ?? '-');
            $templateProcessor->setValue('suspectBirthPlace', $firstSuspect->birth_place ?? '-');
            $templateProcessor->setValue('suspectBirthDate', $firstSuspect->birth_date ? Carbon::parse($firstSuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('suspectGenderName', $firstSuspect->gender->name ?? ($firstSuspect->gender_short_name ?? '-'));
            $templateProcessor->setValue('suspectJobName', $firstSuspect->job->name ?? ($firstSuspect->occupation ?? '-'));
            $templateProcessor->setValue('suspectNationality', $firstSuspect->country->name ?? ($firstSuspect->nationality ?? 'Indonesia'));
            $templateProcessor->setValue('suspectFullAddress', $firstSuspect->address ?? '-');
        } else {
            $templateProcessor->setValue('suspectName', '-');
            $templateProcessor->setValue('suspectIdentityNumber', '-');
            $templateProcessor->setValue('suspectBirthPlace', '-');
            $templateProcessor->setValue('suspectBirthDate', '-');
            $templateProcessor->setValue('suspectGenderName', '-');
            $templateProcessor->setValue('suspectJobName', '-');
            $templateProcessor->setValue('suspectNationality', '-');
            $templateProcessor->setValue('suspectFullAddress', '-');
        }

        // Pejabat Penandatangan
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

        $tempFileName = 'Surat_Perintah_Penahanan_' . str_replace(['/', '\\', ' '], '_', $document->document_number ?? $document->id) . '.docx';
        $tempPath = storage_path('app/' . $tempFileName);
        $templateProcessor->saveAs($tempPath);

        // Hapus template sementara setelah proses selesai
        if (file_exists($tempFilteredTemplate)) {
            @unlink($tempFilteredTemplate);
        }

        return response()->download($tempPath, $tempFileName)->deleteFileAfterSend(true);
    }

    /**
     * Filter opsi penetapan tersangka penahanan pada Word template (Poin 8)
     */
    private function filterPenahananDocx($sourcePath, $targetPath, $kodeJenisPenahanan, $lokasiPenahanan, $kodeSatkerTempatPenahanan)
    {
        copy($sourcePath, $targetPath);

        $zip = new \ZipArchive();
        if ($zip->open($targetPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $rows = $dom->getElementsByTagName('tr');
        $rowsToRemove = [];

        if ($kodeJenisPenahanan == 1) {
            $text = 'rumah tahanan Negara ' . ($lokasiPenahanan ?: '...');
            if ($kodeSatkerTempatPenahanan) {
                $text .= ' (Satker: ' . $kodeSatkerTempatPenahanan . ')';
            }
            $text .= ';';
        } elseif ($kodeJenisPenahanan == 2) {
            $text = 'rumah tempat tinggal/kediaman tersangka di ' . ($lokasiPenahanan ?: '.....') . ';';
        } else {
            $text = 'kota tempat tinggal/kediaman tersangka di ' . ($lokasiPenahanan ?: '...') . ';';
        }

        foreach ($rows as $tr) {
            $content = $tr->textContent;
            $isRutan = strpos($content, 'rumah tahanan Negara') !== false;
            $isRumah = strpos($content, 'rumah tempat tinggal') !== false;
            $isKota = strpos($content, 'kota tempat tinggal') !== false;

            if ($isRutan) {
                if ($kodeJenisPenahanan == 1) {
                    $this->replaceTrText($dom, $tr, $text);
                } else {
                    $rowsToRemove[] = $tr;
                }
            }

            if ($isRumah) {
                if ($kodeJenisPenahanan == 2) {
                    $this->replaceTrText($dom, $tr, $text);
                } else {
                    $rowsToRemove[] = $tr;
                }
            }

            if ($isKota) {
                if ($kodeJenisPenahanan == 3) {
                    $this->replaceTrText($dom, $tr, $text);
                } else {
                    $rowsToRemove[] = $tr;
                }
            }
        }

        foreach ($rowsToRemove as $r) {
            if ($r->parentNode) {
                $r->parentNode->removeChild($r);
            }
        }

        $zip->addFromString('word/document.xml', $dom->saveXML());
        $zip->close();
    }

    private function replaceTrText(\DOMDocument $dom, \DOMElement $tr, $text)
    {
        $cells = $tr->getElementsByTagName('tc');
        if ($cells->length > 0) {
            $lastCell = $cells->item($cells->length - 1);
            $p = $lastCell->getElementsByTagName('p')->item(0);
            if ($p) {
                while ($p->firstChild) {
                    $p->removeChild($p->firstChild);
                }
                $r = $dom->createElement('w:r');
                $rPr = $dom->createElement('w:rPr');
                $rFonts = $dom->createElement('w:rFonts');
                $rFonts->setAttribute('w:ascii', 'Arial Narrow');
                $rFonts->setAttribute('w:hAnsi', 'Arial Narrow');
                $rPr->appendChild($rFonts);
                $r->appendChild($rPr);
                $t = $dom->createElement('w:t');
                $t->nodeValue = $text;
                $r->appendChild($t);
                $p->appendChild($r);
            }
        }
    }

    /**
     * Tampilan Detail / Format JSON S-17 (SPPT-TI / Pusiknas)
     */
    public function show($id, Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratPerintahPenahananDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects.gender',
            'suspects.education',
            'suspects.job',
            'suspects.religion',
            'suspects.maritalStatus',
            'suspects.country',
            'suspects.location',
            'suratPerintahPenahananDocumentOfficers.rank',
            'suratPerintahPenahananDocumentOfficers.position',
            'status',
            'attachment'
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;

        // Build JSON format persis syarat user
        $jsonPayload = $this->buildJsonPayload($document);

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json($jsonPayload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return view('docs.surat-perintah-penahanan-document.show', compact('document', 'accident', 'jsonPayload'));
    }

    /**
     * Helper membuat JSON S-17 persis dengan contoh user
     */
    public function buildJsonPayload(SuratPerintahPenahananDocument $document)
    {
        $accident = $document->accident;

        // 1. Identitas Dokumen
        $identitasDokumen = [
            'nomor'                            => $document->nomor ?? $document->document_number ?? '-',
            'tanggal'                          => $document->tanggal ? Carbon::parse($document->tanggal)->format('Y-m-d') : ($document->document_date ? Carbon::parse($document->document_date)->format('Y-m-d') : date('Y-m-d')),
            'nomor_spdp'                       => $document->nomor_spdp ?? '-',
            'tanggal_spdp'                     => $document->tanggal_spdp ? Carbon::parse($document->tanggal_spdp)->format('Y-m-d') : date('Y-m-d'),
            'kode_satker_penerbit_spdp'        => $document->kode_satker_penerbit_spdp ?? ($accident->polres->satker_code ?? '006.09.05'),
            'nomor_surat_perintah_penangkapan' => $document->nomor_surat_perintah_penangkapan ?? null,
        ];

        // 2. Konten Dokumen - Tersangka
        $daftarTersangka = [];
        $daftarUuPasalDoc = $document->messages['daftar_uu_pasal'] ?? ['Pasal 109 ayat (1) KUHAP'];

        foreach ($document->suspects as $suspect) {
            $daftarTersangka[] = [
                'nama'                    => $suspect->name ?? '-',
                'tempat_lahir'            => $suspect->birth_place ?? '-',
                'kode_jenis_kelamin'      => intval($suspect->gender->pusiknas_id ?? $suspect->gender->emp_id ?? $suspect->gender_id ?? 1),
                'alamat'                  => $suspect->address ?? '-',
                'kode_wilayah'            => $suspect->location->emp_id ?? ($suspect->location_id ?? ($accident->polres->satker_code ?? '11.01.17')),
                'kode_pendidikan'         => intval($suspect->education->pusiknas_id ?? $suspect->education->emp_id ?? 1),
                'kode_pekerjaan'          => intval($suspect->job->pusiknas_id ?? $suspect->job->emp_id ?? 87),
                'nama_ibu'                => $suspect->mother_name ?? '-',
                'kode_agama'              => intval($suspect->religion->pusiknas_id ?? $suspect->religion->emp_id ?? 1),
                'kode_status_perkawinan'  => intval($suspect->maritalStatus->pusiknas_id ?? $suspect->maritalStatus->emp_id ?? 1),
                'kode_warga_negara'       => strtolower($suspect->country->emp_id ?? ($suspect->nationality ?? 'idn')),
                'umur_saat_tindak_pidana' => intval($suspect->age ?? ($suspect->birth_date ? Carbon::parse($suspect->birth_date)->age : 21)),
                'daftar_uu_pasal'         => $daftarUuPasalDoc,
            ];
        }

        if (empty($daftarTersangka)) {
            $daftarTersangka[] = [
                'nama'                    => '-',
                'tempat_lahir'            => '-',
                'kode_jenis_kelamin'      => 1,
                'alamat'                  => '-',
                'kode_wilayah'            => '11.01.17',
                'kode_pendidikan'         => 1,
                'kode_pekerjaan'          => 87,
                'nama_ibu'                => '-',
                'kode_agama'              => 1,
                'kode_status_perkawinan'  => 1,
                'kode_warga_negara'       => 'idn',
                'umur_saat_tindak_pidana' => 21,
                'daftar_uu_pasal'         => $daftarUuPasalDoc,
            ];
        }

        // 3. Konten Dokumen - Pejabat Penandatangan
        $pejabatPenandatangan = [];
        $officers = $document->suratPerintahPenahananDocumentOfficers;
        foreach ($officers as $officer) {
            $pejabatPenandatangan[] = [
                'nama'        => PeopleNameHelper::getFullName($officer->first_title, $officer->first_name, $officer->last_name, $officer->last_title),
                'nomor_induk' => $officer->register_number ?? '-',
                'jabatan'     => $officer->position->name ?? ($officer->position_id ?? '-'),
                'pangkat'     => $officer->rank->name ?? ($officer->rank_id ?? '-'),
            ];
        }

        if (empty($pejabatPenandatangan)) {
            $pejabatPenandatangan[] = [
                'nama'        => '-',
                'nomor_induk' => '-',
                'jabatan'     => '-',
                'pangkat'     => '-',
            ];
        }

        $kontenDokumen = [
            'tersangka'                    => $daftarTersangka,
            'kode_jenis_penahanan'         => intval($document->kode_jenis_penahanan ?? 1),
            'kode_satker_tempat_penahanan' => $document->kode_satker_tempat_penahanan ?: null,
            'tanggal_mulai'                => $document->tanggal_mulai ? Carbon::parse($document->tanggal_mulai)->format('Y-m-d') : ($document->start_date ? Carbon::parse($document->start_date)->format('Y-m-d') : date('Y-m-d')),
            'tanggal_akhir'                => $document->tanggal_akhir ? Carbon::parse($document->tanggal_akhir)->format('Y-m-d') : ($document->end_date ? Carbon::parse($document->end_date)->format('Y-m-d') : date('Y-m-d')),
            'pejabat_penandatangan'        => $pejabatPenandatangan,
        ];

        return [
            'kode_jenis_dokumen' => 's17',
            'identitas_dokumen'  => $identitasDokumen,
            'konten_dokumen'     => $kontenDokumen,
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
                'errors'  => 'Terjadi kesalahan pada sistem.',
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
            'accident_id'                 => 'required',
            'tanggal'                     => 'required|date_format:Y-m-d',
            'nomor_spdp'                  => 'required|string|max:255',
            'tanggal_spdp'                => 'required|date_format:Y-m-d',
            'kode_satker_penerbit_spdp'   => 'required|string|max:50',
            'nomor_surat_perintah_penangkapan' => 'nullable|string|max:255',
            'kode_jenis_penahanan'        => 'required|integer|in:1,2,3',
            'kode_satker_tempat_penahanan'=> 'nullable|string|max:100',
            'tanggal_mulai'               => 'required|date_format:Y-m-d',
            'tanggal_akhir'               => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'suspects'                    => 'required|array|min:1',
            'signatory'                   => 'required',
        ], [
            'tanggal.required'                   => 'Mohon mengisi Tanggal Surat Perintah Penahanan.',
            'nomor_spdp.required'                => 'Mohon mengisi Nomor SPDP.',
            'tanggal_spdp.required'              => 'Mohon mengisi Tanggal SPDP.',
            'kode_satker_penerbit_spdp.required' => 'Mohon mengisi Kode Satker Penerbit SPDP.',
            'kode_jenis_penahanan.required'      => 'Mohon memilih Jenis Penahanan.',
            'tanggal_mulai.required'             => 'Mohon mengisi Tanggal Mulai Penahanan.',
            'tanggal_akhir.required'             => 'Mohon mengisi Tanggal Akhir Penahanan.',
            'tanggal_akhir.after_or_equal'       => 'Tanggal Akhir harus sama atau setelah Tanggal Mulai.',
            'suspects.required'                  => 'Mohon memilih minimal 1 Tersangka.',
            'suspects.min'                       => 'Mohon memilih minimal 1 Tersangka.',
            'signatory.required'                 => 'Mohon memilih Pejabat Penandatangan.',
        ]);
    }
}
