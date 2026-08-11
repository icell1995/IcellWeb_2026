<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Peoples\AuthorizedSignatory;
use App\Models\Polres;

class IsFormsConfirmationComplete
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
        $active = env('FORMS_CONFIRMATION_ACTIVE', false);
        
        if($active == true){
            $user = Auth::user();

            if($user->role_id == 3){
                $polres_id = $user->polres_id;
                $polres = Polres::find($polres_id);
                $authorizedSignatory = AuthorizedSignatory::where('polres_id', $polres_id)->count();
                
                if($polres->is_complete == false && $polres->address != NULL && $authorizedSignatory > 0){
                    return redirect()->route('forms.confirmation');
                }
            }
        }

        return $next($request);
    }
}
