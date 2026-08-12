<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CheckCanEntryDocument
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && Gate::denies('can-entry-document')) {
            $routeName = $request->route() ? $request->route()->getName() : '';
            if ($routeName === 'doc.createDocumentRouter' || str_ends_with($routeName, '.create') || str_ends_with($routeName, '.store')) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Anda tidak memiliki akses untuk menambah dokumen.'], 403);
                }
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah dokumen.');
            }
        }
        return $next($request);
    }
}
