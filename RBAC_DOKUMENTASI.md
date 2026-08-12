# Dokumentasi RBAC (Role-Based Access Control) — iCell

> **Bahasa:** Indonesia  
> **Tanggal dibuat:** Juni 2026  
> **Status:** Audit menyeluruh — ditemukan banyak ketidakkonsistenan

---

## Daftar Isi

1. [Gambaran Umum Arsitektur](#1-gambaran-umum-arsitektur)
2. [Struktur Database RBAC](#2-struktur-database-rbac)
3. [Model yang Terlibat](#3-model-yang-terlibat)
4. [Daftar Role yang Ada](#4-daftar-role-yang-ada)
5. [Daftar Permission (Hak Akses)](#5-daftar-permission-hak-akses)
6. [Middleware yang Digunakan](#6-middleware-yang-digunakan)
7. [Gate dan AuthServiceProvider](#7-gate-dan-authserviceprovider)
8. [Penerapan di Routes (web.php)](#8-penerapan-di-routes-webphp)
9. [Filter by Role di Controller](#9-filter-by-role-di-controller)
10. [Masalah dan Ketidakkonsistenan yang Ditemukan](#10-masalah-dan-ketidakkonsistenan-yang-ditemukan)
11. [Ringkasan Alur RBAC Saat Ini](#11-ringkasan-alur-rbac-saat-ini)
12. [Rekomendasi Perbaikan](#12-rekomendasi-perbaikan-prioritas)

---

## 1. Gambaran Umum Arsitektur

Sistem RBAC di iCell menggunakan **dua mekanisme** yang berbeda sekaligus dan berjalan berdampingan (hybrid), yaitu:

| Mekanisme | Cara Kerja | Digunakan Di |
|-----------|-----------|--------------|
| **Permission-based (Gate `can:`)** | Cek permission string pada tabel `permissions` lewat pivot `permission_role` | Routes `web.php`, beberapa controller |
| **Role ID Hardcoded** | Cek langsung nilai `users.role_id` dengan `switch` atau `if` | Banyak controller (OfficerController, statistikaController, dsb.) |

Selain itu, ditemukan sisa kode lama yang menggunakan library pihak ketiga **Cartalyst Sentinel** yang sudah tidak digunakan namun belum dihapus sepenuhnya dari beberapa controller.

---

## 2. Struktur Database RBAC

### Tabel Utama

```
lib.roles
├── id              (integer, primary key, manual increment)
├── name            (varchar 100)
├── description     (text, nullable)
├── level           (integer, default 4) ← ditambahkan April 2026
├── code            (varchar, nullable)
├── full_name       (varchar, nullable)
├── sort            (bigint, default 0)
├── is_active       (boolean, default true)
├── state           (integer)
├── deleted_at      (soft delete)
├── created_at
└── updated_at
```

```
permissions
├── id              (auto-increment)
├── name            (varchar 100) ← format: "modul.AKSI" (misal: personnel.R)
├── state           (integer)
├── created_at
└── updated_at
```

```
permission_role  (tabel pivot)
├── role_id         (FK → lib.roles.id)
└── permission_id   (FK → permissions.id)
```

```
public.users
├── id
├── role_id         (FK → lib.roles.id) ← kolom kunci RBAC
├── polda_id
├── polres_id
├── police_id
└── ...
```

> **Catatan Skema:** Tabel `roles` dipindahkan dari skema `public` ke `lib` pada migrasi `2023_07_14_222105`. Sedangkan `permissions` dan `permission_role` tetap di skema `public`.

### Kolom `level` di `lib.roles`

Kolom `level` ditambahkan pada **April 2026** (migrasi `2026_04_07_095053`). Ini merupakan penambahan terbaru untuk menggantikan pendekatan ID-hardcoded, namun implementasinya **belum sepenuhnya diterapkan** ke seluruh codebase.

---

## 3. Model yang Terlibat

### `App\Models\User`

- Relasi `belongsTo` ke `App\Models\Lib\Role` via kolom `role_id`
- Memiliki 5 scope query berdasarkan role_id hardcoded:
  - `scopeIsAdminHeadquarter` → `role_id = 1`
  - `scopeIsAdminPolda` → `role_id = 2`
  - `scopeIsAdminPolice` → `role_id = 3`
  - `scopeIsOfficerPolice` → `role_id = 4`
  - `scopeIsSignatoryPolice` → `role_id = 5`
- Method `hasPermission($permission)` dengan **in-request caching** via `$permissionCache` property

> ⚠️ **Catatan:** Ada **import yang tidak dipakai** di baris 11: `use App\Http\Middleware\AuthorityLevel;` — middleware ini bahkan tidak ada filenya!

### `App\Models\Lib\Role`

- Table: `lib.roles`
- `public $incrementing = false` — ID dikelola secara manual (rawan race condition)
- Relasi `belongsToMany` ke `Permission` via tabel pivot `permission_role`
- Ada dua method yang identik: `permissions()` dan `allRolePermissions()`

### `App\Models\Permission`

- Table: `permissions`
- Relasi `belongsToMany` ke `Role` via `permission_role`

### `App\Models\RolePermission`

- Table: `permission_role`
- Fillable: `role_id`, `permission_id`
- Kolom `have_access` sudah di-comment — sisa dari sistem lama

---

## 4. Daftar Role yang Ada

Berdasarkan kode dan seeder:

| ID | Nama | Level | Deskripsi |
|----|------|-------|-----------|
| 1  | Level 1 / Korlantas | — | Admin Korlantas / Super Admin |
| 2  | Helpdesk | 2 | Built-in, tidak dapat dimodifikasi. Akses penuh kecuali dokumen TTE & Persetujuan |
| 3  | Level 3 | — | Admin Polres |
| 4  | Level 4 | — | Officer/Penyidik Polres |
| 5  | Level 5 | — | Signatory/Penandatangan |
| 6  | Level 6 | — | Terdaftar di seeder, belum ada deskripsi jelas |

**Catatan Penting:**
- Role Level 2 (Helpdesk) adalah satu-satunya role yang **terikat pada kolom `level`** secara eksplisit
- Role Level 2 tidak dapat diedit melalui UI (`RoleNewController::edit()` redirect jika `level === 2`)
- Role lainnya masih direferensikan langsung via `role_id` hardcoded di controller

---

## 5. Daftar Permission (Hak Akses)

Format permission: **`modul.AKSI`**

| Kode Aksi | Arti |
|-----------|------|
| `R`  | Read / Baca |
| `C`  | Create / Tambah |
| `U`  | Update / Edit |
| `D`  | Delete / Hapus |
| `E`  | Export |
| `I`  | Import |
| `DN` | Download |
| `UP` | Upload |

### Daftar Lengkap Modul (dari `RolePermissionSeeder`):

**Anggota:**
- `personnel.R`, `personnel.C`, `personnel.U`
- `signatories.R`, `certification.R`

**Manajemen Akses:**
- `permission.R`
- `role.R`, `role.C`, `role.U`

**Laporan / Kasus:**
- `accident.R`, `accident.U`
- `case.R`, `case.U`, `case.E`
- `productivity.R` _(di-OR dengan `productivity-lp.R` di Gate)_
- `productivity-lp.R`, `productivity-lp.C`, `productivity-lp.U`, `productivity-lp.D`
- `recap.R`

**Dokumen TTE & Persetujuan:**
- `document-signature-verif.R`, `document-signature-verif.U`
- `document-signature.R`, `document-signature.U`
- `document-approval.R`, `document-approval.U`
- `document-approval-upload.R`, `document-approval-upload.U`, `document-approval-upload.UP`

**Statistika / Anev:**
- `statistics.R`, `statistics.E`
- `report-individu.R`
- `anev.R`, `anev.E`

**DPO / DPB:**
- `dpo.R`, `dpb.R`

**Kategori & Wilayah:**
- `category-info.R`, `territory.R`
- `organization.R`, `organization.D`
- `tutorial.R`
- `commander-wish.R`, `commander-wish.E`, `commander-wish.DN`

**Katalog Daftar Laka:**
- `catalog-pangkat.R`, `catalog-kerusakan.R`, `catalog-kondisi-cahaya.R`
- `catalog-pendidikan.R`, `catalog-pengaturan-simpang.R`
- `catalog-tipe-kecelakaan.R`, `catalog-titik-acuan.R`

**Katalog Dokumen:**
- `catalog-saksi.R`, `catalog-tersangka.R`, `catalog-penahanan.R`
- `catalog-penggeledahan.R`, `catalog-penyitaan.R`, `catalog-penyegelan.R`
- `catalog-labfor.R`, `catalog-rekening-bank.R`, `catalog-dpo-dpb.R`
- `catalog-polda.R`, `catalog-polres.R`

**CMS & Integrasi:**
- `cms.R`, `cms.C`, `cms.U`, `cms.D`, `cms.I`, `cms.E`, `cms.DN`, `cms.UP`
- `irsms.R`

---

## 6. Middleware yang Digunakan

Terdaftar di `app/Http/Kernel.php`:

```
'prevent-back-history'           → PreventBackHistory
'is-forms-confirmation-complete' → IsFormsConfirmationComplete
'document-access'                → DocumentAccessMiddleware
'api-auth'                       → ApiAuthMiddleware
'is-administrator'               → IsAdministratorMiddleware
'is-signatory'                   → IsSignatoryMiddleware
'is-evaluation-form-filled'      → IsEvaluationFormFilledMiddleware
```

### Detail Setiap Middleware

**`IsAdministratorMiddleware`**
- Hanya mengizinkan `role_id == 1`
- ⚠️ **Tidak dipakai di route manapun** — dead code

**`IsSignatoryMiddleware`**
- Mendelegasikan pengecekan ke Gate `esign-document`.
- Jika pengguna berhak TTE (Gate allows) tetapi belum memiliki passphrase, pengguna akan diarahkan (redirect) ke halaman konfirmasi e-signature.

**`DocumentAccessMiddleware`**
- Bukan cek role, tapi validasi parameter `accident_id` di URL

**`IsFormsConfirmationComplete`**
- Cek apakah user sudah mengisi form evaluasi/konfirmasi

---

## 7. Gate dan AuthServiceProvider

File: `app/Providers/AuthServiceProvider.php`

### Mekanisme Utama: `Gate::before`

```php
Gate::before(function ($user, $ability) {
    if ($user && $user->hasPermission($ability)) {
        return true;
    }
});
```

Ini adalah **pintu utama** RBAC. Setiap `can:namaPermission` di routes melewati sini, yang memanggil `$user->hasPermission($ability)`.

### Gate Khusus

**`productivity.R` — Gate OR:**
```php
return $user->hasPermission('productivity.R')
    || $user->hasPermission('productivity-lp.R');
```

**`organization.R` — Gate OR:**
```php
return $user->hasPermission('organization.R')
    || $user->hasPermission('organization.D');
```

**`viewPulse`:**
```php
return $user->hasPermission('pulse.R');
```

**`esign-document` — Validasi Pejabat TTE:**
Pengecekan hak penandatanganan dokumen TTE (class = SIGNATORY, jabatan & cluster `is_can_signatory == true`).
```php
$officer = $user->officer;
if (!$officer || $officer->class !== 'SIGNATORY' || !isset($officer->position->positionCluster)) {
    return false;
}
$positionCluster = $officer->position->positionCluster;
return $positionCluster->is_can_signatory == true 
    && isset($officer->position->is_can_signatory) 
    && $officer->position->is_can_signatory == true;
```

**`can-entry-document` — Validasi Input Dokumen:**
Mengecek apakah user (khususnya Admin Polres) diperbolehkan menginput dokumen ke dalam sistem.
```php
if ($user->role_id == 3 && !empty($user->polres_id) && $user->polres_id != 0) {
    return (bool)($user->properties['is_can_entry_document'] ?? false);
}
return true;
```

---

## 8. Penerapan di Routes (web.php)

### Hierarki Middleware

```
[semua request]
  └── auth, prevent-back-history
        └── is-evaluation-form-filled, is-signatory
              ├── can:permission.R       → /permission/*
              ├── can:role.R             → /role/*, /role-new/*
              ├── can:personnel.R        → /pengguna/*, /petugas/*, /signatories/*, /personnel/*
              │     ├── can:personnel.C  → tambah data
              │     └── can:personnel.U  → edit/hapus data
              ├── can:catalog-*.R        → berbagai resource katalog
              ├── can:productivity.R     → 100+ resource dokumen PDF/Word
              ├── can:accident.R         → /accident/*
              ├── can:statistics.R       → /statistika/*
              ├── can:recap.R            → /rekap/*
              ├── can:territory.R        → /wilayah/*
              ├── can:organization.R     → /organisasi/*
              ├── can:dpo.R              → /dpo/*
              ├── can:dpb.R              → /dpb/*
              └── (TANPA permission)     → /caraousel/*, /commander-wish/*,
                                           /anggota/*, /document-action/*,
                                           /document-signature/*, /document-approval/*
```

> ⚠️ **MASALAH KRITIS:** Route-route berikut **TIDAK dilindungi permission** manapun — semua user yang login bisa akses:
> - `/caraousel/*` — pengelolaan carousel gambar
> - `/commander-wish/*` — fitur Commander Wish
> - `/anggota/*` — daftar anggota
> - `/document-action/*` — aksi dokumen (request approval, upload)
> - `/document-signature/*` — penandatanganan dokumen
> - `/document-approval/*` — persetujuan dokumen

---

## 9. Filter by Role di Controller

Selain permission Gate, banyak controller melakukan **filter data berdasarkan `role_id`** langsung:

### `OfficerController.php` (4 switch statement)
```
role_id = 2 → tampilkan officer di polda sendiri saja
role_id = 3 → tampilkan officer di polres sendiri saja
default     → tampilkan semua officer
```

### `statistikaController.php` (3 fungsi)
```
role_id = 2         → Polda & Polres milik user
role_id = 3,4,5     → Polres milik user
default             → semua Polda & Polres
```

### `PersonnelController.php` (6+ kondisi)
```
role_id = 3 → filter by polres
role_id = 5 → filter by signatory
```

### `CommanderWishController.php` ⚠️
```php
// BERBAHAYA: masih pakai Sentinel yang sudah tidak aktif!
$roleData = Sentinel::getUser()->role_id;
if ($roleData > 2) {
    // blok dengan JS alert + redirect — cara yang sangat tidak proper
}
```

### Controller Lainnya yang Menggunakan role_id
- `KPolresController.php` — switch by role_id
- `ExportRekapController.php` — filter export by role_id
- `CMS/CheckOfficerDigitalSignatureController.php` — cek role_id 1 dan 3
- `CMS/TicketingController.php` — filter `assigned_to` hanya role_id=1
- `api/LoginController.php` — response data berbeda per role_id
- `api/AccidentRecentController.php` — filter data per role_id
- `api/PolresController.php` — filter data per role_id
- `api/PoldaController.php` — filter data per role_id

---

## 10. Masalah dan Ketidakkonsistenan yang Ditemukan

### ❌ KRITIS

**1. Dua Sistem RBAC Berjalan Bersamaan**
- Routes: `can:permission.R` (permission-based)
- Controllers: `switch($user->role_id)` (role ID hardcoded)
- Tidak terpusat, sulit dipelihara

**2. Route Sensitif Tidak Dilindungi Permission**
- `/document-action/*`, `/document-signature/*`, `/document-approval/*` hanya perlu login
- Semua user yang login bisa akses tanpa batasan role

**3. Penggunaan `Sentinel` yang Sudah Ditinggalkan**
- `CommanderWishController.php` baris 501, 514
- `KPolresController.php` baris 304
- `Authority.php` baris 113
- Berisiko crash jika Sentinel tidak ter-install/ter-boot

**4. `Authority.php` Sudah Tidak Relevan**
- Menggunakan Sentinel
- Mereferensi kolom pivot `have_access` yang sudah tidak ada di skema
- Mereferensi class `App\Role` dan `App\Permission` yang tidak ada di namespace yang benar

**5. `IsAdministratorMiddleware` Tidak Dipakai**
- Terdaftar di Kernel.php tapi tidak digunakan di route manapun — dead code

**6. Import `AuthorityLevel` di `User.php` — File Tidak Ada**
- `use App\Http\Middleware\AuthorityLevel;` di baris 11
- File middleware ini tidak ada di direktori Middleware

**7. Race Condition pada Pembuatan Role**
- `RoleNewController::store()` pakai `DB::max('id') + 1`
- Tidak thread-safe, rawan konflik ID

**8. `RoleSeeder.php` Tidak Sinkron**
- Membuat role tanpa kolom `level` yang sudah ada di skema

### ⚠️ SEDANG

**9. Magic Numbers `role_id` Tersebar di Codebase**
- Nilai 1, 2, 3, 4, 5 tersebar di 10+ file tanpa konstanta
- File yang mengandung hardcoded role_id:
  - `OfficerController.php` (4 tempat)
  - `statistikaController.php` (3 tempat)
  - `PersonnelController.php` (6+ tempat)
  - `AuthServiceProvider.php` (Gate viewPulse)
  - `CheckOfficerDigitalSignatureController.php` (3 tempat)
  - Dan masih banyak lagi...

**10. Duplikasi Route `role` dan `role-new`**
- `/role/*` → `RoleController` (versi lama, banyak komentar)
- `/role-new/*` → `RoleNewController` (versi baru)
- Hidup berdampingan, membingungkan

**11. `RoleController.php` Penuh Komentar (>50% kode)**
- Sisa sistem lama berbasis `have_access`, `authority_read`, dll.

**12. Gate `viewPulse` Inkonsisten**
- Satu-satunya Gate yang cek `role_id` langsung, bukan permission string

**13. Route dengan Nama Angka Tidak Bermakna**
```php
Route::get('/2', ...)->name('2');
Route::get('/3', ...)->name('3');
```

### 📋 MINOR

**14. Komentar Kode Lama di `User.php` dan `Role.php`**
- Banyak komentar fungsi lama yang sudah tidak dipakai

**15. `PermissionsRoleSeeder.php` vs `RolePermissionSeeder.php`**
- Kemungkinan duplikat seeder, salah satunya sudah usang

---

## 11. Ringkasan Alur RBAC Saat Ini

### Alur untuk Route dengan `can:permission.X`

```
Request masuk
    ↓
Middleware: auth (cek login)
    ↓
Middleware: is-evaluation-form-filled
    ↓
Middleware: is-signatory
    ↓
Gate: can:namaPermission
    ↓
Gate::before() → $user->hasPermission('namaPermission')
    ↓
Query ke DB: ambil semua permission milik role_id user
    ↓
in_array('namaPermission', $permissionCache)
    ↓ true              ↓ false
  Lanjut ke Controller  403 Forbidden
```

### Alur untuk Route Tanpa `can:` (hardcoded di Controller)

```
Request masuk
    ↓
Middleware: auth (cek login)
    ↓
Controller::method()
    ↓
$user = Auth::user()
switch ($user->role_id) {
    case 1: akses penuh ke semua data
    case 2: filter by polda_id user
    case 3: filter by polres_id user
    default: tergantung controller
}
    ↓
Data yang dikembalikan disesuaikan dengan role
```

---

## 12. Rekomendasi Perbaikan (Prioritas)

### Prioritas Tinggi

1. **Hapus `Sentinel` sepenuhnya** dari `CommanderWishController`, `KPolresController`, dan `Authority.php`. Ganti dengan `Auth::user()`.

2. **Lindungi route dokumen sensitif** dengan permission:
   - `/document-action/*` → tambahkan `can:document-action.R`
   - `/document-signature/*` → tambahkan `can:document-signature.R`
   - `/document-approval/*` → tambahkan `can:document-approval.R`

3. **Buat konstanta untuk role_id** agar tidak ada magic number:
   ```php
   // app/Enums/RoleLevel.php
   const ADMIN_KORLANTAS = 1;
   const HELPDESK = 2;
   const ADMIN_POLRES = 3;
   const OFFICER = 4;
   const SIGNATORY = 5;
   ```

4. **Hapus atau refactor `Authority.php`** — class ini sudah tidak relevan.

5. **Hapus import `AuthorityLevel`** dari `User.php` (file tidak ada).

### Prioritas Sedang

6. **Konsolidasi** `RoleController` dan `RoleNewController` menjadi satu controller bersih.

7. **Ganti `max('id') + 1`** dengan PostgreSQL sequence atau UUID untuk pembuatan role.

8. **Tambahkan permission** untuk fitur yang saat ini filter by role_id di controller, agar sistem lebih konsisten.

9. **Hapus `IsAdministratorMiddleware`** atau terapkan di route yang sesuai.

### Prioritas Rendah

10. Bersihkan komentar kode lama di `RoleController.php` dan `User.php`.
11. Berikan nama yang bermakna pada route `/2`, `/3`, `/4`, `/5`.
12. Sinkronkan `RoleSeeder.php` untuk selalu menyertakan kolom `level`.
13. Hapus atau merge `PermissionsRoleSeeder.php` yang kemungkinan duplikat.

---

*Dokumen ini dibuat berdasarkan audit menyeluruh pada codebase per Juni 2026.*
