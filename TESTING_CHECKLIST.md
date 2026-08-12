# 📋 Lembar Uji Coba Komprehensif RBAC & Aturan Ekspektasi — iCell

Dokumen ini berisi panduan pengujian terperinci (100% modul dan halaman) untuk memverifikasi bahwa sistem pembatasan menu, tombol aksi ekspor/CRUD, proteksi rute, dan ekspektasi aturan bisnis berjalan dengan sempurna.

---

## 🛠️ Persiapan Pengujian (0%)
Sebelum memulai, pastikan database dalam keadaan bersih dan permission ter-sinkronisasi:
- [ ] Jalankan seeder permission di terminal: `php artisan db:seed --class=RolePermissionSeeder`
- [ ] Pastikan Anda login dengan akun **Helpdesk** (Role ID = 2) untuk mengelola konfigurasi hak akses role lainnya.

---

## 1. Uji Coba Aturan Bisnis Khusus (Rules Ekspektasi)

### A. Validasi TTE Kasat (Pendidikan Last Schooling)
Menguji aturan: Kasat (Level 5) bisa TTE, tetapi jika pendidikannya di bawah S1 (ID < 8), ia tidak boleh didaftarkan TTE.
- [ ] Login sebagai **Helpdesk** / **Admin**.
- [ ] Buka halaman **Anggota → Tambah Personel** (atau Edit Personel).
- [ ] Pilih Jabatan: **Kasat** / Jabatan level 5.
- [ ] Pada dropdown pendidikan terakhir, pilih **SMA / D3** (ID < 8).
  - *Ekspektasi:* Checkbox *"Daftarkan Pejabat TTE"* otomatis hilang/uncheck.
- [ ] Pada dropdown pendidikan terakhir, pilih **S1 / S2** (ID >= 8).
  - *Ekspektasi:* Checkbox *"Daftarkan Pejabat TTE"* muncul kembali.
- [ ] Coba paksa kirim request (bypass frontend) dengan status TTE aktif menggunakan pendidikan < S1.
  - *Ekspektasi:* Backend ([PersonnelController.php](file:///Users/am/Jobs/Icell_new/app/Http/Controllers/PersonnelController.php)) mengubah paksa status class menjadi `MEMBER` (bukan `SIGNATORY`).

### B. Pembatasan Tambah Dokumen untuk Polres (canEntryDocument)
Menguji aturan: Polres (Role ID 3 + Polres Satker) secara default tidak bisa menambah dokumen kecuali dicentang `properties->is_can_entry_document` saat penambahan anggota.
- [ ] Buat user dengan **Role ID = 3** dan pilih Satker tingkat **Polres**.
- [ ] **KASUS 1 (Unchecked):** Jangan centang opsi **Izin Tambah Dokumen** pada profil anggota tersebut.
  - [ ] Login sebagai user Polres tersebut.
  - [ ] Masuk ke detail kasus / produktivitas perkara.
    - *Ekspektasi:* Tombol **Tambah Dokumen** dan **Tambah Terlapor** tersembunyi (tidak dirender).
  - [ ] Buka langsung URL `/document/create` atau rute store terkait.
    - *Ekspektasi:* Terjadi redirect dengan pesan error *"Anda tidak memiliki akses untuk menambah dokumen."* (403).
- [ ] **KASUS 2 (Checked):** Login kembali sebagai Helpdesk, buka profil anggota tersebut dan centang opsi **Izin Tambah Dokumen**.
  - [ ] Login kembali sebagai user Polres tersebut.
  - [ ] Buka detail kasus / produktivitas perkara.
    - *Ekspektasi:* Tombol **Tambah Dokumen** dan **Tambah Terlapor** muncul dan dapat diklik dengan normal.

### C. Aturan Proteksi Penghapusan & Duplikasi Role
- [ ] Masuk ke **Manajemen Akses → Role** (`/role-new`).
- [ ] Coba hapus role bawaan sistem (ID 1 sampai 5).
  - *Ekspektasi:* Tombol Hapus tidak dirender, dan rute backend menolaknya dengan pesan error *"Role default tidak boleh dihapus."*
- [ ] Coba buat role baru dengan nama yang sama dengan role yang sudah ada (misal: "administrator" atau "HELPDESK" - case insensitive).
  - *Ekspektasi:* Sistem menolak dan menampilkan pesan error validasi bahwa nama role sudah digunakan.

---

## 2. Pengujian Otorisasi Sidebar Menu (Akses Baca `.R`)
Buat role khusus (misal: "TEST-ROLE") melalui akun Helpdesk. Untuk setiap item berikut, uji dengan **mencentang** dan **tidak mencentang** permission terkait, lalu verifikasi tampilan sidebar pada user dengan role tersebut:

### A. Modul Anggota & Personel
- [ ] **Personel (`personnel.R`):**
  - [ ] Centang: Menu sidebar **Anggota → Personel** muncul.
  - [ ] Uncheck: Menu sidebar **Anggota → Personel** tersembunyi.
- [ ] **Pejabat TTE (`signatories.R`):**
  - [ ] Centang: Menu sidebar **Anggota → Pejabat TTE** muncul.
  - [ ] Uncheck: Menu sidebar **Anggota → Pejabat TTE** tersembunyi.
- [ ] **Sertifikasi Personel (`certification.R`):**
  - [ ] Centang: Menu sidebar **Anggota → Sertifikasi** muncul.
  - [ ] Uncheck: Menu sidebar **Anggota → Sertifikasi** tersembunyi.

### B. Modul Laporan
- [ ] **Register Perkara Laka (`accident.R`):**
  - [ ] Centang: Menu sidebar **Laporan → Register Perkara Laka** muncul.
  - [ ] Uncheck: Menu **Laporan → Register Perkara Laka** tersembunyi.
- [ ] **Register Perkara Jatanlin (`case.R`):**
  - [ ] Centang: Menu sidebar **Laporan → Register Perkara Jatanlin** muncul.
  - [ ] Uncheck: Menu **Laporan → Register Perkara Jatanlin** tersembunyi.
- [ ] **Perkara Ditangani (`productivity.R` atau `productivity-lp.R`):**
  - [ ] Centang: Menu sidebar **Laporan → Perkara Ditangani** muncul.
  - [ ] Uncheck: Menu **Laporan → Perkara Ditangani** tersembunyi.
- [ ] **Rekap (`recap.R`):**
  - [ ] Centang: Menu sidebar **Laporan → Rekap** muncul.
  - [ ] Uncheck: Menu **Laporan → Rekap** tersembunyi.

### C. Modul TTE & Persetujuan Dokumen
- [ ] **Tanda Tangan TTE Verifikasi (`document-signature-verif.R`):**
  - [ ] Centang: Menu sidebar **Tanda Tangan Dokumen TTE (Verifikasi)** muncul.
  - [ ] Uncheck: Menu **Tanda Tangan Dokumen TTE (Verifikasi)** tersembunyi.
- [ ] **Tanda Tangan TTE (`document-signature.R`):**
  - [ ] Centang: Menu sidebar **Tanda Tangan Dokumen TTE** muncul.
  - [ ] Uncheck: Menu **Tanda Tangan Dokumen TTE** tersembunyi.
- [ ] **Persetujuan Dokumen (`document-approval.R`):**
  - [ ] Centang: Menu sidebar **Persetujuan Dokumen** muncul.
  - [ ] Uncheck: Menu **Persetujuan Dokumen** tersembunyi.
- [ ] **Persetujuan Dokumen Upload (`document-approval-upload.R`):**
  - [ ] Centang: Menu sidebar **Persetujuan Dokumen (Upload)** muncul.
  - [ ] Uncheck: Menu **Persetujuan Dokumen (Upload)** tersembunyi.

### D. Modul Statistika & Anev
- [ ] **Statistika Bulanan/Mingguan/Harian (`statistics.R`):**
  - [ ] Centang: Sub-menu **Statistika Bulanan, Mingguan, Harian** muncul.
  - [ ] Uncheck: Sub-menu **Statistika Bulanan, Mingguan, Harian** tersembunyi.
- [ ] **Laporan Individu (`report-individu.R`):**
  - [ ] Centang: Sub-menu **Laporan Individu** muncul.
  - [ ] Uncheck: Sub-menu **Laporan Individu** tersembunyi.
- [ ] **Anev (`anev.R`):**
  - [ ] Centang: Sub-menu **Anev** muncul.
  - [ ] Uncheck: Sub-menu **Anev** tersembunyi.

### E. Modul Katalog (Daftar, Dokumen, Polda, Polres)
- [ ] **Katalog Daftar (`catalog-pangkat.R` s/d `catalog-titik-acuan.R`):**
  - [ ] Centang salah satu: Menu **Katalog → Daftar → [Item]** muncul.
  - [ ] Uncheck semua: Sub-menu **Katalog → Daftar** tersembunyi.
- [ ] **Katalog Dokumen (`catalog-saksi.R` s/d `catalog-dpo-dpb.R`):**
  - [ ] Centang salah satu: Menu **Katalog → Dokumen → [Item]** muncul.
  - [ ] Uncheck semua: Sub-menu **Katalog → Dokumen** tersembunyi.
- [ ] **Katalog Polda & Polres (`catalog-polda.R` / `catalog-polres.R`):**
  - [ ] Centang: Menu **Katalog → Polda / Polres** muncul.
  - [ ] Uncheck: Menu **Katalog → Polda / Polres** tersembunyi.

### F. CMS & Integrasi
- [ ] **CMS (`cms.R`):**
  - [ ] Centang: Menu **CMS** (merah) di bagian bawah muncul.
  - [ ] Uncheck: Menu **CMS** tersembunyi.
- [ ] **IRSMS (`irsms.R`):**
  - [ ] Centang: Menu **IRSMS** muncul.
  - [ ] Uncheck: Menu **IRSMS** tersembunyi.
- [ ] **CMS Dashboard Pulse (`pulse.R`):**
  - [ ] Centang: Masuk ke CMS, menu **Tools → Pulse** muncul di sidebar.
  - [ ] Uncheck: Masuk ke CMS, menu **Tools → Pulse** tersembunyi.

---

## 3. Pengujian Tombol Aksi Ekspor (Akses Ekspor `.E` / `.DN`)

Untuk setiap modul di bawah ini, uji dengan login sebagai user yang **memiliki izin baca (`.R`) tetapi TIDAK memiliki izin ekspor (`.E`)**, kemudian uji ulang dengan **memberikan izin ekspor (`.E`)**:

### A. Statistika Bulanan, Mingguan, Harian
- [ ] **KASUS 1 (Tanpa `statistics.E`):**
  - [ ] Buka Statistika Bulanan / Mingguan / Harian.
  - [ ] Lakukan pencarian data dengan tombol *"Cek Hasil"*.
  - *Ekspektasi:* **Tombol ekspor berwarna biru dengan label "Export data" tidak dirender di layar.**
  - [ ] Tembak langsung URL: `/statistika/ExportMonth` atau `/statistika/ExportDays`.
  - *Ekspektasi:* Diblokir dengan error **403 Forbidden**.
- [ ] **KASUS 2 (Dengan `statistics.E`):**
  - [ ] Buka kembali Statistika, lakukan pencarian data.
  - *Ekspektasi:* **Tombol ekspor muncul** dan dapat diklik untuk mengunduh Excel.

### B. Register Perkara Jatanlin
- [ ] **KASUS 1 (Tanpa `case.E`):**
  - [ ] Buka Laporan → Register Perkara Jatanlin.
  - *Ekspektasi:* DataTable memuat data kasus, tetapi **tombol tombol hijau "Excel" di atas tabel disembunyikan**.
- [ ] **KASUS 2 (Dengan `case.E`):**
  - *Ekspektasi:* **Tombol hijau "Excel" muncul** dan dapat diklik untuk mengekspor isi tabel ke Excel.

### C. Anev Perkara
- [ ] **KASUS 1 (Tanpa `anev.E`):**
  - [ ] Buka Statistika/Anev → Anev.
  - [ ] Lakukan pencarian data anev.
  - *Ekspektasi:* Tabel DataTable Anev muncul, tetapi **tombol merah "Copy" dan tombol hijau "Excel" di atas DataTable disembunyikan**.
- [ ] **KASUS 2 (Dengan `anev.E`):**
  - *Ekspektasi:* **Tombol "Copy" dan "Excel" muncul** di atas DataTable Anev.

### D. Commander Wish
- [ ] **KASUS 1 (Tanpa `commander-wish.E`):**
  - [ ] Buka menu Commander Wish.
  - *Ekspektasi:* Tombol **"Cari"** ada, tetapi **tombol hijau "Unduh PPT" disembunyikan**.
  - [ ] Tembak langsung URL: `/commander-wish/generate-presentation`.
  - *Ekspektasi:* Diblokir dengan error **403 Forbidden**.
- [ ] **KASUS 2 (Dengan `commander-wish.E`):**
  - *Ekspektasi:* **Tombol hijau "Unduh PPT" muncul** dan berfungsi normal.

### E. CMS Solved Tickets & Request Data
- [ ] **CMS Solved Tickets:**
  - [ ] **Tanpa `cms.E`:** Tombol hijau **"Export Excel"** di atas tabel tiket solved tersembunyi. URL `/cms/solved/export` me-return error 403.
  - [ ] **Dengan `cms.E`:** Tombol **"Export Excel"** muncul dan berfungsi.
- [ ] **CMS Request Data:**
  - [ ] **Tanpa `cms.E`:** Tombol hijau **"Export Excel"** di atas tabel request-data tersembunyi. URL `/cms/request-data/export/excel` me-return error 403.
  - [ ] **Dengan `cms.E`:** Tombol **"Export Excel"** muncul dan berfungsi.

---

## 4. Pengujian Tombol Aksi Tambah, Edit, & Delete (Akses `.C` / `.U` / `.D`)

Uji dengan login sebagai user yang **memiliki izin baca (`.R`) tetapi tidak memiliki izin tulis/ubah (`.C`/`.U`/`.D`)**, kemudian uji ulang dengan memberikan izin terkait:

### A. Pengguna Aplikasi & Personel
- [ ] **Daftar Pengguna Aplikasi:**
  - [ ] **Tanpa `personnel.C`:** Tombol floating lingkaran merah muda **(+) Tambah** di kanan bawah tersembunyi.
  - [ ] **Tanpa `personnel.U`:** Kolom aksi tabel hanya menampilkan tanda dash `-` (tombol **Edit** dan **Non-aktifkan** disembunyikan).
  - [ ] **Dengan Izin:** Semua tombol muncul kembali.
- [ ] **Daftar Penyidik / Personel:**
  - [ ] **Tanpa `personnel.C`:** Tombol floating lingkaran merah muda **(+) Tambah** tersembunyi.
  - [ ] **Tanpa `personnel.U`:** Kolom aksi tabel hanya menampilkan tanda dash `-` (tombol **Edit** dan **Non-aktifkan** disembunyikan).
  - [ ] **Dengan Izin:** Semua tombol muncul kembali.

### B. Perkara Ditangani (Modul productivity & productivity-lp)
- [ ] **Tabel Dokumen Kasus:**
  - [ ] **Tanpa `productivity-lp.C`:** Tombol **"Tambah Dokumen"** disembunyikan.
  - [ ] **Tanpa `productivity-lp.U`:** Tombol **"Edit"** di kolom aksi disembunyikan.
  - [ ] **Tanpa `productivity-lp.D`:** Tombol **"Hapus"** di kolom aksi disembunyikan.
- [ ] **Tabel Partisipan Kasus (Terlapor):**
  - [ ] **Tanpa `productivity-lp.C`:** Tombol **"Tambah Terlapor"** disembunyikan.
  - [ ] **Tanpa `productivity-lp.U`:** Tombol **"Edit"** di kolom aksi disembunyikan.
  - [ ] **Tanpa `productivity-lp.D`:** Tombol **"Hapus"** di kolom aksi disembunyikan.
- [ ] **Tindakan Dokumen (Approval / Upload):**
  - [ ] **Tanpa `document-action.U`:** Tombol **"Approval"** dan **"Upload Request"** pada tabel dokumen kasus disembunyikan.

---

## 🏁 Hasil Akhir Pengujian
- **Total Modul Diuji:** 100% (Seluruh modul iCell)
- **Status Akhir:**
  - [ ] **LULUS 100%** (Tidak ada kendala, error fungsional, maupun tombol yang luput dari penyembunyian).
