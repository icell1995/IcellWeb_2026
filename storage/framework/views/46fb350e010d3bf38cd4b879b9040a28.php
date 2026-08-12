<!-- filepath: /Users/asepsyaepul/Documents/GitHub/Icell_new/resources/views/components/login-instruction-modal.blade.php -->
<div class="modal fade" id="loginInstructionModal" tabindex="-1" aria-labelledby="loginInstructionModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title" id="loginInstructionModalLabel">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Panduan Login ICELL
                </h5>
            </div>
            <div class="modal-body border-0">
                <!-- ICELL Login Guide -->
                <h6 class="fw-bold mb-3">Login ICELL</h6>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="text-center mb-3">
                            <i class="bi bi-person-badge fs-1 text-primary"></i>
                        </div>
                        <h6 class="text-center fw-bold">1. Masukkan Kredensial</h6>
                        <p class="text-center small">
                            Gunakan nama pengguna dan kata sandi yang telah diberikan oleh administrator sistem.
                        </p>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="text-center mb-3">
                            <i class="bi bi-shield-check fs-1 text-primary"></i>
                        </div>
                        <h6 class="text-center fw-bold">2. Verifikasi OTP</h6>
                        <p class="text-center small">
                            Kode OTP akan dikirim ke Email Polri Anda. Masukkan 6 digit kode untuk melanjutkan.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-laptop fs-1 text-primary"></i>
                        </div>
                        <h6 class="text-center fw-bold">3. Akses Sistem</h6>
                        <p class="text-center small">
                            Setelah terverifikasi, Anda akan dialihkan ke dashboard sesuai hak akses.
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Email Polri Login Guide -->
                <h6 class="fw-bold mb-3">Login Email Polri</h6>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="text-center mb-3">
                            <i class="bi bi-globe fs-1 text-success"></i>
                        </div>
                        <h6 class="text-center fw-bold">1. Buka Link Email Polri</h6>
                        <p class="text-center small">
                            Kunjungi <a href="https://mail.polri.go.id" target="_blank">mail.polri.go.id</a> melalui
                            browser Anda.
                        </p>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="text-center mb-3">
                            <i class="bi bi-envelope-fill fs-1 text-success"></i>
                        </div>
                        <h6 class="text-center fw-bold">2. Masukkan Kredensial</h6>
                        <p class="text-center small">
                            Masukkan email dan kata sandi yang telah diberikan kepada Anda.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        </div>
                        <h6 class="text-center fw-bold">3. Akses Email</h6>
                        <p class="text-center small">
                            Setelah berhasil login, Anda akan diarahkan ke halaman utama email.
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <div class="alert alert-warning">
                    <h6 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Penting:</h6>
                    <ul class="mb-0">
                        <li>Pastikan anda mengubah password email polri secara berkala</li>
                        <li>Jangan bagikan kredensial dan kode OTP kepada siapapun</li>
                        <li>Gunakan koneksi internet yang stabil untuk menghindari kegagalan verifikasi</li>
                        <li>Jika terjadi kendala email polri, Silahkan hubungi administrator sistem melalui <a
                                href="https://wa.me/6281119607917" target="_blank" class="alert-link"><i
                                    class="bi bi-whatsapp"></i> WhatsApp : DIVTIK Mabes Polri</a></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-dismiss="modal">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/am/Jobs/IcellWeb_2026/resources/views/components/login-instruction-modal.blade.php ENDPATH**/ ?>