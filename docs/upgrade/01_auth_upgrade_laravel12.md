# Laporan Pembaruan Modul Autentikasi (Auth & Login) — Migrasi Laravel 12

Dokumen ini mencatat seluruh temuan, perbaikan bug kritis, penyesuaian dependensi, dan pengujian yang telah diselesaikan pada modul **Autentikasi & Login** dalam rangka migrasi ke **Laravel 12.70.2** dengan **PHP 8.5.12**.

---

## 1. Ringkasan Status Framework & Runtime

* **Versi Framework:** `Laravel Framework 12.70.2` (Berhasil di-upgrade langsung dari Laravel 10).
* **Versi PHP:** `PHP 8.5.12 (cli)` (ZTS Visual C++ 2022 x64).
* **Database Driver:** PostgreSQL 17.4 (`pdo_pgsql` aktif, 377 tabel terdeteksi).
* **Status Route:** 1,593 routes terdaftar tanpa conflict.

---

## 2. Rincian Perbaikan pada Modul Autentikasi / Login

### A. Perbaikan Syntax Bug Fatal pada `LoginController.php`
* **File:** `app/Http/Controllers/LoginController.php` (baris 335)
* **Masalah:**
  ```php
  // SEBELUM (Syntax Error pada PHP):
  if (!$userId) {
      return response()->json()([
          'status' => 'Error',
          'message' => 'Session OTP tidak ditemukan, Silahkan Login Ulang'
      ]);
  }
  ```
  Pemanggilan ganda `json()([ ... ])` menyebabkan `Fatal error: Function name must be a string` ketika sesi OTP kadaluarsa.
* **Perbaikan:**
  ```php
  // SESUDAH:
  if (!$userId) {
      return response()->json([
          'status' => 'Error',
          'message' => 'Session OTP tidak ditemukan, Silahkan Login Ulang'
      ]);
  }
  ```

---

### B. Sinkronisasi Tabel Password Resets
* **File:** `config/auth.php` (baris 96)
* **Masalah:** Konfigurasi default Laravel baru mengarah ke tabel `password_reset_tokens`. Namun skema database riil proyek ini menggunakan tabel `password_resets`.
* **Perbaikan:** Mengubah `'table' => 'password_reset_tokens'` menjadi `'table' => 'password_resets'`, sehingga mekanisme forgot password dan reset token sinkron dengan database PostgreSQL.

---

### C. Pembaruan Paket & Engine Captcha
* **File:** `composer.json` & `config/app.php`
* **Masalah:** Versi lama `mews/captcha: ^3.3` terkunci di framework lama dan `intervention/image: ^2.7`.
* **Perbaikan:**
  * Bump `mews/captcha` ke `^3.5` dan `intervention/image` ke `^3.7`.
  * Hapus duplikasi deklarasi service provider dan alias `Captcha` di `config/app.php`.
  * Verifikasi rute `GET /refresh_captcha` dan helper `{!! Captcha::img() !!}` berfungsi 100%.

---

### D. Perbaikan Typo Namespace pada Middleware `Kernel.php`
* **File:** `app/Http/Kernel.php` (baris 63)
* **Masalah:** Typo fatal `\Apxp\Http\Middleware\ValidateSignature::class`.
* **Perbaikan:** Diperbaiki menjadi `\App\Http\Middleware\ValidateSignature::class`.

---

### E. Perbaikan Dinamisasi User ID pada `UserActionObserver.php`
* **File:** `app/Observers/UserActionObserver.php`
* **Masalah:** `$this->user = Auth::user()->id` disimpan di dalam `__construct()`. Saat observer diinisialisasi pada fase boot aplikasi, user belum login sehingga bernilai `null` dan berisiko membuat `created_by_user_id` / `updated_by_user_id` kosong.
* **Perbaikan:** Menggunakan pemanggilan dinamis `Auth::id() ?? $this->user` langsung pada event `creating`, `updating`, dan `deleting`.

---

### F. Penanganan Library QR Code
* **File:** `config/app.php`, `app/Providers/AppServiceProvider.php`, `routes/web.php`
* **Masalah:** `simplesoftwareio/simple-qrcode: ^4.2` memiliki konflik dependensi dengan `laravel/fortify` (karena `bacon/bacon-qr-code: ^2.0` vs `^3.0`).
* **Perbaikan:**
  * Digantikan dengan fork resmi terawat: `f9webltd/simple-qrcode: ^5.0`.
  * Ditambahkan `class_alias` backward-compatibility di `AppServiceProvider::boot()`:
    ```php
    if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode') && class_exists('F9WebLtd\QrCode\Facades\QrCode')) {
        class_alias('F9WebLtd\QrCode\Facades\QrCode', 'SimpleSoftwareIO\QrCode\Facades\QrCode');
    }
    ```
    Hal ini memastikan seluruh controller lama yang mengimpor namespace `SimpleSoftwareIO` tetap berjalan tanpa error.

---

## 3. Hasil Pengujian Modul Autentikasi

| Endpoint / Komponen | Metode Uji | Hasil | Status |
| :--- | :--- | :--- | :--- |
| `GET /login` | HTTP Kernel Request | HTTP 200 OK (Blade view, assets, captcha rendered) | **LULUS** |
| `POST /authenticate` | Controller Direct Invocation | HTTP 200 JSON Response (`status: errorCaptcha`) | **LULUS** |
| `POST /verifyOtp` | Validasi Session Kosong | HTTP 200 JSON Response (`Session OTP tidak ditemukan`) | **LULUS** |
| `GET /refresh_captcha` | Captcha Generation | HTTP 200 JSON Response (`{"captcha": "<img ...>"}`) | **LULUS** |
| Database Auth Query | Eloquent User Query via CLI | Berhasil query record pengguna dari schema PostgreSQL | **LULUS** |
| Route Discovery | `php artisan route:list` | 1,593 rute terbaca sukses tanpa fatal error | **LULUS** |

---

## 4. Rekomendasi Tahap Selanjutnya

1. **Tahap 2:** Pengujian & Penyesuaian Role & Permission (`spatie/laravel-permission: ^6.25`).
2. **Tahap 3:** Pengujian & Penyesuaian Modul DataTables (`yajra/laravel-datatables-oracle: ^12.7`).
3. **Tahap 4:** Pengujian Ekspor/Impor Dokumen (PDF, Excel, Word).
