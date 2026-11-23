<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Enums\DocumentTypeEnum;
use App\Http\Middleware\EnsureDocumentTypeIsActive;
use App\Models\Lib\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentFeatureFlagTest extends TestCase
{
    /**
     * 1. Test integritas DocumentTypeEnum PHP 8.2+.
     */
    public function test_document_type_enum_integrity(): void
    {
        $cases = DocumentTypeEnum::cases();
        $this->assertNotEmpty($cases, 'DocumentTypeEnum harus memiliki case.');

        foreach ($cases as $case) {
            $this->assertNotEmpty($case->value, "Enum {$case->name} harus memiliki value ID.");
            $this->assertNotEmpty($case->label(), "Enum {$case->name} harus memiliki label.");
            $this->assertNotEmpty($case->baseRoute(), "Enum {$case->name} harus memiliki baseRoute.");
            $this->assertEquals(2, strlen($case->parentId()), "Parent ID untuk {$case->name} harus 2 digit.");
        }
    }

    /**
     * 2. Test resolusi nama route ke DocumentTypeEnum.
     */
    public function test_document_type_enum_resolves_route_name(): void
    {
        $resolved = DocumentTypeEnum::tryFromRouteName('doc.surat-perintah-penyelidikan-document.create');
        $this->assertEquals(DocumentTypeEnum::SURAT_PERINTAH_PENYELIDIKAN, $resolved);

        $sp2hpResolved = DocumentTypeEnum::tryFromRouteName('doc.sp2hp-document.create');
        $this->assertEquals(DocumentTypeEnum::SP2HP, $sp2hpResolved);

        $unknown = DocumentTypeEnum::tryFromRouteName('cms.home.index');
        $this->assertNull($unknown);
    }

    /**
     * 3. Test Middleware EnsureDocumentTypeIsActive mengizinkan dokumen aktif.
     */
    public function test_middleware_allows_active_document(): void
    {
        Cache::forever(DocumentCategory::CACHE_KEY, [
            'id:0101' => true,
            'route:doc.surat-perintah-penyelidikan-document' => true,
        ]);

        $request = Request::create('/document/surat-perintah-penyelidikan-document/create', 'GET');
        $route = new Route(['GET'], '/document/surat-perintah-penyelidikan-document/create', []);
        $route->name('doc.surat-perintah-penyelidikan-document.create');
        $request->setRouteResolver(fn () => $route);

        $middleware = new EnsureDocumentTypeIsActive();
        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * 4. Test Middleware EnsureDocumentTypeIsActive memblokir mutasi (create/store) dokumen nonaktif dengan HTTP 404.
     */
    public function test_middleware_blocks_inactive_document_mutations_with_404(): void
    {
        Cache::forever(DocumentCategory::CACHE_KEY, [
            'id:0101' => false,
            'route:doc.surat-perintah-penyelidikan-document' => false,
        ]);

        $request = Request::create('/document/surat-perintah-penyelidikan-document/create', 'GET');
        $route = new Route(['GET'], '/document/surat-perintah-penyelidikan-document/create', []);
        $route->name('doc.surat-perintah-penyelidikan-document.create');
        $request->setRouteResolver(fn () => $route);

        $middleware = new EnsureDocumentTypeIsActive();

        $this->expectException(NotFoundHttpException::class);
        $middleware->handle($request, function ($req) {
            return response('SHOULD_NOT_REACH_HERE');
        });
    }

    /**
     * 5. Test Middleware EnsureDocumentTypeIsActive mengizinkan read-only (show/download) dokumen nonaktif untuk data historis.
     */
    public function test_middleware_allows_inactive_document_read_only_access(): void
    {
        Cache::forever(DocumentCategory::CACHE_KEY, [
            'id:0101' => false,
            'route:doc.surat-perintah-penyelidikan-document' => false,
        ]);

        $request = Request::create('/document/surat-perintah-penyelidikan-document/123/show', 'GET');
        $route = new Route(['GET'], '/document/surat-perintah-penyelidikan-document/{id}/show', []);
        $route->name('doc.surat-perintah-penyelidikan-document.show');
        $request->setRouteResolver(fn () => $route);

        $middleware = new EnsureDocumentTypeIsActive();
        $response = $middleware->handle($request, function ($req) {
            return response('SHOW_HISTORICAL_DATA_OK');
        });

        $this->assertEquals('SHOW_HISTORICAL_DATA_OK', $response->getContent());
    }

    /**
     * 6. Test proteksi CMS Document Management dari user tanpa autentikasi / non-admin.
     */
    public function test_unauthenticated_user_cannot_access_cms_document_management(): void
    {
        $response = $this->get('/cms/document-management');
        $response->assertRedirect(route('login'));
    }

    /**
     * 7. Test user non-administrator (role_id != 1) diblokir dengan 403 Forbidden.
     */
    public function test_non_admin_user_is_forbidden_from_cms_document_management(): void
    {
        $nonAdmin = new \App\Models\User();
        $nonAdmin->id = 999;
        $nonAdmin->role_id = 2; // Operator / User biasa

        $response = $this->actingAs($nonAdmin)->get('/cms/document-management');
        $response->assertStatus(403);
    }

    /**
     * 8. Test DocumentFeatureFlagService methods and architecture contract.
     */
    public function test_service_methods_and_contract(): void
    {
        $service = app(\App\Services\Doc\DocumentFeatureFlagService::class);
        $this->assertInstanceOf(\App\Services\Doc\DocumentFeatureFlagService::class, $service);
        $this->assertTrue(method_exists($service, 'getDashboardData'));
        $this->assertTrue(method_exists($service, 'toggleDocument'));
        $this->assertTrue(method_exists($service, 'toggleStage'));
        $this->assertTrue(method_exists($service, 'batchToggleStage'));
        $this->assertTrue(method_exists($service, 'syncFromEnum'));
        $this->assertTrue(method_exists($service, 'getDocumentHistory'));
    }

    /**
     * 9. Test Middleware EnsureDocumentTypeIsActive memblokir surat jika Kategori (STAGE) induknya nonaktif.
     */
    public function test_middleware_blocks_document_when_parent_stage_is_inactive(): void
    {
        Cache::forever(DocumentCategory::CACHE_KEY, [
            'stage:01' => false,
            'id:0101' => false,
            'route:doc.surat-perintah-penyelidikan-document' => false,
        ]);

        $request = Request::create('/document/surat-perintah-penyelidikan-document/create', 'GET');
        $route = new \Illuminate\Routing\Route(['GET'], '/document/surat-perintah-penyelidikan-document/create', []);
        $route->name('doc.surat-perintah-penyelidikan-document.create');
        $request->setRouteResolver(fn () => $route);

        $middleware = new EnsureDocumentTypeIsActive();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $middleware->handle($request, function ($req) {
            return response('SHOULD_BE_BLOCKED');
        });
    }
}
