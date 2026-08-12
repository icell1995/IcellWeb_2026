<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Lib;

use App\Http\Controllers\Controller;
use App\Http\Resources\Lib\PoliceResource;
use Illuminate\Http\Request;

use App\Models\Lib\Police;

class PoliceController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 100);
            $page = $request->input('page', 1);

            $polices = Police::active()
                                ->orderBy('id', 'asc')
                                ->paginate($perPage, ['*'], 'page', $page);

            if ($polices->isEmpty()) {
                return $this->errorResponse('Not Found', 'Data not found.', 404);
            }

            return $this->successResponse(
                PoliceResource::collection($polices),
                'Success',
                $polices->currentPage(),
                $polices->total(),
                $polices->lastPage(),
                $polices->count(),
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Internal Server Error', 'An error occurred on the server.', 500);
        }
    }

     /**
     * Generate a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $page
     * @param  int  $totalData
     * @param  int  $totalPage
     * @param  int  $totalDataSent
     * @return \Illuminate\Http\Response
     */
    private function successResponse($data, $message, $page, $totalData, $totalPage, $totalDataSent)
    {
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'message' => $message,
            'pagination' => [
                'Page' => $page,
                'TotalData' => $totalData,
                'TotalPage' => $totalPage,
                'TotalDataSent' => $totalDataSent,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Generate an error response.
     *
     * @param  string  $status
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    private function errorResponse($status, $message, $code)
    {
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => null,
        ]);
    }

    private function getPublicIpAddress(Request $request){
        // Check if the request has X-Forwarded-For header
        $ipAddress = $request->header('X-Forwarded-For');

        // If X-Forwarded-For header is not present, check X-Real-IP header
        if (empty($ipAddress)) {
            $ipAddress = $request->header('X-Real-IP');
        }

        // If both headers are not present, use the remote address
        if (empty($ipAddress)) {
            $ipAddress = $request->ip();
        }

        return $ipAddress;
    }
}
