# Daftar Deployment File: Fitur Maintenance Mode Dinamis

Berikut adalah daftar lengkap seluruh file yang dibuat (baru) dan diedit (lama) beserta penjelasan fungsionalnya. Silakan gunakan daftar ini sebagai referensi untuk melakukan *deploy/push* dari environment *development* ke server *production*.

## 📄 Daftar File (Baru & Dimodifikasi)

1. **`app/Console/Commands/MaintenanceDownCommand.php`** *(File Baru)*
   *Custom Artisan Command* yang menggantikan default command dari Laravel saat mode maintenance diaktifkan, agar mendukung fitur spesifik seperti penyimpanan waktu tenggat (kadaluwarsa / durasi `end_time`).

2. **`app/Http/Controllers/CMS/MaintenanceModeController.php`** *(File Baru)*
   Controller yang menangani *logic backend* untuk mengaktifkan dan menonaktifkan maintenance secara dinamis via UI (tanpa terminal), menangani perhitungan kalkulasi durasi / *countdown*, serta return dalam bentuk respon JSON (AJAX) bagi Admin.

3. **`resources/views/cms/maintenance-mode/index.blade.php`** *(File Baru)*
   Antarmuka (UI) khusus Admin untuk mengelola fitur Maintenance Mode di CMS. Tampilan ini sudah disesuaikan agar seragam (uniform) secara visual dengan struktur standar `box` layout dari template dashboard ICELL secara keseluruhan.

4. **`database/migrations/xxxx_xx_xx_xxxxxx_create_log_maintenances_table.php`** *(File Baru)*
   File migrasi database untuk membuat tabel `log_maintenances`. Berperan sebagai kotak hitam pencatat jejak (*audit log / history*) kapan, durasi, URL, dan siapa User/Admin yang mengaktifkan serta mematikan fungsi pemeliharaan sistem.

5. **`app/Models/Log/LogMaintenance.php`** *(File Baru)*
   Model Eloquent yang bertugas untuk menghubungkan struktur dan operasi interaksi antar tabel log tersebut terhadap operasi di tingkat backend.

6. **`routes/cms.php`** *(File Dimodifikasi)*
   Penambahan route grup pada menu CMS untuk menampung metode `MaintenanceModeController` yaitu halaman utama `/cms/maintenance-mode`, endpoint `activate` (POST), dan `deactivate` (POST).

7. **`resources/views/cms/sidebar/sidebarmenu.blade.php`** *(File Dimodifikasi)*
   Registrasi item dan penempatan menu navigasi baru "Maintenance Mode" di panel Sidebar admin bagian kiri (diklasifikasikan di bawah kategori folder *Tools*).

8. **`app/Http/Middleware/RestrictNonHelpdeskWrite.php`** *(File Dimodifikasi)*
   Diperbarui (*di-update*) untuk menyertakan pengecualian (*exception bypass*) pada *URI Request*. Tanpa pengecualian ini, pengguna CMS berelevansi *role id 1 (Admin)* yang tidak memiliki frasa "Helpdesk" di dalam namanya akan diblokir dengan *error 403 Forbidden* saat mencoba melakukan `POST Request` aktivasi system down.

9. **`app/Http/Middleware/PreventRequestsDuringMaintenance.php`** *(File Dimodifikasi)*
   * Diperbarui agar mendukung *Auto-Up / Auto-Recovery* (Sistem secara otomatis mengubah mode menjadi *Online* jika sistem membaca batas waktu *end_time* habis).
   * Pengecualian proteksi bagi para Admin yang sedang bekerja di menu setting Maintenance Mode sehingga CMS tetap bisa dibuka walau keseluruhan *system maintenance*.

10. **`resources/views/errors/503.blade.php`** *(File Dimodifikasi)*
   Modifikasi visual error page 503 agar menangkap konfigurasi internal maintenance yang dideklarasikan oleh file internal JSON-nya laravel `storage/framework/down`. Menghidupkan status waktu *countdown jam / menit / detik*.

> **Tips Git:** Saat menggunakan `git` untuk commit, pastikan Anda juga melakukan `git add` secara eksplisit pada 3 file baru yang ditambahkan di atas.

---

## 🚀 Hal Penting Setelah Proses Deployment Server

Setelah file-file di atas di *upload* / berada di Server, **Wajib Jalankan Perintah Berikut di Terminal Server (CWD/Root):**

1. Clear cache pada routes framework untuk membuang penempatan internal sistem lama:
   ```bash
   php artisan route:clear
   ```

2. Clear seluruh caching views / blade templates:
   ```bash
   php artisan view:clear
   ```

3. Opsional Cache Clearer (tapi disarankan):
   ```bash
   php artisan cache:clear
   ```

4. **[KRUSIAL]** Pastikan hak akses direktori file sistem dari aplikasi Laravel untuk path `storage/framework/` dapat dibaca dan **ditulis (*Write / Modify Permissions*)** secara eksekusi penuh oleh pengguna web server (e.g., *www-data* di Nginx/Ubuntu Server). Jika perizinan status foldernya *Read Only*, file payload internal `"down"` tidak bisa diciptakan.
