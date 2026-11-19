<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratPermintaanVisumDocument\SuratPermintaanVisumDocument;
use App\Models\InvolvedPeople;
use App\Models\Officer;
use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Suspect;
use App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocument;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPermintaanVisumDocumentController extends Controller
{
    use DocsOfficersTraits;

    /**
     * Tampilkan form pembuatan Surat Permintaan Visum Et Repertum / Visum Et Psikiatrikum (S-48)
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

        // Jika sudah ada dokumen yang tersimpan, redirect ke halaman edit
        $existingDoc = SuratPermintaanVisumDocument::where('accident_id', $accidentId)->first();
        if ($existingDoc) {
            return redirect()->route('doc.surat-permintaan-visum-document.edit', [
                'id' => $existingDoc->id,
                'accident_id' => $accidentId,
            ]);
        }

        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $involvedList = InvolvedPeople::with(['gender', 'job', 'religion'])
            ->where('accident_id', $accidentId)
            ->get();

        $suspectList = Suspect::with(['gender', 'job', 'religion'])
            ->where('accident_id', $accidentId)
            ->get();

        // Default wilayah polres
        $defaultLocation = '';
        if ($accident && $accident->polres) {
            $reg = trim($accident->polres->polres_regency ?? '');
            if (preg_match('/^KAB\.?\s+/i', $reg)) {
                $reg = preg_replace('/^KAB\.?\s+/i', 'Kabupaten ', $reg);
            }
            $defaultLocation = ucwords(strtolower(trim($reg)));
            if (empty($defaultLocation)) {
                $rawName = trim(preg_replace('/^(POLRESTABES|POLRESTA|POLRES METRO|POLRES|KEPOLISIAN RESOR|POLSEK|POLDA)\s+/i', '', trim($accident->polres->name ?? '')));
                if (preg_match('/^(KABUPATEN|KOTA)\s+/i', $rawName)) {
                    $defaultLocation = ucwords(strtolower($rawName));
                } else {
                    $defaultLocation = 'Kabupaten ' . ucwords(strtolower($rawName));
                }
            }
        }

        // Query Pejabat Penandatangan Polres
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
            $authorizedSignatories = AuthorizedSignatory::where('polres_id', $accident->polres_id)->get();
        }

        // Query Anggota Penyidik Polres
        $memberOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        if ($memberOfficers->isEmpty()) {
            $memberOfficers = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->member()
                ->get();
        }

        // Deteksi riwayat RS / Medis dari Dokumen S-23 jika ada
        $s23Document = null;
        try {
            $s23Document = SuratPerintahPembantaranPenahananDocument::where('accident_id', $accidentId)->latest()->first();
        } catch (\Throwable $e) {
            // Ignore if table not present
        }

        return view('docs.surat-permintaan-visum-document.create', compact(
            'accident',
            'accidentId',
            'involvedList',
            'suspectList',
            'defaultLocation',
            'authorizedSignatories',
            'memberOfficers',
            's23Document'
        ));
    }

    /**
     * Simpan data Surat Permintaan Visum Et Repertum / Psikiatrikum ke Database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        // Fallbacks otomatis jika field tidak terisi
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
            'lampiran_surat' => 'nullable',
            'nama_rumah_sakit' => 'required',
            'kota_rumah_sakit' => 'required',
            'nomor_lp' => 'required',
            'satker_penyidik' => 'required',
            'nama_korban' => 'required',
            'nik_korban' => 'required',
            'jenis_kelamin_korban' => 'required',
            'kewarganegaraan_korban' => 'required',
            'agama_korban' => 'required',
            'tempat_lahir_korban' => 'required',
            'tanggal_lahir_korban' => 'required',
            'pekerjaan_korban' => 'required',
            'alamat_korban' => 'required',
            'jenis_luka' => 'required',
            'penemu' => 'required',
            'lokasi_temu' => 'required',
            'hari_lapor' => 'required',
            'tgl_lapor' => 'required',
            'hari_temu' => 'required',
            'tgl_temu' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'nrp_penandatangan' => 'required',
            'nama_kontak' => 'required',
            'hp_kontak' => 'required',
            'petugas_penyerah' => 'required',
            'petugas_rs' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $document = SuratPermintaanVisumDocument::create([
                'id' => (string) Str::uuid(),
                'accident_id' => $accidentId,
                'document_category_id' => '0714',
                'status_id' => '2',
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'lampiran_surat' => $request->lampiran_surat ?: 'otomatis',
                'nama_rumah_sakit' => $request->nama_rumah_sakit,
                'kota_rumah_sakit' => $request->kota_rumah_sakit,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : null,
                'satker_penyidik' => $request->satker_penyidik,
                'nama_korban' => $request->nama_korban,
                'nik_korban' => $request->nik_korban,
                'jenis_kelamin' => $request->jenis_kelamin_korban,
                'kewarganegaraan' => $request->kewarganegaraan_korban,
                'tempat_lahir' => $request->tempat_lahir_korban,
                'tanggal_lahir' => $request->tanggal_lahir_korban ? Carbon::parse($request->tanggal_lahir_korban)->format('Y-m-d') : null,
                'pekerjaan' => $request->pekerjaan_korban,
                'agama' => $request->agama_korban,
                'alamat' => $request->alamat_korban,
                'hari_lapor' => $request->hari_lapor,
                'tanggal_lapor' => $request->tgl_lapor ? Carbon::parse($request->tgl_lapor)->format('Y-m-d') : null,
                'jam_lapor' => $request->jam_lapor,
                'penemu' => $request->penemu,
                'hari_temu' => $request->hari_temu,
                'tanggal_temu' => $request->tgl_temu ? Carbon::parse($request->tgl_temu)->format('Y-m-d') : null,
                'jam_temu' => $request->jam_temu,
                'lokasi_temu' => $request->lokasi_temu,
                'jenis_luka' => $request->jenis_luka,
                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => $request->nrp_penandatangan,
                'nama_kontak' => $request->nama_kontak,
                'hp_kontak' => $request->hp_kontak,
                'petugas_penyerah' => $request->petugas_penyerah,
                'petugas_rs' => $request->petugas_rs,
                'created_by_user_id' => Auth::id(),
                'ip_addresses' => ['created_ip' => $request->ip()],
                'timestamps' => ['created_at' => now()],
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Permintaan Visum Et Repertum / Visum Et Psikiatrikum berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan Dokumen Visum: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form edit Surat Permintaan Visum Et Repertum / Psikiatrikum
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($arg1, $arg2 = null)
    {
        $id = $arg1 instanceof Request ? $arg2 : $arg1;
        $document = SuratPermintaanVisumDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        $involvedList = InvolvedPeople::with(['gender', 'job', 'religion'])
            ->where('accident_id', $accidentId)
            ->get();

        $suspectList = Suspect::with(['gender', 'job', 'religion'])
            ->where('accident_id', $accidentId)
            ->get();

        $defaultLocation = $document->dikeluarkan_di ?: '';

        // Query Pejabat Penandatangan Polres
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
            $authorizedSignatories = AuthorizedSignatory::where('polres_id', $accident->polres_id)->get();
        }

        // Query Anggota Penyidik Polres
        $memberOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        if ($memberOfficers->isEmpty()) {
            $memberOfficers = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->member()
                ->get();
        }

        $s23Document = null;
        try {
            $s23Document = SuratPerintahPembantaranPenahananDocument::where('accident_id', $accidentId)->latest()->first();
        } catch (\Throwable $e) {
            // Ignore
        }

        return view('docs.surat-permintaan-visum-document.edit', compact(
            'document',
            'accident',
            'accidentId',
            'involvedList',
            'suspectList',
            'defaultLocation',
            'authorizedSignatories',
            'memberOfficers',
            's23Document'
        ));
    }

    /**
     * Perbarui data Surat Permintaan Visum Et Repertum / Psikiatrikum
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $document = SuratPermintaanVisumDocument::findOrFail($id);
        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        $defaultNomorLp = $request->input('nomor_lp') ?: ($request->input('accidentNumber') ?: ($document->nomor_lp ?: ($accident->no_lp ?? '-')));
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
            'lampiran_surat' => 'nullable',
            'nama_rumah_sakit' => 'required',
            'kota_rumah_sakit' => 'required',
            'nomor_lp' => 'required',
            'satker_penyidik' => 'required',
            'nama_korban' => 'required',
            'nik_korban' => 'required',
            'jenis_kelamin_korban' => 'required',
            'kewarganegaraan_korban' => 'required',
            'agama_korban' => 'required',
            'tempat_lahir_korban' => 'required',
            'tanggal_lahir_korban' => 'required',
            'pekerjaan_korban' => 'required',
            'alamat_korban' => 'required',
            'jenis_luka' => 'required',
            'penemu' => 'required',
            'lokasi_temu' => 'required',
            'hari_lapor' => 'required',
            'tgl_lapor' => 'required',
            'hari_temu' => 'required',
            'tgl_temu' => 'required',
            'nama_penandatangan' => 'required',
            'pangkat_penandatangan' => 'required',
            'nrp_penandatangan' => 'required',
            'nama_kontak' => 'required',
            'hp_kontak' => 'required',
            'petugas_penyerah' => 'required',
            'petugas_rs' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : $document->tanggal_surat,
                'dikeluarkan_di' => $request->dikeluarkan_di,
                'lampiran_surat' => $request->lampiran_surat ?: ($document->lampiran_surat ?: 'otomatis'),
                'nama_rumah_sakit' => $request->nama_rumah_sakit,
                'kota_rumah_sakit' => $request->kota_rumah_sakit,
                'nomor_lp' => $request->nomor_lp,
                'tanggal_lp' => $request->tanggal_lp ? Carbon::parse($request->tanggal_lp)->format('Y-m-d') : $document->tanggal_lp,
                'satker_penyidik' => $request->satker_penyidik,
                'nama_korban' => $request->nama_korban,
                'nik_korban' => $request->nik_korban,
                'jenis_kelamin' => $request->jenis_kelamin_korban,
                'kewarganegaraan' => $request->kewarganegaraan_korban,
                'tempat_lahir' => $request->tempat_lahir_korban,
                'tanggal_lahir' => $request->tanggal_lahir_korban ? Carbon::parse($request->tanggal_lahir_korban)->format('Y-m-d') : $document->tanggal_lahir,
                'pekerjaan' => $request->pekerjaan_korban,
                'agama' => $request->agama_korban,
                'alamat' => $request->alamat_korban,
                'hari_lapor' => $request->hari_lapor,
                'tanggal_lapor' => $request->tgl_lapor ? Carbon::parse($request->tgl_lapor)->format('Y-m-d') : $document->tanggal_lapor,
                'jam_lapor' => $request->jam_lapor,
                'penemu' => $request->penemu,
                'hari_temu' => $request->hari_temu,
                'tanggal_temu' => $request->tgl_temu ? Carbon::parse($request->tgl_temu)->format('Y-m-d') : $document->tanggal_temu,
                'jam_temu' => $request->jam_temu,
                'lokasi_temu' => $request->lokasi_temu,
                'jenis_luka' => $request->jenis_luka,
                'nama_penandatangan' => $request->nama_penandatangan,
                'pangkat_penandatangan' => $request->pangkat_penandatangan,
                'nrp_penandatangan' => $request->nrp_penandatangan,
                'nama_kontak' => $request->nama_kontak,
                'hp_kontak' => $request->hp_kontak,
                'petugas_penyerah' => $request->petugas_penyerah,
                'petugas_rs' => $request->petugas_rs,
                'updated_by_user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Permintaan Visum Et Repertum / Visum Et Psikiatrikum berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui Dokumen Visum: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download dokumen Word (.docx) Surat Permintaan Visum berdasarkan ID dokumen yang tersimpan
     *
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id, Request $request)
    {
        try {
            $document = SuratPermintaanVisumDocument::findOrFail($id);
            $accident = Accident::with(['polres.polda'])->findOrFail($document->accident_id);

            $templateFile = public_path('word-template/suratpermintaanvisum.docx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word suratpermintaanvisum.docx tidak ditemukan.');
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

            // 2. Metadata Surat
            $templateProcessor->setValue('documentNumber', $document->nomor_surat ?: '-');
            $templateProcessor->setValue('documentClassificationName', 'Biasa');

            $appendixToken = '__DYNAMIC_APPENDIX_FIELD__';
            $isDynamicAppendix = empty($document->lampiran_surat) 
                || in_array(strtolower(trim($document->lampiran_surat)), [
                    'otomatis', 
                    'otomatis (sesuai jumlah lembar saat diunduh)', 
                    'dua lembar', 
                    'satu lembar', 
                    '1 lembar', 
                    '2 lembar', 
                    '1 (satu) lembar', 
                    '2 (dua) lembar'
                ]);

            if ($isDynamicAppendix) {
                $templateProcessor->setValue('appendix', $appendixToken);
            } else {
                $templateProcessor->setValue('appendix', $document->lampiran_surat);
            }

            // 3. Rumah Sakit Tujuan
            $templateProcessor->setValue('targetHospitalName', $document->nama_rumah_sakit ?: '-');
            $templateProcessor->setValue('targetHospitalLocation', $document->kota_rumah_sakit ?: $documentLocation);

            // 4. Poin 1: Rujukan Laporan Polisi
            $lpNumber = $document->nomor_lp ?: ($accident->no_lp ?: '-');
            $lpDate = $document->tanggal_lp 
                ? Carbon::parse($document->tanggal_lp)->locale('id')->translatedFormat('d F Y') 
                : ($accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('lpNumber', $lpNumber);
            $templateProcessor->setValue('lpDate', $lpDate);

            // 5. Poin 2: Uraian Perkara
            $investigatorUnit = $document->satker_penyidik ?: ('Satuan Lalu Lintas Resor ' . ucwords(strtolower($accident->polres->name ?? '')));
            $templateProcessor->setValue('investigatorUnitName', $investigatorUnit);
            $templateProcessor->setValue('crimeAllegation', 'kecelakaan lalu lintas');
            $templateProcessor->setValue('allegedArticle', 'Pasal 310 / 311 UU No. 22 Tahun 2009');

            // 6. Poin 2: Identitas Korban
            $templateProcessor->setValue('victimName', $document->nama_korban ?: '-');
            $templateProcessor->setValue('victimIdentityNumber', $document->nik_korban ?: '-');
            $templateProcessor->setValue('victimGender', $document->jenis_kelamin ?: '-');
            $templateProcessor->setValue('victimNationality', $document->kewarganegaraan ?: 'Indonesia');
            $templateProcessor->setValue('victimBirthPlace', $document->tempat_lahir ?: '-');
            
            $victimBirthDateFormatted = '-';
            if ($document->tanggal_lahir) {
                $victimBirthDateFormatted = Carbon::parse($document->tanggal_lahir)->locale('id')->translatedFormat('d F Y');
            }
            $templateProcessor->setValue('victimBirthDate', $victimBirthDateFormatted);
            $templateProcessor->setValue('victimJobName', $document->pekerjaan ?: '-');
            $templateProcessor->setValue('victimReligionName', $document->agama ?: '-');
            $templateProcessor->setValue('victimAddress', $document->alamat ?: '-');

            // 7. Poin 2: Riwayat Sebelum ke RS
            $reportDay = $document->hari_lapor ?: 'Senin';
            $reportDateFormatted = $document->tanggal_lapor ? Carbon::parse($document->tanggal_lapor)->locale('id')->translatedFormat('d F Y') : $lpDate;
            $reportTime = $document->jam_lapor ?: '10.00';

            $foundBy = $document->penemu ?: 'Polisi/Masyarakat';
            $foundDay = $document->hari_temu ?: 'Senin';
            $foundDateFormatted = $document->tanggal_temu ? Carbon::parse($document->tanggal_temu)->locale('id')->translatedFormat('d F Y') : $lpDate;
            $foundTime = $document->jam_temu ?: '09.00';
            $foundPlace = $document->lokasi_temu ?: 'TKP Kejadian';
            $injuryCondition = $document->jenis_luka ?: 'fisik';

            $templateProcessor->setValue('reportDay', $reportDay);
            $templateProcessor->setValue('reportDateFormatted', $reportDateFormatted);
            $templateProcessor->setValue('reportTime', $reportTime);

            $templateProcessor->setValue('foundBy', $foundBy);
            $templateProcessor->setValue('foundDay', $foundDay);
            $templateProcessor->setValue('foundDateFormatted', $foundDateFormatted);
            $templateProcessor->setValue('foundTime', $foundTime);
            $templateProcessor->setValue('foundPlace', $foundPlace);
            $templateProcessor->setValue('injuryCondition', $injuryCondition);

            // 8. Poin 3: Kontak Penyidik
            $templateProcessor->setValue('contactOfficerRole', 'Penyidik Pembantu');
            $templateProcessor->setValue('contactOfficerName', $document->nama_kontak ?: 'Penyidik');
            $templateProcessor->setValue('contactOfficerPhone', $document->hp_kontak ?: '-');

            // 9. Penandatangan (Tanda Tangan Pejabat)
            $sigHead = 'KEPALA SATUAN LALU LINTAS RESOR ' . strtoupper($accident->polres->full_name ?? $accident->polres->name ?? '');
            $sigName = $document->nama_penandatangan ?: '-';
            $sigRank = $document->pangkat_penandatangan ?: 'AKP';
            $sigNrp = $document->nrp_penandatangan ?: '-';

            $templateProcessor->setValue('signatoryHeadText', $sigHead);
            $templateProcessor->setValue('signatoryName', $sigName);
            $templateProcessor->setValue('signatoryRankName', strtoupper($sigRank));
            $templateProcessor->setValue('signatoryRegisterNumber', $sigNrp);

            // 10. Lembar Tanda Terima RS (Footer)
            $receiptDay = Carbon::now()->locale('id')->translatedFormat('l');
            $receiptDate = Carbon::now()->locale('id')->translatedFormat('d F Y');
            $receiptTime = Carbon::now()->format('H.i');
            $hospitalOfficer = $document->petugas_rs ?: 'Petugas Medis RS';
            $handoverOfficer = $document->petugas_penyerah ?: ($document->nama_kontak ?: 'Petugas Penyerah');
            $handoverRankNrp = $sigRank . ' NRP ' . $sigNrp;

            $templateProcessor->setValue('receiptDay', $receiptDay);
            $templateProcessor->setValue('receiptDate', $receiptDate);
            $templateProcessor->setValue('receiptTime', $receiptTime);
            $templateProcessor->setValue('hospitalOfficerName', $hospitalOfficer);
            $templateProcessor->setValue('handoverOfficerName', $handoverOfficer);
            $templateProcessor->setValue('handoverOfficerRankNrp', $handoverRankNrp);

            // Simpan file temporary dan download
            $safeDocNo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $document->nomor_surat ?: 'Visum');
            $filename = 'generate/' . Str::uuid() . '_Surat_Permintaan_Visum_' . $safeDocNo . '.docx';
            $fullPath = public_path($filename);
            $templateProcessor->saveAs($fullPath);

            // Jika lampiran dinamis, suntikkan Word Field NUMPAGES agar otomatis menghitung jumlah lembar halaman saat dibuka di Word
            if ($isDynamicAppendix && file_exists($fullPath)) {
                $zip = new \ZipArchive();
                if ($zip->open($fullPath) === true) {
                    $xml = $zip->getFromName('word/document.xml');

                    $fld = '<w:fldSimple w:instr=" NUMPAGES  \* MERGEFORMAT "><w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="19"/><w:szCs w:val="19"/></w:rPr><w:t>1</w:t></w:r></w:fldSimple><w:r><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="19"/><w:szCs w:val="19"/></w:rPr><w:t xml:space="preserve"> lembar</w:t></w:r>';

                    $runTarget = '<w:r w:rsidRPr="00D80388"><w:rPr><w:rFonts w:ascii="Arial Narrow" w:hAnsi="Arial Narrow"/><w:sz w:val="19"/><w:szCs w:val="19"/></w:rPr><w:t>' . $appendixToken . '</w:t></w:r>';

                    if (strpos($xml, $runTarget) !== false) {
                        $xml = str_replace($runTarget, $fld, $xml);
                    } else {
                        $xml = str_replace('<w:t>' . $appendixToken . '</w:t>', $fld, $xml);
                    }

                    $zip->addFromString('word/document.xml', $xml);

                    // Aktifkan updateFields di settings.xml agar Word otomatis merefresh saat file dibuka
                    $settingsXml = $zip->getFromName('word/settings.xml');
                    if ($settingsXml && strpos($settingsXml, 'w:updateFields') === false) {
                        $settingsXml = str_replace('</w:settings>', '<w:updateFields w:val="true"/></w:settings>', $settingsXml);
                        $zip->addFromString('word/settings.xml', $settingsXml);
                    }

                    $zip->close();
                }
            }

            return response()->download($fullPath, 'Surat_Permintaan_Visum_' . $safeDocNo . '.docx')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh Surat Visum: ' . $e->getMessage());
        }
    }

    /**
     * Hapus (Soft Delete) Dokumen Surat Permintaan Visum
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        try {
            $document = SuratPermintaanVisumDocument::findOrFail($id);
            $accidentId = $document->accident_id;
            $document->delete();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Permintaan Visum Et Repertum / Visum Et Psikiatrikum berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Dokumen Visum: ' . $e->getMessage());
        }
    }

    /**
     * Legacy method untuk download langsung jika ada form lama
     */
    public function downloadWord(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $document = SuratPermintaanVisumDocument::where('accident_id', $accidentId)->first();
        if ($document) {
            return $this->download($document->id, $request);
        }
        return redirect()->back()->with('error', 'Dokumen belum tersimpan di database.');
    }
}
