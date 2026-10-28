<?php

namespace App\Helpers;

class AppModeHelper
{
    /**
     * Cek apakah aplikasi berjalan dalam mode PRODUCTION.
     *
     * @return bool
     */
    public static function isProduction(): bool
    {
        $mode = config('app.mode') ?? env('APP_MODE', 'LOCAL');
        return strtoupper((string) $mode) === 'PRODUCTION';
    }

    /**
     * Evaluasi apakah pemanggilan API eksternal diizinkan berjalan.
     * Aturan Kebijakan:
     * - AccidentController@index SELALU diizinkan (true) di semua mode (Local, Dev, Staging, Prod).
     * - Seluruh pemanggilan API eksternal lainnya HANYA diizinkan jika APP_MODE=PRODUCTION.
     *
     * @param string $callerContext Nama context pemanggil (opsional, misal: 'AccidentController@index')
     * @return bool
     */
    public static function isApiEnabled(string $callerContext = ''): bool
    {
        if ($callerContext === 'AccidentController@index') {
            return true;
        }

        return self::isProduction();
    }
}
