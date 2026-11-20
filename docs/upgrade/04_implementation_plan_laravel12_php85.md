# Implementation Plan IcellWeb — Laravel 12 dan PHP 8.5

**Baseline yang diperiksa** — Checkout lokal `update/laravel-v12` pada commit `653bc8f`, PHP CLI 8.5.12, Laravel 12.70.2. Pemeriksaan ini menggunakan konfigurasi contoh, SQLite sementara, dan transport email dalam memori. Target rilis adalah build dari lockfile yang tervalidasi pada PHP 8.5 dan lulus pengujian pada konfigurasi deployment. [Audit teknis](03_modernization_audit_laravel12_php85.md)

## Hasil pengecekan ulang

| Pemeriksaan | Hasil |
| --- | --- |
| Composer | `composer validate --no-check-publish` dan `composer check-platform-reqs --lock` lulus pada PHP 8.5.12. Manifest masih menyatakan `^8.2`. [Manifest](../../composer.json) |
| Route | Ditemukan 17 nama duplikat pada konfigurasi audit. Tahap serialisasi dan kompilasi dari `route:cache` gagal pertama kali pada `password.request` untuk `/password/reset`. [Route web](../../routes/web.php) · [Konfigurasi Fortify](../../config/fortify.php) |
| Konfigurasi dan TTE | Command masih membaca konfigurasi melalui `env()`, menulis token ke `.env`, dan mencetaknya. Scheduler mengarahkan output ke log. Status token pada log historis dan konfigurasi production perlu diperiksa oleh pengelola deployment. [Command](../../app/Console/Commands/Production/RenewESignatureAPIToken.php) · [Scheduler](../../app/Console/Kernel.php) |
| Kompatibilitas aplikasi | Empat parameter implisit nullable dan tiga pemanggilan `curl_close()` masih ada. Uji Carbon menghasilkan `25.997260273972604` untuk contoh usia sehari sebelum ulang tahun ke-26. Kontrak usia dari integrasi belum diuji. [Controller kecelakaan](../../app/Http/Controllers/AccidentController.php) · [Payload EMP](../../app/Http/Controllers/EmpIntegration/Report/SuspectInvestigationDeterminationReportController.php) |
| Email OTP | Transport dalam memori menghasilkan HTML dan plain text berisi OTP. Subject dinamis tertimpa menjadi `O T P Mail`. [OTPMail](../../app/Mail/OTPMail.php) |
| PHPUnit | **19 dari 20 lulus**. `ExampleTest` mengharapkan HTTP 200 pada `/`, tetapi controller mengarahkan ke `/login` dengan HTTP 302. Suite dijalankan dengan `--no-configuration` dan konfigurasi pengujian sementara karena pemeriksaan keterbacaan bootstrap PHPUnit pada Windows gagal. Pengujian penuh dengan konfigurasi standar dan PostgreSQL masih terbuka. [ExampleTest](../../tests/Feature/ExampleTest.php) · [Controller login](../../app/Http/Controllers/LoginController.php) |

## PR 1 — Runtime PHP 8.5 dan baseline pengujian

1. Selaraskan constraint PHP di `composer.json` dengan seri yang didukung. Untuk target yang dibatasi ke PHP 8.5.x gunakan `~8.5.0`, atau naikkan batas bawah sesuai patch tertua yang masih dipakai deployment. Gunakan PHP 8.5 untuk Composer dan runner pengujian. Sinkronkan metadata lockfile setelah perubahan manifest. Tinjau diff versi dependensi sebelum menerima perubahan resolusi.
2. Benahi lingkungan pengujian Windows agar PHPUnit dapat membuka `vendor/autoload.php`. Pisahkan konfigurasi tes dari data dan endpoint production. Pertahankan tes unit cepat dan siapkan job PostgreSQL untuk fitur yang menggunakan skema serta SQL khusus proyek.
3. Ubah tes root menjadi assertion redirect ke `/login` jika alur yang berlaku memang redirect. Tambahkan pemeriksaan halaman login sesuai alur aplikasi dan jalankan kembali suite dengan konfigurasi standar.
4. Validasi manifest, instal dari lockfile, jalankan `composer check-platform-reqs --lock` dan `composer audit --locked`. Catat hasil pemeriksaan untuk image atau host deployment yang sebenarnya. Periksa PHP web, worker, dan scheduler serta ekstensi PostgreSQL dan Kafka yang dipakai aplikasi. [Konfigurasi PHPUnit](../../phpunit.xml) · [Konfigurasi database](../../config/database.php)

**Kriteria lulus** — Clean install dari lockfile lulus di PHP 8.5. Suite standar berjalan tanpa kegagalan. Temuan advisori ditangani sebelum rilis sesuai dampaknya.

## PR 2 — Konflik route dan cache

1. Jadikan Fortify kandidat pemilik nama standar `password.*` karena fitur reset dan view provider sudah terdaftar. Verifikasi dahulu link email, form, password broker, middleware, dan penggunaan URL Laravel UI. Terapkan pembatasan registrasi Laravel UI secara selektif atau beri nama kompatibilitas pada rute legacy yang masih dipakai. Pertahankan URL reset yang masih aktif, termasuk tautan yang telah dikirim, sampai transisi aman.
2. Petakan setiap nama duplikat ke pemanggilnya. Ubah nama dan pemanggil dalam perubahan yang sama. Cocokkan metode HTTP, parameter, dan tujuan redirect. Untuk `forget-password`, beri nama terpisah pada submit POST dan ubah action formulirnya. Untuk SP2HP, verifikasi route tampil dan unduh masing-masing mengarah ke aksi yang dimaksud.

| Kelompok | Nama yang ditemukan |
| --- | --- |
| Fortify dan Laravel UI | `password.request`, `password.reset`, `password.email`, `password.update`, `password.confirm` |
| Lupa sandi kustom dan halaman utama | `forget-password`, `index` |
| SPDP | `spdp.create`, `spdp.store`, `spdp.show`, `spdp.edit`, `spdp.update` |
| Rekap dan produktivitas | `add_surat_tugas`, `autocomplete.fetch`, `file.upload.post`, `edit_tersangka` |
| SP2HP | `doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.download` |

**Kriteria lulus** — Seluruh route bisa di-cache di staging dan aplikasi bisa boot dari cache. Reset sandi, OTP, dashboard, SPDP, SP2HP, dan route dokumen mempertahankan tujuan, metode, parameter, serta batas akses yang diuji baik dengan cache maupun tanpa cache. [Route dokumen](../../routes/document.php) · [Provider Fortify](../../app/Providers/FortifyServiceProvider.php)

## PR 3 — Konfigurasi cache dan siklus token TTE

1. Hilangkan pencetakan token dari command dan pastikan pesan gagal tidak menyertakan kredensial. Jika pemeriksaan terbatas pada log scheduler menunjukkan token pernah tercatat, minta pengelola merotasi token yang terdampak dan menangani log sesuai kebijakan penyimpanan.
2. Petakan pembaca `env()` yang benar-benar dieksekusi. Gunakan konfigurasi yang tersedia untuk `APP_MODE` dan `APP_DEBUG`. Tempatkan pengaturan tetap untuk TTE, WhatsApp, Puskarda, dan feature flag ke file konfigurasi yang relevan. Perbarui contoh konfigurasi dengan placeholder. Uji nilai default dan mode LOCAL serta PRODUCTION.
3. Beri command dan semua pemanggil TTE satu pembaca token runtime. Pilih penyimpanan bersama yang bisa diakses scheduler, web, dan worker. Jika PostgreSQL sudah menjadi penyimpanan bersama, gunakan penyimpanan token TTE khusus dengan nilai terenkripsi dan perubahan atomik. Simpan token baru hanya setelah respons penyedia tervalidasi. Jika refresh gagal, pertahankan token lama selama masih valid dan kembalikan exit code gagal. Cegah refresh berjalan bersamaan bila scheduler dijalankan pada beberapa instance.
4. Pindahkan pembaca di `DocumentSignatureController`, `ESignatureConfirmationController`, controller pengecekan personel/TTE, dan `BiroSdmController` bersama dengan writer di command. Siapkan token melalui konfigurasi deployment atau proses inisialisasi sebelum cutover. Jalankan fake HTTP untuk rotasi sukses dan gagal, lalu smoke test terhadap penyedia staging. [Controller penandatanganan](../../app/Http/Controllers/DocumentSignatureController.php) · [Command rotasi](../../app/Console/Commands/Production/RenewESignatureAPIToken.php)

**Kriteria lulus** — Dengan `config:cache` aktif, penandatanganan dan cek personel memakai token yang baru dirotasi tanpa restart worker. Kegagalan refresh mempertahankan token lama yang valid. Command selesai dengan exit code yang sesuai dan output bebas token.

## PR 4 — Regresi PHP 8.5, Carbon, dan OTP

| Area | Perubahan | Uji penerimaan |
| --- | --- | --- |
| Parameter nullable | Hapus parameter `Request` yang terbukti tidak dipakai setelah memeriksa pemanggil, atau gunakan tipe yang sesuai kontrak. Ubah `array $errors = null` menjadi `?array $errors = null`. | Download dan penghapusan dokumen serta error response UpdateLP berjalan pada PHP 8.5 tanpa deprecation terkait. |
| cURL | Hapus tiga pemanggilan `curl_close()` di `AccidentController`. Pertahankan perilaku request dan error handling. | Jalur sukses, timeout, dan koneksi gagal tetap sesuai, dengan deprecation reporting aktif. |
| Carbon 3 | Cocokkan tujuh lokasi `diffInYears()` pada integrasi EMP dan Pusiknas dengan spesifikasi tipe dan tanggal acuan per endpoint. Terapkan konversi hanya di batas payload yang membutuhkannya. | Payload sesuai kontrak untuk sebelum, saat, dan sesudah ulang tahun, serta tanggal kosong atau tidak valid. |
| OTP | Jadikan `envelope()` sumber subject dinamis dan teruskan data kedua template melalui `content()`. | Transport tes menghasilkan subject yang sesuai input dan kedua format isi tetap benar. |

PR 4 bisa berjalan paralel dengan PR 2 dan PR 3 setelah baseline pengujian tersedia.

## Validasi staging dan rilis

1. Jalankan migrasi tertunda di salinan PostgreSQL yang disanitasi. Bila instalasi baru didukung, uji jalur bootstrap yang dipakai proyek. Periksa khususnya perubahan kolom lama dengan `->change()`, search path multi-skema, nullable, default, dan indeks. Buat migrasi korektif jika ditemukan perbedaan skema yang perlu diperbaiki.
2. Bangun artefak dari lockfile. Jalankan `config:cache`, `route:cache`, dan `view:cache` dari rilis staging. Uji login, OTP, reset sandi, akses antarsatker, dokumen historis, ekspor, DataTables, TTE, worker, dan scheduler dengan konfigurasi yang setara dengan deployment.
3. Catat hasil dan kondisi rollback. Siapkan artefak aplikasi sebelumnya dan prosedur pemulihan cache, worker, serta scheduler. Bila perubahan mencakup penyimpanan token atau migrasi skema, pastikan versi lama dan baru dapat beroperasi selama transisi atau siapkan langkah pemulihan data sebelum cutover.

**Keputusan rilis** — Promosikan setelah PR 1–4 dan verifikasi staging lulus, dengan keluaran pengujian, hasil cache, dan smoke test tercatat. Optimasi query produktivitas, konversi `$casts` dan `boot()`, penggantian generator UUID, serta migrasi skeleton masuk backlog setelah rilis. Prioritaskan klaim performa berdasarkan pengukuran dan perubahan mass assignment berdasarkan jalur tulis yang terbukti perlu diperketat. [Audit modernisasi](03_modernization_audit_laravel12_php85.md)
