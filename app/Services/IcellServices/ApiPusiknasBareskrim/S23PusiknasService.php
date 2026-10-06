<?php

namespace App\Services\IcellServices\ApiPusiknasBareskrim;

use Carbon\Carbon;
use App\Models\Officer;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;

class S23PusiknasService
{
    /**
     * Membentuk struktur payload JSON Dokumen S-23 (SPRIN BANTAR)
     * Sesuai spesifikasi resmi SPPT-TI (Kode Proses: DAT-5 / Swagger sppt_ti.yaml).
     *
     * @param mixed $document Model dokumen S-23 (SuratPerintahPembantaranPenahananDocument)
     * @return array
     */
    public function buildPayload($document): array
    {
        $accident = $document->accident;

        // 1. Ekstraksi Pejabat Penandatangan (Signatory)
        $pejabatPenandatangan = $this->buildSignatories($document);

        // 2. Kode Satker Penerbit SPDP dengan fallback ke Polres induk perkara
        $satkerPenerbit = $document->kode_satker_penerbit_spdp;
        if (empty($satkerPenerbit) && $accident && $accident->polres) {
            $satkerPenerbit = $accident->polres->satker_code ?: $accident->polres->code;
        }

        // 3. Susun Identitas Dokumen Sesuai SPPT-TI DAT-5
        $nomorS17 = $document->nomor_surat_perintah_penahanan;
        if (empty($nomorS17) && $document->suratPerintahPenahananDocument) {
            $nomorS17 = $document->suratPerintahPenahananDocument->nomor ?? $document->suratPerintahPenahananDocument->document_number ?? '';
        }

        $identitasDokumen = [
            'nomor' => (string) ($document->nomor_surat ?? ''),
            'tanggal' => $document->tanggal_surat ? Carbon::parse($document->tanggal_surat)->format('Y-m-d') : null,
            'nomor_spdp' => (string) ($document->nomor_spdp ?? ''),
            'tanggal_spdp' => $document->tanggal_spdp ? Carbon::parse($document->tanggal_spdp)->format('Y-m-d') : null,
            'kode_satker_penerbit_spdp' => (string) ($satkerPenerbit ?? ''),
            'nomor_s17' => (string) ($nomorS17 ?? ''),
        ];

        // 4. Susun Konten Dokumen Sesuai SPPT-TI DAT-5
        $kontenDokumen = [
            'nama_dokter' => $document->nama_dokter ?: null,
            'tempat_rawat_inap' => $document->tempat_rawat_inap ?: null,
            'tanggal_mulai_rawat_inap' => $document->tanggal_mulai_rawat_inap
                ? Carbon::parse($document->tanggal_mulai_rawat_inap)->format('Y-m-d')
                : ($document->tanggal_surat ? Carbon::parse($document->tanggal_surat)->format('Y-m-d') : null),
            'pejabat_penandatangan' => $pejabatPenandatangan,
        ];

        // 5. Kembalikan Format Final Selaras Swagger SPPT-TI DAT-5
        return [
            'kode_jenis_dokumen' => 's23',
            'identitas_dokumen' => $identitasDokumen,
            'konten_dokumen' => $kontenDokumen,
        ];
    }

    /**
     * Membentuk array data pejabat penandatangan (minItems: 1, schema: aparat_negara)
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
            $firstTitle = $signatory->first_title ? trim($signatory->first_title) . ' ' : '';
            $firstName = trim($signatory->first_name ?? '');
            $lastName = $signatory->last_name ? ' ' . trim($signatory->last_name) : '';
            $lastTitle = $signatory->last_title ? ', ' . trim($signatory->last_title) : '';
            $fullName = trim($firstTitle . $firstName . $lastName . $lastTitle);

            if (empty($fullName)) {
                $fullName = trim("{$signatory->first_name} {$signatory->last_name}");
            }

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
            if ((empty($position) || empty($rank) || empty($fullName)) && !empty($nrp)) {
                $masterOfficer = Officer::with(['rank', 'position'])
                    ->where('identifier_number', $nrp)
                    ->first();
                if ($masterOfficer) {
                    $fullName = $fullName ?: ($masterOfficer->full_name ?: trim("{$masterOfficer->first_name} {$masterOfficer->last_name}"));
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
