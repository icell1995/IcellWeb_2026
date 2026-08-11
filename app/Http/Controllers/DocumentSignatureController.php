<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use App\Models\Accident;
use App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter;
use App\Models\LHGP;
use App\Models\ESignature;
use App\Models\Ref;
use App\Models\Suspect;
use App\Models\SuratSpdp;

use App\Models\Officer;
use App\Models\User;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\LaporanHasilGelarPerkara;
use App\Models\SuratKetetapanPenetapanTersangka;

use App\Traits\DocsOfficersTraits;

class DocumentSignatureController extends Controller
{
    private $userAuth;

    use DocsOfficersTraits;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {    
            $userAuth = Auth::user();
            $this->userAuth = $userAuth;

            //check is there police id
            if(!in_array($userAuth->role_id, [3, 5])){
                return redirect()->route('home')->with('error', 'Akses data ditolak');
            }
    
            return $next($request);
        });
    }
    
    public function index()
    {
        $user = Auth::user();

        $statusIds = ['9', '10', '11', '86'];
        $documentsCollection = $this->getDocumentsByStatus($user, $statusIds);
        
        $documents = $documentsCollection->sortByDesc('updated_at');

        $viewData = [
            'documents' => $documents
        ];

        return view('document-signature.index', $viewData);
    }

    public function view()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

        $viewData = [
            'accidentId' => $accidentId,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'document' => $document,
            'attachment' => $document->attachment()->first()
        ];

        return view('document-signature.view', $viewData);
    }
    
    public function sign()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $user = $this->userAuth;

        //check if user is role 5
        if($user->role_id != '5'){
            return redirect()->route('home')->withErrors(['message' => 'Akses data ditolak']);
        }

        $officer = Officer::withRelated()
            ->selectFullName()
            ->where('user_id', $user->id)
            ->first();

        $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

        //check if document is already signed
        if(in_array($document->status_id,  [10, 11, 86]))
        {
            return redirect()->route('document-signature.index')->withErrors(['message' => 'Dokumen sudah ditandatangani']);
        }

        $viewData = [
            'accidentId' => $accidentId,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'document' => $document,
            'officer' => $officer,
            'user' => $user
        ];

        return view('document-signature.sign', $viewData);
    }

    public function signProcess(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars($request->document_id);
        $documentCategoryId = htmlspecialchars($request->document_category_id);

        $user = $this->userAuth;
        //check if user is role 5
        if($user->role_id != '5'){
            return abort(403);
            // return response()->json([
            //     'code' => 403,
            //     'status' => 'FORBIDDEN',
            //     'message' => 'Failed',
            //     'data' => [
            //         'message' => 'Akses data ditolak',
            //         'status' => 'FAILED'
            //     ]
            // ], 403);
        }
                
        $passphrase = $request->passphrase;

        if(empty($passphrase)){
            return response()->json([
                'code' => 400,
                'status' => 'BAD_REQUEST',
                'message' => 'Failed',
                'data' => [
                    'message' => 'Passphrase tidak boleh kosong',
                    'status' => 'FAILED'
                ]
            ], 400);
        }
        
        $officer = Officer::withRelated()
            ->selectFullName()
            ->where('user_id', $user->id)
            ->first();
        
        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);
            
            if(!in_array($document->status_id, [9])){
                abort(419);
            }

            if(!empty($document)){
                // post header to e-signature
                $authorizationToken = env('ESIGNATURE_API_TOKEN');

                // post body to e-signature
                $identityNumber = $officer->identity_number;
                $passphraseCode = $passphrase;

                // get document
                $documentId = $document->id;

                // get attachment file
                $documentAttachment = $document->attachment()->first();
                $documentAttachmentPath = public_path('documents/attachments/' . $documentAttachment->name);
                $encodedDocumentAttachment = base64_encode(File::get($documentAttachmentPath));

                // send file to e-signature with http post request
                // $response = Http::withHeaders([
                //     'Authorization' => $authorizationToken
                // ])->post(env('ESIGNATURE_API_HOST') . '/api/values/SignTTEICELL', [
                //     'IdDokumen' => $documentId,
                //     'NIK' => $identityNumber,
                //     'PassPhrase' => $passphraseCode,
                //     'Base64' => $encodedDocumentAttachment
                // ]);

                $response = Http::withHeaders([
                    'Authorization' => $authorizationToken
                ])
                ->timeout(90)
                ->connectTimeout(30)
                ->retry(3, 100)
                ->post(env('ESIGNATURE_API_HOST') . '/api/values/SignTTEICELL', [
                    'IdDokumen' => $documentId,
                    'NIK' => $identityNumber,
                    'PassPhrase' => $passphraseCode,
                    'Base64' => $encodedDocumentAttachment
                ]);

                // save response file from e-signature
                $responseBody = $response->body();
                // return $responseBody;
                $responseBodyJson = json_decode($responseBody, true);

		Log::info('DocumentSignatureController API Response: ', [$responseBodyJson]);

                $responseBodyJsonData = $responseBodyJson['Data'];

                $responseDocument = $responseBodyJson['Data']['FileBase64TTE'] ?? [];
                $responseMessage = $responseBodyJsonData['message'];
                $responseStatus = $responseBodyJsonData['Status'];

                if($responseMessage == 'SUCCESS'){
                    // save response file from e-signature
                    $responseDocumentDecoded = base64_decode($responseDocument);
                    $newDocumentExtension = 'pdf';
                    $newDocumentName = Str::uuid() . '.' . $newDocumentExtension;

                    // save response file to documents/attachments
                    $responseDocumentPath = public_path('documents/attachments/' . $newDocumentName);
                    File::put($responseDocumentPath, $responseDocumentDecoded);

                    // save backup file to storage/documents/*/attachments
                    $responseDocumentBackupPath = storage_path('documents/' . $document->documentCategory->alt_code . '/attachments/' . $newDocumentName);
                    File::put($responseDocumentBackupPath, $responseDocumentDecoded);

                    // save response to database
                    $documentAttachment->name = $newDocumentName;
                    $documentAttachment->original_name = $newDocumentName;
                    $documentAttachment->mimetype = 'application/pdf';
                    $documentAttachment->extension = $newDocumentExtension;
                    $documentAttachment->save();
                    
                    // update status document
                    $document->status_id = '86';
                    $document->save();

                    // update release date to documents
                    if($documentCategoryId == '0204'){
                        SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
                            ->where('status_id', '86')
                            ->update([
                                'released_at' => Carbon::now()
                            ]);
                        
                        SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                            ->where('status_id', '86')
                            ->update([
                                'released_at' => Carbon::now()
                            ]);

                        SuratPerintahTugasDocument::where('accident_id', $accidentId)
                            ->where('status_id', '86')
                            ->update([
                                'released_at' => Carbon::now()
                            ]);

                        LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
                            ->where('status_id', '86')
                            ->update([
                                'released_at' => Carbon::now()
                            ]);

                        SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
                            ->where('status_id', '86')
                            ->update([
                                'released_at' => Carbon::now()
                            ]);

                        SuratPemberitahuanDimulainyaPenyidikanDocument::where('id', $documentId)
                            ->update([
                                'released_at' => Carbon::now()
                            ]);
                    }

                    // save passphrase to officer
                    Officer::where('user_id', $user->id)
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
            }

            return response()->json([
                'code' => 400,
                'status' => 'BAD_REQUEST',
                'message' => 'Dokumen Tidak Ditemukan',
                'data' => [
                    'message' => 'Document not found',
                    'status' => 'FAILED'
                ]
            ], 400);
        }catch(\Exception $e){
            DB::rollback();
            //add log
            Log::error('DocumentSignatureController : ', [$e->getMessage()]);
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

    public function verificationIndex()
    {
        $user = Auth::user();

        $statusIds = ['8', '9', '10', '12'];
        $documentsCollection = $this->getDocumentsByStatus($user, $statusIds);
        
        $documents = $documentsCollection->sortByDesc('updated_at');

        $viewData = [
            'documents' => $documents
        ];

        return view('document-signature.verification.index', $viewData);
    }

    public function verificationView()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));
        
        $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

        $viewData = [
            'accidentId' => $accidentId,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'document' => $document
        ];

        return view('document-signature.verification.view', $viewData);
    }

    public function verificationSave(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $isVerified = htmlspecialchars($request->isVerified);
        $message = htmlspecialchars($request->message);

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

            if(!in_array($document->status_id, [8])){
                abort(419);
            }

            if(!empty($document)){
                if(filter_var($isVerified, FILTER_VALIDATE_BOOLEAN) == true){
                    $document->status_id = '12';
                }else{
                    $document->status_id = '4';
                    $document->messages = [
                        'reason_approval_rejected' => $message,
                    ];
                }

                $document->save();

                DB::commit();
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('document-signature.verification.index');
        }

        return redirect()->route('document-signature.verification.index');
    }

    public function verificationRollback(Request $request)
    {
        $accidentId = $request->accidentId;
        $documentId = $request->documentId;
        $documentCategoryId = $request->documentCategoryId;

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

            if(!in_array($document->status_id, [9])){
                abort(419);
            }

            if(!empty($document)){
                $document->status_id = '2';
                $document->save();

                DB::commit();
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('document-signature.verification.index');
        }

        return redirect()->route('document-signature.verification.index');
    }

    public function verificationFinish(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

        $viewData = [
            'accidentId' => $accidentId,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'document' => $document
        ];

        return view('document-signature.verification.finish', $viewData);
    }

    public function verificationFinishSave(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $isVerified = htmlspecialchars($request->isVerified);

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

            if(!in_array($document->status_id, [10])){
                abort(419);
            }

            if(!empty($document)){
                if(filter_var($isVerified, FILTER_VALIDATE_BOOLEAN) == true){
                    $document->status_id = '11';
                }
                $document->save();

                DB::commit();
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('document-signature.verification.index');
        }

        return redirect()->route('document-signature.verification.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('signature');
        $name = time().'.'.$image->getClientOriginalExtension();
        $path = $image->storeAs('public/signatures', $name);

        return back()
            ->with('success','Signature uploaded successfully.')
            ->with('signature',$name);
    }

    //=================================================================================
    
    private function getDocumentRouter($documentCategoryId, $documentId, $accidentId)
    {
        $documentModels = [
            '0204' => SuratPemberitahuanDimulainyaPenyidikanDocument::class,
        ];

        if (array_key_exists($documentCategoryId, $documentModels)) {
            $document = $documentModels[$documentCategoryId]::with(['accident','documentCategory'])
                ->where('id', $documentId)
                ->first();
        } else {
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
        }

        return $document;
    }

    private function getDocumentsByStatus($user, $statusIds) {
        $documentTypes = [
            SuratPemberitahuanDimulainyaPenyidikanDocument::class,
        ];

        $documentsCollection = Collection::make();
    
        foreach ($documentTypes as $documentType) {
            $documents = $documentType::with(['accident', 'documentCategory'])
                ->whereHas('accident', function ($query) use ($user) {
                    // $query->where('polres_id', $user->polres_id);
                    $query->whereIn('polres_id', $this->getOldNewPolresIds($user->polres_id));
                })
                ->whereIn('status_id', $statusIds)
                ->get();
    
            if (!$documents->isEmpty()) {
                $documentsCollection = $documentsCollection->merge($documents);
            }
        }
        
        return $documentsCollection;
    }
}
