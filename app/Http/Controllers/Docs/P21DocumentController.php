<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\Accident;
use App\Models\Doc\P21Document\P21Document;

class P21DocumentController extends Controller
{
    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function show($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $viewData = [
        ];

        return view('docs.p21-document.show', $viewData);
    }
    
    public function create()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $viewData = [
            'accidentId' => $accidentId
        ];

        return view('docs.p21-document.create', $viewData);
    }
    
    public function store(Request $request)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

         // Get URL Parameter
         $accidentId = htmlspecialchars($request->accident_id);

         // Redirect
         return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function edit($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $viewData = [
            'accidentId' => $accidentId
        ];
        
        return view('docs.p21-document.edit', $viewData);
    }
   
    public function update(Request $request, $id)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get URL Parameter
        $accidentId = htmlspecialchars($request->accident_id);

        // Redirect
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }
    
    public function delete($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        // Redirect
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }
    
    public function download($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        
    }

    public function validateRequestForm(Request $request)
    {
        try{
            $validator = $this->validateForm($request);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Silahkan menunggu proses simpan data',
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false, 
                'errors' => 'Terjadi kesalahan pada sistem.',
                'code' => 500,
            ], 500);
        }
    }

    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
        ], [
        ]);
    }
}
