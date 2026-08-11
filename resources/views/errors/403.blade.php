<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Dibatasi | Korlantas Polri - ICELL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .animate-slide-up { animation: slideInUp 0.8s ease-out forwards; }

        .gradient-bg {
            background: linear-gradient(135deg, #0a1931 0%, #173380 50%, #1b3eb1 100%);
            position: relative;
            overflow: hidden;
        }

        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .glass-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .info-glass {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .text-shadow {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4 relative">

    <!-- Decorative Elements -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-72 h-72 bg-red-600 rounded-full mix-blend-screen filter blur-3xl opacity-10 animate-pulse delay-75"></div>

    <div class="glass-card max-w-4xl w-full rounded-3xl p-8 md:p-12 relative z-10 animate-slide-up">

        <!-- Header with Logos -->
        <div class="flex flex-col items-center mb-8">
            <div class="flex justify-center items-center gap-4 md:gap-8 mb-6">
                <img src="{{ asset('images/logo2x.png') }}" alt="Korlantas Polri" class="h-24 md:h-28 w-auto object-contain drop-shadow-lg">
            </div>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-yellow-400 to-transparent"></div>
        </div>

        <!-- Error Visual -->
        <div class="text-center mb-6">
            <div class="inline-block animate-float mb-4">
                <i class="bi bi-shield-lock-fill text-6xl md:text-8xl text-yellow-400 drop-shadow-[0_0_15px_rgba(250,204,21,0.5)]"></i>
            </div>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-2 text-shadow">403</h1>
            <h2 class="text-xl md:text-2xl font-bold text-white/90 uppercase tracking-widest">Akses Dibatasi</h2>
        </div>

        <!-- Description -->
        <p class="text-white/80 text-center text-sm md:text-lg leading-relaxed mb-10 max-w-2xl mx-auto">
            Mohon maaf, sistem mendeteksi bahwa kredensial Anda tidak memiliki otoritas untuk mengakses modul 
            <span class="font-bold text-white uppercase">ICELL (Informasi Cepat Penyidikan Laka Lantas)</span>. 
            Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
        </p>

        <!-- Divider -->
        <div class="border-t border-white/10 pt-8 mt-8">
            <!-- Contact Info -->
            <div class="text-center mb-6">
                <p class="text-white/60 text-xs md:text-sm mb-6 uppercase tracking-widest font-bold">
                    Hubungi Pusat Bantuan
                </p>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-6">
                    <a href="mailto:icell.korlantas@gmail.com"
                        class="glass-badge w-full md:w-auto px-6 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group inline-flex items-center justify-center gap-3">
                        <i class="bi bi-envelope-at text-white text-xl"></i>
                        <span class="text-white font-semibold">Email Helpdesk</span>
                    </a>
                    <a href="https://wa.me/6285136824141" target="_blank"
                        class="glass-badge w-full md:w-auto px-6 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 group inline-flex items-center justify-center gap-3">
                        <i class="bi bi-whatsapp text-white text-xl"></i>
                        <span class="text-white font-semibold">WhatsApp Support</span>
                    </a>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
                <button onclick="window.history.back()" class="text-white/50 hover:text-white text-xs md:text-sm transition-colors flex items-center justify-center gap-2 mx-auto">
                    <i class="bi bi-arrow-left"></i> Kembali ke halaman sebelumnya
                </button>
            </div>
        </div>
    </div>

</body>
</html>