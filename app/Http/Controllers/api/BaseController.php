<?php



namespace App\Http\Controllers\api;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller as Controller;



class BaseController extends Controller

{

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendResponse(mixed $result, string $message = ''): \Illuminate\Http\JsonResponse
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): \Illuminate\Http\JsonResponse
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
       return response()->json($response, $code);
    }

}