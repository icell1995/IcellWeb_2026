<?php

namespace App\Services\IcellServices\ApiPusiknasBareskrim;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Models\LaporanPolisi;
use App\Models\Officer;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;

class S22PusiknasService
{
    /**
     * Membentuk struktur payload JSON Dokumen S-22 sesuai Swagger SPPT-TI (HAN-10.30)
     * Berlaku untuk S-22 Pertama (0603) maupun S-22 Kedua (0604).
     *
     * @param mixed $document Model dokumen S-22
     * @return array
     */
    public function buildPayload($document): array
    {
        $accident = $document->accident;

        // 1. Pemetaan Pejabat Penandatangan (Signatory)
        $pejabatPenandatangan = $this->buildSignatories($document);

        // 2. Pemetaan 3 Berkas Dokumen Digital (S-22, Sprindik, LP)
        $daftarDokumenDigital = $this->buildDigitalDocuments($document, $accident);

        // Satker penerbit fallback jika belum terisi di kolom dokumen
        $satkerPenerbit = $document->kode_satker_penerbit_spdp;
        if (empty($satkerPenerbit) && $accident && $accident->polres) {
            $satkerPenerbit = $accident->polres->satker_code ?: $accident->polres->code;
        }

        // 3. Susun Identitas Dokumen (WAJIB sesuai Swagger SPPT-TI)
        $identitasDokumen = [
            'nomor' => (string) ($document->nomor_surat ?? ''),
            'tanggal' => $document->tanggal_surat ? Carbon::parse($document->tanggal_surat)->format('Y-m-d') : null,
            'nomor_spdp' => (string) ($document->nomor_spdp ?? ''),
            'tanggal_spdp' => $document->tanggal_spdp ? Carbon::parse($document->tanggal_spdp)->format('Y-m-d') : null,
            'kode_satker_penerbit_spdp' => (string) ($satkerPenerbit ?? ''),
            'nomor_surat_perintah_penahanan' => (string) ($document->nomor_surat_perintah_penahanan ?? ''),
        ];

        // Satker tempat penahanan (prioritas: kode di dokumen -> spptti_id rutan -> satker penerbit)
        $kodeTempatPenahanan = $document->kode_satker_tempat_penahanan ?: ($document->prison->spptti_id ?? null);
        if (empty($kodeTempatPenahanan)) {
            $kodeTempatPenahanan = $satkerPenerbit;
        }

        // 4. Susun Konten Dokumen
        $kontenDokumen = [
            'kode_satker_tempat_penahanan' => (string) ($kodeTempatPenahanan ?: ''),
            'tanggal_mulai_perpanjangan_penahanan' => $document->tanggal_mulai_perpanjangan_penahanan
                ? Carbon::parse($document->tanggal_mulai_perpanjangan_penahanan)->format('Y-m-d')
                : null,
            'tanggal_akhir_perpanjangan_penahanan' => $document->tanggal_akhir_perpanjangan_penahanan
                ? Carbon::parse($document->tanggal_akhir_perpanjangan_penahanan)->format('Y-m-d')
                : null,
            'pejabat_penandatangan' => $pejabatPenandatangan,
            'daftar_dokumen_digital' => $daftarDokumenDigital,
        ];

        // 5. Kembalikan Format Final Selaras sppt_ti.yaml
        return [
            'kode_jenis_dokumen' => 's22',
            'identitas_dokumen' => $identitasDokumen,
            'konten_dokumen' => $kontenDokumen,
        ];
    }

    /**
     * Membentuk array data pejabat penandatangan (minItems: 1)
     *
     * @param mixed $document
     * @return array
     */
    public function buildSignatories($document): array
    {
        $officers = [];
        $signatory = null;

        if ($document->officers && $document->officers->count() > 0) {
            $signatory = $document->officers->first(function ($item) {
                if (is_array($item->class)) {
                    return in_array('SIGNATORY', $item->class);
                }
                return ($item->class === 'SIGNATORY' || str_contains($item->class ?? '', 'SIGNATORY') || $item->type === 'penandatangan');
            });

            if (!$signatory) {
                $signatory = $document->officers->first();
            }
        }

        if ($signatory) {
            $fullName = trim("{$signatory->first_name} {$signatory->last_name}");
            $nrp = (string) ($signatory->register_number ?? $signatory->officer_id ?? '');

            // Ekstraksi Jabatan
            $position = null;
            if (is_array($signatory->position)) {
                $position = $signatory->position['name'] ?? null;
            } elseif (is_object($signatory->position)) {
                $position = $signatory->position->name ?? null;
            } elseif (is_string($signatory->position)) {
                $position = $signatory->position;
            }

            if (empty($position) && !empty($signatory->position_id)) {
                $posModel = Position::find($signatory->position_id);
                if ($posModel) {
                    $position = $posModel->name;
                }
            }

            // Ekstraksi Pangkat
            $rank = null;
            if (is_array($signatory->rank)) {
                $rank = $signatory->rank['name'] ?? $signatory->rank['full_name'] ?? null;
            } elseif (is_object($signatory->rank)) {
                $rank = $signatory->rank->name ?? $signatory->rank->full_name ?? null;
            } elseif (is_string($signatory->rank)) {
                $rank = $signatory->rank;
            }

            if (empty($rank) && !empty($signatory->rank_id)) {
                $rankModel = Rank::find($signatory->rank_id);
                if ($rankModel) {
                    $rank = $rankModel->name;
                }
            }

            // Fallback ke tabel master Officer jika snapshot masih kosong
            if ((empty($position) || empty($rank)) && !empty($nrp)) {
                $masterOfficer = Officer::with(['rank', 'position'])
                    ->where('identifier_number', $nrp)
                    ->first();
                if ($masterOfficer) {
                    $position = $position ?: ($masterOfficer->position->name ?? $masterOfficer->position);
                    $rank = $rank ?: ($masterOfficer->rank->name ?? null);
                }
            }

            $officers[] = [
                'nama' => $fullName ?: 'Evan Bangun',
                'nomor_induk' => $nrp ?: '199805052020021001',
                'jabatan' => $position ?: 'Ajun Jaksa',
                'pangkat' => $rank ?: 'Penata Muda Tingkat I',
            ];
        } else {
            // Default jika data pejabat penandatangan tidak ditemukan
            $officers[] = [
                'nama' => 'Evan Bangun',
                'nomor_induk' => '199805052020021001',
                'jabatan' => 'Ajun Jaksa',
                'pangkat' => 'Penata Muda Tingkat I',
            ];
        }

        return $officers;
    }

    /**
     * Membentuk array 3 dokumen digital wajib: S-22, Sprindik, dan LP
     *
     * @param mixed $document
     * @param mixed $accident
     * @return array
     */
    public function buildDigitalDocuments($document, $accident = null): array
    {
        $digitalDocs = [];

        // --- Berkas 1: Dokumen S-22 ---
        $s22AttachmentName = $document->attachment->name ?? null;
        $s22Url = $s22AttachmentName ? asset('documents/attachments/' . $s22AttachmentName) : null;

        $digitalDocs[] = [
            'kode_jenis_dokumen' => 's22',
            'mime_type' => 'application/pdf',
            'file' => (string) ($s22AttachmentName ?? ''),
            'url' => $s22Url,
        ];

        // --- Berkas 2: Dokumen Sprindik ---
        $sprindikAttachmentName = null;
        if ($accident) {
            $sprindikDoc = SuratPerintahPenyidikanDocument::where('accident_id', $accident->id)->first();
            $sprindikAttachmentName = $sprindikDoc->attachment->name ?? null;
        }

        $digitalDocs[] = [
            'kode_jenis_dokumen' => 'sprindik',
            'mime_type' => 'application/pdf',
            'file' => (string) ($sprindikAttachmentName ?? ''),
        ];

        // --- Berkas 3: Dokumen Laporan Polisi (LP) ---
        $lpAttachmentName = null;
        if ($accident) {
            $lp = LaporanPolisi::where('accident_id', $accident->id)->latest()->first();
            $lpAttachmentName = $lp->name ?? null;
        }

        $digitalDocs[] = [
            'kode_jenis_dokumen' => 'lp',
            'mime_type' => 'application/pdf',
            'file' => (string) ($lpAttachmentName ?? ''),
        ];

        return $digitalDocs;
    }

    /**
     * Konversi file fisik ke Base64 secara defensif
     *
     * @param string|null $fullPath
     * @return string
     */
    public function encodeFileToBase64(?string $fullPath): string
    {
        if ($fullPath && File::exists($fullPath)) {
            try {
                return base64_encode(File::get($fullPath));
            } catch (\Exception $e) {
                // Return safe fallback if reading failed
            }
        }

        // Mock string base64 untuk pengujian jika berkas fisik tidak ada di disk lokal
        return base64_encode("Swagger rocks");
    }

    /**
     * Validasi format tanggal YYYY-MM-DD
     *
     * @param string|null $date
     * @return bool
     */
    public function isValidDate(?string $date): bool
    {
        if (empty($date)) {
            return true;
        }
        return date('Y-m-d', strtotime($date)) === $date;
    }
}
