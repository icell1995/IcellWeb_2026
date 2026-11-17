<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Lib\DocumentCategory;
use App\Enums\DocumentTypeEnum;

class EnsureDocumentTypeIsActive
{
    /**
     * Actions that mutate or create document records.
     * When a document type is inactive, these actions are blocked completely.
     */
    protected array $mutativeActions = [
        'create',
        'store',
        'edit',
        'update',
        'delete',
        'destroy',
        'apiValidateRequestForm',
        'validateRequestForm',
        'generateWord',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $documentIdParam Optional explicit document ID parameter
     */
    public function handle(Request $request, Closure $next, ?string $documentIdParam = null): Response
    {
        $route = $request->route();
        $routeName = $route?->getName() ?? '';
        $activeMap = DocumentCategory::getActiveMap();

        $documentCategoryActive = true;

        // 1. Check if document category ID is explicitly passed as parameter or query
        $catId = $documentIdParam;
        if (empty($catId) && $route instanceof \Illuminate\Routing\Route) {
            try {
                $catId = $route->parameter('document_category_id');
            } catch (\Throwable) {
                $catId = null;
            }
        }
        $catId = $catId ?: $request->query('document_category_id') ?: $request->input('document_category_id');

        if (!empty($catId) && isset($activeMap['id:' . $catId])) {
            $documentCategoryActive = (bool) $activeMap['id:' . $catId];
        } else {
            // 2. Identify document type via route name
            $matchedEnum = DocumentTypeEnum::tryFromRouteName($routeName);

            if ($matchedEnum) {
                $documentCategoryActive = $activeMap['id:' . $matchedEnum->value] ?? true;
            } else {
                // Check if route name matches any base_route in active map
                foreach ($activeMap as $key => $isActive) {
                    if (str_starts_with($key, 'route:')) {
                        $baseRoute = substr($key, 6);
                        if (str_starts_with($routeName, $baseRoute . '.')) {
                            $documentCategoryActive = (bool) $isActive;
                            break;
                        }
                    }
                }
            }
        }

        // If active, proceed without obstruction
        if ($documentCategoryActive) {
            return $next($request);
        }

        // Determine route action (e.g. create, store, edit, show, download)
        $action = str_contains($routeName, '.') ? substr(strrchr($routeName, '.'), 1) : '';

        // Block mutative operations when inactive (return 404 to avoid information disclosure)
        if (empty($action) || in_array($action, $this->mutativeActions, true) || $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis dokumen ini saat ini sedang dinonaktifkan oleh regulasi atau administrator.',
                ], 404);
            }

            abort(404, 'Formulir atau jenis dokumen ini saat ini sedang dinonaktifkan.');
        }

        // Allow read-only operations (show, download) for historical records
        return $next($request);
    }
}
