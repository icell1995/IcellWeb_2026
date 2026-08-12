# Dokumentasi Modul Baru ICELL 2025

Dokumentasi ini merangkum dua modul utama yang baru saja diimplementasikan/diperbarui pada sistem ICELL: **Monitor Integrasi Log** dan **Dashboard Lomba ICELL 2025**.

---

## 1. Monitor Integrasi Log (CMS)

Modul ini berfungsi untuk memantau status sinkronisasi data antara aplikasi ICELL dengan aplikasi eksternal secara *real-time*.

### Informasi Teknis
- **Route:** `/cms/integration-monitor`
- **Controller:** `app/Http/Controllers/CMS/IntegrationMonitorController.php`
- **View:** `resources/views/cms/integration-monitor/index.blade.php`

### Fitur Utama
- **Status Koneksi:** Menampilkan status *online/offline* dan waktu sinkronisasi terakhir untuk 4 aplikasi:
    - TAR Korlantas
    - IRSMS Korlantas
    - Divtik Polri
    - EMP Bareskrim
- **Filter Berdasarkan Periode:** 
    - Harian (Default)
    - Mingguan
    - Bulanan
    - Custom Date (via Datepicker)
- **Tabel Log Detail:** Menampilkan riwayat transaksi data termasuk jumlah data yang berhasil/gagal ditarik, pesan error (jika ada), dan status final.
- **Export Data:** Mendukung export log ke format Excel, PDF, dan Print untuk kebutuhan pelaporan.

---

## 2. Dashboard Lomba ICELL 2025

Dashboard kompetitif mandiri yang menampilkan performa penyelesaian perkara (Selra) antar Polda di seluruh Indonesia untuk tahun periode 2025.

### Informasi Teknis
- **Route:** `/dashboardlombaicell`
- **Controller:** `app/Http/Controllers/LombaDashboardController.php`
- **View:** `resources/views/lomba-dashboard/index.blade.php`

### Fitur Utama
- **Tampilan Fullscreen:** Halaman mandiri tanpa *sidebar* atau *header* utama aplikasi agar fokus pada penyajian data (mirip dashboard monitoring TV).
- **Pembagian Kategori (Tabs):** Data dipisahkan secara otomatis berdasarkan jumlah kecelakaan per tahun:
    - **Kategori 1:** Polda dengan jumlah Laka > 5000 / tahun.
    - **Kategori 2:** Polda dengan jumlah Laka 1500 - 5000 / tahun.
    - **Kategori 3:** Polda dengan jumlah Laka < 1500 / tahun.
- **Sticky Header & Tabs:** Header judul dan tombol navigasi kategori tetap menempel di atas layar saat pengguna melakukan *scrolling*.
- **Logika Penilaian Bobot:** Menampilkan perhitungan persentase bobot penyelesaian perkara berdasarkan parameter:
    - P21 (Bobot: 6)
    - SP3 (Bobot: 2)
    - Diversi (Bobot: 2)
    - SP2LID (Bobot: 1)
- **Running Text (Marquee):** Banner pengumuman dinamis di bagian atas layar.
- **Pewarnaan Polda:** Setiap baris Polda memiliki identitas warna yang konsisten sesuai dengan standar visual sistem utama.

---

### Catatan Penggunaan
- Modul **Monitor Integrasi** dapat diakses melalui menu CMS oleh admin yang memiliki akses.
- Modul **Dashboard Lomba** dapat diakses langsung melalui link `/dashboardlombaicell` atau melalui tombol navigasi yang tersedia di Beranda utama.
