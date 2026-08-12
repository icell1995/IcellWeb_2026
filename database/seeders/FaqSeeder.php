<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama jika ada
        Faq::truncate();

        $dummyFaqs = [
            [
                'kategori' => 'Login & Akun',
                'pertanyaan' => 'Bagaimana cara memulihkan akun jika lupa kata sandi?',
                'jawaban' => "1. Pada halaman login utama ICELL, klik tombol \"Forget Password\".\n2. Masukkan alamat email terdaftar atau NRP Anda.\n3. Periksa kotak masuk email Anda dan klik link tautan pemulihan kata sandi.\n4. Jika email tidak kunjung masuk dalam 5 menit, silakan hubungi tim Helpdesk/Admin Polres setempat untuk reset password manual.",
                'is_active' => true,
            ],
            [
                'kategori' => 'TTE & E-Signature',
                'pertanyaan' => 'Mengapa tanda tangan dokumen (TTE) gagal dengan pesan kesalahan passphrase?',
                'jawaban' => "Kegagalan TTE biasanya disebabkan oleh dua kemungkinan utama:\n\n1. Passphrase Salah: Pastikan Anda tidak keliru mengetik passphrase BSrE Anda (perhatikan huruf besar/kecil).\n2. Sertifikat Kadaluwarsa: Sertifikat elektronik BSrE memiliki masa aktif. Jika sudah kadaluwarsa, silakan hubungi Admin Polda untuk pengajuan registrasi ulang sertifikat elektronik.",
                'is_active' => true,
            ],
            [
                'kategori' => 'Sinkronisasi Data',
                'pertanyaan' => 'Berapa lama waktu sinkronisasi data kecelakaan dari IRSMS Korlantas ke ICELL?',
                'jawaban' => "Data kecelakaan yang diinput melalui sistem IRSMS Korlantas akan otomatis ditarik dan disinkronkan ke dalam sistem ICELL setiap 15 menit sekali.\n\nJika data belum muncul setelah 30 menit, silakan periksa menu \"Monitor Integrasi\" di dashboard CMS untuk melihat apakah status koneksi API IRSMS sedang mengalami gangguan (Offline).",
                'is_active' => true,
            ],
            [
                'kategori' => 'Input Kasus',
                'pertanyaan' => 'Bagaimana cara melampirkan berkas barang bukti kendaraan?',
                'jawaban' => "1. Masuk ke halaman detail kasus kecelakaan terkait.\n2. Gulir ke bawah hingga menemukan section \"Daftar Barang Bukti\".\n3. Klik tombol \"Tambah Barang Bukti\".\n4. Isi spesifikasi kendaraan (nomor polisi, nomor rangka, nomor mesin, dan pemilik).\n5. Unggah foto kendaraan pada kolom lampiran (format PNG/JPG, maks 2MB) lalu klik \"Simpan\".",
                'is_active' => true,
            ],
            [
                'kategori' => 'Hak Akses',
                'pertanyaan' => 'Mengapa saya (Penyidik/Officer) tidak bisa mengakses menu Review dan Rekap?',
                'jawaban' => "Sesuai dengan matriks RBAC (Role-Based Access Control) ICELL:\n\n- Menu \"Review\" dan \"Rekap\" hanya dapat diakses oleh user dengan Role Administrator (Role 1), Helpdesk (Role 2), atau Admin Polres (Role 3).\n- Penyidik/Officer (Role 4) didesain hanya fokus pada menu input data, laporan kasus, dan riwayat dokumen yang sedang mereka tangani secara aktif.",
                'is_active' => true,
            ],
        ];

        foreach ($dummyFaqs as $faq) {
            Faq::create($faq);
        }
    }
}
