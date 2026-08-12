<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        /* Base Styles */
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .email-wrapper {
            width: 100%;
            padding: 20px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 25px;
        }

        .logo-container {
            margin-bottom: 5px;
            /* background-color: #1e3a8a; */
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
        }

        .img-icell {
            height: 85px;
            max-width: 225px;
            display: inline-block;
        }

        .header h2 {
            color: #1e3a8a;
            font-size: 26px;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .header p {
            color: #6b7280;
            margin: 0;
            font-size: 15px;
        }

        /* Content */
        .message {
            font-size: 16px;
            line-height: 1.6;
            padding: 0 10px;
        }

        .message p {
            margin: 15px 0;
        }

        .otp-container {
            margin: 30px 0;
            text-align: center;
        }

        .otp-code {
            display: inline-block;
            padding: 18px 30px;
            background: #e0e7ff;
            color: #1e3a8a;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 6px;
            border-radius: 8px;
            border: 1px dashed #3b82f6;
        }

        .expiry {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            margin: 25px 0;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
        }

        .highlight {
            color: #1e40af;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .footer p {
            margin: 8px 0;
        }

        /* Mobile Responsive */
        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 25px;
                border-radius: 0;
            }

            .otp-code {
                font-size: 24px;
                padding: 15px 20px;
                letter-spacing: 4px;
            }

            .logo {
                font-size: 20px;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="header">
                {{-- <div class="logo">ICELL</div> --}}
                <div class="logo-container">
                    <img src="{{ asset('images/logoICELLTransparent.png') }}" alt="Logo ICELL Korlantas Polri"
                        class="img-icell img-fluid">
                </div>
                <h2>Kode Verifikasi ICELL</h2>
                <p>Sistem Verifikasi Korlantas Polri</p>
            </div>

            <div class="message">
                <p>Yth. Pengguna,</p>
                <p>Terima kasih telah menggunakan layanan ICELL Korlantas Polri. Untuk melanjutkan proses verifikasi
                    identitas Anda, silakan gunakan kode OTP berikut:</p>
            </div>

            <div class="otp-container">
                <span class="otp-code">{{ $otp }}</span>
            </div>

            <div class="expiry">
                <span class="highlight">PERHATIAN:</span> Kode ini hanya berlaku selama {{ $expiryMinutes }} menit.<br>
                <span class="highlight">JANGAN</span> berikan kode ini kepada siapapun termasuk pihak yang mengaku dari
                ICELL Korlantas.
            </div>

            <div class="footer">
                <p>Jika Anda tidak meminta kode ini, mohon abaikan email ini atau hubungi tim dukungan kami.</p>
                <p>© {{ date('Y') }} ICELL KORLANTAS POLRI. Seluruh Hak Cipta Dilindungi.</p>
                <p style="color: #d1d5db;">Email ini dikirim secara otomatis, mohon tidak membalas.</p>
            </div>
        </div>
    </div>
</body>

</html>
