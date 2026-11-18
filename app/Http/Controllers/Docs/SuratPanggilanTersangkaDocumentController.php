<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPanggilanTersangkaDocument\SuratPanggilanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuratPanggilanTersangkaDocumentController extends Controller
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
            : 'Pasal 310 ayat (4) UU No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        $suratKetetapanTentangPenetapanTersangkaDocuments = collect();
        try {
            $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect.job'])
                ->where('accident_id', $accidentId)
                ->orderByDesc('created_at')
                ->get();
        } catch (\Exception $e) {}

        $sketRow = $suratKetetapanTentangPenetapanTersangkaDocuments->first();

        $suspects = collect();
        try {
            $suspects = Suspect::with(['job'])->where('accident_id', $accidentId)->orderBy('name')->get();
        } catch (\Exception $e) {}

        $suspect = $suspects->first();
        if (!$suspect && $sketRow && $sketRow->suspect && $sketRow->suspect->isNotEmpty()) {
            $suspect = $sketRow->suspect->first();
        }

        $namaTersangka = $suspect ? $suspect->name : ($accident->driver_name ?? 'Tersangka');
        $pekerjaanTersangka = ($suspect && $suspect->job) ? $suspect->job->name : ($suspect->job_name ?? 'Swasta');
        $alamatTersangka = $suspect ? $suspect->address : ($accident->driver_address ?? '-');

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
            'accident', 'accidentId', 'suratPerintahPenyidikanDocuments', 'sprindik',
            'suratKetetapanTentangPenetapanTersangkaDocuments', 'sketRow',
            'suspects', 'suspect', 'namaTersangka', 'pekerjaanTersangka', 'alamatTersangka',
            'pasalText', 'authorizedSignatories', 'availableOfficers'
        );
    }

    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);
        return view('docs.surat-panggilan-tersangka-document.create', $data);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        SuratPanggilanTersangkaDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->input('nomor_surat'),
                'document_date' => $request->filled('tanggal_surat') ? Carbon::parse($request->input('tanggal_surat'))->format('Y-m-d') : null,
                'data' => $payload,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Panggilan Tersangka berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = SuratPanggilanTersangkaDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.surat-panggilan-tersangka-document.edit', array_merge($data, compact('savedDoc', 'savedData')));
    }

    public function update(Request $request)
    {
        $accidentId = $request->input('accident_id');
        Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        SuratPanggilanTersangkaDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->input('nomor_surat'),
                'document_date' => $request->filled('tanggal_surat') ? Carbon::parse($request->input('tanggal_surat'))->format('Y-m-d') : null,
                'data' => $payload,
                'created_by' => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Panggilan Tersangka berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        SuratPanggilanTersangkaDocument::where('accident_id', $accidentId)->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Panggilan Tersangka berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        Settings::setOutputEscapingEnabled(true);
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        // If accessed via GET / direct link without form input, merge saved doc
        if (!$request->filled('nomor_surat')) {
            $savedDoc = SuratPanggilanTersangkaDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/surat_panggilan_tersangka.docx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template word surat panggilan tersangka tidak ditemukan.');
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

        $defaultAlamat = $polres ? ($polres->address . ', ' . $polres->polres_district) : 'Alamat Kesatuan';

        $kopstuk = $request->input('kopstuk', $defaultKopstuk);
        $alamatKesatuan = $request->input('alamat_kesatuan', $defaultAlamat);
        $nomorSurat = $request->input('nomor_surat', 'S.Pgl/Tsk.1/' . ($accident->id ?? '001') . '/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas');

        // Dasar
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

        // Sket Penetapan Tersangka
        $sketRow = null;
        try {
            if ($request->filled('surat_ketetapan_tersangka_id')) {
                $sketRow = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect.job'])
                    ->find($request->input('surat_ketetapan_tersangka_id'));
            }
            if (!$sketRow) {
                $sketRow = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect.job'])
                    ->where('accident_id', $accidentId)
                    ->latest()
                    ->first();
            }
        } catch (\Exception $e) {}

        $suspect = null;
        try {
            if ($request->filled('suspect_id')) {
                $suspect = Suspect::with(['job'])->where('accident_id', $accidentId)->find($request->input('suspect_id'));
            }
            if (!$suspect && $sketRow && $sketRow->suspect && $sketRow->suspect->isNotEmpty()) {
                $suspect = $sketRow->suspect->first();
            }
            if (!$suspect) {
                $suspect = Suspect::with(['job'])->where('accident_id', $accidentId)->first();
            }
        } catch (\Exception $e) {}

        $noSketTersangka = $sketRow->document_number ?? $request->input('no_sket_tersangka', 'S.Tap/01/' . date('m') . '/RES.0.0./' . date('Y') . '/Lantas');
        $tanggalSketTersangka = $this->formatDateIndo($sketRow && $sketRow->document_date ? $sketRow->document_date : $request->input('tanggal_sket_tersangka', date('d-m-Y')));
        $namaSketTersangka = $suspect ? $suspect->name : $request->input('nama_sket_tersangka', $accident->driver_name ?? '-');

        // Tersangka
        $namaTersangka = $request->input('nama_tersangka', $suspect ? $suspect->name : ($accident->driver_name ?? '-'));
        $pekerjaanTersangka = $request->input('pekerjaan_tersangka', ($suspect && $suspect->job) ? $suspect->job->name : ($suspect->occupation ?? 'Swasta'));
        $alamatTersangka = $request->input('alamat_tersangka', $suspect ? $suspect->address : ($accident->driver_address ?? '-'));

        // Pemeriksaan (Penyidik)
        $namaPenyidik = $request->input('nama_penyidik', 'Penyidik / Tim Penyidik');
        if ($request->filled('investigator_id')) {
            $inv = Officer::with(['rank', 'position'])->selectFullName()->find($request->investigator_id);
            if ($inv) {
                $namaPenyidik = ($inv->rank->name ?? '') . ' ' . ($inv->full_name ?? ($inv->first_name . ' ' . $inv->last_name));
            }
        }
        $tempatPemeriksaan = $request->input('tempat_pemeriksaan') ?: '-';
        
        $hariPemeriksaan = $request->input('hari_pemeriksaan') ?: '-';
        $tanggalPemeriksaan = $this->formatDateIndo($request->input('tanggal_pemeriksaan', Carbon::now()->addDays(3)->format('d-m-Y')));
        $jamPemeriksaan = $request->input('jam_pemeriksaan') ?: '-';

        // Perkara
        $jenisTindakPidana = $request->input('jenis_tindak_pidana', 'Kecelakaan Lalu Lintas');
        $pasalPelanggaran = $request->input('pasal_pelanggaran');
        if (empty($pasalPelanggaran)) {
            if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                $parts = [];
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    if ($law->flag == 'MAIN') {
                        $parts[] = trim(($law->constitution_chapter ?? '') . ' ' . ($law->crimeConstitution->name ?? ''));
                    } elseif ($law->flag == 'ADDITIONAL' || !empty($law->constitution)) {
                        $parts[] = trim($law->constitution);
                    }
                }
                $pasalPelanggaran = implode(', ', array_filter($parts));
            }
            if (empty($pasalPelanggaran)) {
                $pasalPelanggaran = 'Pasal 310 ayat (4) UU No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
            }
        }
        $tkp = $request->input('tkp', $accident->road_name ?? '-');
        $waktuKejadian = $request->input('waktu_kejadian', $tanggalLp);
        $dokumenBawaan = $request->input('dokumen_bawaan', 'Identitas Diri (KTP/SIM asli), STNK kendaraan, dan dokumen pendukung lainnya');

        // Kontak Penyidik
        $kontakPenyidikNama = $request->input('kontak_penyidik_nama', '-');
        $kontakPenyidikPangkat = $request->input('kontak_penyidik_pangkat', '-');
        $kontakPenyidikHp = $request->input('kontak_penyidik_hp', '-');
        if ($request->filled('liaison_officer_id')) {
            $liaison = Officer::with(['rank', 'position'])->selectFullName()->find($request->liaison_officer_id);
            if ($liaison) {
                $kontakPenyidikNama = $liaison->full_name ?? ($liaison->first_name . ' ' . $liaison->last_name);
                $kontakPenyidikPangkat = ($liaison->rank->name ?? '') . ' / ' . $liaison->register_number;
                if (!$request->filled('kontak_penyidik_hp') && !empty($liaison->phone_number)) {
                    $kontakPenyidikHp = $liaison->phone_number;
                }
            }
        }

        // Footer / Penandatangan
        $kotaSurat = $request->input('kota_surat', $polres->polres_district ?? 'Kota');
        $tanggalSurat = $this->formatDateIndo($request->input('tanggal_surat', Carbon::now()->format('d-m-Y')));
        $jabatanPenandatangan = $request->input('jabatan_penandatangan', 'KASAT LANTAS');
        $namaPenandatangan = $request->input('nama_penandatangan', '-');
        $pangkatNrpPenandatangan = $request->input('pangkat_nrp_penandatangan', 'PANGKAT / NRP. -');

        if ($request->filled('signatory_id')) {
            $sig = Officer::with(['rank', 'position'])->selectFullName()->find($request->signatory_id);
            if ($sig) {
                $jabatanPenandatangan = $sig->position->name ?? 'KASAT LANTAS';
                $namaPenandatangan = $sig->full_name ?? ($sig->first_name . ' ' . $sig->last_name);
                $pangkatNrpPenandatangan = ($sig->rank->name ?? 'PANGKAT') . ' NRP ' . $sig->register_number;
            }
        }

        // Mapping
        $template->setValue('kopstuk', $kopstuk);
        $template->setValue('alamat_kesatuan', $alamatKesatuan);
        $template->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $template->setValue('resorPoliceFullName', $resorPoliceFullName);
        $template->setValue('resorPoliceAddress', $resorPoliceAddress);
        $template->setValue('nomor_surat', $nomorSurat);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tanggalLp);
        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tanggalSprindik);
        $template->setValue('no_sket_tersangka', $noSketTersangka);
        $template->setValue('tanggal_sket_tersangka', $tanggalSketTersangka);
        $template->setValue('nama_sket_tersangka', $namaSketTersangka);
        $template->setValue('nama_tersangka', $namaTersangka);
        $template->setValue('pekerjaan_tersangka', $pekerjaanTersangka);
        $template->setValue('alamat_tersangka', $alamatTersangka);
        $template->setValue('nama_penyidik', $namaPenyidik);
        $template->setValue('tempat_pemeriksaan', $tempatPemeriksaan);
        $template->setValue('hari_pemeriksaan', $hariPemeriksaan);
        $template->setValue('tanggal_pemeriksaan', $tanggalPemeriksaan);
        $template->setValue('jam_pemeriksaan', $jamPemeriksaan);
        $template->setValue('jenis_tindak_pidana', $jenisTindakPidana);
        $template->setValue('pasal_pelanggaran', $pasalPelanggaran);
        $template->setValue('tkp', $tkp);
        $template->setValue('waktu_kejadian', $waktuKejadian);
        $template->setValue('dokumen_bawaan', $dokumenBawaan);
        $template->setValue('kontak_penyidik_nama', $kontakPenyidikNama);
        $template->setValue('kontak_penyidik_pangkat', $kontakPenyidikPangkat);
        $template->setValue('kontak_penyidik_hp', $kontakPenyidikHp);
        $template->setValue('kota_surat', $kotaSurat);
        $template->setValue('tanggal_surat', $tanggalSurat);
        $template->setValue('jabatan_penandatangan', $jabatanPenandatangan);
        $template->setValue('nama_penandatangan', $namaPenandatangan);
        $template->setValue('pangkat_nrp_penandatangan', $pangkatNrpPenandatangan);

        $tempFileName = 'Surat_Panggilan_Tersangka_' . time() . '.docx';
        $tempFilePath = storage_path('app/public/' . $tempFileName);
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }
        $template->saveAs($tempFilePath);

        $downloadName = 'Surat_Panggilan_Tersangka_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $accident->no_lp ?? 'Perkara') . '.docx';
        return response()->download($tempFilePath, $downloadName)->deleteFileAfterSend(true);
    }
}
