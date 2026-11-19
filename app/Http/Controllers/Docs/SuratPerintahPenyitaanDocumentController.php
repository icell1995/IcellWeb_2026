<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPerintahPenyitaanDocument\SuratPerintahPenyitaanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuratPerintahPenyitaanDocumentController extends Controller
{
    private function formatDateIndo($dateStr)
    {
        if (empty($dateStr) || $dateStr === '-') return '-';
        try {
            return Carbon::parse($dateStr)->locale('id')->translatedFormat('d F Y');
        } catch (\Exception $e) {
            return $dateStr;
        }
    }

    private function getNamaHari($dateStr)
    {
        if (empty($dateStr) || $dateStr === '-') return 'Senin';
        try {
            $days = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
            $dayEn = Carbon::parse($dateStr)->format('l');
            return $days[$dayEn] ?? 'Senin';
        } catch (\Exception $e) {
            return 'Senin';
        }
    }

    private function getRomawiBulan($bulanAngka)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[(int)$bulanAngka] ?? 'I';
    }

    private function getMasterData($accidentId)
    {
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Cari sprindik jika ada
        $suratPerintahPenyidikanDocuments = collect();
        try {
            $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentLaws.crimeConstitution'])
                ->where('accident_id', $accidentId)
                ->orderByDesc('created_at')
                ->get();

            foreach ($suratPerintahPenyidikanDocuments as $sp) {
                $parts = [];
                foreach ($sp->suratPerintahPenyidikanDocumentLaws as $law) {
                    if ($law->flag == 'MAIN') {
                        $chapter = $law->constitution_chapter ?? '';
                        $name = $law->crimeConstitution->name ?? '';
                        $t = trim($chapter . ' ' . $name);
                        if ($t) $parts[] = $t;
                    } elseif ($law->flag == 'ADDITIONAL' || !empty($law->constitution)) {
                        $parts[] = trim($law->constitution);
                    }
                }
                $sp->formatted_laws = implode(', ', array_filter($parts));
            }
        } catch (\Exception $e) {}

        $sprindik = $suratPerintahPenyidikanDocuments->first();
        $pasalText = ($sprindik && !empty($sprindik->formatted_laws))
            ? $sprindik->formatted_laws
            : '';

        $suspects = collect();
        try {
            $suspects = Suspect::with(['job'])->where('accident_id', $accidentId)->orderBy('name')->get();
        } catch (\Exception $e) {}

        $suspect = $suspects->first();
        $namaTersangkaDefault = $suspect ? $suspect->name : ($accident->driver_name ?? '');

        // Ambil data barang bukti dari database jika tersedia
        $barangBuktiText = '';
        try {
            $bbList = DB::table('daftar_barang_bukti')->where('accident_id', $accidentId)->get();
            if ($bbList->isNotEmpty()) {
                $bbItems = [];
                foreach ($bbList as $bb) {
                    $itemDesc = $bb->nama_barang ?? $bb->item_name ?? $bb->description ?? '';
                    if ($itemDesc) $bbItems[] = $itemDesc;
                }
                if (!empty($bbItems)) {
                    $barangBuktiText = implode(', ', $bbItems);
                }
            }
        } catch (\Exception $e) {}

        $getOldNewPolresIds = [$accident->polres_id];
        try {
            $mapped = DB::table('polres_migration_history')
                ->where('old_police_id', $accident->polres_id)
                ->orWhere('new_police_id', $accident->polres_id)
                ->first();
            if ($mapped) {
                $getOldNewPolresIds = [$mapped->old_police_id, $mapped->new_police_id];
            }
        } catch (\Exception $e) {}

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

        if ($authorizedSignatories->isEmpty()) {
            $authorizedSignatories = Officer::with(['rank', 'position'])
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->active()
                ->orderBy('first_name')
                ->get();
        }

        $availableOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->active()
            ->orderBy('first_name')
            ->get();

        // Default data form (dikosongkan agar pengguna mengisi data riil)
        $defaultDocNumber = '';
        $defaultValidUntil = '';

        return compact(
            'accident', 'accidentId', 'suratPerintahPenyidikanDocuments', 'sprindik', 'pasalText', 'suspects', 'namaTersangkaDefault',
            'barangBuktiText', 'authorizedSignatories', 'availableOfficers', 'defaultDocNumber', 'defaultValidUntil'
        );
    }

    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);
        return view('docs.surat-perintah-penyitaan-document.create', $data);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        $docNumber = $request->input('nomor_surat_perintah') ?? '-';
        $dateInput = $request->input('tanggal_surat') ?? $request->input('tanggal_dikeluarkan');
        $docDate = $dateInput ? Carbon::parse($dateInput)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        SuratPerintahPenyitaanDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $docNumber,
                'document_date' => $docDate,
                'data' => $payload,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        if ($request->has('download_word')) {
            return $this->downloadWord($request);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Penyitaan (S-17) berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = SuratPerintahPenyitaanDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.surat-perintah-penyitaan-document.edit', array_merge($data, compact('savedDoc', 'savedData')));
    }

    public function update(Request $request)
    {
        return $this->store($request);
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $doc = SuratPerintahPenyitaanDocument::where('accident_id', $accidentId)->first();
        if ($doc) {
            $doc->delete();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Penyitaan berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Jika request tidak lengkap (misal download langsung dari link accordion), ambil dari database
        if (!$request->filled('nomor_surat_perintah')) {
            $savedDoc = SuratPerintahPenyitaanDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/surat_perintah_penyitaan.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template dokumen Surat Perintah Penyitaan tidak ditemukan.');
        }

        Settings::setOutputEscapingEnabled(true);
        $template = new TemplateProcessor($templatePath);

        // 1. Kop Surat
        $poldaName = strtoupper($accident->polres->polda->name ?? 'JAWA TIMUR');
        $polresName = strtoupper($accident->polres->name ?? 'RESOR KOTA');
        $polresAddress = $accident->polres->address ?? 'Jalan Raya Kepolisian No. 1';

        $template->setValue('daerahPoliceFullName', $poldaName);
        $template->setValue('resorPoliceFullName', $polresName);
        $template->setValue('resorPoliceAddress', $polresAddress);

        // 2. Nomor Surat
        $noSurat = $request->input('nomor_surat_perintah') ?? '-';
        $template->setValue('nomor_surat_perintah', $noSurat);

        // 3. Dasar
        $dasarPasalUu = $request->input('dasar_pasal_uu') ?? '-';
        $noLp = $request->input('no_lp') ?? ($accident->no_lp ?? '-');
        $tglLp = $this->formatDateIndo($request->input('tanggal_lp') ?? ($accident->accident_report_date ?? date('Y-m-d')));
        $noSprindik = $request->input('no_sprindik') ?? '-';
        $tglSprindik = $request->input('tanggal_sprindik') ? $this->formatDateIndo($request->input('tanggal_sprindik')) : '-';

        if ($request->filled('surat_perintah_penyidikan_id')) {
            $sprindikObj = SuratPerintahPenyidikanDocument::find($request->input('surat_perintah_penyidikan_id'));
            if ($sprindikObj) {
                $noSprindik = $sprindikObj->document_number ?? $noSprindik;
                $tglSprindik = $this->formatDateIndo($sprindikObj->document_date ?? date('Y-m-d'));
            }
        }

        $template->setValue('dasar_pasal_uu', $dasarPasalUu);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tglLp);
        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tglSprindik);

        // 4. Untuk (Poin 1 s/d 7)
        $bendaSitaanText = $request->input('benda_sitaan_text') ?? '-';
        $tindakPidana = $request->input('tindak_pidana') ?? '-';
        $pasalPelanggaran = $request->input('pasal_pelanggaran') ?? '-';
        $pasalKuhpBaru = $request->input('pasal_kuhp_baru') ?? '-';
        $tkp = $request->input('tkp') ?? ($accident->accident_road ?? $accident->accident_address ?? '-');
        $tglKejadian = $this->formatDateIndo($request->input('tanggal_kejadian') ?? ($accident->accident_date ?? date('Y-m-d')));
        $kurunWaktu = $request->input('kurun_waktu') ?? '-';
        $namaTersangka = $request->input('nama_tersangka') ?? ($accident->driver_name ?? '-');
        $tglBerlakuSampai = $request->input('tanggal_berlaku_sampai') ? $this->formatDateIndo($request->input('tanggal_berlaku_sampai')) : '-';

        $template->setValue('benda_sitaan_text', $bendaSitaanText);
        $template->setValue('tindak_pidana', $tindakPidana);
        $template->setValue('pasal_pelanggaran', $pasalPelanggaran);
        $template->setValue('pasal_kuhp_baru', $pasalKuhpBaru);
        $template->setValue('tkp', $tkp);
        $template->setValue('tanggal_kejadian', $tglKejadian);
        $template->setValue('kurun_waktu', $kurunWaktu);
        $template->setValue('nama_tersangka', $namaTersangka);
        $template->setValue('tanggal_berlaku_sampai', $tglBerlakuSampai);

        // 5. Petugas yang ditugaskan (Lampiran)
        $kesatuanDefault = 'Satlantas ' . ($accident->polres->name ?? 'Polres');

        // Petugas 1
        $p1 = Officer::with(['rank', 'position'])->find($request->input('petugas_1_id'));
        $p1Nama = $p1 ? $p1->full_name : ($request->input('petugas_1_nama') ?? '-');
        $p1Pangkat = $p1 ? ($p1->rank->name ?? '') : ($request->input('petugas_1_pangkat') ?? '-');
        $p1Nrp = $p1 ? ($p1->register_number ?? $p1->nrp ?? '-') : ($request->input('petugas_1_nrp') ?? '-');
        $p1Jabatan = $p1 ? ($p1->position->name ?? 'Penyidik') : ($request->input('petugas_1_jabatan') ?? 'Penyidik');
        $p1Kesatuan = $request->input('petugas_1_kesatuan') ?? $kesatuanDefault;

        // Petugas 2
        $p2 = Officer::with(['rank', 'position'])->find($request->input('petugas_2_id'));
        $p2Nama = $p2 ? $p2->full_name : ($request->input('petugas_2_nama') ?? '-');
        $p2Pangkat = $p2 ? ($p2->rank->name ?? '') : ($request->input('petugas_2_pangkat') ?? '-');
        $p2Nrp = $p2 ? ($p2->register_number ?? $p2->nrp ?? '-') : ($request->input('petugas_2_nrp') ?? '-');
        $p2Jabatan = $p2 ? ($p2->position->name ?? 'Penyidik Pembantu') : ($request->input('petugas_2_jabatan') ?? 'Penyidik Pembantu');
        $p2Kesatuan = $request->input('petugas_2_kesatuan') ?? $kesatuanDefault;

        // Petugas 3 (Opsional)
        $p3 = Officer::with(['rank', 'position'])->find($request->input('petugas_3_id'));
        $p3Nama = $p3 ? $p3->full_name : ($request->input('petugas_3_nama') ?? '-');
        $p3Pangkat = $p3 ? ($p3->rank->name ?? '') : ($request->input('petugas_3_pangkat') ?? '-');
        $p3Nrp = $p3 ? ($p3->register_number ?? $p3->nrp ?? '-') : ($request->input('petugas_3_nrp') ?? '-');
        $p3Jabatan = $p3 ? ($p3->position->name ?? 'Penyidik Pembantu') : ($request->input('petugas_3_jabatan') ?? '-');
        $p3Kesatuan = $request->input('petugas_3_kesatuan') ?? ($p3 ? $kesatuanDefault : '-');

        $template->setValue('petugas_1_nama', $p1Nama);
        $template->setValue('petugas_1_pangkat', $p1Pangkat);
        $template->setValue('petugas_1_nrp', $p1Nrp);
        $template->setValue('petugas_1_jabatan', $p1Jabatan);
        $template->setValue('petugas_1_kesatuan', $p1Kesatuan);

        $template->setValue('petugas_2_nama', $p2Nama);
        $template->setValue('petugas_2_pangkat', $p2Pangkat);
        $template->setValue('petugas_2_nrp', $p2Nrp);
        $template->setValue('petugas_2_jabatan', $p2Jabatan);
        $template->setValue('petugas_2_kesatuan', $p2Kesatuan);

        $template->setValue('petugas_3_nama', $p3Nama);
        $template->setValue('petugas_3_pangkat', $p3Pangkat);
        $template->setValue('petugas_3_nrp', $p3Nrp);
        $template->setValue('petugas_3_jabatan', $p3Jabatan);
        $template->setValue('petugas_3_kesatuan', $p3Kesatuan);

        // 6. Yang Menerima Perintah (Penyidik Penerima)
        $penerima = Officer::with(['rank'])->find($request->input('penerima_id')) ?? $p1;
        $penerimaNama = $penerima ? $penerima->full_name : ($request->input('penerima_perintah_nama') ?? $p1Nama);
        $penerimaPangkatNrp = $penerima ? (($penerima->rank->name ?? '') . ' NRP. ' . ($penerima->register_number ?? $penerima->nrp ?? '')) : ($request->input('penerima_perintah_pangkat_nrp') ?? ($p1Pangkat . ' NRP. ' . $p1Nrp));

        $template->setValue('penerima_perintah_nama', $penerimaNama);
        $template->setValue('penerima_perintah_pangkat_nrp', $penerimaPangkatNrp);

        // 7. Atasan Penyidik / Penandatangan
        $atasan = Officer::with(['rank', 'position'])->find($request->input('atasan_id'));
        $atasanNama = $atasan ? $atasan->full_name : ($request->input('atasan_penyidik_nama') ?? '-');
        $atasanPangkatNrp = $atasan ? (($atasan->rank->name ?? '') . ' NRP. ' . ($atasan->register_number ?? $atasan->nrp ?? '')) : ($request->input('atasan_penyidik_pangkat_nrp') ?? '-');
        $atasanSebutan = $atasan ? strtoupper($atasan->position->name ?? 'ATASAN PENYIDIK') : ($request->input('atasan_penyidik_sebutan') ?? 'ATASAN PENYIDIK');

        $template->setValue('atasan_penyidik_nama', $atasanNama);
        $template->setValue('atasan_penyidik_pangkat_nrp', $atasanPangkatNrp);
        $template->setValue('atasan_penyidik_sebutan', $atasanSebutan);

        $tempatDikeluarkan = $request->input('tempat_dikeluarkan') ?? ($accident->polres->name ?? 'Tempat');
        $tglDikeluarkan = $this->formatDateIndo($request->input('tanggal_dikeluarkan') ?? ($request->input('tanggal_surat') ?? date('Y-m-d')));

        $template->setValue('tempat_dikeluarkan', $tempatDikeluarkan);
        $template->setValue('tanggal_dikeluarkan', $tglDikeluarkan);

        $tempFileName = 'Surat_Perintah_Penyitaan_' . time() . '.docx';
        $tempFilePath = storage_path('app/public/' . $tempFileName);
        $template->saveAs($tempFilePath);

        return response()->download($tempFilePath)->deleteFileAfterSend(true);
    }
}

