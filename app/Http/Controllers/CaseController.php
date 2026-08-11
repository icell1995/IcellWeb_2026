<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaseController extends Controller
{
    public function registerIndex()
    {
        $viewData = [

        ];

        return view('produktivitas.case.register.index', $viewData);
    }
}
