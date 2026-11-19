<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratPencabutanDpbDocument\SuratPencabutanDpbDocument;
use App\Models\Dpb;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPencabutanDpbDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Tampilkan form pembuatan Surat Pencabutan Daftar Pencarian Barang (S-24.6.2)
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?: $request->input('accident_id');
        if (!$accidentId) {
            return redirect()->back()->with('error', 'Parameter accident_id tidak ditemukan.');
        }

        // Jika sudah ada dokumen yang tersimpan, redirect langsung ke halaman edit
        $existingDoc = SuratPencabutanDpbDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.surat-pencabutan-dpb-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Ambil data pejabat penandatangan aktif
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->signatory()
                ->get();
        }

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->orderBy('first_name', 'asc')
                ->get();
        }

        // Ambil daftar petugas penyidik polres
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

        // Cek apakah perkara ini sudah memiliki pencatatan barang di tabel DPB
        $existingDpbItems = Dpb::where('accident_id', $accidentId)->get();

        return view('docs.surat-pencabutan-dpb-document.create', compact(
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'existingDpbItems'
        ));
    }

    /**
     * Simpan data Surat Pencabutan Daftar Pencarian Barang ke Database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        // Nilai fallback otomatis jika tidak diisi
        $defaultNomorLp = $request->input('nomor_lp') ?: ($request->input('accidentNumber') ?: ($accident->no_lp ?? '-'));
        $defaultLocation = $request->input('dikeluarkan_di') ?: ($accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat'));
        $defaultSatker = $request->input('satker_penyidik') ?: ($accident ? 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? '')) : 'Satuan Lalu Lintas');
        $defaultTanggalLp = $request->input('tanggal_lp') ?: ($accident && $accident->report_date ? Carbon::parse($accident->report_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'));

        $request->merge([
            'nomor_lp' => $defaultNomorLp,
            'dikeluarkan_di' => $defaultLocation,
            'satker_penyidik' => $defaultSatker,
            'tanggal_lp' => $defaultTanggalLp,
        ]);

        $request->validate([
            'accident_id' => 'required',
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required',
            'dikeluarkan_di' => 'required',
            'nama_barang' => 'required',
            'jumlah_barang' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'jabatan_penandatangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Format Taksiran Harga agar selalu ada prefix 'Rp. '
            $rawHarga = trim($request->input('taksiran_harga') ?? '');
            if ($rawHarga !== '') {
                $cleanHarga = preg_replace('/^Rp\.?\s*/i', '', $rawHarga);
                $taksiranHarga = 'Rp. ' . $cleanHarga;
            } else {
                $taksiranHarga = '-';
            }

            // Format Distribusi Surat (Tembusan dinamis)
            $rawTembusan = $request->input('tembusan') ?: ($request->input('carbonCopies') ?: []);
            $filteredTembusan = [];
            if (is_array($rawTembusan)) {
                foreach ($rawTembusan as $t) {
                    if (trim($t) !== '') {
                        $filteredTembusan[] = trim($t);
                    }
                }
            }
            if (empty($filteredTembusan)) {
                $filteredTembusan = [
                    'Ketua Pengadilan Negeri ' . ucwords(strtolower($accident->polres->name ?? '')),
                    'Kepala Kejaksaan Negeri ' . ucwords(strtolower($accident->polres->name ?? '')),
                    'Penyidik / Pemohon',
                ];
            }

            $document = SuratPencabutanDpbDocument::create([
                'id' => (string) Str::uuid(),
                'accident_id' => $accidentId,
                'document_category_id' => '0715',
                'status_id' => '2',
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : null,
                'satker_penyidik' => $request->satker_penyidik,

                // Dasar Pencabutan
                'nama_instansi_dasar' => $request->nama_instansi_dasar,
                'nomor_surat_dasar' => $request->nomor_surat_dasar,
                'tanggal_surat_dasar' => $request->tanggal_surat_dasar ? Carbon::parse($request->tanggal_surat_dasar)->format('Y-m-d') : null,
                'nomor_dpb' => $request->nomor_dpb,
                'tanggal_dpb' => $request->tanggal_dpb ? Carbon::parse($request->tanggal_dpb)->format('Y-m-d') : null,

                // 12 Butir Keterangan Barang Bukti (Spesimen S-24.6.2)
                'nama_barang' => $request->nama_barang,
                'jumlah_barang' => $request->jumlah_barang,

                // Poin 3: Tanda Khusus
                'no_registrasi' => $request->no_registrasi,
                'isi_silinder' => $request->isi_silinder,
                'bahan_bakar' => $request->bahan_bakar,
                'warna_tnkb' => $request->warna_tnkb,
                'tahun_registrasi' => $request->tahun_registrasi,
                'no_bpkb' => $request->no_bpkb,
                'tanda_khusus_lain' => $request->tanda_khusus_lain,

                // Poin 4: Ciri-ciri
                'tipe_barang' => $request->tipe_barang,
                'jenis_barang' => $request->jenis_barang,
                'model_barang' => $request->model_barang,
                'ciri_lain' => $request->ciri_lain,

                // Poin 5 s/d 12
                'warna_barang' => $request->warna_barang,
                'tahun_pembuatan' => $request->tahun_pembuatan,
                'taksiran_harga' => $taksiranHarga,
                'merek_dagang' => $request->merek_dagang,
                'nomor_pembuatan' => $request->nomor_pembuatan,
                'tempat_pembuatan' => $request->tempat_pembuatan,
                'cara_hilangnya' => $request->cara_hilangnya,
                'keterangan_lain' => $request->keterangan_lain,

                // Distribusi Tembusan (Dinamis seperti S-22 / S-23)
                'tembusan' => $filteredTembusan,
                'kepada_1' => $filteredTembusan[0] ?? null,
                'kepada_2' => $filteredTembusan[1] ?? null,
                'kepada_3' => $filteredTembusan[2] ?? null,

                // Pejabat Penandatangan
                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => $request->nrp_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan,

                // Audit
                'created_by_user_id' => Auth::id() ?: 1,
                'ip_addresses' => ['created_ip' => $request->ip()],
                'timestamps' => ['created_at' => Carbon::now()->toIso8601String()],
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Pencabutan Daftar Pencarian Barang (S-24.6.2) berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Surat Pencabutan DPB: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form pengubahan Surat Pencabutan Daftar Pencarian Barang
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id, Request $request)
    {
        $document = SuratPencabutanDpbDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Ambil data pejabat penandatangan aktif
        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->signatory()
                ->get();
        }

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = AuthorizedSignatory::select('*', DB::raw("CONCAT(first_title, ' ', first_name, ' ', last_name, ', ', last_title) AS full_name"))
                ->where('polres_id', $accident->polres_id)
                ->orderBy('first_name', 'asc')
                ->get();
        }

        // Ambil daftar petugas penyidik polres
        $officers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->orderBy('first_name', 'asc')
            ->get();

        $existingDpbItems = Dpb::where('accident_id', $accidentId)->get();

        return view('docs.surat-pencabutan-dpb-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'authorizedSignatories',
            'officers',
            'existingDpbItems'
        ));
    }

    /**
     * Perbarui data Surat Pencabutan Daftar Pencarian Barang
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $document = SuratPencabutanDpbDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        $defaultNomorLp = $request->input('nomor_lp') ?: ($document->nomor_lp ?: ($accident->no_lp ?? '-'));
        $defaultLocation = $request->input('dikeluarkan_di') ?: ($document->dikeluarkan_di ?: ($accident && in_array($accident->polres->id ?? '', ['1114']) ? 'Medan' : ($accident->polres->name ?? 'Tempat')));
        $defaultSatker = $request->input('satker_penyidik') ?: ($document->satker_penyidik ?: ($accident ? 'Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? '')) : 'Satuan Lalu Lintas'));
        $defaultTanggalLp = $request->input('tanggal_lp') ?: ($document->tanggal_lp ?: ($accident && $accident->report_date ? Carbon::parse($accident->report_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d')));

        $request->merge([
            'nomor_lp' => $defaultNomorLp,
            'dikeluarkan_di' => $defaultLocation,
            'satker_penyidik' => $defaultSatker,
            'tanggal_lp' => $defaultTanggalLp,
        ]);

        $request->validate([
            'nomor_surat' => 'required',
            'tanggal_surat' => 'required',
            'dikeluarkan_di' => 'required',
            'nama_barang' => 'required',
            'jumlah_barang' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'jabatan_penandatangan' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Format Taksiran Harga agar selalu ada prefix 'Rp. '
            $rawHarga = trim($request->input('taksiran_harga') ?? '');
            if ($rawHarga !== '') {
                $cleanHarga = preg_replace('/^Rp\.?\s*/i', '', $rawHarga);
                $taksiranHarga = 'Rp. ' . $cleanHarga;
            } else {
                $taksiranHarga = $document->taksiran_harga ?: '-';
            }

            // Format Distribusi Surat (Tembusan dinamis)
            $rawTembusan = $request->input('tembusan') ?: ($request->input('carbonCopies') ?: []);
            $filteredTembusan = [];
            if (is_array($rawTembusan)) {
                foreach ($rawTembusan as $t) {
                    if (trim($t) !== '') {
                        $filteredTembusan[] = trim($t);
                    }
                }
            }

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : $document->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : $document->tanggal_lp,
                'satker_penyidik' => $request->satker_penyidik,

                'nama_instansi_dasar' => $request->nama_instansi_dasar,
                'nomor_surat_dasar' => $request->nomor_surat_dasar,
                'tanggal_surat_dasar' => $request->tanggal_surat_dasar ? Carbon::parse($request->tanggal_surat_dasar)->format('Y-m-d') : $document->tanggal_surat_dasar,
                'nomor_dpb' => $request->nomor_dpb,
                'tanggal_dpb' => $request->tanggal_dpb ? Carbon::parse($request->tanggal_dpb)->format('Y-m-d') : $document->tanggal_dpb,

                'nama_barang' => $request->nama_barang,
                'jumlah_barang' => $request->jumlah_barang ?: $document->jumlah_barang,

                'no_registrasi' => $request->no_registrasi,
                'isi_silinder' => $request->isi_silinder,
                'bahan_bakar' => $request->bahan_bakar,
                'warna_tnkb' => $request->warna_tnkb,
                'tahun_registrasi' => $request->tahun_registrasi,
                'no_bpkb' => $request->no_bpkb,
                'tanda_khusus_lain' => $request->tanda_khusus_lain,

                'tipe_barang' => $request->tipe_barang,
                'jenis_barang' => $request->jenis_barang,
                'model_barang' => $request->model_barang,
                'ciri_lain' => $request->ciri_lain,

                'warna_barang' => $request->warna_barang,
                'tahun_pembuatan' => $request->tahun_pembuatan,
                'taksiran_harga' => $taksiranHarga,
                'merek_dagang' => $request->merek_dagang,
                'nomor_pembuatan' => $request->nomor_pembuatan,
                'tempat_pembuatan' => $request->tempat_pembuatan,
                'cara_hilangnya' => $request->cara_hilangnya,
                'keterangan_lain' => $request->keterangan_lain,

                // Distribusi Tembusan (Dinamis seperti S-22 / S-23)
                'tembusan' => !empty($filteredTembusan) ? $filteredTembusan : $document->tembusan,
                'kepada_1' => $filteredTembusan[0] ?? $document->kepada_1,
                'kepada_2' => $filteredTembusan[1] ?? $document->kepada_2,
                'kepada_3' => $filteredTembusan[2] ?? $document->kepada_3,

                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => $request->nrp_penandatangan,
                'jabatan_penandatangan' => $request->jabatan_penandatangan ?: $document->jabatan_penandatangan,

                'updated_by_user_id' => Auth::id() ?: 1,
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Pencabutan Daftar Pencarian Barang (S-24.6.2) berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Surat Pencabutan DPB: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Surat Pencabutan DPB berdasarkan ID dokumen
     *
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id, Request $request)
    {
        try {
            $document = SuratPencabutanDpbDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/surat_pencabutan_dpb.docx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word surat_pencabutan_dpb.docx tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templateFile);

            // 1. Kop Surat & Lokasi
            $poldaFullName = $accident->polres->polda->name ?? ($accident->polres->polda->full_name ?? '');
            $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114']))
                ? 'DIREKTORAT LALU LINTAS'
                : 'RESOR ' . strtoupper($accident->polres->full_name ?? $accident->polres->name ?? '');
            $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_district ?? '') . ', ' . ($accident->polres->polres_zipcode ?? '')));

            $documentLocation = $document->dikeluarkan_di ?: 'Tempat';
            $docDateFormatted = $document->tanggal_surat
                ? Carbon::parse($document->tanggal_surat)->locale('id')->translatedFormat('d F Y')
                : Carbon::now()->locale('id')->translatedFormat('d F Y');

            $templateProcessor->setValue('daerahPoliceFullName', strtoupper($poldaFullName));
            $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
            $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
            $templateProcessor->setValue('documentLocation', $documentLocation);
            $templateProcessor->setValue('documentDate', $docDateFormatted);

            // 2. Identitas Surat
            $templateProcessor->setValue('documentNumber', $document->nomor_surat ?: '-');

            // 3. Poin Berdasarkan
            $templateProcessor->setValue('baseInstitutionName', $document->nama_instansi_dasar ?: 'Kepolisian');
            $templateProcessor->setValue('baseDocumentNumber', $document->nomor_surat_dasar ?: '-');
            $baseDocDateFormatted = $document->tanggal_surat_dasar
                ? Carbon::parse($document->tanggal_surat_dasar)->locale('id')->translatedFormat('d F Y')
                : '-';
            $templateProcessor->setValue('baseDocumentDate', $baseDocDateFormatted);

            $templateProcessor->setValue('dpbNumber', $document->nomor_dpb ?: ($document->nomor_surat ?: '-'));
            $dpbDateFormatted = $document->tanggal_dpb
                ? Carbon::parse($document->tanggal_dpb)->locale('id')->translatedFormat('d F Y')
                : '-';
            $templateProcessor->setValue('dpbDate', $dpbDateFormatted);

            // 4. Tabel 12 Butir Keterangan Barang Bukti
            $templateProcessor->setValue('itemName', $document->nama_barang ?: '-');
            $templateProcessor->setValue('quantity', $document->jumlah_barang ?: '1 (satu) unit');

            // Poin 3: Tanda-tanda khusus
            $templateProcessor->setValue('registrationNumber', $document->no_registrasi ?: '-');
            $templateProcessor->setValue('cylinderCapacity', $document->isi_silinder ?: '-');
            $templateProcessor->setValue('fuelType', $document->bahan_bakar ?: '-');
            $templateProcessor->setValue('tnkbColor', $document->warna_tnkb ?: '-');
            $templateProcessor->setValue('registrationYear', $document->tahun_registrasi ?: '-');
            $templateProcessor->setValue('bpkbNumber', $document->no_bpkb ?: '-');
            $templateProcessor->setValue('specialSignsOther', $document->tanda_khusus_lain ? 'Lain-lain: ' . $document->tanda_khusus_lain : '-');

            // Poin 4: Ciri-ciri
            $templateProcessor->setValue('itemType', $document->tipe_barang ?: '-');
            $templateProcessor->setValue('itemCategory', $document->jenis_barang ?: '-');
            $templateProcessor->setValue('itemModel', $document->model_barang ?: '-');
            $templateProcessor->setValue('physicalFeaturesOther', $document->ciri_lain ? 'Ciri lain: ' . $document->ciri_lain : '-');

            // Poin 5 s/d 12
            $templateProcessor->setValue('itemColor', $document->warna_barang ?: '-');
            $templateProcessor->setValue('manufactureYear', $document->tahun_pembuatan ?: '-');

            // Format Taksiran Harga selalu berawalan Rp.
            $rawPrice = $document->taksiran_harga;
            if ($rawPrice) {
                $digits = preg_replace('/[^0-9]/', '', $rawPrice);
                if (!empty($digits) && is_numeric($digits)) {
                    $formattedPrice = 'Rp. ' . number_format((float) $digits, 0, ',', '.') . ',-';
                } else {
                    $clean = preg_replace('/^Rp\.?\s*/i', '', trim($rawPrice));
                    $formattedPrice = 'Rp. ' . $clean;
                }
            } else {
                $formattedPrice = 'Rp. -';
            }
            $templateProcessor->setValue('estimatedPrice', $formattedPrice);

            $templateProcessor->setValue('tradeMark', $document->merek_dagang ?: '-');
            $templateProcessor->setValue('engineFrameNumber', $document->nomor_pembuatan ?: '-');
            $templateProcessor->setValue('manufacturePlace', $document->tempat_pembuatan ?: '-');
            $templateProcessor->setValue('lossChronology', $document->cara_hilangnya ?: '-');
            $templateProcessor->setValue('importantNotes', $document->keterangan_lain ?: '-');

            // 5. Tembusan / Distribusi Kepada (Dinamis sesuai S-22 / S-23)
            $carbonCopies = $document->tembusan;
            if (empty($carbonCopies) || !is_array($carbonCopies)) {
                $carbonCopies = array_filter([$document->kepada_1, $document->kepada_2, $document->kepada_3]);
            }
            if (empty($carbonCopies)) {
                $carbonCopies = [
                    'Ketua Pengadilan Negeri ' . ucwords(strtolower($accident->polres->name ?? '')),
                    'Kepala Kejaksaan Negeri ' . ucwords(strtolower($accident->polres->name ?? '')),
                    'Penyidik / Pemohon',
                ];
            }

            $blockDist = [];
            $iter = 1;
            foreach ($carbonCopies as $ccVal) {
                if (trim($ccVal)) {
                    $blockDist[] = [
                        'distribution_iteration' => $iter++,
                        'distribution_name' => trim($ccVal),
                    ];
                }
            }
            if (empty($blockDist)) {
                $blockDist[] = [
                    'distribution_iteration' => 1,
                    'distribution_name' => '-',
                ];
            }

            $templateProcessor->cloneRowAndSetValues('distribution_iteration', $blockDist);

            // 6. Penandatangan (Atasan Penyidik)
            $sigHead = strtoupper($document->jabatan_penandatangan ?: 'ATASAN PENYIDIK');
            $templateProcessor->setValue('signatoryHeadText', $sigHead);
            $templateProcessor->setValue('signatoryName', $document->nama_penandatangan ?: '-');
            $templateProcessor->setValue('signatoryRankName', strtoupper($document->pangkat_penandatangan ?: '-'));
            $cleanNrp = preg_replace('/[^0-9]/', '', (string)($document->nrp_penandatangan ?? ''));
            $templateProcessor->setValue('signatoryRegisterNumber', $cleanNrp ?: '-');

            // Simpan file temporary dan download
            $safeDocNo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document->nomor_surat ?: 'Pencabutan_DPB');
            $filename = 'generate/' . Str::uuid() . '_Surat_Pencabutan_DPB_' . $safeDocNo . '.docx';
            $fullPath = public_path($filename);
            $templateProcessor->saveAs($fullPath);

            return response()->download($fullPath, 'Surat_Pencabutan_DPB_' . $safeDocNo . '.docx')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh Surat Pencabutan DPB: ' . $e->getMessage());
        }
    }

    /**
     * Hapus (Soft Delete) Dokumen Surat Pencabutan DPB
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        try {
            $document = SuratPencabutanDpbDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Pencabutan Daftar Pencarian Barang (S-24.6.2) berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Dokumen Pencabutan DPB: ' . $e->getMessage());
        }
    }
}
