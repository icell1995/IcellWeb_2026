<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Lib\Police;
use App\Models\Officer;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckOfficerDigitalSignatureController extends Controller
{
    private $policeId;
    private $userAuth;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $policeId = request()->policeId;
            $this->policeId = $policeId;
    
            $userAuth = Auth::user();
            $this->userAuth = $userAuth;

            if (!in_array($userAuth->role_id, [1, 2])) {
                // Redirect back to dashboard
                return redirect()->route('home')->with('error', 'Akses data ditolak');
            }
    
            return $next($request);
        });
    }

    public function index()
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->whereHas('officer', function ($query) {
                $query->where('passphrase', '!=', null);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');
        
        if($userAuth->role_id == 3 && !empty($policeId)){
            $usersQuery->where('police_id', $policeId);
        }elseif(empty($policeId) && !in_array($userAuth->role_id, [1, 2])){
            return redirect()->route('home');
        }
        
        $users = $usersQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
        ];

        return view('cms.check-officer-digital-signature.index', $viewData);
    }

    public function test(Request $request)
    {
        $passphrase = $request->passphrase;
        $userId = $request->user_id;

        $officer = Officer::where('user_id', $userId)->first();
        if(empty($officer)){
            return response()->json([
                'code' => 400,
                'status' => 'BAD_REQUEST',
                'message' => 'Officer Not Found',
                'data' => [
                    'message' => 'Officer Not Found',
                    'status' => 'FAILED'
                    ]
                ], 400);
        }
        
        $identityNumber = $officer->identity_number;
        if(empty($identityNumber)){
            return response()->json([
                'code' => 400,
                'status' => 'BAD_REQUEST',
                'message' => 'NIK tidak boleh kosong',
                'data' => [
                    'message' => 'NIK tidak boleh kosong',
                    'status' => 'FAILED'
                ]
            ], 400);
        }

        $passphrase = $request->passphrase;
        if(empty($passphrase)){
            return response()->json([
                'code' => 400,
                'status' => 'BAD_REQUEST',
                'message' => 'Passphrase tidak boleh kosong',
                'data' => [
                    'message' => 'Passphrase tidak boleh kosong',
                    'status' => 'FAILED'
                ]
            ], 400);
        }

        DB::beginTransaction();
        try{ 
            // post header to e-signature
            $authorizationToken = env('ESIGNATURE_API_TOKEN');

            // post body to e-signature
            $passphraseCode = $passphrase;

            $documentAttachmentPath = public_path('file/Test_Passphrase_TTE.docx');
            $encodedDocumentAttachment = base64_encode(File::get($documentAttachmentPath));

            // send file to e-signature with http post request
            $response = Http::withHeaders([
                'Authorization' => $authorizationToken
            ])->post(env('ESIGNATURE_API_HOST') . '/api/values/SignTTEICELL', [
                'IdDokumen' => Str::uuid(),
                'NIK' => $identityNumber,
                'PassPhrase' => $passphraseCode,
                'Base64' => $encodedDocumentAttachment
            ]);

            // save response file from e-signature
            $responseBody = $response->body();
            // return $responseBody;
            $responseBodyJson = json_decode($responseBody, true);

            $responseBodyJsonData = $responseBodyJson['Data'];

            $responseMessage = $responseBodyJsonData['message'];
            $responseStatus = $responseBodyJsonData['Status'];

            if($responseMessage == 'SUCCESS'){
                // save passphrase to officer
                Officer::where('user_id', $userId)
                    ->update([
                        'passphrase' => $passphraseCode
                    ]);

                DB::commit();

                //return message response
                return response()->json([
                    'code' => 200,
                    'status' => 'OK',
                    'message' => 'Success',
                    'data' => [
                        'message' => $responseMessage,
                        'status' => $responseStatus
                    ]
                ], 200);
            }else{
                //return message response
                $responseMessageDecode = json_decode($responseMessage, true);
                $responseMessageError = $responseMessageDecode['error'];

                return response()->json([
                    'code' => 400,
                    'status' => 'BAD_REQUEST',
                    'message' => 'Sistem Dari BSrE Sedang Down, Silahkan Coba Kembali Secara Berkala',
                    'data' => [
                        'message' => $responseMessageError,
                        'status' => $responseStatus
                    ]
                ], 400);
            }
        }catch(\Exception $e){
            DB::rollback();
            //add log
            Log::error('CheckOfficerDigitalSignature : ', [$e->getMessage()]);
            //return message response
            return response()->json([
                'code' => 500,
                'status' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Sistem Dari BSrE Sedang Down, Silahkan Coba Kembali Secara Berkala',
                'data' => [
                    'message' => $e->getMessage(),
                    'status' => 'FAILED'
                ]
            ], 500);
        }
    }
}
