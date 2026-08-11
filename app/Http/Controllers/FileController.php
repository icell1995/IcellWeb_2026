<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(){
        $pathToFile = public_path('documents/attachments/XGOzvYFt8wzqhnPaQtMj20VCsnl1YW5MRnJ2lZgP.docx');
        return response()->file($pathToFile);
    }
}
