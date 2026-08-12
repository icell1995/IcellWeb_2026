<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class QrCodeController extends Controller
{
    //
    public function generateQRCode()
    {
        $qrCodes = [];
        // $qrCodes['withImage'] = QrCode::size(200)->format('png')->mergeString('/public/images/logo.png', .3)->generate('https://www.binaryboxtuts.com/');
        $qrCodes['withImage'] = QrCode::size(200)
            ->format('png')
            ->style('round')
            ->errorCorrection('L')
            ->merge('/public/images/logo2.png', .4)
            ->generate('https://icell.korlantas.polri.go.id/');
        return view('qrCode', $qrCodes);
    }

    public function generateAndEmbedQRCode()
    {
        // Path ke gambar yang ingin digabungkan
        $imagePath = public_path('images/logo2.png');

        // Generate QR code dengan gambar
        $qrCode = QrCode::size(200)
            ->format('png')
            ->style('round')
            ->errorCorrection('L')
            ->merge($imagePath, .4)
            ->generate('https://icell.korlantas.polri.go.id/');

        // Simpan gambar QR code ke direktori sementara
        $tempQrCodePath = public_path('temp/qrcode.png');
        File::put($tempQrCodePath, $qrCode);
        // Selanjutnya akan menyisipkan QR code ke dalam dokumen Word
    }
}
