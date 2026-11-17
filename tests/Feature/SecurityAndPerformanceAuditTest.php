<?php

namespace Tests\Feature;

use App\Http\Controllers\LoginController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Mail\OTPMail;
use App\Models\User;
use App\Services\Regional\RegionalScopeService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ReflectionClass;
use Tests\TestCase;

class SecurityAndPerformanceAuditTest extends TestCase
{
    /**
     * 1. Pastikan rute-rute sensitif me-redirect pengguna tanpa autentikasi ke /login.
     */
    public function test_unauthenticated_users_are_redirected_from_protected_routes(): void
    {
        $protectedGetRoutes = [
            '/anev',
            '/anev/get_report_individu',
            '/dashboardicell',
            '/search',
            '/search_user',
            '/produktivitas/ajax/regencies/11',
            '/produktivitas/ajax/districts/1101',
            '/produktivitas/ajax/villages/110101',
        ];

        foreach ($protectedGetRoutes as $uri) {
            $response = $this->get($uri);
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * 2. Pastikan OTPMail tidak mengekspos properti $otp sebagai public dan user non-superadmin tidak membocorkan OTP.
     */
    public function test_otp_is_not_publicly_exposed_in_mail_or_login_controller(): void
    {
        $mailReflection = new ReflectionClass(OTPMail::class);
        $this->assertTrue($mailReflection->hasProperty('otp'));
        $otpProperty = $mailReflection->getProperty('otp');
        $this->assertTrue(
            $otpProperty->isProtected() || $otpProperty->isPrivate(),
            'Properti $otp pada OTPMail harus protected atau private.'
        );

        $loginControllerSource = file_get_contents((new ReflectionClass(LoginController::class))->getFileName());
        // Memastikan default case (Level 2-6) selalu mengirim 'otp' => null
        $this->assertStringContainsString("'otp' => null", $loginControllerSource);
    }

    /**
     * 3. Pastikan RegionalScopeService mencegah IDOR lintas Satker dan eskalasi hak akses ke Super Admin.
     */
    public function test_regional_scope_service_blocks_idor_and_privilege_escalation(): void
    {
        $polresAdmin = new User();
        $polresAdmin->id = 'actor-polres-1';
        $polresAdmin->role_id = 3;
        $polresAdmin->polda_id = '11';
        $polresAdmin->polres_id = '1101';
        $polresAdmin->police_id = '1101';

        $superAdminTarget = new User();
        $superAdminTarget->id = 'target-super-admin';
        $superAdminTarget->role_id = 1;
        $superAdminTarget->polda_id = '11';
        $superAdminTarget->polres_id = '1101';
        $superAdminTarget->police_id = '1101';

        $otherPolresTarget = new User();
        $otherPolresTarget->id = 'target-other-polres';
        $otherPolresTarget->role_id = 4;
        $otherPolresTarget->polda_id = '12';
        $otherPolresTarget->polres_id = '1201';
        $otherPolresTarget->police_id = '1201';

        $samePolresTarget = new User();
        $samePolresTarget->id = 'target-same-polres';
        $samePolresTarget->role_id = 4;
        $samePolresTarget->polda_id = '11';
        $samePolresTarget->polres_id = '1101';
        $samePolresTarget->police_id = '1101';

        // Admin Polres tidak boleh memodifikasi Super Admin meskipun satkernya sama
        $this->assertFalse(RegionalScopeService::authorizeTargetUser($polresAdmin, $superAdminTarget));

        // Admin Polres tidak boleh memodifikasi personel Polres lain
        $this->assertFalse(RegionalScopeService::authorizeTargetUser($polresAdmin, $otherPolresTarget));

        // Admin Polres boleh memodifikasi personel di Polres yang sama
        $this->assertTrue(RegionalScopeService::authorizeTargetUser($polresAdmin, $samePolresTarget));

        // Admin Polres tidak boleh menetapkan role Super Admin (1) atau Admin Polda (2)
        $this->assertFalse(RegionalScopeService::canAssignRole($polresAdmin, 1));
        $this->assertFalse(RegionalScopeService::canAssignRole($polresAdmin, 2));
        $this->assertTrue(RegionalScopeService::canAssignRole($polresAdmin, 4));
    }

    /**
     * 4. Pastikan upload file non-gambar ditolak oleh validator pada endpoint profil, carousel, dan struktur organisasi.
     */
    public function test_non_image_uploads_are_rejected_on_upload_endpoints(): void
    {
        $this->withoutMiddleware();

        $fakeUser = new User();
        $fakeUser->id = 'test-user-upload';
        $fakeUser->role_id = 1;
        $fakeUser->username = 'test_uploader';

        $maliciousPhpFile = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');

        $responseProfile = $this->actingAs($fakeUser)->postJson('/update_profile', [
            'avatar' => $maliciousPhpFile,
        ]);
        $responseProfile->assertStatus(422)->assertJsonValidationErrors(['avatar']);

        $responseCarousel = $this->actingAs($fakeUser)->postJson('/caraousel/save_image', [
            'name_image' => $maliciousPhpFile,
        ]);
        $responseCarousel->assertStatus(422)->assertJsonValidationErrors(['name_image']);

        $responseStruktur = $this->actingAs($fakeUser)->postJson('/organisasi/struktur-organisasi', [
            'filename' => $maliciousPhpFile,
        ]);
        $responseStruktur->assertStatus(422)->assertJsonValidationErrors(['filename']);
    }

    /**
     * 5. Pastikan ApiAuthMiddleware memvalidasi token sesuai scope instansi dengan hash_equals.
     */
    public function test_api_auth_middleware_enforces_service_token_scope(): void
    {
        config([
            'services.icell_api_tokens.emp' => 'emp-secret-token-123',
            'services.icell_api_tokens.irsms' => 'irsms-secret-token-456',
        ]);

        $middleware = new ApiAuthMiddleware();

        // Token EMP tidak boleh diterima pada endpoint ber-scope IRSMS
        $wrongScopeRequest = Request::create('/api/irsms/test', 'POST');
        $wrongScopeRequest->headers->set('Authorization', 'Bearer emp-secret-token-123');
        $wrongScopeResponse = $middleware->handle($wrongScopeRequest, fn () => response('OK', 200), 'irsms');
        $this->assertEquals(401, $wrongScopeResponse->getStatusCode());

        // Token IRSMS diterima pada endpoint ber-scope IRSMS
        $validScopeRequest = Request::create('/api/irsms/test', 'POST');
        $validScopeRequest->headers->set('Authorization', 'Bearer irsms-secret-token-456');
        $validScopeResponse = $middleware->handle($validScopeRequest, fn () => response('OK', 200), 'irsms');
        $this->assertEquals(200, $validScopeResponse->getStatusCode());
    }
}

