document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        const loginSection = document.querySelector('.login-section');
        if (loginSection) {
            loginSection.classList.add('show');
        }
    }, 100);
});

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(initializeLogin, 100);
});

function initializeLogin() {
    let otpModal;
    let countdownTimer;
    let countdownSeconds = 60; // 5 menit untuk pertama kali

    function initOtpModal() {
        if (!otpModal) {
            otpModal = new bootstrap.Modal(document.getElementById('otpModal'), {
                backdrop: 'static',
                keyboard: false,
                focus: true
            });
        }
        return otpModal;
    }

    // Inisialisasi di awal
    initOtpModal();

    // Fungsi countdown awal
    function startCountdown() {
        countdownSeconds = 60; // Setel ulang ke 10 detik untuk demo kembalikan 300 untuk 5 menit
        const countdownElement = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendOtpBtn');
        const countdownContainer = document.getElementById('countdownContainer');

        // Hide resend button and show countdown container
        resendBtn.classList.add('d-none');
        resendBtn.classList.remove('show');
        resendBtn.disabled = true;
        countdownContainer.style.display = 'block';
        countdownContainer.classList.remove('countdown-finished');

        // Bersihkan timer jika sudah ada
        if (countdownTimer) {
            clearInterval(countdownTimer);
        }

        countdownTimer = setInterval(() => {
            const minutes = Math.floor(countdownSeconds / 60);
            const seconds = countdownSeconds % 60;

            const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (countdownElement) {
                countdownElement.textContent = timeString;
            } else {
                console.error("Countdown element not found!");
            }

            // Change color and animation based on remaining time
            if (countdownSeconds <= 30) {
                countdownElement.className = 'fw-bold countdown-critical';
            } else if (countdownSeconds <= 60) {
                countdownElement.className = 'fw-bold countdown-warning';
            } else {
                countdownElement.className = 'fw-bold';
            }

            countdownSeconds--;

            // When countdown finished
            if (countdownSeconds < 0) {
                clearInterval(countdownTimer);

                // Hide countdown container
                setTimeout(() => {
                    countdownContainer.style.display = 'none';
                    countdownContainer.style.opacity = '0';
                    countdownContainer.style.transform = 'translateY(-10px)';
                }, 2000);

                // Show and enable resend button with animation
                setTimeout(() => {
                    resendBtn.classList.remove('d-none');
                    resendBtn.disabled = false;

                    setTimeout(() => {
                        resendBtn.classList.add('show');
                    }, 100);

                    resendBtn.style.animation = 'slideInFromBottom 0.2s ease-out';
                }, 2500);
            }
        }, 1000);
    }

    function startResendCountdown(seconds) {
        console.log('Starting resend countdown for', seconds, 'seconds');

        // Clear any existing timer
        if (countdownTimer) {
            clearInterval(countdownTimer);
        }

        const resendBtn = document.getElementById('resendOtpBtn');
        const countdownContainer = document.getElementById('countdownContainer');

        // Recreate countdown element to ensure it exists
        countdownContainer.innerHTML = `
            <span class="text-primary">
                <i class="bi bi-clock"></i>
                Kirim ulang dalam <span id="countdown" class="fw-bold">${Math.floor(seconds / 60)}:${(seconds % 60).toString().padStart(2, '0')}</span>
            </span>
        `;

        const countdownElement = document.getElementById('countdown');

        // Ensure resend button is hidden during countdown
        resendBtn.classList.add('d-none');
        resendBtn.classList.remove('show');
        resendBtn.disabled = true;

        // Make countdown container visible
        countdownContainer.style.display = 'block';
        countdownContainer.style.opacity = '1';
        countdownContainer.style.transform = 'translateY(0)';

        // Set initial seconds
        let remainingSeconds = seconds;

        // Update countdown display initially
        updateCountdownDisplay();

        // Start the interval
        countdownTimer = setInterval(() => {
            remainingSeconds--;

            // Check if countdown is complete BEFORE updating display
            if (remainingSeconds < 0) {
                // Stop the timer immediately
                clearInterval(countdownTimer);
                countdownTimer = null;

                // Show completion message
                // countdownContainer.innerHTML = `
                //     <span class="text-success countdown-finished-message">
                //         <i class="bi bi-check-circle"></i>
                //         Anda dapat mengirim ulang kode OTP sekarang
                //     </span>
                // `;

                // Hide countdown container after showing message
                setTimeout(() => {
                    countdownContainer.style.display = 'none';
                }, 1500);

                // Show and enable resend button
                setTimeout(() => {
                    resendBtn.classList.remove('d-none');
                    resendBtn.disabled = false;
                    setTimeout(() => resendBtn.classList.add('show'), 50);
                    resendBtn.style.animation = 'slideInFromBottom 0.2s ease-out';
                }, 2000);

                return; // Exit early to prevent display update
            }

            // Only update display if we have time remaining
            updateCountdownDisplay();
        }, 1000);

        // Helper function to update the countdown display
        function updateCountdownDisplay() {
            // Safety check - never display negative values
            if (remainingSeconds < 0) return;

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (countdownElement) {
                countdownElement.textContent = timeString;

                // Add visual indicators based on remaining time
                if (remainingSeconds <= 30) {
                    countdownElement.className = 'fw-bold countdown-critical';
                } else if (remainingSeconds <= 60) {
                    countdownElement.className = 'fw-bold countdown-warning';
                } else {
                    countdownElement.className = 'fw-bold';
                }
            }
        }
    }

    function stopCountdown() {
        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }
    }

    function hideResendButton() {
        const resendBtn = document.getElementById('resendOtpBtn');
        resendBtn.classList.remove('show');
        setTimeout(() => {
            resendBtn.classList.add('d-none');
            resendBtn.disabled = true;
        }, 300);
    }

    function hideAlerts() {
        const otpError = document.getElementById('otpError');
        const otpSuccess = document.getElementById('otpSuccess');
        otpError.textContent = '';
        otpSuccess.textContent = '';
        otpError.classList.add('d-none');
        otpSuccess.classList.add('d-none');
    }


    function resendOTP() {
        const otpError = document.getElementById('otpError');
        const otpSuccess = document.getElementById('otpSuccess');
        const countdownContainer = document.getElementById('countdownContainer');
        const fallbackOtpContainer = document.getElementById('fallbackOtpContainer');
        const fallbackOtpCode = document.getElementById('fallbackOtpCode');

        // Clear previous messages
        otpError.textContent = '';
        otpSuccess.textContent = '';
        fallbackOtpCode.textContent = '';
        fallbackOtpContainer.style.display = 'none';

        // Hide resend button and show countdown container with loading state
        hideResendButton();
        hideAlerts();

        // Show countdown container again and display loading state
        countdownContainer.style.display = 'block';
        countdownContainer.style.opacity = '1';
        countdownContainer.style.transform = 'translateY(0)';
        countdownContainer.innerHTML = `
            <span class="text-primary">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Sedang mengirim kode OTP baru...
            </span>
        `;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(window.routes.resendOtp || 'resend-otp', {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({})
        })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.status === 'success') {
                    setOtpFormDisabled(false); // Aktifkan kembali form OTP
                    hideAlerts(); // Clear any previous alerts

                    // Show success message first
                    countdownContainer.innerHTML = `<span class="text-success">
                        <i class="bi bi-check-circle"></i>
                        Kode OTP baru telah berhasil terkirim.</span>
                    `;

                    // Clear OTP inputs
                    document.querySelectorAll('.otp-input').forEach(input => {
                        input.value = '';
                    });
                    document.querySelector('.otp-input').focus();

                    // Tampilkan fallback OTP hanya jika ada nilai OTP yang valid (bukan null, undefined, atau string kosong)
                    if (data.otp && data.otp !== null && data.otp !== '' && data.otp !== undefined) {
                        document.getElementById('fallbackOtpCode').textContent = data.otp;
                        document.getElementById('fallbackOtpContainer').style.display = 'block';
                    } else {
                        document.getElementById('fallbackOtpContainer').style.display = 'none';
                    }

                    // Show success message for a moment before showing countdown
                    setTimeout(() => {
                        // Set up countdown container for 3 minutes
                        countdownContainer.innerHTML = `
                            <span class="text-primary">
                                <i class="bi bi-clock"></i>
                                Kirim ulang dalam <span id="countdown" class="fw-bold">3:00</span>
                            </span>
                        `;
                        startResendCountdown(180); // 3 minutes = 180 seconds
                    }, 2000); // Show success message for 2 seconds before showing countdown
                } else {
                    // Show error and restore resend button
                    otpError.textContent = data.message || 'Gagal mengirim ulang kode OTP. Silakan coba lagi.';

                    countdownContainer.innerHTML = `
                        <span class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            Gagal mengirim ulang kode
                        </span>
                    `;

                    // Hide countdown container and show resend button again
                    setTimeout(() => {
                        countdownContainer.style.display = 'none';

                        const resendBtn = document.getElementById('resendOtpBtn');
                        resendBtn.classList.remove('d-none');
                        resendBtn.disabled = false;
                        setTimeout(() => resendBtn.classList.add('show'), 50);
                    }, 2000);
                }
            })
            .catch((error) => {
                console.error('Resend OTP error:', error);
                otpError.textContent = 'Terjadi kesalahan saat mengirim ulang kode OTP.';

                countdownContainer.innerHTML = `
                    <span class="text-danger">
                        <i class="bi bi-wifi-off"></i>
                        Koneksi bermasalah
                    </span>
                `;

                // Hide countdown container and show resend button again
                setTimeout(() => {
                    countdownContainer.style.display = 'none';
                    const resendBtn = document.getElementById('resendOtpBtn');
                    resendBtn.classList.remove('d-none');
                    resendBtn.disabled = false;
                    setTimeout(() => resendBtn.classList.add('show'), 50);
                }, 2000);
            });
    }

    // Loading Screen Functions dengan timing dinamis
    function showLoadingScreen() {
        const loadingScreen = document.getElementById('loadingScreen');
        if (loadingScreen) {
            loadingScreen.classList.remove('d-none');
            loadingScreen.offsetHeight;
            loadingScreen.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function hideLoadingScreen() {
        const loadingScreen = document.getElementById('loadingScreen');
        if (loadingScreen) {
            loadingScreen.classList.remove('show');
            setTimeout(() => {
                loadingScreen.classList.add('d-none');
                document.body.style.overflow = '';
            }, 150);
        }
    }

    function updateLoadingProgress(percentage, message, step) {
        function tryUpdate(attempts = 0) {
            const progressBar = document.getElementById('loadingProgressBar');
            const loadingMessage = document.getElementById('loadingMessage');
            const loadingStep = document.getElementById('loadingStep');

            if (!loadingStep && attempts < 5) {
                setTimeout(() => tryUpdate(attempts + 1), 50);
                return;
            }

            if (progressBar) {
                progressBar.style.width = percentage + '%';
                progressBar.setAttribute('aria-valuenow', percentage);
            }

            if (message && loadingMessage) {
                loadingMessage.textContent = message;
            }

            if (step && loadingStep) {
                // Animasi subtle pada perubahan step
                const oldStep = loadingStep.textContent;
                if (oldStep !== step) {
                    loadingStep.style.opacity = '0';
                    setTimeout(() => {
                        loadingStep.textContent = step;
                        loadingStep.style.opacity = '1';
                    }, 200);
                }
            }
        }

        tryUpdate();
    }

    // Fungsi adaptif untuk loading steps dengan preload home page
    function adaptiveLoadingSteps(redirectUrl) {
        const minLoadingTime = 1800; // Minimal 1.8 detik untuk UX
        const maxLoadingTime = 5000; // Maksimal 5 detik jika request lambat

        const startTime = Date.now();
        let homePageLoaded = false;
        let animationComplete = false;
        let redirectTriggered = false;

    // Steps dengan timing yang lebih realistis
        const steps = [
            { progress: 20, message: 'Memverifikasi sesi...', step: 'Validasi kredensial...', delay: 300 },
            { progress: 40, message: 'Memuat konfigurasi...', step: 'Menyiapkan dashboard...', delay: 400 },
            { progress: 60, message: 'Mengambil data pengguna...', step: 'Loading user preferences...', delay: 400 },
            { progress: 80, message: 'Menyiapkan UI dashboard...', step: 'Mengoptimalkan tampilan...', delay: 400 }
            // Step 100% akan ditambahkan setelah home page load
        ];

        // Fungsi untuk melakukan redirect setelah semua proses selesai
        function tryRedirect() {
            if (redirectTriggered) return;

            if (homePageLoaded && animationComplete) {
                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minLoadingTime - elapsedTime);

                // Final step setelah home page terload
                updateLoadingProgress(100, 'Selesai!', 'Mengalihkan ke dashboard...');

                setTimeout(() => {
                    redirectTriggered = true;
                    console.log(`✅ Redirect ke ${redirectUrl} setelah ${elapsedTime + remainingTime}ms`);
                    window.location.href = redirectUrl;
                }, remainingTime + 300); // Tambah 300ms untuk final animation
            }
        }

        // Eksekusi loading step animation
        let currentStep = 0;
        function executeStep() {
            if (currentStep < steps.length) {
                const step = steps[currentStep];
                updateLoadingProgress(step.progress, step.message, step.step);
                currentStep++;

                setTimeout(executeStep, step.delay);
            } else {
                // Semua langkah animasi selesai
                animationComplete = true;
                tryRedirect();
            }
        }

        // Mulai animasi
        setTimeout(executeStep, 200);

        // Pre-load home page dengan request async
        function preloadHomePage() {
            const headers = {
                "Cache-Control": "no-cache",
                "X-Requested-With": "XMLHttpRequest",
                "X-PRELOAD": "true"
            };

            // Dapatkan CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                headers["X-CSRF-TOKEN"] = csrfToken.getAttribute('content');
            }

            // Gunakan redirectUrl dari parameter, atau ambil hanya path-nya jika URL lengkap
            let preloadUrl = redirectUrl;
            try {
                // Jika redirectUrl adalah URL lengkap, ambil path-nya saja
                const urlObj = new URL(redirectUrl);
                preloadUrl = urlObj.pathname;
            } catch (e) {
                // Bukan URL lengkap, gunakan path saja
                console.log("Preloading path:", redirectUrl);
            }

            // Tambahkan parameter untuk menandakan ini request preload
            preloadUrl += (preloadUrl.includes('?') ? '&' : '?') + '_preload=1';

            console.log(`🔄 Preloading: ${preloadUrl}`);

            fetch(preloadUrl, {
                method: "GET",
                headers: headers,
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    console.log(`✅ Home page preloaded (${response.status})`);
                    homePageLoaded = true;
                    tryRedirect();

                    // Optional: Preload assets juga
                    return response.text();
                })
                .then(html => {
                    // Optional: Parse HTML untuk preload asset penting
                    preloadCriticalAssets(html);
                })
                .catch(error => {
                    console.warn('⚠️ Preload error:', error);
                    // Jika gagal preload, tetap izinkan redirect
                    homePageLoaded = true;
                    tryRedirect();
                });

        // Safety timeout jika request terlalu lama
            setTimeout(() => {
                if (!homePageLoaded) {
                    console.warn('⚠️ Preload timeout, forcing completion');
                    homePageLoaded = true;
                    tryRedirect();
                }
            }, maxLoadingTime);
        }

        // Optional: Preload critical assets dari HTML
        function preloadCriticalAssets(html) {
            try {
                // Cari asset penting seperti CSS dan JS
                const cssLinks = html.match(/href="([^"]+\.css[^"]*)"/g) || [];
                const jsLinks = html.match(/src="([^"]+\.js[^"]*)"/g) || [];

                // Preload CSS
                cssLinks.slice(0, 3).forEach(link => {
                    const href = link.match(/href="([^"]+)"/)[1];
                    preloadResource(href, 'style');
                });

                // Preload JS
                jsLinks.slice(0, 3).forEach(link => {
                    const src = link.match(/src="([^"]+)"/)[1];
                    preloadResource(src, 'script');
                });
            } catch (e) {
                console.warn('Asset preloading error:', e);
            }
        }

        // Helper untuk preload resource
        function preloadResource(url, type) {
            if (!url || url.includes('data:')) return;

            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = url.startsWith('/') ? url : `/${url}`;
            link.as = type;
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        }

        // Mulai preload
        preloadHomePage();
    }

    // Login Form Handler
    const loginForm = document.getElementById('form-login');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(window.routes.authenticate, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                },
                body: formData
            })
                .then(res => {
                    if (res.status === 429) {
                        const retryAfter = res.headers.get('Retry-After') || 60;
                        return { status: 'rate_limit', seconds: parseInt(retryAfter) };
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.status === 'otp_required' || data.status === 'api_error' || data.status === 'saungwa_logout' || data.status === 'email_error') {
                        const modal = initOtpModal();
                        modal.show();

                        // Tampilkan instruksi OTP dari response jika ada
                        const otpInstructions = document.getElementById('otpInstructions');
                        if (otpInstructions) {
                            otpInstructions.textContent = data.message || 'Silakan masukkan kode OTP yang telah dikirim ke WhatsApp Anda.';
                        }

                        const fallbackOtpContainer = document.getElementById('fallbackOtpContainer');
                        const fallbackOtpCode = document.getElementById('fallbackOtpCode');

                        // Reset OTP fallback
                        fallbackOtpCode.textContent = '';
                        fallbackOtpContainer.style.display = 'none';

                        if (data.otp && data.otp !== null && data.otp !== '' && data.otp !== undefined) {

                            const otpFormatted = data.otp.toString().replace(/\D/g, '');
                            document.getElementById('fallbackOtpCode').textContent = otpFormatted;
                            document.getElementById('fallbackOtpContainer').style.display = 'block';
                        } else {
                            document.getElementById('fallbackOtpContainer').style.display = 'none';
                        }

                        // Start countdown when OTP modal is shown
                        setTimeout(() => {
                            startCountdown();
                        }, 500);
                    } else if (data.status === 'rate_limit' || data.status === 'too_many_attempts') {
                        let timerInterval;
                        let secondsLeft = data.seconds;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Terlalu Banyak Percobaan Login',
                            html: 'Anda telah melakukan terlalu banyak percobaan login yang gagal.<br>Silakan tunggu selama <b>' + secondsLeft + '</b> detik sebelum dapat mencoba kembali.',
                            timer: secondsLeft * 1000,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Tutup',
                            didOpen: () => {
                                Swal.showLoading();
                                const timer = Swal.getHtmlContainer().querySelector('b');
                                timerInterval = setInterval(() => {
                                    secondsLeft--;
                                    if (timer) timer.textContent = secondsLeft;
                                    if (secondsLeft <= 0) {
                                        clearInterval(timerInterval);
                                    }
                                }, 1000);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                                submitButton.disabled = false;
                                submitButton.textContent = originalText;
                            }
                        });
                        
                        // Clear and reload captcha automatically
                        if (window.jQuery) {
                            jQuery('#reload').trigger('click');
                            jQuery('#captcha').val('');
                        } else {
                            const reloadBtn = document.getElementById('reload');
                            if (reloadBtn) reloadBtn.click();
                            const captchaInput = document.getElementById('captcha');
                            if (captchaInput) captchaInput.value = '';
                        }
                        return;
                    } else {
                        // Helper to refresh captcha image and clear input on failure
                        const refreshCaptchaAndClear = () => {
                            if (window.jQuery) {
                                jQuery('#reload').trigger('click');
                                jQuery('#captcha').val('');
                            } else {
                                const reloadBtn = document.getElementById('reload');
                                if (reloadBtn) reloadBtn.click();
                                const captchaInput = document.getElementById('captcha');
                                if (captchaInput) captchaInput.value = '';
                            }
                        };

                        if(data.status === 'invalid_format'){
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: data.message,
                                confirmButtonText: 'Tutup',
                            });
                            refreshCaptchaAndClear();
                        }else if(data.status === 'invalid_phone'){
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: data.message,
                                confirmButtonText: 'Tutup',
                            });
                            refreshCaptchaAndClear();
                        }else if(data.status === 'unauthorized'){
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: data.message,
                                confirmButtonText: 'Tutup',
                            });
                            refreshCaptchaAndClear();
                        } else if (data.status === 'errorCaptcha') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Captcha Salah',
                                text: 'Captcha yang Anda masukkan salah. Silakan coba lagi.',
                                confirmButtonText: 'Tutup',
                                timerProgressBar: true
                            });
                            refreshCaptchaAndClear();
                        } else if (data.status === 'success') {
                            showLoadingScreen();

                            // No more unnecessary timeout, langsung gunakan adaptive loading
                            const redirectUrl = data.redirect || '/home';
                            adaptiveLoadingSteps(redirectUrl);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Gagal',
                                html: data.message || 'Terjadi kesalahan.',
                                confirmButtonText: 'Tutup',
                                timerProgressBar: true
                            });
                            refreshCaptchaAndClear();
                        }
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Bermasalah',
                        text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                        confirmButtonText: 'Tutup',
                        timerProgressBar: true
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
        });
    }

    // OTP Form Handler
    const otpForm = document.getElementById('form-otp');
    if (otpForm) {
        otpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const otp = Array.from(document.querySelectorAll('.otp-input')).map(input => input.value).join('');

            if (otp.length !== 6) {
                document.getElementById('otpError').textContent = 'Lengkapi 6 digit OTP.';
                return;
            }

            // Show OTP error or success alert
            function showOtpAlert(type, message) {
                const otpError = document.getElementById('otpError');
                const otpSuccess = document.getElementById('otpSuccess');
                if (type === 'error') {
                    otpError.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${message}`;
                    otpError.classList.remove('d-none');
                    otpSuccess.classList.add('d-none');
                } else if (type === 'success') {
                    otpSuccess.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>${message}`;
                    otpSuccess.classList.remove('d-none');
                    otpError.classList.add('d-none');
                } else {
                    otpError.classList.add('d-none');
                    otpSuccess.classList.add('d-none');
                }
            }

            $('#form-otp').on('submit', function (e) {
                e.preventDefault();
                // Saat error
                showOtpAlert('error', data.message || 'Kode OTP tidak valid!');

                // Saat sukses
                showOtpAlert('success', 'OTP berhasil diverifikasi!');

                // Untuk menyembunyikan semua alert
                hideAlerts();
            });

            // Tampilkan loading di modal atau di atas form jika ingin
            document.getElementById('otpError').textContent = '';
            document.getElementById('otpSuccess').textContent = 'Memverifikasi...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(window.routes.verifyOtp, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ otp })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' || data.success) {
                        stopCountdown();
                        const modal = initOtpModal();
                        hideAlerts();
                        modal.hide();

                        showLoadingScreen();
                        const redirectUrl = data.redirect || '/home';
                        adaptiveLoadingSteps(redirectUrl);
                    } else {
                        showOtpAlert('error', data.message || 'Kode OTP tidak valid!');
                        document.querySelectorAll('.otp-input').forEach(input => input.value = '');
                        document.querySelector('.otp-input').focus();

                        // Jika error karena percobaan melebihi batas, disable form
                        if (
                            data.message &&
                            data.message.includes('Percobaan melebihi batas')
                        ) {
                            setOtpFormDisabled(true);
                        }
                    }
                })
                .catch((error) => {
                    console.error('❌ OTP verification error:', error);
                    showOtpAlert('error', 'Terjadi kesalahan saat verifikasi OTP.');
                    document.querySelectorAll('.otp-input').forEach(input => input.value = '');
                    document.querySelector('.otp-input').focus();
                })
                .finally(() => {
                    document.getElementById('otpSuccess').textContent = '';
                });
        });
    }

    $(document).ready(function () {
        $('#otpModal').on('shown.bs.modal', function () {
            // Fokus ke input OTP pertama
            $('#form-otp .otp-input').first().focus();
        });
    });

    // Resend OTP Button Handler
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    if (resendOtpBtn) {
        resendOtpBtn.addEventListener('click', resendOTP);
    }

    // Clear countdown when modal is hidden
    document.getElementById('otpModal').addEventListener('hidden.bs.modal', function () {
        setOtpFormDisabled(false);
        showOtpAlert();
        stopCountdown();
        // Reset UI
        document.getElementById('otpError').textContent = '';
        document.getElementById('otpSuccess').textContent = '';
        document.querySelectorAll('.otp-input').forEach(input => input.value = '');

        // Reset resend button and countdown container state
        const resendBtn = document.getElementById('resendOtpBtn');
        const countdownContainer = document.getElementById('countdownContainer');

        resendBtn.classList.add('d-none');
        resendBtn.classList.remove('show');
        resendBtn.disabled = true;

        // Reset countdown container
        countdownContainer.style.display = 'none';
        countdownContainer.style.opacity = '1';
        countdownContainer.style.transform = 'translateY(0)';
        countdownContainer.classList.remove('countdown-finished');

        // Restore original countdown content
        countdownContainer.innerHTML = `
            <span class="text-primary">
                <i class="bi bi-clock"></i>
                Kirim ulang dalam <span id="countdown" class="fw-bold">5:00</span>
            </span>
        `;
    });

    // OTP Input Validation (existing code remains same)
    document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
        input.addEventListener('keypress', function (e) {
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            let numbers = paste.replace(/\D/g, '');

            if (numbers.length > 0) {
                for (let i = 0; i < numbers.length && (index + i) < inputs.length; i++) {
                    inputs[index + i].value = numbers[i];
                }
                let nextIndex = Math.min(index + numbers.length, inputs.length - 1);
                inputs[nextIndex].focus();

                // Cek jika semua input sudah terisi setelah paste, langsung submit
                const allFilled = Array.from(inputs).every(inp => inp.value.length === 1);
                if (allFilled) {
                    document.getElementById('form-otp').requestSubmit();
                }
            }
        });

        input.addEventListener('input', function (e) {
            this.value = this.value.replace(/\D/g, '');

            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            // Auto submit jika semua input sudah terisi
            const allFilled = Array.from(inputs).every(inp => inp.value.length === 1);
            if (allFilled) {
                document.getElementById('form-otp').requestSubmit();
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    function setOtpFormDisabled(disabled = true) {
        document.querySelectorAll('.otp-input').forEach(input => {
            input.disabled = disabled;
        });
        // Jika ada tombol resend, bisa juga disable/enable di sini jika perlu
    }
}
