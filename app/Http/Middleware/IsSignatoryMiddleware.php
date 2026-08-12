<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\Officer;

class IsSignatoryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $active = true;

        if ($active == true) {
            $officer = Officer::with(['position.positionCluster'])->where('user_id', Auth::id())->first();


            if (isset($officer->position->positionCluster)) {
                $positionCluster = $officer->position->positionCluster;

                if (
                    $positionCluster->is_can_signatory == true && isset($officer->position->is_can_signatory) &&
                    $officer->position->is_can_signatory == true
                ) {
                    if (empty($officer->passphrase)) {
                        return redirect()->route('esignature-confirmation.index');
                    }
                }
            }
        }

        return $next($request);
    }
}
