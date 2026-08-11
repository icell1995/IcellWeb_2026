<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\Log\EvaluationFormFill;

class IsEvaluationFormFilledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $active = true;

        if($active == true){
            $userCount = EvaluationFormFill::where('user_id', Auth::id())->count();
    
            if ($userCount == 0) {
                return redirect()->route('evaluation-form-fill.index');
            }
        }

        return $next($request);
    }
}
