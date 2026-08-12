<?php

namespace App\Http\Controllers\IcellServices\ApiIrsmsKorlantas;

use App\Http\Controllers\Controller;
use App\Models\Lib\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PositionController extends Controller
{
    public function getPosition(Request $request)
    {
        if ($request->bearerToken() !== 'mIXopU4hPzTKQpHFlVwjT3uUvkw9hb#IRSMS') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API Token',
                'error_code' => 'INVALID_TOKEN'
            ], 401);
        }

        if (!$request->isJson()) {
            return response()->json([
                'success' => false,
                'mesasage' => 'Only JSON requests are accepted'
            ], 415);
        }

        try {
            // Get and sanitize input parameters
            $roleId = htmlspecialchars($request->input('role_id'));
            $poldaId = htmlspecialchars($request->input('polda_id'));
            $polresId = htmlspecialchars($request->input('polres_id'));

            // Validate that role_id exists
            if (empty($roleId)) {
                throw new \InvalidArgumentException('Role is required', 400);
            }

            // For role_id = 2 (Polda level), polda_id is required
            if ($roleId == '2') {
                if (empty($poldaId)) {
                    throw new \InvalidArgumentException('Polda selection is required for this role', 400);
                }

                $positions = Position::where('police_id', $poldaId)
                    ->where('is_active', true)
                    ->get();
            }
            // For other roles (assuming they need polres_id)
            else {
                if (empty($poldaId)) {
                    throw new \InvalidArgumentException('Polda selection is required first', 400);
                }

                if (empty($polresId)) {
                    throw new \InvalidArgumentException('Polres selection is required for this role', 400);
                }

                $positions = Position::where('police_id', $polresId)
                    ->where('is_active', true)
                    ->get();
            }

            // Check if positions were found
            if ($positions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No positions found for the selected criteria',
                    'error_code' => 'NOT_FOUND'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $positions,
                'count' => $positions->count()
            ]);
        } catch (\InvalidArgumentException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'BAD_REQUEST'
            ], $e->getCode() ?: 400);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error_code' => 'DATABASE_ERROR',
                'details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        } catch (\Exception $e) {
            // Handle all other exceptions
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred',
                'error_code' => 'INTERNAL_SERVER_ERROR',
                'details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
}
