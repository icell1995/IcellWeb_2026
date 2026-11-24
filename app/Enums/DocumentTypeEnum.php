<?php

namespace App\Enums;

enum DocumentTypeEnum: string
{
    // STAGE 01: PENYELIDIKAN
    case SURAT_PERINTAH_PENYELIDIKAN = '0101';

    // STAGE 02: PENYIDIKAN
    case SURAT_PERINTAH_PENYIDIKAN = '0201';
    case SPDP_PUSIKNAS = '0204';
    case SURAT_PERINTAH_PENGHENTIAN_PENYIDIKAN = '0205';
    case SURAT_KETETAPAN_PENGHENTIAN_PENYIDIKAN = '0206';
    case SURAT_PEMBERITAHUAN_UPAYA_DIVERSI = '0211';
    case SURAT_PERMOHONAN_PENETAPAN_DIVERSI = '0212';
    case SURAT_KETETAPAN_PENETAPAN_TERSANGKA = '0215';
    case SURAT_PEMBERITAHUAN_PENGHENTIAN_PENYIDIKAN = '0216';

    // STAGE 04: PENGGELEDAHAN
    case SURAT_PERMINTAAN_PENGGELEDAHAN = '0404';
    case SURAT_LAPORAN_PERSETUJUAN_PENGGELEDAHAN = '0405';

    // STAGE 05: PENYITAAN
    case SURAT_PERMINTAAN_IZIN_PENYITAAN = '0504';
    case SURAT_LAPORAN_PERSETUJUAN_PENYITAAN = '0505';

    // STAGE 06: PENAHANAN
    case SURAT_PERINTAH_PENAHANAN = '0601';
    case SURAT_PERINTAH_PENANGGUHAN_PENAHANAN = '0603';
    case SURAT_PERINTAH_PENCABUTAN_PENANGGUHAN_PENAHANAN = '0604';
    case SURAT_PERMOHONAN_PERPANJANGAN_PENAHANAN_KEJAKSAAN = '0605';
    case SURAT_PERMINTAAN_PERPANJANGAN_PENAHANAN_LANJUTAN = '0606';
    case SURAT_PERMINTAAN_PERPANJANGAN_PENAHANAN_LANJUTAN_KEDUA = '0607';
    case SURAT_PERINTAH_PEMBANTARAN_PENAHANAN = '0608';
    case BERITA_ACARA_PENAHANAN = '0609';

    // STAGE 07: KELENGKAPAN PENYIDIKAN
    case SURAT_PERINTAH_TUGAS = '0702';
    case LAPORAN_HASIL_GELAR_PERKARA = '0706';
    case SP2HP = '0709';

    // STAGE 08: PENYERAHAN BERKAS PERKARA
    case TAHAP_1 = '0806';
    case TAHAP_2 = '0807';

    public function label(): string
    {
        return match ($this) {
            self::SURAT_PERINTAH_PENYELIDIKAN => 'SURAT PERINTAH PENYELIDIKAN',
            self::SURAT_PERINTAH_PENYIDIKAN => 'SURAT PERINTAH PENYIDIKAN',
            self::SPDP_PUSIKNAS => 'SURAT PEMBERITAHUAN DIMULAINYA PENYIDIKAN',
            self::SURAT_PERINTAH_PENGHENTIAN_PENYIDIKAN => 'SURAT PERINTAH PENGHENTIAN PENYIDIKAN',
            self::SURAT_KETETAPAN_PENGHENTIAN_PENYIDIKAN => 'SURAT KETETAPAN PENGHENTIAN PENYIDIKAN',
            self::SURAT_PEMBERITAHUAN_UPAYA_DIVERSI => 'SURAT PEMBERITAHUAN UPAYA DIVERSI',
            self::SURAT_PERMOHONAN_PENETAPAN_DIVERSI => 'SURAT PERMOHONAN PENETAPAN KESEPAKATAN DIVERSI',
            self::SURAT_KETETAPAN_PENETAPAN_TERSANGKA => 'SURAT KETETAPAN TENTANG PENETAPAN TERSANGKA',
            self::SURAT_PEMBERITAHUAN_PENGHENTIAN_PENYIDIKAN => 'SURAT PEMBERITAHUAN PENGHENTIAN PENYIDIKAN (SP3)',
            self::SURAT_PERMINTAAN_PENGGELEDAHAN => 'SURAT PERMINTAAN IZIN PENGGELEDAHAN',
            self::SURAT_LAPORAN_PERSETUJUAN_PENGGELEDAHAN => 'SURAT LAPORAN GUNA MEMPEROLEH PERSETUJUAN PENGGELEDAHAN KEPADA KETUA PENGADILAN NEGERI',
            self::SURAT_PERMINTAAN_IZIN_PENYITAAN => 'SURAT PERMINTAAN IZIN PENYITAAN',
            self::SURAT_LAPORAN_PERSETUJUAN_PENYITAAN => 'SURAT LAPORAN PERSETUJUAN PENYITAAN',
            self::SURAT_PERINTAH_PENAHANAN => 'SURAT PERINTAH PENAHANAN',
            self::SURAT_PERINTAH_PENANGGUHAN_PENAHANAN => 'SURAT PERINTAH PENANGGUHAN PENAHANAN',
            self::SURAT_PERINTAH_PENCABUTAN_PENANGGUHAN_PENAHANAN => 'SURAT PERINTAH PENCABUTAN PENANGGUHAN PENAHANAN',
            self::SURAT_PERMOHONAN_PERPANJANGAN_PENAHANAN_KEJAKSAAN => 'SURAT PERMOHONAN PERPANJANGAN PENAHANAN KEJAKSAAN',
            self::SURAT_PERMINTAAN_PERPANJANGAN_PENAHANAN_LANJUTAN => 'SURAT PERMINTAAN PERPANJANGAN PENAHANAN KE KETUA PENGADILAN NEGERI (PERTAMA 30 HARI)',
            self::SURAT_PERMINTAAN_PERPANJANGAN_PENAHANAN_LANJUTAN_KEDUA => 'SURAT PERMINTAAN PERPANJANGAN PENAHANAN KE KETUA PENGADILAN NEGERI (KEDUA 30 HARI)',
            self::SURAT_PERINTAH_PEMBANTARAN_PENAHANAN => 'SURAT PERINTAH PEMBANTARAN PENAHANAN',
            self::BERITA_ACARA_PENAHANAN => 'BERITA ACARA PENAHANAN',
            self::SURAT_PERINTAH_TUGAS => 'SURAT PERINTAH TUGAS',
            self::LAPORAN_HASIL_GELAR_PERKARA => 'LAPORAN HASIL GELAR PERKARA',
            self::SP2HP => 'SURAT PEMBERITAHUAN PERKEMBANGAN HASIL PENYIDIKAN (SP2HP)',
            self::TAHAP_1 => 'SURAT PENGIRIMAN BERKAS PERKARA TAHAP I',
            self::TAHAP_2 => 'SURAT PENGIRIMAN BERKAS PERKARA TAHAP II',
        };
    }

    public function baseRoute(): string
    {
        return match ($this) {
            self::SURAT_PERINTAH_PENYELIDIKAN => 'doc.surat-perintah-penyelidikan-document',
            self::SURAT_PERINTAH_PENYIDIKAN => 'doc.surat-perintah-penyidikan-document',
            self::SPDP_PUSIKNAS => 'doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document',
            self::SURAT_PERINTAH_PENGHENTIAN_PENYIDIKAN => 'doc.surat-perintah-penghentian-penyidikan-document',
            self::SURAT_KETETAPAN_PENGHENTIAN_PENYIDIKAN => 'doc.surat-ketetapan-penghentian-penyidikan-document',
            self::SURAT_PEMBERITAHUAN_UPAYA_DIVERSI => 'doc.surat-pemberitahuan-upaya-diversi-document',
            self::SURAT_PERMOHONAN_PENETAPAN_DIVERSI => 'doc.surat-permohonan-penetapan-diversi-document',
            self::SURAT_KETETAPAN_PENETAPAN_TERSANGKA => 'doc.surat-ketetapan-tentang-penetapan-tersangka-document',
            self::SURAT_PEMBERITAHUAN_PENGHENTIAN_PENYIDIKAN => 'doc.surat-pemberitahuan-penghentian-penyidikan-document',
            self::SURAT_PERMINTAAN_PENGGELEDAHAN => 'doc.surat-permintaan-penggeledahan-document',
            self::SURAT_LAPORAN_PERSETUJUAN_PENGGELEDAHAN => 'doc.surat-laporan-guna-memperoleh-persetujuan-penggeledahan-document',
            self::SURAT_PERMINTAAN_IZIN_PENYITAAN => 'doc.surat-permintaan-izin-penyitaan-document',
            self::SURAT_LAPORAN_PERSETUJUAN_PENYITAAN => 'doc.surat-laporan-persetujuan-penyitaan-document',
            self::SURAT_PERINTAH_PENAHANAN => 'doc.surat-perintah-penahanan-document',
            self::SURAT_PERINTAH_PENANGGUHAN_PENAHANAN => 'doc.surat-perintah-penangguhan-penahanan-document',
            self::SURAT_PERINTAH_PENCABUTAN_PENANGGUHAN_PENAHANAN => 'doc.surat-perintah-pencabutan-penangguhan-penahanan-document',
            self::SURAT_PERMOHONAN_PERPANJANGAN_PENAHANAN_KEJAKSAAN => 'doc.surat-permohonan-perpanjangan-penahanan-kejaksaan-document',
            self::SURAT_PERMINTAAN_PERPANJANGAN_PENAHANAN_LANJUTAN => 'doc.surat-permintaan-perpanjangan-penahanan-lanjutan-document',
            self::SURAT_PERMINTAAN_PERPANJANGAN_PENAHANAN_LANJUTAN_KEDUA => 'doc.surat-permintaan-perpanjangan-penahanan-lanjutan-kedua-document',
            self::SURAT_PERINTAH_PEMBANTARAN_PENAHANAN => 'doc.surat-perintah-pembantaran-penahanan-document',
            self::BERITA_ACARA_PENAHANAN => 'doc.berita-acara-penahanan-document',
            self::SURAT_PERINTAH_TUGAS => 'doc.surat-perintah-tugas-document',
            self::LAPORAN_HASIL_GELAR_PERKARA => 'doc.laporan-hasil-gelar-perkara-document',
            self::SP2HP => 'doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document',
            self::TAHAP_1 => 'doc.tahap-1-document',
            self::TAHAP_2 => 'doc.tahap-2-document',
        };
    }

    public function parentId(): string
    {
        return substr($this->value, 0, 2);
    }

    /**
     * Map alternative base route aliases (e.g. sp2hp-document).
     */
    public static function tryFromRouteName(string $routeName): ?self
    {
        // Special alias check
        if (str_starts_with($routeName, 'doc.sp2hp-document.')) {
            return self::SP2HP;
        }

        foreach (self::cases() as $case) {
            if (str_starts_with($routeName, $case->baseRoute() . '.')) {
                return $case;
            }
        }

        return null;
    }
}
