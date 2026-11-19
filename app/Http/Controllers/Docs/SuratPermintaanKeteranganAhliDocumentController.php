<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPermintaanKeteranganAhliDocument\SuratPermintaanKeteranganAhliDocument;
use App\Models\Officer;
use App\Models\Suspect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPermintaanKeteranganAhliDocumentController extends Controller
{
    private function formatDateIndo($dateStr)
    {
        if (empty($dateStr)) return '-';
        try {
            $months = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            $d = Carbon::parse($dateStr);
            return $d->day . ' ' . $months[$d->month] . ' ' . $d->year;
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
            'accident', 'accidentId', 'suratPerintahPenyidikanDocuments', 'sprindik',
            'suspects', 'suspect', 'namaTersangka', 'pasalText',
            'authorizedSignatories', 'availableOfficers'
        );
    }

    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);
        return view('docs.surat-permintaan-keterangan-ahli-document.create', $data);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        SuratPermintaanKeteranganAhliDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->input('nomor_surat'),
                'document_date'   => $request->filled('tanggal_surat') ? Carbon::parse($request->input('tanggal_surat'))->format('Y-m-d') : null,
                'data'            => $payload,
                'created_by'      => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permintaan Keterangan Ahli berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = SuratPermintaanKeteranganAhliDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.surat-permintaan-keterangan-ahli-document.edit', array_merge($data, compact('savedDoc', 'savedData')));
    }

    public function update(Request $request)
    {
        $accidentId = $request->input('accident_id');
        Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        SuratPermintaanKeteranganAhliDocument::updateOrCreate(
            ['accident_id' => $accidentId],
            [
                'document_number' => $request->input('nomor_surat'),
                'document_date'   => $request->filled('tanggal_surat') ? Carbon::parse($request->input('tanggal_surat'))->format('Y-m-d') : null,
                'data'            => $payload,
                'created_by'      => auth()->user()->name ?? 'System',
            ]
        );

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permintaan Keterangan Ahli berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        SuratPermintaanKeteranganAhliDocument::where('accident_id', $accidentId)->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permintaan Keterangan Ahli berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        Settings::setOutputEscapingEnabled(true);
        $accidentId = $request->input('accident_id') ?? $request->query('accident_id');
        $accident = Accident::with(['polres.polda'])->find($accidentId);

        if (!$request->filled('nomor_surat')) {
            $savedDoc = SuratPermintaanKeteranganAhliDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/surat_permintaan_keterangan_ahli.docx');
        if (!file_exists($templatePath)) {
            $templatePath = public_path('word-template/surat_panggilan_saksi_ahli.docx');
        }
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template word Surat Permintaan Keterangan Ahli tidak ditemukan.');
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

        // Nomor & Info Surat
        $nomorSurat = $request->input('nomor_surat', 'B/01/I/RES.0.0./' . date('Y') . '/Lantas');
        $klasifikasi = $request->input('klasifikasi', 'Biasa');
        $lampiran = $request->input('lampiran', '-');
        $hal = $request->input('hal', 'bantuan permintaan keterangan ahli.');

        $kotaSurat = $request->input('kota_surat', $polres->polres_district ?? 'Kota');
        $tanggalSurat = $this->formatDateIndo($request->input('tanggal_surat', Carbon::now()->format('d-m-Y')));

        $tujuanSurat = $request->input('tujuan_surat', 'KEPALA PUSAT PELAPORAN DAN ANALISIS TRANSAKSI KEUANGAN');
        $kotaTujuan = $request->input('kota_tujuan', $polres->polres_district ?? 'Kota/Kab');

        // Rujukan
        $dasarUuLain = $request->input('dasar_uu_lain', 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan');
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

        // Poin 2 Perkara
        $sebutanPimpinan = $request->input('sebutan_pimpinan', 'Ka/Dekan/Saudara*)');
        $namaSatkerPenyidik = $request->input('nama_satker_penyidik', 'Satlantas ' . ($polres->name ?? 'Polres'));
        $jenisTindakPidana = $request->input('jenis_tindak_pidana', 'Kecelakaan Lalu Lintas');
        $tkp = $request->input('tkp', $accident->accident_road ?? $accident->accident_address ?? '-');
        $tanggalKejadian = $this->formatDateIndo($request->input('tanggal_kejadian', $accident->accident_date ?? date('d-m-Y')));
        $waktuKejadian = $request->input('waktu_kejadian', $accident->accident_time ?? 'sekira jam 10.00 WIB');

        $namaTersangka = $request->input('nama_tersangka', '-');
        if ($request->filled('suspect_id')) {
            $sus = Suspect::find($request->input('suspect_id'));
            if ($sus) $namaTersangka = $sus->name;
        }

        $pasalPelanggaran = $request->input('pasal_pelanggaran', 'Pasal 310 ayat (4) UU No. 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan');
        $rawPasalKuhpBaru = $request->input('pasal_kuhp_baru');
        $pasalKuhpBaru = (!empty($rawPasalKuhpBaru) && trim($rawPasalKuhpBaru) !== '-') ? '(atau ' . trim($rawPasalKuhpBaru) . ')' : '';

        // Poin 3 Permintaan Ahli & Jadwal
        $namaInstansiAhli = $request->input('nama_instansi_ahli') ?: $tujuanSurat;
        $bidangAhli = $request->input('bidang_ahli') ?: '-';
        $hariPemeriksaan = $request->input('hari_pemeriksaan') ?: '-';
        $tanggalPemeriksaan = $this->formatDateIndo($request->input('tanggal_pemeriksaan', Carbon::now()->addDays(3)->format('d-m-Y')));
        $jamPemeriksaan = $request->input('jam_pemeriksaan') ?: '-';
        $tempatPemeriksaan = $request->input('tempat_pemeriksaan') ?: '-';

        // Penyidik Pemeriksa
        $namaPenyidik = $request->input('nama_penyidik', 'Penyidik Pembantu');
        if ($request->filled('officer_id')) {
            $off = Officer::with(['rank'])->selectFullName()->find($request->officer_id);
            if ($off) {
                $namaPenyidik = ($off->rank->name ?? '') . ' ' . ($off->full_name ?? ($off->first_name . ' ' . $off->last_name));
            }
        }

        // Kontak Penyidik & Penyidik Pembantu
        $kontakPenyidikNama = $request->input('kontak_penyidik_nama', '-');
        $kontakPenyidikPangkat = $request->input('kontak_penyidik_pangkat', '-');
        $kontakPenyidikHp = $request->input('kontak_penyidik_hp', '-');

        if ($request->filled('investigator_officer_id')) {
            $inv = Officer::with(['rank'])->selectFullName()->find($request->investigator_officer_id);
            if ($inv) {
                $kontakPenyidikNama = $inv->full_name ?? ($inv->first_name . ' ' . $inv->last_name);
                $kontakPenyidikPangkat = $inv->rank->name ?? '-';
                if (empty($kontakPenyidikHp) || $kontakPenyidikHp == '-') {
                    $kontakPenyidikHp = $inv->phone_number;
                }
            }
        }

        $kontakPembantuNama = $request->input('kontak_pembantu_nama', '-');
        $kontakPembantuPangkat = $request->input('kontak_pembantu_pangkat', '-');
        $kontakPembantuHp = $request->input('kontak_pembantu_hp', '-');

        if ($request->filled('helper_officer_id')) {
            $hlp = Officer::with(['rank'])->selectFullName()->find($request->helper_officer_id);
            if ($hlp) {
                $kontakPembantuNama = $hlp->full_name ?? ($hlp->first_name . ' ' . $hlp->last_name);
                $kontakPembantuPangkat = $hlp->rank->name ?? '-';
                if (empty($kontakPembantuHp) || $kontakPembantuHp == '-') {
                    $kontakPembantuHp = $hlp->phone_number;
                }
            }
        }

        // Penandatangan
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

        // Tembusan
        $tembusan1 = $request->input('tembusan_1', 'Kapolres ' . ($polres->name ?? '') . ' (sebagai laporan)');
        $tembusan2 = $request->input('tembusan_2', 'Pengawas Penyidikan (Wassidik)');

        // Replace template placeholders
        $template->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $template->setValue('resorPoliceFullName', $resorPoliceFullName);
        $template->setValue('resorPoliceAddress', $resorPoliceAddress);

        $template->setValue('nomor_surat', $nomorSurat);
        $template->setValue('klasifikasi', $klasifikasi);
        $template->setValue('lampiran', $lampiran);
        $template->setValue('hal', $hal);

        $template->setValue('kota_surat', $kotaSurat);
        $template->setValue('tanggal_surat', $tanggalSurat);
        $template->setValue('tujuan_surat', $tujuanSurat);
        $template->setValue('kota_tujuan', $kotaTujuan);

        $template->setValue('dasar_uu_lain', $dasarUuLain);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tanggalLp);
        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tanggalSprindik);

        $template->setValue('sebutan_pimpinan', $sebutanPimpinan);
        $template->setValue('nama_satker_penyidik', $namaSatkerPenyidik);
        $template->setValue('jenis_tindak_pidana', $jenisTindakPidana);
        $template->setValue('tkp', $tkp);
        $template->setValue('tanggal_kejadian', $tanggalKejadian);
        $template->setValue('waktu_kejadian', $waktuKejadian);
        $template->setValue('nama_tersangka', $namaTersangka);
        $template->setValue('pasal_pelanggaran', $pasalPelanggaran);
        $template->setValue('pasal_kuhp_baru', $pasalKuhpBaru);

        $template->setValue('nama_instansi_ahli', $namaInstansiAhli);
        $template->setValue('bidang_ahli', $bidangAhli);
        $template->setValue('hari_pemeriksaan', $hariPemeriksaan);
        $template->setValue('tanggal_pemeriksaan', $tanggalPemeriksaan);
        $template->setValue('jam_pemeriksaan', $jamPemeriksaan);
        $template->setValue('tempat_pemeriksaan', $tempatPemeriksaan);
        $template->setValue('nama_penyidik', $namaPenyidik);

        $template->setValue('kontak_penyidik_nama', $kontakPenyidikNama);
        $template->setValue('kontak_penyidik_pangkat', $kontakPenyidikPangkat);
        $template->setValue('kontak_penyidik_hp', $kontakPenyidikHp);

        $template->setValue('kontak_pembantu_nama', $kontakPembantuNama);
        $template->setValue('kontak_pembantu_pangkat', $kontakPembantuPangkat);
        $template->setValue('kontak_pembantu_hp', $kontakPembantuHp);

        $template->setValue('tembusan_1', $tembusan1);
        $template->setValue('tembusan_2', $tembusan2);

        $template->setValue('jabatan_penandatangan', $jabatanPenandatangan);
        $template->setValue('nama_penandatangan', $namaPenandatangan);
        $template->setValue('pangkat_nrp_penandatangan', $pangkatNrpPenandatangan);

        // Header / backward compatibility aliases
        $template->setValue('documentNumber', $nomorSurat);
        $template->setValue('documentDate', $tanggalSurat);
        $template->setValue('documentLocation', $kotaSurat);
        $template->setValue('courtName', $tujuanSurat);
        $template->setValue('courtLocation', $kotaTujuan);

        $cleanNomor = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nomorSurat);
        $fileName = 'Surat_Permintaan_Keterangan_Ahli_' . $cleanNomor . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);

        $template->saveAs($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
