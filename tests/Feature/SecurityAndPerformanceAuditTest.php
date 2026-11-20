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

    /**
     * 6. Pastikan RegionalScopeService::authorizeAccident mencegah akses dokumen lintas Satker (Anti-IDOR).
     */
    public function test_regional_scope_service_authorizes_accident_by_satker(): void
    {
        $polresInvestigator = new User();
        $polresInvestigator->id = 'inv-1101';
        $polresInvestigator->role_id = 4;
        $polresInvestigator->polda_id = '11';
        $polresInvestigator->polres_id = '1101';
        $polresInvestigator->police_id = '1101';

        $poldaAdmin = new User();
        $poldaAdmin->id = 'polda-11';
        $poldaAdmin->role_id = 2;
        $poldaAdmin->polda_id = '11';
        $poldaAdmin->polres_id = null;

        $superAdmin = new User();
        $superAdmin->id = 'super-1';
        $superAdmin->role_id = 1;

        $ownAccident = (object) [
            'id' => 'acc-1101',
            'polda_id' => '11',
            'polres_id' => '1101',
            'police_id' => '1101',
        ];

        $samePoldaOtherPolresAccident = (object) [
            'id' => 'acc-1102',
            'polda_id' => '11',
            'polres_id' => '1102',
            'police_id' => '1102',
        ];

        $otherPoldaAccident = (object) [
            'id' => 'acc-1201',
            'polda_id' => '12',
            'polres_id' => '1201',
            'police_id' => '1201',
        ];

        // Penyidik hanya boleh mengakses perkara di Polresnya sendiri
        $this->assertTrue(RegionalScopeService::authorizeAccident($polresInvestigator, $ownAccident));
        $this->assertFalse(RegionalScopeService::authorizeAccident($polresInvestigator, $samePoldaOtherPolresAccident));
        $this->assertFalse(RegionalScopeService::authorizeAccident($polresInvestigator, $otherPoldaAccident));

        // Admin Polda boleh mengakses perkara di seluruh Polres dalam Poldanya, tapi tidak Polda lain
        $this->assertTrue(RegionalScopeService::authorizeAccident($poldaAdmin, $ownAccident));
        $this->assertTrue(RegionalScopeService::authorizeAccident($poldaAdmin, $samePoldaOtherPolresAccident));
        $this->assertFalse(RegionalScopeService::authorizeAccident($poldaAdmin, $otherPoldaAccident));

        // Super Admin boleh mengakses seluruh perkara
        $this->assertTrue(RegionalScopeService::authorizeAccident($superAdmin, $otherPoldaAccident));
    }

    /**
     * 7. Pastikan seluruh rute modul dokumen baru terlindungi middleware document-access.
     */
    public function test_new_document_routes_enforce_document_access_middleware(): void
    {
        $newDocRoutes = [
            'doc.resume-document.create',
            'doc.daftar-saksi-document.create',
            'doc.daftar-ahli-document.create',
            'doc.daftar-tersangka-document.create',
            'doc.surat-perintah-penangkapan-document.create',
            'doc.surat-daftar-pencarian-orang-document.create',
            'doc.surat-perintah-tugas-penangkapan-document.create',
            'doc.surat-perintah-penggeledahan-rumah-document.create',
            'doc.surat-panggilan-tersangka-document.create',
            'doc.surat-permintaan-keterangan-ahli-document.create',
            'doc.berita-acara-pemeriksaan-saksi-document.create',
            'doc.berita-acara-pemeriksaan-ahli-document.create',
            'doc.berita-acara-pemeriksaan-tersangka-document.create',
            'doc.surat-perintah-penggeledahan-angkutan-document.create',
            'doc.surat-perintah-penggeledahan-badan-document.create',
            'doc.surat-perintah-penyitaan-document.create',
            'doc.surat-perintah-pengembalian-barang-bukti-document.create',
        ];

        $router = app('router');
        foreach ($newDocRoutes as $routeName) {
            $route = $router->getRoutes()->getByName($routeName);
            $this->assertNotNull($route, "Route {$routeName} harus terdaftar.");
            $middlewares = $route->gatherMiddleware();
            $this->assertContains('auth', $middlewares, "Route {$routeName} wajib memiliki middleware auth.");
            $this->assertContains('document-access', $middlewares, "Route {$routeName} wajib memiliki middleware document-access.");
        }
    }

    /**
     * 8. Pastikan tidak ada regresi SQL injection string concatenation atau undefined variable di AccidentController & WordController.
     */
    public function test_controllers_are_free_from_sql_concatenation_and_merge_regressions(): void
    {
        $accidentControllerSource = file_get_contents(app_path('Http/Controllers/AccidentController.php'));
        $this->assertStringNotContainsString('$DocSuratTugas1', $accidentControllerSource);
        $this->assertStringNotContainsString("accident_id = '\$get_accident'", $accidentControllerSource);
        $this->assertStringNotContainsString("accident_id='\$get_accident'", $accidentControllerSource);
        $this->assertStringNotContainsString('accident_id = \\\'\' . $get_accident', $accidentControllerSource);

        $wordControllerSource = file_get_contents(app_path('Http/Controllers/WordController.php'));
        $this->assertStringNotContainsString('accident_id = \\\'\'.$id', $wordControllerSource);

        $resumeControllerSource = file_get_contents(app_path('Http/Controllers/Docs/ResumeDocumentController.php'));
        $this->assertStringContainsString('use App\Models\Peoples\AuthorizedSignatory;', $resumeControllerSource);

        $bladeSource = file_get_contents(resource_path('views/produktivitas/case-document-table.blade.php'));
        $this->assertMatchesRegularExpression('/if\s*\(\$isBaSerahTerimaDocument\)\s*\{\s*continue;\s*\}/', $bladeSource);
    }

    /**
     * 9. Pastikan PhpWord output escaping aktif secara global untuk mencegah OOXML injection & file docx rusak.
     */
    public function test_phpword_output_escaping_is_enabled_globally(): void
    {
        $this->assertTrue(\PhpOffice\PhpWord\Settings::isOutputEscapingEnabled());
    }
}

