<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Dalam Pemeliharaan - POLRI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(59, 130, 246, 0.6);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .animate-slide-up {
            animation: slideInUp 0.8s ease-out forwards;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #173380 0%, #1b3eb1 50%, #2563eb 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .glass-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .countdown-glass {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo-shadow {
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.3));
        }

        .text-shadow {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

@php
    // Read maintenance data from the down file
    $maintenanceData = [];
    $downFilePath = storage_path('framework/down');
    if (file_exists($downFilePath)) {
        $maintenanceData = json_decode(file_get_contents($downFilePath), true) ?? [];
    }

    $endTime = $maintenanceData['end_time'] ?? null;
    $startedAt = $maintenanceData['started_at'] ?? null;
    $durationMinutes = $maintenanceData['duration_minutes'] ?? null;
@endphp

<body class="gradient-bg min-h-screen flex items-center justify-center p-4 relative">

    <!-- Decorative Elements -->
    <div
        class="absolute top-10 left-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse">
    </div>
    <div
        class="absolute bottom-10 right-10 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse delay-75">
    </div>

    <div class="glass-card max-w-4xl w-full rounded-3xl shadow-2xl p-8 md:p-12 relative z-10 animate-slide-up">

        <!-- Header with Logo -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="glass-badge rounded-2xl p-4">
                    <img src="{{ asset('images/logo2x.png') }}" alt="Maintenance in Progress"
                        class="w-16 md:w-24 h-auto object-scale-down aspect-square rounded-2xl">
                </div>
            </div>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-yellow-400 to-transparent mx-auto"></div>
        </div>

        <!-- Main Heading -->
        <h1 class="text-xl md:text-3xl font-bold text-white text-center mb-2 text-shadow">
            Sistem ICELL Dalam Pemeliharaan
        </h1>

        <!-- Description -->
        <p
            class="text-white/80 text-center text-base text-sm md:text-md leading-relaxed mb-8 md:mb-10 max-w-2xl mx-auto">
            Mohon maaf atas ketidaknyamanan ini. Kami sedang melakukan pemeliharaan terjadwal untuk meningkatkan
            kualitas layanan sistem informasi.
            @if ($durationMinutes)
                Estimasi durasi pemeliharaan: <strong class="text-white">{{ $durationMinutes }} menit</strong>.
            @else
                Sistem akan kembali beroperasi dalam waktu singkat.
            @endif
        </p>

        <!-- Countdown -->
        @if ($endTime)
        <div class="flex justify-center gap-3 md:gap-5 mb-10">
            <div
                class="countdown-glass rounded-2xl p-5 md:p-7 min-w-[80px] md:min-w-[110px] text-center transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                <span class="block text-3xl md:text-5xl font-bold text-white mb-2" id="hours">00</span>
                <span class="block text-xs md:text-sm text-white/80 uppercase font-semibold tracking-wider">Jam</span>
            </div>
            <div
                class="countdown-glass rounded-2xl p-5 md:p-7 min-w-[80px] md:min-w-[110px] text-center transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                <span class="block text-3xl md:text-5xl font-bold text-white mb-2" id="minutes">00</span>
                <span class="block text-xs md:text-sm text-white/80 uppercase font-semibold tracking-wider">Menit</span>
            </div>
            <div
                class="countdown-glass rounded-2xl p-5 md:p-7 min-w-[80px] md:min-w-[110px] text-center transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                <span class="block text-3xl md:text-5xl font-bold text-white mb-2" id="seconds">00</span>
                <span class="block text-xs md:text-sm text-white/80 uppercase font-semibold tracking-wider">Detik</span>
            </div>
        </div>
        @else
        <div class="flex justify-center mb-10">
            <div class="animate-float">
                <i class="bi bi-gear-wide-connected text-5xl md:text-7xl text-yellow-400 drop-shadow-[0_0_15px_rgba(250,204,21,0.5)]"></i>
            </div>
        </div>
        @endif

        <!-- Status Badge -->
        <div class="flex justify-center mb-8">
            <div class="glass-badge inline-flex items-center gap-3 px-6 py-3 rounded-full">
                <div class="relative">
                    <span class="flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                    </span>
                </div>
                <span class="text-white text-sm md:text-md font-semibold" id="status-text">Pemeliharaan Sedang Berlangsung</span>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-white/20 pt-8 mt-8">

            <!-- Contact Info -->
            <div class="text-center mb-6">
                <p class="text-white/70 text-sm md:text-base mb-4">
                    Untuk bantuan darurat, silakan hubungi:
                </p>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-8">
                    <a href="mailto:icell.korlantas@gmail.com?subject=Bantuan%20Darurat%20ICELL&body=Mohon%20bantuan%20terkait%20sistem%20ICELL"
                        class="glass-badge px-6 py-3 rounded-lg hover:bg-white/20 transition-all duration-300 group inline-flex items-center gap-3">
                        <i
                            class="bi bi-envelope text-white text-md md:text-xl group-hover:scale-110 transition-transform"></i>
                        <span class="text-white text-md md:text-lg font-semibold">icell.korlantas@gmail.com</span>
                    </a>
                    <a href="https://wa.me/6285136824141" target="_blank"
                        class="glass-badge px-6 py-3 rounded-lg hover:bg-white/20 transition-all duration-300 group inline-flex items-center gap-3">
                        <i
                            class="bi bi-whatsapp text-white text-md md:text-xl group-hover:scale-110 transition-transform"></i>
                        <span class="text-white text-md md:text-lg font-semibold">Helpdesk ICELL</span>
                    </a>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="text-center">
                <p class="text-white/50 text-xs md:text-sm">
                    <i class="bi bi-info-circle me-2"></i>
                    Waktu pemeliharaan dapat berubah sewaktu-waktu
                </p>
            </div>
        </div>
    </div>

    <script>
        @if ($endTime)
        // End time from server (Unix timestamp in milliseconds)
        const maintenanceEnd = {{ $endTime }} * 1000;

        function updateCountdown() {
            const now = Date.now();
            const distance = maintenanceEnd - now;

            if (distance <= 0) {
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                document.getElementById('status-text').textContent = 'Sistem Segera Kembali Online...';

                // Auto-reload after countdown ends (server middleware will allow through)
                setTimeout(() => location.reload(), 3000);
                return;
            }

            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
        @else
        // No duration set, auto-reload every 30 seconds to check if maintenance ended
        setInterval(() => location.reload(), 30000);
        @endif
    </script>
</body>

</html>