<?php

namespace Tests\Feature;

use App\Helpers\AgeHelper;
use App\Helpers\AppModeHelper;
use App\Mail\OTPMail;
use App\Services\ESignature\ESignatureTokenService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Laravel12Php85ModernizationTest extends TestCase
{
    public function test_route_names_have_no_duplicates_and_key_routes_resolve_correctly(): void
    {
        $routes = Route::getRoutes()->getRoutes();
        $seen = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            if (!$name) {
                continue;
            }
            $this->assertArrayNotHasKey($name, $seen, "Duplicate route name detected: {$name}");
            $seen[$name] = $route->uri();
        }

        $this->assertSame('forgot-password', Route::getRoutes()->getByName('password.request')->uri());
        $this->assertSame('password/reset', Route::getRoutes()->getByName('legacy.password.request')->uri());
        $this->assertSame('forget-password', Route::getRoutes()->getByName('forget-password.post')->uri());
        $this->assertSame('produktivitas/spdp/spdp_create', Route::getRoutes()->getByName('spdp.create')->uri());
        $this->assertSame(
            'document/surat-pemberitahuan-perkembangan-hasil-penyidikan-document/{id}/show',
            Route::getRoutes()->getByName('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.show')->uri()
        );
        $this->assertSame(
            'document/surat-pemberitahuan-perkembangan-hasil-penyidikan-document/{id}/download',
            Route::getRoutes()->getByName('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.download')->uri()
        );
    }

    public function test_otp_mail_uses_dynamic_subject_and_renders_html_and_plain_text(): void
    {
        Mail::fake();

        $mailable = new OTPMail('987654', 5, 'Kode Verifikasi Login ICELL');

        $this->assertSame('Kode Verifikasi Login ICELL', $mailable->envelope()->subject);

        $renderedHtml = $mailable->render();
        $this->assertStringContainsString('987654', $renderedHtml);

        $content = $mailable->content();
        $this->assertSame('email.otp', $content->view);
        $this->assertSame('email.otp_plain', $content->text);
        $this->assertSame('987654', $content->with['otp']);
        $this->assertSame(5, $content->with['expiryMinutes']);
    }

    public function test_age_helper_handles_carbon_3_whole_years_and_edge_cases(): void
    {
        // 1 day before 26th birthday -> 25 (int, not 25.997...)
        $this->assertSame(25, AgeHelper::calculateWholeYears('2000-05-10', '2026-05-09'));

        // Exact 26th birthday -> 26
        $this->assertSame(26, AgeHelper::calculateWholeYears('2000-05-10', '2026-05-10'));

        // 1 day after 26th birthday -> 26
        $this->assertSame(26, AgeHelper::calculateWholeYears('2000-05-10', '2026-05-11'));

        // Birth date after reference date -> null
        $this->assertNull(AgeHelper::calculateWholeYears('2030-01-01', '2026-05-10'));

        // Empty or invalid dates -> null
        $this->assertNull(AgeHelper::calculateWholeYears(null, '2026-05-10'));
        $this->assertNull(AgeHelper::calculateWholeYears('', '2026-05-10'));
        $this->assertNull(AgeHelper::calculateWholeYears('not-a-date', '2026-05-10'));
    }

    public function test_app_mode_helper_reads_from_config_only(): void
    {
        config(['app.mode' => 'LOCAL']);
        $this->assertFalse(AppModeHelper::isProduction());

        config(['app.mode' => 'PRODUCTION']);
        $this->assertTrue(AppModeHelper::isProduction());
    }

    public function test_esignature_token_rotation_succeeds_without_leaking_token_and_preserves_old_token_on_failure(): void
    {
        if (!Schema::hasTable('esignature_tokens')) {
            $this->markTestSkipped('esignature_tokens table is not migrated in this test connection.');
        }

        $originalRow = DB::table('esignature_tokens')
            ->where('provider', ESignatureTokenService::PROVIDER_KEY)
            ->first();

        try {
            config([
                'services.esignature.host' => 'https://tte.example.test',
                'services.esignature.username' => 'icell_user',
                'services.esignature.password' => 'secret_pass',
                'services.esignature.token' => 'Bearer initial-config-token',
            ]);

            Cache::forget('esignature:runtime_token:icell_tte');

            // 1. Successful rotation, followed by 2. Failed rotation (HTTP 500)
            Http::fake([
                'https://tte.example.test/api/values/GetTokenICELL' => Http::sequence()
                    ->push(['data' => 'super-secret-rotated-token-123'], 200)
                    ->push(['message' => 'upstream error'], 500),
            ]);

            $exitCode = Artisan::call('production:esignature:renew-api-token');
            $output = Artisan::output();

            $this->assertSame(0, $exitCode);
            $this->assertStringNotContainsString('super-secret-rotated-token-123', $output);

            $service = app(ESignatureTokenService::class);
            $this->assertSame('Bearer super-secret-rotated-token-123', $service->getToken());

            // 2. Failed rotation must preserve existing valid token and return non-zero exit code
            $failExitCode = Artisan::call('production:esignature:renew-api-token');
            $this->assertSame(1, $failExitCode);
            $this->assertSame('Bearer super-secret-rotated-token-123', $service->getToken());
        } finally {
            Cache::forget('esignature:runtime_token:icell_tte');
            if ($originalRow) {
                DB::table('esignature_tokens')->updateOrInsert(
                    ['provider' => ESignatureTokenService::PROVIDER_KEY],
                    [
                        'encrypted_token' => $originalRow->encrypted_token,
                        'refreshed_at' => $originalRow->refreshed_at,
                        'expires_at' => $originalRow->expires_at,
                        'updated_at' => $originalRow->updated_at,
                        'created_at' => $originalRow->created_at,
                    ]
                );
            } else {
                DB::table('esignature_tokens')
                    ->where('provider', ESignatureTokenService::PROVIDER_KEY)
                    ->delete();
            }
        }
    }

    public function test_no_active_env_calls_or_curl_close_remain_in_app_directory(): void
    {
        $accidentController = file_get_contents(app_path('Http/Controllers/AccidentController.php'));
        $this->assertStringNotContainsString('curl_close(', $accidentController);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path()));
        $activeEnvCalls = [];

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $lines = file($file->getPathname());
            foreach ($lines as $idx => $line) {
                $trimmed = ltrim($line);
                if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*')) {
                    continue;
                }
                if (preg_match('/\benv\s*\(/', $line)) {
                    $activeEnvCalls[] = $file->getFilename() . ':' . ($idx + 1);
                }
            }
        }

        $this->assertSame([], $activeEnvCalls, 'Active env() calls found outside config/: ' . implode(', ', $activeEnvCalls));
    }
}
