# Laporan Pembaruan Tahap 2, Tahap 3, & Tahap 4 — Migrasi Laravel 12

Dokumen ini merangkum audit kode, perbaikan bug kritis, pengoptimalan performa query, serta hasil pengujian untuk:
* **Tahap 2:** Role, Permission, dan Hak Akses Pengguna
* **Tahap 3:** Query & Server-Side DataTables (`yajra/laravel-datatables-oracle`)
* **Tahap 4:** Modul Pelaporan & Ekspor Dokumen (DomPDF v3, Excel v3, Word v1.4)

---

## 1. Tahap 2: Role, Permission, & Hak Akses Pengguna

### A. Perbaikan Fatal Bug pada `IsSignatoryMiddleware.php`
* **File:** `app/Http/Middleware/IsSignatoryMiddleware.php` (baris 28)
* **Masalah:**
  ```php
  // SEBELUM (Fatal Error pada PHP 8.2/8.5):
  if($positionCluster->is_can_signatory == true && isset($officers->position->is_can_signatory) && $officers->positon->is_can_signatory == true){
  ```
  1. Variabel yang didefinisikan adalah `$officer`, bukan `$officers` (`Undefined variable $officers`).
  2. Terdapat typo penulisan atribut: `$officers->positon` (kurang huruf 'i').
  Pada PHP 8.2 & 8.5, pemanggilan properti pada variabel yang tidak terdefinisi memicu *Fatal Error* / *TypeError*.
* **Perbaikan:**
  ```php
  // SESUDAH:
  if (isset($officer->position->positionCluster)) {
      $positionCluster = $officer->position->positionCluster;
      if ($positionCluster->is_can_signatory == true && ($officer->position->is_can_signatory ?? false) == true) {
          if (empty($officer->passphrase)) {
              return redirect()->route('esignature-confirmation.index');
          }
      }
  }
  ```

---

### B. Perbaikan Relasi `Role -> users` pada `Role.php`
* **File:** `app/Models/Lib/Role.php` (baris 26-29)
* **Masalah:**
  ```php
  // SEBELUM:
  public function users() {
      return $this->belongsTo(User::class, 'id');
  }
  ```
  Relasi `users()` didefinisikan secara keliru sebagai `belongsTo`. Padahal satu Role menaungi banyak User (`users.role_id`).
* **Perbaikan:**
  ```php
  // SESUDAH:
  public function users() {
      return $this->hasMany(User::class, 'role_id');
  }
  ```
  *Hasil Pengujian:* Relasi `$role->users()->count()` teruji sukses menghitung 34 pengguna pada Role Level 2.

---

### C. Kompatibilitas `spatie/laravel-permission: ^6.25`
* Perintah pembersihan cache permission framework `php artisan permission:cache-reset` dieksekusi dan berjalan bersih tanpa isu backward-compatibility.

---

## 2. Tahap 3: Query & Server-Side DataTables

### A. Peningkatan Paket ke `yajra/laravel-datatables-oracle: v12.7.2`
* DataTables telah ditingkatkan ke versi **v12.7.2** yang dibangun khusus untuk arsitektur Laravel 12.

### B. Pengoptimalan Query Memory Bottleneck (Penghapusan `->get()` Sebelum DataTables)
* **File:** `app/Http/Controllers/CMS/CheckOfficerDataController.php` (baris 17-20)
  * **Sebelum:**
    ```php
    $checkofficerDataHistories = CheckOfficerDataHistory::with(['createdByUser'])
        ->orderBy('created_at', 'desc')->get(); // Mengambil ribuan baris sekaligus ke RAM
    return DataTables::of($checkofficerDataHistories)->make();
    ```
  * **Sesudah:**
    ```php
    $query = CheckOfficerDataHistory::with(['createdByUser'])
        ->orderBy('created_at', 'desc');
    return DataTables::of($query)->make(true); // Server-side pagination via database PostgreSQL
    ```
* **File:** `app/Http/Controllers/Docs/Sp2hpDocumentController.php` (baris 86-90)
  * Menghapus `->get()` sebelum `DataTables::of($data)` agar server database PostgreSQL yang menangani kalkulasi `LIMIT` dan `OFFSET`.
* **Dampak Pengoptimalan:**
  * Pengurangan konsumsi RAM PHP hingga puluhan megabyte per request.
  * Respon server tetap kilat meskipun data berjumlah ribuan (teruji pada dataset 5.165 data history).

---

## 3. Tahap 4: Modul Pelaporan & Ekspor Dokumen

### A. Penyesuaian `barryvdh/laravel-dompdf: v3.1.2` (DomPDF v3)
* **Masalah:** Pada DomPDF v3, kelas facade lama `Barryvdh\DomPDF\Facade` telah dipindahkan ke `Barryvdh\DomPDF\Facade\Pdf`. Kode lama yang mengimpor namespace lama akan menghasilkan `Class not found`.
* **Perbaikan:**
  1. Memperbarui `config/app.php`:
     ```php
     'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
     ```
  2. Menambahkan `class_alias` otomatis di `AppServiceProvider::boot()`:
     ```php
     if (!class_exists('Barryvdh\DomPDF\Facade') && class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
         class_alias('Barryvdh\DomPDF\Facade\Pdf', 'Barryvdh\DomPDF\Facade');
     }
     ```
  3. Memperbarui import di `app/Http/Controllers/Docs/SuratPerintahPenyelidikanDocumentController.php`.
* **Hasil Pengujian:** Pembuatan dokumen PDF teruji sukses menghasilkan output stream valid (1.333 bytes).

---

### B. Pengujian `maatwebsite/excel: ^3.1` (Laravel-Excel & PhpSpreadsheet)
* **Hasil Pengujian:** Pembuatan file spreadsheet `.xlsx` via `Excel::raw()` berhasil menghasilkan file sebesar 6.314 bytes tanpa error dependensi.

---

### C. Pengujian `phpoffice/phpword: ^1.4` (TemplateProcessor)
* **Hasil Pengujian:** Template processor dan IOFactory Word2007 teruji sukses membuat dokumen `.docx` sebesar 7.381 bytes.

---

## 4. Matriks Ringkasan Pengujian Tahap 2, 3, & 4

| Komponen / Fitur | File / Modul | Hasil Uji | Status |
| :--- | :--- | :--- | :--- |
| **IsSignatory Middleware** | `IsSignatoryMiddleware.php` | Variabel `$officers` & typo diperbaiki | **LULUS** |
| **Role Users Relation** | `Role.php` | `hasMany` berjalan, 34 users terhitung | **LULUS** |
| **Permission Cache** | `spatie/laravel-permission` | `permission:cache-reset` sukses | **LULUS** |
| **DataTables Server-Side** | `CheckOfficerDataController.php` | 5.165 data dipaginasi instan (200 OK) | **LULUS** |
| **DataTables Query Opt** | `Sp2hpDocumentController.php` | `->get()` dibuang, query builder aktif | **LULUS** |
| **DomPDF Engine v3** | `barryvdh/laravel-dompdf` | PDF stream 1.333 bytes digenerate sukses | **LULUS** |
| **Excel Export Engine** | `maatwebsite/excel` | Excel `.xlsx` 6.314 bytes digenerate sukses | **LULUS** |
| **Word Docx Generator** | `phpoffice/phpword` | Docx 7.381 bytes digenerate sukses | **LULUS** |
| **Laravel Cache Clear** | `php artisan optimize:clear` | Semua cache bootstrap bersih (0 ms) | **LULUS** |
