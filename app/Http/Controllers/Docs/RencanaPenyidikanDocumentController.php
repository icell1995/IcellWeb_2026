<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\RencanaPenyidikanDocument\RencanaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RencanaPenyidikanDocumentController extends Controller
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

    private function getMasterData($accidentId)
    {
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

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
            : 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        $suspects = collect();
        try {
            $suspects = Suspect::with(['job'])->where('accident_id', $accidentId)->orderBy('name')->get();
        } catch (\Exception $e) {}
        $suspect = $suspects->first();
        $namaTersangka = $suspect ? $suspect->name : ($accident->driver_name ?? 'Tersangka');

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

        return compact(
            'accident', 'accidentId', 'suratPerintahPenyidikanDocuments', 'sprindik', 'suspects', 'suspect', 'namaTersangka', 'pasalText', 'authorizedSignatories', 'availableOfficers'
        );
    }

    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);
        return view('docs.rencana-penyidikan-document.create', $data);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        RencanaPenyidikanDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->input('nomor_surat'),
                'document_date' => $request->filled('tanggal_surat') ? Carbon::parse($request->input('tanggal_surat'))->format('Y-m-d') : null,
                'data' => $payload,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Rencana Penyidikan berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = RencanaPenyidikanDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.rencana-penyidikan-document.edit', array_merge($data, compact('savedDoc', 'savedData')));
    }

    public function update(Request $request)
    {
        $accidentId = $request->input('accident_id');
        Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        RencanaPenyidikanDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->input('nomor_surat'),
                'document_date' => $request->filled('tanggal_surat') ? Carbon::parse($request->input('tanggal_surat'))->format('Y-m-d') : null,
                'data' => $payload,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Rencana Penyidikan berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        RencanaPenyidikanDocument::where('accident_id', $accidentId)->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Rencana Penyidikan berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        Settings::setOutputEscapingEnabled(true);
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        // If accessed via GET / direct link without form input, merge saved doc
        if (!$request->filled('nomor_surat')) {
            $savedDoc = RencanaPenyidikanDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/rencana_penyidikan.docx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template word rencana penyidikan tidak ditemukan.');
        }

        $template = new TemplateProcessor($templatePath);

        // Header / Kopstuk
        $polres = $accident->polres ?? null;
        $polda = $polres->polda ?? null;

        $daerahPoliceFullName = $polda ? strtoupper($polda->name ?? $polda->full_name) : '-';
        $daerahPoliceFullName = trim(preg_replace('/^(POLDA|KEPOLISIAN DAERAH)\s+/i', '', $daerahPoliceFullName));

        $resorPoliceFullName = (in_array($polres->id ?? '', ['1114'])) 
            ? 'DIREKTORAT LALU LINTAS' 
            : 'RESOR ' . strtoupper(trim(preg_replace('/^(RESOR|POLRES|KEPOLISIAN RESOR)\s+/i', '', ($polres->name ?? $polres->full_name ?? '-'))));

        $addressParts = array_filter([$polres->address ?? null, $polres->polres_zipcode ?? null]);
        $resorPoliceAddress = !empty($addressParts) ? ucwords(strtolower(implode(', ', $addressParts))) : ($polres->polres_district ?? '-');

        $defaultKopstuk = "KEPOLISIAN NEGARA REPUBLIK INDONESIA\n";
        if ($polres && $polres->polda) {
            $defaultKopstuk .= strtoupper($polres->polda->name ?? '') . "\n";
        }
        if ($polres) {
            $defaultKopstuk .= strtoupper($polres->name ?? $polres->full_name ?? '') . "\n";
        }
        $defaultKopstuk .= "SATUAN LALU LINTAS";

        $kopstuk = $request->input('kopstuk', $defaultKopstuk);
        $nomorSurat = $request->input('nomor_surat', 'B/' . ($accident->id ?? '001') . '/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas');

        // I. Dasar
        $noLp = $request->input('no_lp', $accident->no_lp ?? '-');
        $tanggalLp = $this->formatDateIndo($request->input('tanggal_lp', $accident && $accident->accident_date ? $accident->accident_date : date('d-m-Y')));
        
        $sprindik = null;
        try {
            if ($request->filled('surat_perintah_penyidikan_id')) {
                $sprindik = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentLaws.crimeConstitution'])
                    ->find($request->input('surat_perintah_penyidikan_id'));
            }
            if (!$sprindik) {
                $sprindik = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentLaws.crimeConstitution'])
                    ->where('accident_id', $accidentId)
                    ->latest()
                    ->first();
            }
        } catch (\Exception $e) {}

        $noSprindik = $sprindik->document_number ?? $request->input('no_sprindik', 'SP.Sidik/01/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas');
        $tanggalSprindik = $this->formatDateIndo($sprindik && $sprindik->document_date ? $sprindik->document_date : $request->input('tanggal_sprindik', date('d-m-Y')));

        // II. Perkara
        $jenisTindakPidana = $request->input('jenis_tindak_pidana', 'Kecelakaan Lalu Lintas');
        $pasalDilanggar = $request->input('pasal_dilanggar');
        if (empty($pasalDilanggar)) {
            if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                $parts = [];
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    if ($law->flag == 'MAIN') {
                        $parts[] = trim(($law->constitution_chapter ?? '') . ' ' . ($law->crimeConstitution->name ?? ''));
                    } elseif ($law->flag == 'ADDITIONAL' || !empty($law->constitution)) {
                        $parts[] = trim($law->constitution);
                    }
                }
                $pasalDilanggar = implode(', ', array_filter($parts));
            }
            if (empty($pasalDilanggar)) {
                $pasalDilanggar = 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
            }
        }

        $korban = $request->input('korban', '-');
        
        $suspect = null;
        try {
            if ($request->filled('suspect_id')) {
                $suspect = Suspect::where('accident_id', $accidentId)->find($request->input('suspect_id'));
            }
            if (!$suspect) {
                $suspect = Suspect::where('accident_id', $accidentId)->first();
            }
        } catch (\Exception $e) {}
        $tersangka = $suspect ? $suspect->name : $request->input('tersangka', $accident->driver_name ?? '-');

        // III. Hasil Pemeriksaan Sementara
        $keteranganSaksi = $request->input('keterangan_saksi') ?: '-';
        $keteranganTersangka = $request->input('keterangan_tersangka') ?: '-';
        $barangBukti = $request->input('barang_bukti') ?: '-';

        // IV. Kesimpulan Sementara
        $kesimpulan1 = $request->input('kesimpulan_1') ?: '-';
        $kesimpulan2 = $request->input('kesimpulan_2') ?: '-';

        // V. Rencana Penyidikan dan Saran
        $rencanaA = $request->input('rencana_a') ?: '-';
        $rencanaB = $request->input('rencana_b') ?: '-';
        $saranA = $request->input('saran_a') ?: '-';
        $saranB = $request->input('saran_b') ?: '-';

        // Footer / Signatures
        $dikeluarkanDi = $request->input('dikeluarkan_di', $polres->polres_district ?? 'Kota');
        $tanggalSurat = $this->formatDateIndo($request->input('tanggal_surat', Carbon::now()->format('d-m-Y')));
        
        $namaPenerima = $request->input('nama_penerima', '-');
        $pangkatNrpPenerima = $request->input('pangkat_nrp_penerima', 'PANGKAT / NRP. -');
        if ($request->filled('penerima_id')) {
            $penerima = Officer::with(['rank', 'position'])->selectFullName()->find($request->penerima_id);
            if ($penerima) {
                $namaPenerima = $penerima->full_name ?? ($penerima->first_name . ' ' . $penerima->last_name);
                $pangkatNrpPenerima = ($penerima->rank->name ?? 'PANGKAT') . ' NRP. ' . $penerima->register_number;
            }
        }

        $namaAtasan = $request->input('nama_atasan', '-');
        $pangkatNrpAtasan = $request->input('pangkat_nrp_atasan', 'PANGKAT / NRP. -');
        if ($request->filled('atasan_id')) {
            $atasan = Officer::with(['rank', 'position'])->selectFullName()->find($request->atasan_id);
            if ($atasan) {
                $namaAtasan = $atasan->full_name ?? ($atasan->first_name . ' ' . $atasan->last_name);
                $pangkatNrpAtasan = ($atasan->rank->name ?? 'PANGKAT') . ' NRP. ' . $atasan->register_number;
            }
        }

        // Mapping nilai ke template
        $template->setValue('kopstuk', $kopstuk);
        $template->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $template->setValue('resorPoliceFullName', $resorPoliceFullName);
        $template->setValue('resorPoliceAddress', $resorPoliceAddress);
        $template->setValue('nomor_surat', $nomorSurat);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tanggalLp);
        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tanggalSprindik);
        $template->setValue('jenis_tindak_pidana', $jenisTindakPidana);
        $template->setValue('pasal_dilanggar', $pasalDilanggar);
        $template->setValue('korban', $korban);
        $template->setValue('tersangka', $tersangka);
        $template->setValue('keterangan_saksi', $keteranganSaksi);
        $template->setValue('keterangan_tersangka', $keteranganTersangka);
        $template->setValue('barang_bukti', $barangBukti);
        $template->setValue('kesimpulan_1', $kesimpulan1);
        $template->setValue('kesimpulan_2', $kesimpulan2);
        $template->setValue('rencana_a', $rencanaA);
        $template->setValue('rencana_b', $rencanaB);
        $template->setValue('saran_a', $saranA);
        $template->setValue('saran_b', $saranB);
        $template->setValue('dikeluarkan_di', $dikeluarkanDi);
        $template->setValue('tanggal_surat', $tanggalSurat);
        $template->setValue('nama_penerima', $namaPenerima);
        $template->setValue('pangkat_nrp_penerima', $pangkatNrpPenerima);
        $template->setValue('nama_atasan', $namaAtasan);
        $template->setValue('pangkat_nrp_atasan', $pangkatNrpAtasan);

        // Dynamic Table Giat Sidik
        $giatList = $request->input('giat', []);
        if (is_array($giatList) && count($giatList) > 0) {
            $template->cloneRow('t_no', count($giatList));
            foreach ($giatList as $idx => $g) {
                $num = $idx + 1;
                $template->setValue("t_no#$num", $num);
                $template->setValue("t_waktu_tempat#$num", $g['waktu_tempat'] ?? '-');
                $template->setValue("t_giat#$num", $g['giat'] ?? '-');
                $template->setValue("t_personil#$num", $g['personil'] ?? '-');
                $template->setValue("t_alat#$num", $g['alat'] ?? '-');
                $template->setValue("t_dana#$num", $g['dana'] ?? '-');
                $template->setValue("t_ket#$num", $g['ket'] ?? '-');
            }
        } else {
            $template->setValue('t_no', '1');
            $template->setValue('t_waktu_tempat', $request->input('t_waktu_tempat', 'Kantor Satlantas'));
            $template->setValue('t_giat', $request->input('t_giat', 'Pemeriksaan saksi dan olah TKP lanjutan'));
            $template->setValue('t_personil', $request->input('t_personil', '2 Personil'));
            $template->setValue('t_alat', $request->input('t_alat', 'Kendaraan Dinas dan Blanko'));
            $template->setValue('t_dana', $request->input('t_dana', 'Duk Ops'));
            $template->setValue('t_ket', $request->input('t_ket', '-'));
        }

        $tempFileName = 'Rencana_Penyidikan_' . time() . '.docx';
        $tempFilePath = storage_path('app/public/' . $tempFileName);
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }
        $template->saveAs($tempFilePath);

        $downloadName = 'Rencana_Penyidikan_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $accident->no_lp ?? 'Perkara') . '.docx';
        return response()->download($tempFilePath, $downloadName)->deleteFileAfterSend(true);
    }
}
