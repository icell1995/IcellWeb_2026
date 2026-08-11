<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Log\EvaluationFormFill;
use App\Models\User;

class EvaluationFormController extends Controller
{
    public function index()
    {
        return view('evaluation-form-director');
    }
   
    public function redirect()
    {
        $userId = Auth::id();

        $user = User::with(['officer'])
            ->selectFullNameExpression()
            ->find($userId);

        EvaluationFormFill::updateOrCreate([
            'user_id' => $userId
        ], [
            'user_id' => $user->id,
            'name' => $user->full_name,
            'register_number' => $user->register_number,
            'rank_name' => $user->officer->rank->name ?? null,
            'police_id' => $user->police_id,
        ]);

        return redirect()->away('https://forms.gle/mZ6EndC28coL6X5d9');
    }
}
