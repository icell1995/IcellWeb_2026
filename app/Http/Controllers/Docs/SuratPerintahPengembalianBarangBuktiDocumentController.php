<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Doc\SuratPerintahPengembalianBarangBuktiDocument\SuratPerintahPengembalianBarangBuktiDocument;
use App\Models\Doc\SuratPerintahPenyitaanDocument\SuratPerintahPenyitaanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuratPerintahPengembalianBarangBuktiDocumentController extends Controller
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

        // Cari Surat Perintah Penyitaan jika ada
        $spSita = null;
        try {
            $spSita = SuratPerintahPenyitaanDocument::where('accident_id', $accidentId)->orderByDesc('created_at')->first();
        } catch (\Exception $e) {}

        // Cari Sprint & Skep Penghentian Penyidikan (SP3) jika ada
        $sprintSp3 = null;
        $skepSp3 = null;
        try {
            $sprintSp3 = SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accidentId)->orderByDesc('created_at')->first();
            $skepSp3 = SuratKetetapanPenghentianPenyidikanDocument::where('accident_id', $accidentId)->orderByDesc('created_at')->first();
        } catch (\Exception $e) {}

        // Suspect
        $suspects = collect();
        try {
            $suspects = Suspect::with(['job', 'religion', 'gender', 'country'])->where('accident_id', $accidentId)->orderBy('name')->get();
        } catch (\Exception $e) {}

        $suspect = $suspects->first();

        // Helper untuk ekstrak string bersih dari objek relasi, array, atau JSON string
        $extractName = function ($val, $default = '') {
            if (empty($val)) return $default;
            if (is_object($val)) {
                return $val->name ?? $val->full_name ?? $default;
            }
            if (is_array($val)) {
                return $val['name'] ?? $val['full_name'] ?? $default;
            }
            if (is_string($val)) {
                $trimmed = trim($val);
                if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
                    $decoded = json_decode($trimmed, true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['name'])) {
                        return $decoded['name'];
                    }
                }
                return $val;
            }
            return $default;
        };

        // Data Penerima Barang Bukti (default dari suspect riil jika ada, atau kosong)
        $penerimaNama = $suspect ? $suspect->name : ($accident->driver_name ?? '');
        $penerimaNik = $suspect ? ($suspect->identity_number ?? $suspect->nik ?? '') : '';
        
        $penerimaWn = 'WNI';
        if ($suspect && !empty($suspect->nationality)) {
            $penerimaWn = $suspect->nationality;
        } elseif ($suspect && $suspect->country) {
            $cName = $extractName($suspect->country, 'INDONESIA');
            $penerimaWn = (strtoupper($cName) === 'INDONESIA') ? 'WNI' : 'WNA (' . $cName . ')';
        }

        $penerimaGender = 'Laki-laki';
        if ($suspect) {
            $rawGender = $extractName($suspect->gender ?? $suspect->gender_short_name, '');
            if (!empty($rawGender)) {
                $penerimaGender = (stripos($rawGender, 'perempuan') !== false || stripos($rawGender, 'wanita') !== false) ? 'Perempuan' : 'Laki-laki';
            }
        }

        $pob = $suspect ? ($suspect->birth_place ?? $suspect->place_of_birth ?? '') : '';
        $rawDob = $suspect ? ($suspect->birth_date ?? $suspect->date_of_birth ?? '') : '';
        $dob = $rawDob ? Carbon::parse($rawDob)->format('d-m-Y') : '';
        if ($pob && $dob) {
            $penerimaTtl = $pob . ', ' . $dob;
        } elseif ($pob) {
            $penerimaTtl = $pob;
        } elseif ($dob) {
            $penerimaTtl = $dob;
        } else {
            $penerimaTtl = '';
        }

        $penerimaPekerjaan = $suspect ? $extractName($suspect->job ?? $suspect->occupation, '') : '';
        $penerimaAgama = $suspect ? $extractName($suspect->religion ?? $suspect->religion_short_name, '') : '';
        $penerimaAlamat = $suspect ? ($suspect->address ?? '') : ($accident->accident_address ?? '');

        // Barang Bukti
        $bbPoints = [];
        try {
            $bbList = DB::table('daftar_barang_bukti')->where('accident_id', $accidentId)->get();
            if ($bbList->isNotEmpty()) {
                $huruf = 'a';
                foreach ($bbList as $bb) {
                    $itemDesc = $bb->nama_barang ?? $bb->item_name ?? $bb->description ?? '';
                    if ($itemDesc) {
                        $bbPoints[] = $huruf . '.   ' . $itemDesc . ';';
                        $huruf = chr(ord($huruf) + 1);
                    }
                }
            }
        } catch (\Exception $e) {}

        $daftarBarangBuktiPoinDefault = !empty($bbPoints) ? implode("\n", $bbPoints) : '';

        // Pejabat & Petugas
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

        // Default Data Surat (Dikosongkan agar pengguna mengisi data riil)
        $defaultDocNumber = '';
        $defaultValidUntil = '';

        return compact(
            'accident', 'accidentId', 'suratPerintahPenyidikanDocuments', 'sprindik', 'pasalText',
            'spSita', 'sprintSp3', 'skepSp3', 'suspects', 'suspect',
            'penerimaNama', 'penerimaNik', 'penerimaWn', 'penerimaGender', 'penerimaTtl',
            'penerimaPekerjaan', 'penerimaAgama', 'penerimaAlamat', 'daftarBarangBuktiPoinDefault',
            'authorizedSignatories', 'availableOfficers', 'defaultDocNumber', 'defaultValidUntil'
        );
    }

    public function create(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);
        return view('docs.surat-perintah-pengembalian-barang-bukti-document.create', $data);
    }

    public function store(Request $request)
    {
        $accidentId = $request->input('accident_id');
        $accident = Accident::findOrFail($accidentId);

        $payload = $request->except(['_token', '_method']);

        $docNumber = $request->input('nomor_surat_perintah') ?? '-';
        $dateInput = $request->input('tanggal_surat') ?? $request->input('tanggal_dikeluarkan');
        $docDate = $dateInput ? Carbon::parse($dateInput)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        SuratPerintahPengembalianBarangBuktiDocument::updateOrCreate(
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
            ->with('success', 'Dokumen Surat Perintah Pengembalian Barang Bukti (S-26.1.5) berhasil disimpan.');
    }

    public function edit(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $data = $this->getMasterData($accidentId);

        $savedDoc = SuratPerintahPengembalianBarangBuktiDocument::where('accident_id', $accidentId)->first();
        $savedData = $savedDoc ? ($savedDoc->data ?? []) : [];

        return view('docs.surat-perintah-pengembalian-barang-bukti-document.edit', array_merge($data, compact('savedDoc', 'savedData')));
    }

    public function update(Request $request)
    {
        return $this->store($request);
    }

    public function destroy(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $doc = SuratPerintahPengembalianBarangBuktiDocument::where('accident_id', $accidentId)->first();
        if ($doc) {
            $doc->delete();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Perintah Pengembalian Barang Bukti berhasil dihapus.');
    }

    public function downloadWord(Request $request)
    {
        $accidentId = $request->query('accident_id') ?? $request->input('accident_id');
        $accident = Accident::with(['polres.polda'])->findOrFail($accidentId);

        if (!$request->filled('nomor_surat_perintah')) {
            $savedDoc = SuratPerintahPengembalianBarangBuktiDocument::where('accident_id', $accidentId)->first();
            if ($savedDoc && !empty($savedDoc->data)) {
                $request->merge($savedDoc->data);
            }
        }

        $templatePath = public_path('word-template/surat_perintah_pengembalian_bb.docx');
        if (!file_exists($templatePath)) {
            abort(404, 'Template dokumen Surat Perintah Pengembalian Barang Bukti tidak ditemukan.');
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

        // Sprindik
        $noSprindik = $request->input('no_sprindik') ?? '-';
        $tglSprindik = $request->input('tanggal_sprindik') ? $this->formatDateIndo($request->input('tanggal_sprindik')) : '-';

        // SP Sita
        $noSpSita = $request->input('no_sp_sita') ?? '-';
        $tglSpSita = $request->input('tanggal_sp_sita') ? $this->formatDateIndo($request->input('tanggal_sp_sita')) : '-';

        // Penetapan Pengadilan
        $namaPengadilan = $request->input('nama_pengadilan') ?? '-';
        $noPenetapan = $request->input('no_penetapan_pengadilan') ?? '-';
        $tglPenetapan = $request->input('tanggal_penetapan_pengadilan') ? $this->formatDateIndo($request->input('tanggal_penetapan_pengadilan')) : '-';

        // Skep & Sprint SP3
        $noSkepSp3 = $request->input('no_skep_sp3') ?? '';
        $tglSkepSp3 = $request->input('tanggal_skep_sp3') ? $this->formatDateIndo($request->input('tanggal_skep_sp3')) : '-';
        $dasarSkepSp3 = !empty($noSkepSp3) ? ('Surat Ketetapan tentang Penghentian Penyidikan Nomor: ' . $noSkepSp3 . ' tanggal ' . $tglSkepSp3) : '-';

        $noSprintSp3 = $request->input('no_sprint_sp3') ?? '';
        $tglSprintSp3 = $request->input('tanggal_sprint_sp3') ? $this->formatDateIndo($request->input('tanggal_sprint_sp3')) : '-';
        $dasarSprintSp3 = !empty($noSprintSp3) ? ('Surat Perintah Penghentian Penyidikan Nomor: ' . $noSprintSp3 . ' tanggal ' . $tglSprintSp3) : '-';

        $template->setValue('dasar_pasal_uu', $dasarPasalUu);
        $template->setValue('no_lp', $noLp);
        $template->setValue('tanggal_lp', $tglLp);
        $template->setValue('no_sprindik', $noSprindik);
        $template->setValue('tanggal_sprindik', $tglSprindik);
        $template->setValue('no_sp_sita', $noSpSita);
        $template->setValue('tanggal_sp_sita', $tglSpSita);
        $template->setValue('nama_pengadilan', $namaPengadilan);
        $template->setValue('no_penetapan_pengadilan', $noPenetapan);
        $template->setValue('tanggal_penetapan_pengadilan', $tglPenetapan);
        $template->setValue('dasar_skep_sp3', $dasarSkepSp3);
        $template->setValue('dasar_sprint_sp3', $dasarSprintSp3);

        // 4. Petugas yang Diperintahkan (Kepada)
        $p1 = Officer::with(['rank', 'position'])->find($request->input('petugas_1_id'));
        $p1Nama = $p1 ? $p1->full_name : ($request->input('petugas_1_nama') ?? '-');
        $p1Pangkat = $p1 ? ($p1->rank->name ?? '') : ($request->input('petugas_1_pangkat') ?? '-');
        $p1Nrp = $p1 ? ($p1->register_number ?? $p1->nrp ?? '-') : ($request->input('petugas_1_nrp') ?? '-');
        $p1Jabatan = $p1 ? ($p1->position->name ?? 'Penyidik') : ($request->input('petugas_1_jabatan') ?? 'Penyidik');

        $p2 = Officer::with(['rank', 'position'])->find($request->input('petugas_2_id'));
        $p2Nama = $p2 ? $p2->full_name : ($request->input('petugas_2_nama') ?? '-');
        $p2Pangkat = $p2 ? ($p2->rank->name ?? '') : ($request->input('petugas_2_pangkat') ?? '-');
        $p2Nrp = $p2 ? ($p2->register_number ?? $p2->nrp ?? '-') : ($request->input('petugas_2_nrp') ?? '-');
        $p2Jabatan = $p2 ? ($p2->position->name ?? 'Penyidik Pembantu') : ($request->input('petugas_2_jabatan') ?? 'Penyidik Pembantu');

        $p3 = Officer::with(['rank', 'position'])->find($request->input('petugas_3_id'));
        $p3Nama = $p3 ? $p3->full_name : ($request->input('petugas_3_nama') ?? '-');
        $p3Pangkat = $p3 ? ($p3->rank->name ?? '') : ($request->input('petugas_3_pangkat') ?? '-');
        $p3Nrp = $p3 ? ($p3->register_number ?? $p3->nrp ?? '-') : ($request->input('petugas_3_nrp') ?? '-');
        $p3Jabatan = $p3 ? ($p3->position->name ?? 'Penyidik Pembantu') : ($request->input('petugas_3_jabatan') ?? '-');

        $template->setValue('petugas_1_nama', $p1Nama);
        $template->setValue('petugas_1_pangkat', $p1Pangkat);
        $template->setValue('petugas_1_nrp', $p1Nrp);
        $template->setValue('petugas_1_jabatan', $p1Jabatan);

        $template->setValue('petugas_2_nama', $p2Nama);
        $template->setValue('petugas_2_pangkat', $p2Pangkat);
        $template->setValue('petugas_2_nrp', $p2Nrp);
        $template->setValue('petugas_2_jabatan', $p2Jabatan);

        $template->setValue('petugas_3_nama', $p3Nama);
        $template->setValue('petugas_3_pangkat', $p3Pangkat);
        $template->setValue('petugas_3_nrp', $p3Nrp);
        $template->setValue('petugas_3_jabatan', $p3Jabatan);

        // 5. Untuk (Poin 1 s/d 4)
        $tglPenyitaan = $this->formatDateIndo($request->input('tanggal_penyitaan') ?? ($spSita ? ($spSita->document_date ?? date('Y-m-d')) : date('Y-m-d')));
        $daftarBbPoin = $request->input('daftar_barang_bukti_poin') ?? '-';
        $tglBerlakuSampai = $request->input('tanggal_berlaku_sampai') ? $this->formatDateIndo($request->input('tanggal_berlaku_sampai')) : '-';

        $cleanString = function ($val, $default = '-') {
            if (empty($val)) return $default;
            if (is_string($val)) {
                $trimmed = trim($val);
                if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
                    $decoded = json_decode($trimmed, true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($decoded['name'])) {
                        return $decoded['name'];
                    }
                }
                return $val;
            }
            return $default;
        };

        $penerimaBbNama = $request->input('penerima_bb_nama') ?? ($accident->driver_name ?? '-');
        $penerimaBbNik = $request->input('penerima_bb_nik') ?? '-';
        $penerimaBbWn = $request->input('penerima_bb_kewarganegaraan') ?? 'WNI';
        $penerimaBbGender = $request->input('penerima_bb_jenis_kelamin') ?? '-';
        $penerimaBbTtl = $request->input('penerima_bb_ttl') ?? '-';
        $penerimaBbPekerjaan = $cleanString($request->input('penerima_bb_pekerjaan'), '-');
        $penerimaBbAgama = $cleanString($request->input('penerima_bb_agama'), '-');
        $penerimaBbAlamat = $request->input('penerima_bb_alamat') ?? ($accident->accident_address ?? '-');

        $template->setValue('tanggal_penyitaan', $tglPenyitaan);
        $template->setValue('daftar_barang_bukti_poin', $daftarBbPoin);
        $template->setValue('penerima_bb_nama', $penerimaBbNama);
        $template->setValue('penerima_bb_nik', $penerimaBbNik);
        $template->setValue('penerima_bb_kewarganegaraan', $penerimaBbWn);
        $template->setValue('penerima_bb_jenis_kelamin', $penerimaBbGender);
        $template->setValue('penerima_bb_ttl', $penerimaBbTtl);
        $template->setValue('penerima_bb_pekerjaan', $penerimaBbPekerjaan);
        $template->setValue('penerima_bb_agama', $penerimaBbAgama);
        $template->setValue('penerima_bb_alamat', $penerimaBbAlamat);
        $template->setValue('tanggal_berlaku_sampai', $tglBerlakuSampai);

        // 6. Tanda Tangan
        $penerima = Officer::with(['rank'])->find($request->input('penerima_id')) ?? $p1;
        $penerimaNama = $penerima ? $penerima->full_name : ($request->input('penerima_perintah_nama') ?? $p1Nama);
        $penerimaPangkatNrp = $penerima ? (($penerima->rank->name ?? '') . ' NRP ' . ($penerima->register_number ?? $penerima->nrp ?? '')) : ($p1Pangkat . ' NRP ' . $p1Nrp);

        $template->setValue('penerima_perintah_nama', $penerimaNama);
        $template->setValue('penerima_perintah_pangkat_nrp', $penerimaPangkatNrp);

        $atasan = Officer::with(['rank', 'position'])->find($request->input('atasan_id'));
        $atasanNama = $atasan ? $atasan->full_name : ($request->input('atasan_penyidik_nama') ?? '-');
        $atasanPangkatNrp = $atasan ? (($atasan->rank->name ?? '') . ' NRP ' . ($atasan->register_number ?? $atasan->nrp ?? '')) : ($request->input('atasan_penyidik_pangkat_nrp') ?? '-');
        $atasanSebutan = $atasan ? strtoupper($atasan->position->name ?? 'ATASAN PENYIDIK') : ($request->input('atasan_penyidik_sebutan') ?? 'ATASAN PENYIDIK');

        $template->setValue('atasan_penyidik_nama', $atasanNama);
        $template->setValue('atasan_penyidik_pangkat_nrp', $atasanPangkatNrp);
        $template->setValue('atasan_penyidik_sebutan', $atasanSebutan);

        $tempatDikeluarkan = $request->input('tempat_dikeluarkan') ?? ($accident->polres->name ?? 'Tempat');
        $tglDikeluarkan = $this->formatDateIndo($request->input('tanggal_dikeluarkan') ?? ($request->input('tanggal_surat') ?? date('Y-m-d')));

        $template->setValue('tempat_dikeluarkan', $tempatDikeluarkan);
        $template->setValue('tanggal_dikeluarkan', $tglDikeluarkan);

        $tempFileName = 'Surat_Perintah_Pengembalian_Barang_Bukti_' . time() . '.docx';
        $tempFilePath = storage_path('app/public/' . $tempFileName);
        $template->saveAs($tempFilePath);

        return response()->download($tempFilePath)->deleteFileAfterSend(true);
    }
}

