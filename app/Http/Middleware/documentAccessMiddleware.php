<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Accident;

class DocumentAccessMiddleware
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
         // Get URL Parameter
         $accidentId = htmlspecialchars(request()->query('accident_id'));

         // Check if accident_id is exist
         if (!isset($accidentId) || empty($accidentId)) {
             return redirect()->route('produktivitas')->with('error', 'Parameter ID Kecelakaan tidak ditemukan');
         }
         
         if(!Str::isUuid($accidentId)){
            return redirect()->route('produktivitas')->with('error', 'Parameter ID Kecelakaan tidak valid');
         }
 
         try{
            $accident = Accident::where('id', $accidentId)->exists();
            if (!$accident) {
                return redirect()->route('produktivitas')->with('error', 'Data Kecelakaan tidak ditemukan');
            }
         }catch(\Exception $e){
            return redirect()->route('produktivitas')->with('error', 'Data Kecelakaan tidak ditemukan');
         }

        return $next($request);
    }
}
