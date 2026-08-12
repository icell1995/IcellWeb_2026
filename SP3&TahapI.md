# Daftar Lengkap File Deployment & Modifikasi (SP3 & Tahap 1)

Daftar ini membagi file menjadi file baru yang ditambahkan dan file sistem lama yang dimodifikasi.

## 1. File Baru yang Ditambahkan [NEW]

### Modul SP3 (Lengkap)
- `app/Http/Controllers/Docs/SuratKetetapanPenghentianPenyidikanDocumentController.php`
- `app/Models/Doc/SuratKetetapanPenghentianPenyidikanDocument/SuratKetetapanPenghentianPenyidikanDocument.php`
- `app/Models/Doc/SuratKetetapanPenghentianPenyidikanDocument/SuratKetetapanPenghentianPenyidikanDocumentOfficer.php`
- `app/Models/Doc/SuratKetetapanPenghentianPenyidikanDocument/SuratKetetapanPenghentianPenyidikanDocumentAttachment.php`
- `database/migrations/2026_01_27_000004_create_doc_surat_ketetapan_penghentian_penyidikan_documents_table.php`
- `database/migrations/2026_01_27_000005_create_doc_surat_ketetapan_penghentian_penyidikan_document_officers_table.php`
- `database/migrations/2026_01_27_000006_create_doc_surat_ketetapan_penghentian_penyidikan_document_attachments_table.php`
- `database/migrations/2026_01_27_000007_create_doc_surat_ketetapan_penghentian_penyidikan_document_suspect_pivot_table.php` (Pivot Suspect SP3)

### Modul Tahap 1 (Lengkap & Terstandarisasi)
- `app/Http/Controllers/Docs/Tahap1DocumentController.php`
- `app/Models/Doc/Tahap1Document/Tahap1Document.php`
- `app/Models/Doc/Tahap1Document/Tahap1DocumentOfficer.php`
- `app/Models/Doc/Tahap1Document/Tahap1DocumentAttachment.php`
- `app/Models/Doc/Tahap1Document/Tahap1DocumentSuspectPivot.php` (Model Pivot Suspect Tahap 1)
- `database/migrations/2026_04_21_000008_alter_and_rename_tahap_1_documents_table.php`
- `database/migrations/2026_04_21_000009_create_tahap_1_document_officers_table.php`
- `database/migrations/2026_04_21_000010_create_tahap_1_document_attachments_table.php`
- `database/migrations/2026_04_21_000011_create_tahap_1_document_suspect_pivot_table.php` (Pivot Suspect Tahap 1)
- `database/migrations/2026_04_24_101012_rename_columns_in_tahap_1_document_attachments_table.php` (Migrasi Standarisasi Akhir)

---

## 2. File Sistem yang Dimodifikasi [EDITED]

### Controllers (Update Logic & Audit Trail)
- `app/Http/Controllers/DocumentActionController.php` (Ditambahkan logic upload & audit trail (JSON) untuk modul baru)
- `app/Http/Controllers/AccidentController.php` (Deaktivasi penampilan modul SP2 Lidik lama)

### Config, Seeder & Routes
- `database/seeders/Lib/DocumentCategoriesTableSeeder.php` (Registrasi modul baru dan deaktivasi modul 0112)
- `routes/document.php` (Pendaftaran route untuk SP3 dan Tahap 1)

---

## 3. Catatan Penting Implementasi
- **Relasi Suspect**: Tahap 1 menggunakan Model Pivot khusus (`Tahap1DocumentSuspectPivot`), sedangkan SP3 menggunakan relasi `belongsToMany` standar.
- **Standarisasi Fields**: Kolom `timestamps`, `ip_addresses`, `submitted_at`, dan `released_at` kini terisi secara otomatis lewat Controller untuk validasi Pusiknas.
- **Attachment Storage**: Seluruh lampiran kini menggunakan kolom standar `path`, `name`, dan `mimetype` agar kompatibel dengan fitur view/preview global.
