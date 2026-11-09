<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPerintahPenggeledahanBadanDocument\SuratPerintahPenggeledahanBadanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuratPerintahPenggeledahanBadanDocumentController extends Controller
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
            'authorizedSignatories', 'availableOfficers', 'defaultDocNumber', 'defaultValidUntil'
        );
    }

    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);
        return view('docs.surat-perintah-penggeledahan-badan-document.create', $data);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        $docNumber = $request->input('nomor_surat_perintah') ?? '-';
        $dateInput = $request->input('tanggal_surat') ?? $request->input('tanggal_dikeluarkan');
        $docDate = $dateInput ? Carbon::parse($dateInput)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        SuratPerintahPenggeledahanBadanDocument::updateOrCreate(
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
            ->with('success', 'Dokumen Surat Perintah Penggeledahan Badan dan/atau Pakaian berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = SuratPerintahPenggeledahanBadanDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.surat-perintah-penggeledahan-badan-document.edit', array_merge($data, compact('savedDoc', 'savedData')));
    }

    public function update(Request $request)
    {
        return $this->store($request);
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $doc = SuratPerintahPenggeledahanBadanDocument::where('accident_id', $accidentId)->first();
        if ($doc) {
            $doc->delete();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Penggeledahan Badan dan/atau Pakaian berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        // Jika request tidak lengkap (misal download langsung dari link accordion), ambil dari database
        if (!$request->filled('nomor_surat_perintah')) {
            $savedDoc = SuratPerintahPenggeledahanBadanDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/surat_perintah_penggeledahan_badan.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template dokumen Surat Perintah Penggeledahan Badan dan/atau Pakaian tidak ditemukan.');
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

        // 4. Kepada (Petugas 1, 2, 3)
        $p1Nama = strtoupper($request->input('petugas_1_nama') ?? '-');
        $p1Pangkat = strtoupper($request->input('petugas_1_pangkat') ?? '-');
        $p1Nrp = $request->input('petugas_1_nrp') ?? '-';
        $p1Jabatan = $request->input('petugas_1_jabatan') ?? '-';
        if ($request->filled('petugas_1_id')) {
            $p1 = Officer::with(['rank', 'position'])->selectFullName()->find($request->input('petugas_1_id'));
            if ($p1) {
                $p1Nama = strtoupper($p1->full_name ?? ($p1->first_name . ' ' . $p1->last_name));
                $p1Pangkat = strtoupper($p1->rank->name ?? '');
                $p1Nrp = $p1->register_number ?? $p1->nrp ?? '-';
                $p1Jabatan = $p1->position->name ?? '-';
            }
        }

        $p2Nama = strtoupper($request->input('petugas_2_nama') ?? '-');
        $p2Pangkat = strtoupper($request->input('petugas_2_pangkat') ?? '-');
        $p2Nrp = $request->input('petugas_2_nrp') ?? '-';
        $p2Jabatan = $request->input('petugas_2_jabatan') ?? '-';
        if ($request->filled('petugas_2_id')) {
            $p2 = Officer::with(['rank', 'position'])->selectFullName()->find($request->input('petugas_2_id'));
            if ($p2) {
                $p2Nama = strtoupper($p2->full_name ?? ($p2->first_name . ' ' . $p2->last_name));
                $p2Pangkat = strtoupper($p2->rank->name ?? '');
                $p2Nrp = $p2->register_number ?? $p2->nrp ?? '-';
                $p2Jabatan = $p2->position->name ?? '-';
            }
        }

        $p3Nama = strtoupper($request->input('petugas_3_nama') ?? '');
        $p3Pangkat = strtoupper($request->input('petugas_3_pangkat') ?? '');
        $p3Nrp = $request->input('petugas_3_nrp') ?? '';
        $p3Jabatan = $request->input('petugas_3_jabatan') ?? '';
        if ($request->filled('petugas_3_id')) {
            $p3 = Officer::with(['rank', 'position'])->selectFullName()->find($request->input('petugas_3_id'));
            if ($p3) {
                $p3Nama = strtoupper($p3->full_name ?? ($p3->first_name . ' ' . $p3->last_name));
                $p3Pangkat = strtoupper($p3->rank->name ?? '');
                $p3Nrp = $p3->register_number ?? $p3->nrp ?? '-';
                $p3Jabatan = $p3->position->name ?? '-';
            }
        }

        $template->setValue('petugas_1_nama', $p1Nama);
        $template->setValue('petugas_1_pangkat', $p1Pangkat);
        $template->setValue('petugas_1_nrp', $p1Nrp);
        $template->setValue('petugas_1_jabatan', $p1Jabatan);

        $template->setValue('petugas_2_nama', $p2Nama);
        $template->setValue('petugas_2_pangkat', $p2Pangkat);
        $template->setValue('petugas_2_nrp', $p2Nrp);
        $template->setValue('petugas_2_jabatan', $p2Jabatan);

        if (!empty($p3Nama)) {
            $template->setValue('petugas_3_nama', $p3Nama);
            $template->setValue('petugas_3_pangkat', $p3Pangkat);
            $template->setValue('petugas_3_nrp', $p3Nrp);
            $template->setValue('petugas_3_jabatan', $p3Jabatan);
        } else {
            // Kosongkan placeholder jika tidak ada petugas 3
            $template->setValue('petugas_3_nama', '-');
            $template->setValue('petugas_3_pangkat', '');
            $template->setValue('petugas_3_nrp', '-');
            $template->setValue('petugas_3_jabatan', '-');
        }

        // 5. Untuk
        $namaTersangka = $request->input('nama_tersangka') ?? ($accident->driver_name ?? '-');
        if ($request->filled('suspect_id')) {
            $sObj = Suspect::find($request->input('suspect_id'));
            if ($sObj) {
                $namaTersangka = $sObj->name;
            }
        }
        $tindakPidana = $request->input('tindak_pidana') ?? '-';
        $pasalPelanggaran = $request->input('pasal_pelanggaran') ?? '-';
        $uuNomorTahun = $request->input('uu_nomor_tahun') ?? '-';
        $perihalUu = $request->input('perihal_uu') ?? '-';
        $tkp = $request->input('tkp') ?? ($accident->accident_road ?? $accident->accident_address ?? '-');
        $hariKejadian = $request->input('hari_kejadian') ?? ($accident->accident_date ? $this->getNamaHari($accident->accident_date) : '-');
        $tglKejadian = $this->formatDateIndo($request->input('tanggal_kejadian') ?? ($accident->accident_date ?? date('Y-m-d')));
        $tglBerlakuSampai = $request->input('tanggal_berlaku_sampai') ? $this->formatDateIndo($request->input('tanggal_berlaku_sampai')) : '-';

        $template->setValue('nama_tersangka', $namaTersangka);
        $template->setValue('tindak_pidana', $tindakPidana);
        $template->setValue('pasal_pelanggaran', $pasalPelanggaran);
        $template->setValue('uu_nomor_tahun', $uuNomorTahun);
        $template->setValue('perihal_uu', $perihalUu);
        $template->setValue('tkp', $tkp);
        $template->setValue('hari_kejadian', $hariKejadian);
        $template->setValue('tanggal_kejadian', $tglKejadian);
        $template->setValue('tanggal_berlaku_sampai', $tglBerlakuSampai);

        // 6. Footer / Tanda Tangan
        $tempatDikeluarkan = $request->input('tempat_dikeluarkan') ?? ($accident->polres->name ?? '-');
        $tglDikeluarkan = $this->formatDateIndo($request->input('tanggal_dikeluarkan') ?? ($request->input('tanggal_surat') ?? date('Y-m-d')));

        $penerimaNama = strtoupper($request->input('penerima_perintah_nama') ?? $p1Nama);
        $penerimaPangkatNrp = $request->input('penerima_perintah_pangkat_nrp') ?? "{$p1Pangkat} NRP {$p1Nrp}";
        if ($request->filled('penerima_id')) {
            $penerima = Officer::with(['rank', 'position'])->selectFullName()->find($request->input('penerima_id'));
            if ($penerima) {
                $penerimaNama = strtoupper($penerima->full_name ?? ($penerima->first_name . ' ' . $penerima->last_name));
                $penerimaPangkatNrp = ($penerima->rank->name ?? '') . ' NRP ' . ($penerima->register_number ?? $penerima->nrp ?? '-');
            }
        }

        $atasanSebutan = strtoupper($request->input('atasan_penyidik_sebutan') ?? 'ATASAN PENYIDIK');
        $atasanNama = strtoupper($request->input('atasan_penyidik_nama') ?? '-');
        $atasanPangkatNrp = $request->input('atasan_penyidik_pangkat_nrp') ?? '-';
        if ($request->filled('atasan_id')) {
            $atasan = Officer::with(['rank', 'position'])->selectFullName()->find($request->input('atasan_id'));
            if ($atasan) {
                $atasanNama = strtoupper($atasan->full_name ?? ($atasan->first_name . ' ' . $atasan->last_name));
                $atasanPangkatNrp = ($atasan->rank->name ?? '') . ' NRP ' . ($atasan->register_number ?? $atasan->nrp ?? '-');
                if ($atasan->position) {
                    $atasanSebutan = strtoupper($atasan->position->name ?? '') . ' SELAKU ATASAN PENYIDIK';
                }
            }
        }

        $template->setValue('tempat_dikeluarkan', $tempatDikeluarkan);
        $template->setValue('tanggal_dikeluarkan', $tglDikeluarkan);
        $template->setValue('penerima_perintah_nama', $penerimaNama);
        $template->setValue('penerima_perintah_pangkat_nrp', $penerimaPangkatNrp);
        $template->setValue('atasan_penyidik_sebutan', $atasanSebutan);
        $template->setValue('atasan_penyidik_nama', $atasanNama);
        $template->setValue('atasan_penyidik_pangkat_nrp', $atasanPangkatNrp);

        $safeFileName = 'Surat_Perintah_Penggeledahan_Badan_Pakaian_' . preg_replace('/[^A-Za-z0-9_]/', '_', $accident->accident_serial_number ?? $accidentId) . '_' . date('Ymd_His') . '.docx';
        $tempPath = storage_path('app/' . $safeFileName);
        $template->saveAs($tempPath);

        return response()->download($tempPath, $safeFileName)->deleteFileAfterSend(true);
    }
}
