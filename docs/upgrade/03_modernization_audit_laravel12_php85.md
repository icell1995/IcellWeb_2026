# Audit Modernisasi IcellWeb — Laravel 12 dan PHP 8.5

**Target** — Laravel 12 dengan PHP 8.5.x pada mesin pengembangan, CI, staging, dan production. Gunakan patch PHP 8.5 terbaru yang disetujui tim. Lockfile saat ini lolos pemeriksaan platform pada PHP 8.5.12.

**Baseline** — Branch `update/laravel-v12`, commit `653bc8f`. Composer mengunci Laravel 12.70.2, Carbon 3.15.1, PHPUnit 11.5.57, dan Sanctum 4.3.3. Laravel berhasil boot di PHP 8.5.12. Pengujian lengkap pada konfigurasi deployment PHP 8.5 dan staging PostgreSQL masih diperlukan. Laporan modul sebelumnya tetap menjadi referensi untuk alur bisnis dan hasil uji historis. [Lockfile](../../composer.lock) · [Laporan autentikasi](01_auth_upgrade_laravel12.md) · [Laporan role, DataTables, dan ekspor](02_roles_datatables_export_upgrade_laravel12.md)

## 1. Kesiapan rilis

| Prioritas | Temuan | Tindakan dan kriteria lulus |
| --- | --- | --- |
| P0 | **Runtime dan dependensi.** Manifest menyatakan PHP `^8.2`. Beberapa paket produksi terkunci, termasuk `symfony/clock` dan `symfony/translation`, mensyaratkan PHP ≥ 8.4.1. | Tetapkan PHP 8.5 yang didukung di manifest dan CI. Jika proyek hendak membatasi dukungan ke seri 8.5, gunakan constraint seperti `~8.5.0` atau batas patch lebih tinggi sesuai kebijakan tim. Jalankan Composer melalui PHP 8.5. Bila tim memakai `config.platform.php` untuk resolusi, gunakan patch minimum yang benar-benar didukung dan tetap periksa platform nyata dengan `composer check-platform-reqs --lock`. Cocokkan PHP CLI, web server, worker, dan scheduler, termasuk ekstensi PostgreSQL, Kafka, dan pengolahan berkas. [Manifest](../../composer.json) · [Konfigurasi platform Composer](https://getcomposer.org/doc/06-config.md#platform) |
| P0 | **Nama route duplikat.** Kompilasi route pada konfigurasi audit gagal pada `password.request`. Ada 17 nama duplikat pada konfigurasi tersebut, termasuk `password.reset`, `index`, dan `spdp.create`. | Tentukan pemilik alur reset sandi yang aktif karena Fortify dan `Auth::routes()` sama-sama mendaftarkan route. Petakan pemanggil `route(...)`, redirect, form, tautan email, dan klien sebelum mengubah nama. Pertahankan URL yang masih dikonsumsi atau sediakan kompatibilitas untuk pemanggil lama. Lulus jika `route:cache` berhasil dan route kritis menghasilkan tujuan, metode HTTP, serta middleware yang sama saat cache aktif dan kosong. [Route web](../../routes/web.php) · [Panduan upgrade Laravel 12](https://laravel.com/docs/12.x/upgrade#route-precedence) |
| P0 | **Konfigurasi runtime dan rotasi token TTE.** Pemanggilan `env()` di kode aplikasi bergantung pada pemuatan `.env` atau variabel proses. Command TTE saat ini memperbarui `.env` dan mencetak token. | Gunakan `config('app.mode')` yang sudah tersedia, dan definisikan konfigurasi tetap untuk host dan kredensial integrasi. Simpan token yang berotasi di penyimpanan runtime bersama yang sesuai dengan infrastruktur. Pastikan kegagalan refresh mempertahankan token lama yang masih valid dan keluaran/log bebas nilai token. Lulus jika refresh diikuti penandatanganan berhasil dengan config cache aktif, termasuk dari worker yang sudah berjalan. [Konfigurasi aplikasi](../../config/app.php) · [Command TTE](../../app/Console/Commands/Production/RenewESignatureAPIToken.php) · [Cache konfigurasi Laravel](https://laravel.com/docs/12.x/configuration#configuration-caching) |
| P1 | **Parameter implisit nullable.** Deprecation dimulai di PHP 8.4 dan tetap berlaku di PHP 8.5. Empat dari lima contoh audit lama adalah parameter implisit nullable. Tiga bertipe `Request` di controller dokumen dan satu bertipe `array` di `UpdateLPController`. `mixed` sudah mencakup `null`. | Deklarasikan `?array` ketika nilai null memang diterima. Untuk `Request`, hapus parameter jika terbukti tak terpakai oleh semua pemanggil, wajibkan `Request` jika perlu injeksi Laravel, atau gunakan `?Request` jika null memang bagian kontrak. Lulus jika route download/delete dan pemanggilan internal tetap benar serta pengujian PHP 8.5 bersih dari deprecation aplikasi pada jalur tersebut. [Daftar Ahli](../../app/Http/Controllers/Docs/DaftarAhliDocumentController.php) · [Daftar Tersangka](../../app/Http/Controllers/Docs/DaftarTersangkaDocumentController.php) · [Deprecation PHP 8.4](https://www.php.net/manual/en/migration84.deprecated.php) |
| P1 | **Deprecation baru di PHP 8.5.** Ada tiga pemanggilan `curl_close()` di `AccidentController`. PHP membebaskan objek `CurlHandle` secara otomatis. | Hapus pemanggilan yang deprecated saat mengerjakan controller. Pertahankan perilaku request dan penanganan error. Uji jalur sukses, timeout, dan kegagalan koneksi di PHP 8.5 dengan pelaporan deprecation aktif. Tinjau perubahan inkompatibel PHP 8.5 pada jalur yang dipakai proyek. [AccidentController](../../app/Http/Controllers/AccidentController.php) · [Deprecation PHP 8.5](https://www.php.net/manual/en/migration85.deprecated.php) · [Perubahan inkompatibel PHP 8.5](https://www.php.net/manual/en/migration85.incompatible.php) |
| P1 | **Perilaku Carbon 3.** `diffInYears()` kini menghasilkan `float` bertanda. Hasilnya dipakai untuk `UmurSaatLK` pada integrasi EMP dan perhitungan usia pada dokumen Pusiknas. | Verifikasi tanggal acuan, tipe JSON, dan aturan pembulatan dari kontrak masing-masing. Jika kontrak meminta tahun penuh, uji sebelum, saat, dan setelah ulang tahun. Uji tanggal kosong dan tanggal lahir setelah tanggal acuan. [Contoh payload EMP](../../app/Http/Controllers/EmpIntegration/Report/SuspectInvestigationDeterminationReportController.php) · [Migrasi Carbon 3](https://carbon.nesbot.com/guide/getting-started/migration.html) |
| P1 | **Subject email OTP tertimpa.** Pada uji transport lokal, framework menjalankan `build()`, lalu menerapkan `envelope()` dan `content()`. HTML dan plain text sama-sama memuat kode, sementara subject aktual adalah `O T P Mail`. | Definisikan subject yang disepakati di `envelope()` dan teruskan data yang diperlukan kedua template lewat argumen `with:` pada konstruktor `Content`. Uji subject untuk setiap alur pemanggil dan isi HTML/plain text dengan transport lokal. [OTPMail](../../app/Mail/OTPMail.php) |
| P1 | **Replay migrasi PostgreSQL.** Migrasi lama memuat `->change()`. Laravel 11+ mengharuskan modifier yang ingin dipertahankan ditulis ulang saat mengubah kolom. | Uji migrasi tertunda pada salinan skema yang telah disanitasi. Bila instalasi baru didukung, uji juga jalur bootstrap database dari basis yang resmi dipakai proyek. Periksa tipe, default, nullability, indeks, dan skema PostgreSQL yang terlibat. Tinjau migrasi korektif jika skema yang sudah digunakan perlu dibetulkan. [Migrasi proyek](../../database/migrations) · [Panduan perubahan kolom](https://laravel.com/docs/11.x/upgrade#modifying-columns) |

Periksa advisori saat menyiapkan rilis. Konfigurasi proyek saat ini menonaktifkan pemblokiran resolusi Composer berdasarkan advisori. Tinjau hasil `composer audit --locked` dan dokumentasikan penanganan temuan yang berlaku untuk deployment. [Konfigurasi Composer proyek](../../composer.json) · [Composer audit](https://getcomposer.org/doc/03-cli.md#audit)

## 2. Modernisasi kode per modul

### Halaman produktivitas dan Blade

Mulai dari `AccidentController::view_produktivitas_accident()`. Pemindaian menemukan 170 kemunculan `DB::select()` dalam file controller, 106 di dalam method tersebut. Angka ini menghitung kode sumber. Ukur jumlah query aktual, waktu database, latensi, dan memori pada data staging yang representatif. Pisahkan pengukuran untuk perkara dengan sedikit dan banyak dokumen. [AccidentController](../../app/Http/Controllers/AccidentController.php)

1. Ambil data dokumen sebelum render. Periksa fallback `?? Model::where(...)->first()` di Blade. Jika hasil yang sah memang `null`, fallback itu dapat mengulang query. Uji dokumen ada, tidak ada, dan aksesnya dibatasi.
2. Gabungkan query berulang bila hasilnya setara. Pilih `with()`, `withCount()`, Query Builder, atau SQL dengan parameter binding sesuai bentuk data. Pertahankan filter satker, soft delete, urutan, dan penanganan skema `legacy`.
3. Ukur ulang dengan data yang sama. Perubahan dianggap berhasil jika output dan akses tetap setara serta metrik yang ditargetkan membaik. Gunakan `EXPLAIN` di staging untuk query yang masih dominan sebelum mengusulkan indeks. [Contoh Blade DPO/DPB](../../resources/views/produktivitas/surat-dpo-dpb/dpo-dpb.blade.php) · [Relasi dan eager loading](https://laravel.com/docs/12.x/eloquent-relationships#eager-loading)

### Validasi, otorisasi, dan mass assignment

Saring model dengan `$guarded = []` berdasarkan jalur tulis yang menerima input pengguna atau payload integrasi. Untuk data sensitif, petakan field yang boleh disimpan secara eksplisit atau pakai `$fillable` setelah memeriksa semua pemanggil. Field audit, status internal, dan identitas pemilik ditentukan sisi server. Pada input array bersarang, batasi juga key yang diizinkan.

Ekstrak aturan yang kompleks atau berulang ke Form Request. Pertahankan Policy, Gate, atau middleware yang mengatur akses per satker dan dokumen. Saat memindahkan validasi, uji bentuk respons API, pesan kesalahan, dan redirect formulir web. [Validasi Laravel](https://laravel.com/docs/12.x/validation#form-request-validation) · [Mass assignment](https://laravel.com/docs/12.x/eloquent#mass-assignment)

### UUID dan siklus hidup model

`Webpatser\Uuid\Uuid::generate()` tanpa argumen menghasilkan UUID v1. `Str::uuid()` menghasilkan v4. `HasUuids` pada Laravel 12 menghasilkan v7, sedangkan `Str::orderedUuid()` memakai format ordered v4. Tentukan format yang boleh diterbitkan untuk setiap kelompok model dan integrasi. Jika kontrak masih memerlukan v1, pertahankan generator yang kompatibel sampai kontrak berubah. Periksa juga model yang sudah memakai `HasUuids`. [Panduan upgrade UUID](https://laravel.com/docs/12.x/upgrade#models-and-uuidv7)

Mulai dari satu modul. Pertahankan ID historis, foreign key, route binding, dan ID yang ditetapkan oleh pemanggil. Saat mengganti event `creating` atau memindahkan observer ke `#[ObservedBy]`, uji kapan ID tersedia, urutan event, dan field audit untuk HTTP, queue, serta CLI. Hapus dependensi dan alias setelah referensinya habis dan pengujian kompatibilitas lulus. Klaim manfaat indeks perlu diukur pada beban tulis yang representatif.

### Konsistensi penulisan

- Untuk kode baru atau modul yang sedang diubah, gunakan return type eksplisit pada relasi, `casts(): array`, `Attribute::make()`, serta Form Request jika cocok. Laravel 12 masih mendukung `$casts`, `boot()`, accessor legacy, dan validasi manual.
- Pakai date cast untuk atribut model yang memang merepresentasikan tanggal. Untuk hasil raw SQL dan DTO, lakukan konversi di titik pemetaan data. Uji tanggal null, zona waktu, dan lokal Indonesia saat memindahkan format dari Blade.
- Gunakan `?->` setelah memastikan pola `optional()` yang diganti setara. Periksa guard dan waktu resolusi autentikasi sebelum mengganti `Auth::getUser()` dengan `Auth::user()`. Hapus import Intervention Image v2 yang terbukti tak terpakai.
- Terapkan Laravel Pint pada file yang disentuh. Pisahkan perubahan format luas dari refactor perilaku agar review tetap mudah. File cadangan `.bak*` yang tercatat di Git dapat ditinjau terpisah. Inventaris lama adalah perkiraan. Contohnya, pemindaian `.php` pada baseline menemukan 227 panggilan `Uuid::generate()` di `app/` dan `database/`, sedangkan audit lama menyebut 310. [Eloquent casts dan accessor](https://laravel.com/docs/12.x/eloquent-mutators) · [Laravel Pint](https://laravel.com/docs/12.x/pint)

### Struktur dan dependensi opsional

Pertahankan `bootstrap/app.php`, HTTP dan Console Kernel, exception handler, dan provider yang sudah melayani aplikasi. Migrasi bernama dan middleware constructor tetap valid pada struktur ini. Untuk modul baru, anonymous migration dan middleware di route dapat dipakai sebagai konvensi. Jika skeleton diubah di kemudian hari, uji registrasi middleware, route, exception rendering, command terjadwal, dan provider dalam PR tersendiri.

Evaluasi `doctrine/dbal` berdasarkan penggunaan langsung dan dependensi transitif. `->change()` memakai dukungan skema native Laravel. `spatie/laravel-ignition` dapat dipertahankan sebagai alat development atau dievaluasi terpisah. [Panduan upgrade Laravel 11](https://laravel.com/docs/11.x/upgrade)

## 3. Verifikasi PHP 8.5 dan kesiapan deploy

Jalankan langkah berikut dari checkout lokal atau runner CI khusus pengujian dengan PHP 8.5. Pada Windows, `Get-Command php` dan `php --ini` membantu memastikan PATH dan konfigurasi mengarah ke instalasi baru. Periksa juga versi PHP yang melayani request HTTP dan yang menjalankan queue serta scheduler. Siapkan database dan konfigurasi pengujian terisolasi sebelum menjalankan suite. Lakukan uji cache pada staging karena cache mengubah perilaku startup aplikasi.

```sh
php -v
php --ini
composer validate --no-check-publish
composer install --no-interaction --prefer-dist
composer check-platform-reqs --lock
composer audit --locked
php artisan --version
php artisan test
```

Setelah route duplikat dibenahi, jalankan perintah berikut di staging. Ulangi smoke test sebelum dan sesudah caching dengan konfigurasi deployment yang sama. [Dokumentasi deployment Laravel 12](https://laravel.com/docs/12.x/deployment)

```sh
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

| Area | Bukti kelulusan |
| --- | --- |
| Runtime | Composer memasang lockfile pada PHP 8.5. Pemeriksaan platform lolos pada proses yang dipakai untuk deploy. PHP web, worker, dan scheduler bisa boot. |
| Suite pengujian | Pengujian berjalan dengan konfigurasi repo dan layanan tes terisolasi. Selaraskan `ExampleTest` dengan redirect `/` ke `/login` jika itu kontrak yang diinginkan. |
| Autentikasi dan akses | Login, OTP, reset sandi, serta batas akses per satker benar saat cache aktif maupun kosong. |
| Data dan dokumen | PostgreSQL staging lolos migrasi dan menghasilkan skema sesuai harapan. Uji baca data historis, pembuatan dokumen, render atau ekspor Word/PDF/Excel, serta filter dan paginasi DataTables dari alur yang aktif. |
| Integrasi | Uji TTE dengan token yang dirotasi, payload Carbon 3, serta alur Kafka dan queue yang digunakan. Gunakan endpoint pengujian atau fake yang sesuai kontrak. |
| Performa | Catat jumlah query dan latensi sebelum serta sesudah perubahan produktivitas. Verifikasi hasil, urutan dokumen, dan otorisasi tetap setara. |

**Status pengujian baseline** — Di PHP 8.5.12, Composer berhasil memvalidasi manifest dan persyaratan platform lockfile. PHPUnit menghasilkan 19 lulus dari 20 dalam pengujian SQLite sementara. Satu tes mengharapkan HTTP 200 untuk `/`, sementara controller mengalihkan ke `/login` dengan HTTP 302. Run tersebut memakai `--no-configuration` karena pemeriksaan keterbacaan bootstrap PHPUnit di lingkungan Windows audit gagal. Kompilasi route gagal, sementara pengiriman OTP melalui transport lokal mengonfirmasi subject tertimpa dan kedua format isi tersedia. Kelulusan suite penuh dengan konfigurasi standar pada PHP 8.5, migrasi di staging PostgreSQL, dan uji layanan eksternal masih perlu dibuktikan. [Tes root](../../tests/Feature/ExampleTest.php) · [Controller login](../../app/Http/Controllers/LoginController.php)

## 4. Urutan implementasi

1. **PR runtime dan baseline tes.** Selaraskan Composer, CI, dan dokumentasi runtime dengan PHP 8.5. Benahi lingkungan pengujian, validasi lockfile, dan catat hasil suite di PHP 8.5.
2. **PR cache dan regresi fungsional.** Selesaikan nama route duplikat dan uji reset sandi serta URL yang terdampak. Pindahkan konfigurasi tetap dari pemanggilan `env()` runtime, lalu uji rotasi TTE dengan cache. Tangani nullable, kontrak Carbon 3, dan subject OTP dengan tes regresi yang sesuai.
3. **PR performa dan modernisasi per modul.** Ukur halaman produktivitas, perbaiki query dengan dampak terbesar, lalu kerjakan validasi, mass assignment, UUID, dan konsistensi model secara bertahap.
