document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk mengecek apakah modal harus ditampilkan
    function shouldShowModal() {
        // Cek apakah user telah melihat modal (klik Saya Mengerti)
        const viewedDate = localStorage.getItem('loginInstructionsViewedDate');

        // Jika belum pernah melihat, selalu tampilkan
        if (!viewedDate) {
            return true;
        }

        // Jika sudah melihat, cek apakah sudah lewat 1 minggu
        const lastViewed = new Date(viewedDate);
        const currentDate = new Date();
        const oneWeekInMs = 7 * 24 * 60 * 60 * 1000;

        // Jika belum 1 minggu, tampilkan lagi
        if ((currentDate - lastViewed) < oneWeekInMs) {
            return true;
        }

        // Jika sudah 1 minggu, jangan tampilkan lagi
        return false;
    }

    // Tampilkan modal jika diperlukan
    if (shouldShowModal()) {
        setTimeout(function () {
            var loginInstructionModal = new bootstrap.Modal(document.getElementById('loginInstructionModal'));
            loginInstructionModal.show();
        }, 800); // Delay 800ms agar tidak langsung muncul saat loading
    }



    // Tambahkan event listener untuk tombol "Saya Mengerti"
    document.querySelector('#loginInstructionModal button[data-bs-dismiss="modal"]').addEventListener('click', function () {
        // Simpan tanggal saat pengguna menekan "Saya Mengerti"
        localStorage.setItem('loginInstructionsViewedDate', new Date().toISOString());
        console.log('Modal ditutup, tanggal disimpan:', new Date().toISOString());
    });

    // Tambahkan opsi reset (untuk testing)
    // Bisa diakses via console: resetLoginInstructions()
    window.resetLoginInstructions = function () {
        localStorage.removeItem('loginInstructionsViewedDate');
        alert('Login instructions reset. Modal will appear on next page load.');
    }
});