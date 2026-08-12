$(document).ready(function () {
    $(".btn-burger").click(function () {
        // Mengubah tampilan sidebar dan main
        $(".sidemenu").toggleClass("active");
        $(".main-content").toggleClass("active");
    });

    $("#reset_btn").click(function () {
        // Mengatur ulang nilai-nilai formulir atau mengarahkan ke halaman yang diinginkan
        location.reload(); // Mengganti dengan window.location.href = '/halaman-reset'; jika ingin mengarahkan ke halaman tertentu setelah reset
    });

    $(".parents-menu").click(function () {
        // $('.parents-menu .dropdown-arrow').removeClass('rotate');
        // $(this).find('.dropdown-arrow').toggleClass('rotate');
        // Mengecek apakah panah dropdown dalam keadaan terputar (rotate)
        var isRotated = $(this).find(".dropdown-arrow").hasClass("rotate");

        // Menghapus kelas 'rotate' dari semua .dropdown-arrow di dalam .parents-menu
        $(".parents-menu .dropdown-arrow").removeClass("rotate");

        // Menambahkan kelas 'rotate' jika panah dropdown tidak dalam keadaan terputar sebelumnya,
        // atau menghapusnya jika sebaliknya.
        if (!isRotated) {
            $(this).find(".dropdown-arrow").addClass("rotate");
        }
    });
});
