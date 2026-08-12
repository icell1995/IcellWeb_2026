<?php

namespace App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\V1\Ext;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class UpdateLPController extends Controller
{
    public function updateLP(Request $request)
    {
        $routeAccidentId = $request->route('accident_id'); 
        $queryAccidentId = $request->query('accident_id'); 

        try {
            $validated = $request->validate([
                'id'                   => ['sometimes', 'uuid'], // opsional (body)
                'md'                   => ['sometimes','nullable','integer','min:0','required_without_all:lb,lr'],
                'lb'                   => ['sometimes','nullable','integer','min:0','required_without_all:md,lr'],
                'lr'                   => ['sometimes','nullable','integer','min:0','required_without_all:md,lb'],
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, 'VALIDATION_ERROR', 'Validasi gagal.', $e->errors());
        }

        $bodyAccidentId = $validated['id'] ?? null;
        $accidentId = $bodyAccidentId ?? $routeAccidentId ?? $queryAccidentId;

        if (!$accidentId) {
            return $this->errorResponse(422, 'VALIDATION_ERROR', 'Parameter id (body), {accident_id} (route), atau accident_id (query) wajib diisi.');
        }

        // Jika lebih dari satu sumber ID & nilainya berbeda → tolak
        $candidates = array_values(array_filter([$bodyAccidentId, $routeAccidentId, $queryAccidentId]));
        if (count(array_unique($candidates)) > 1) {
            return $this->errorResponse(422, 'VALIDATION_ERROR', 'id (body), {accident_id} (route), dan accident_id (query) tidak konsisten.');
        }

        // Payload yang boleh diubah
        $payload = Arr::only($validated, ['md','lb','lr']);
        if (empty($payload)) {
            return $this->errorResponse(400, 'BAD_REQUEST', 'Tidak ada kolom yang diubah.');
        }

        try {
            $result = DB::transaction(function () use ($accidentId, $payload, $validated, $request) {
                $row = DB::table('accidents')
                    ->select('id','md','lb','lr','updated_at')
                    ->where('id', $accidentId)
                    ->lockForUpdate()
                    ->first();

                if (!$row) {
                    throw new \RuntimeException('NOT_FOUND');
                }

                // Optimistic concurrency: dukung body.if_unmodified_since atau header If-Unmodified-Since
                $ifUnmodifiedSince = $validated['if_unmodified_since'] ?? $request->header('If-Unmodified-Since');
                if ($ifUnmodifiedSince) {
                    $clientTs = Carbon::parse($ifUnmodifiedSince)->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $serverTs = Carbon::parse($row->updated_at)->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                    if ($serverTs !== $clientTs) {
                        throw new \RuntimeException('CONFLICT'); // 409
                    }
                }

                // Cegah no-op: bila nilai sama persis, kembalikan NO_CHANGE
                $current = [
                    'md'           => $row->md,
                    'lb'           => $row->lb,
                    'lr'           => $row->lr,
                ];
                $incoming = Arr::only($payload, array_keys($current));
                $diff = array_diff_assoc($incoming, $current);
                if (empty($diff)) {
                    throw new \RuntimeException('NO_CHANGE');
                }

                $payload['updated_at'] = now();

                DB::table('accidents')
                    ->where('id', $accidentId)
                    ->update($payload);

                return DB::table('accidents')
                    ->select('id','md','lb','lr','updated_at')
                    ->where('id', $accidentId)
                    ->first();
            });

            return $this->successResponse('Update berhasil.', $result);
        } catch (\RuntimeException $e) {
            switch ($e->getMessage()) {
                case 'NOT_FOUND':
                    return $this->errorResponse(404, 'NOT_FOUND', 'Data accidents tidak ditemukan untuk id tersebut.');
                case 'CONFLICT':
                    return $this->errorResponse(409, 'EDIT_CONFLICT', 'Data telah berubah di server. Silakan refresh data dan coba lagi.');
                case 'NO_CHANGE':
                    return $this->errorResponse(400, 'NO_CHANGE', 'Nilai yang dikirim sama dengan data di server.');
                default:
                    return $this->errorResponse(500, 'INTERNAL_SERVER_ERROR', 'Terjadi kesalahan internal.');
            }
        } catch (\Throwable $e) {
            return $this->errorResponse(500, 'INTERNAL_SERVER_ERROR', $e->getMessage());
        }
    }

    public function updateLPState(Request $request)
    {
        $routeAccidentId = $request->route('accident_id');
        $queryAccidentId = $request->query('accident_id');

        try {
            $validated = $request->validate([
                'id'                  => ['sometimes', 'uuid'],
                'special_info'        => ['sometimes', 'nullable', 'string', 'max:100', 'required_without:state_irsms'],
                'state_irsms'         => ['sometimes', 'nullable', 'integer', 'in:1,2,9', 'required_without:special_info'],

            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse(422, 'VALIDATION_ERROR', 'Validasi gagal.', $e->errors());
        }

        $bodyAccidentId = $validated['id'] ?? null;
        $accidentId = $bodyAccidentId ?? $routeAccidentId ?? $queryAccidentId;

        if (!$accidentId) {
            return $this->errorResponse(422, 'VALIDATION_ERROR', 'Parameter id (body), {accident_id} (route), atau accident_id (query) wajib diisi.');
        }

        // Tolak bila ada >1 sumber ID dan nilainya berbeda
        $candidates = array_values(array_filter([$bodyAccidentId, $routeAccidentId, $queryAccidentId]));
        if (count(array_unique($candidates)) > 1) {
            return $this->errorResponse(422, 'VALIDATION_ERROR', 'id (body), {accident_id} (route), dan accident_id (query) tidak konsisten.');
        }

        // Payload yang boleh diubah
        $payload = Arr::only($validated, ['special_info', 'state_irsms']);
        if (empty($payload)) {
            return $this->errorResponse(400, 'BAD_REQUEST', 'Tidak ada kolom yang diubah.');
        }

        if (array_key_exists('special_info', $payload)) {
            $val = $payload['special_info'];
            if ($val === null) {
                $payload['special_info'] = '-';
            } elseif (is_string($val)) {
                $trim = trim($val);
                // anggap "-" juga kalau string kosong atau string "null"
                $payload['special_info'] = ($trim === '' || strtolower($trim) === 'null')
                    ? '-'
                    : mb_substr($trim, 0, 100); // jaga panjang sesuai validasi
            } else {
                // tipe tak terduga → fallback "-"
                $payload['special_info'] = '-';
            }
        }

        try {
            $result = DB::transaction(function () use ($accidentId, $payload, $validated, $request) {
                $row = DB::table('accidents')
                    ->select('id', 'special_info', 'state_irsms', 'updated_at')
                    ->where('id', $accidentId)
                    ->lockForUpdate()
                    ->first();

                if (!$row) {
                    throw new \RuntimeException('NOT_FOUND');
                }

                // Optimistic concurrency (pakai body.if_unmodified_since atau header If-Unmodified-Since)
                $ifUnmodifiedSince = $validated['if_unmodified_since'] ?? $request->header('If-Unmodified-Since');
                if ($ifUnmodifiedSince) {
                    $clientTs = Carbon::parse($ifUnmodifiedSince)->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $serverTs = Carbon::parse($row->updated_at)->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                    if ($serverTs !== $clientTs) {
                        throw new \RuntimeException('CONFLICT'); // 409
                    }
                }

                // Cegah no-op
                $current = [
                    'special_info' => $row->special_info,
                    'state_irsms'  => $row->state_irsms,
                ];
                $incoming = Arr::only($payload, array_keys($current));
                $diff = array_diff_assoc($incoming, $current);
                if (empty($diff)) {
                    throw new \RuntimeException('NO_CHANGE');
                }

                $payload['updated_at'] = now();

                DB::table('accidents')
                    ->where('id', $accidentId)
                    ->update($payload);

                return DB::table('accidents')
                    ->select('id', 'special_info', 'state_irsms', 'updated_at')
                    ->where('id', $accidentId)
                    ->first();
            });

            return $this->successResponse('Update berhasil.', $result);
        } catch (\RuntimeException $e) {
            switch ($e->getMessage()) {
                case 'NOT_FOUND':
                    return $this->errorResponse(404, 'NOT_FOUND', 'Data accidents tidak ditemukan untuk id tersebut.');
                case 'CONFLICT':
                    return $this->errorResponse(409, 'EDIT_CONFLICT', 'Data telah berubah di server. Silakan refresh data dan coba lagi.');
                case 'NO_CHANGE':
                    return $this->errorResponse(400, 'NO_CHANGE', 'Nilai yang dikirim sama dengan data di server.');
                default:
                    return $this->errorResponse(500, 'INTERNAL_SERVER_ERROR', 'Terjadi kesalahan internal.');
            }
        } catch (\Throwable $e) {
            return $this->errorResponse(500, 'INTERNAL_SERVER_ERROR', $e->getMessage());
        }
    }

    protected function successResponse(string $message = 'OK', mixed $data = null, int $httpStatus = 200)
    {
        return response()->json([
            'status'    => 'success',
            'http_code' => $httpStatus,
            'message'   => $message,
            'data'      => $data,
        ], $httpStatus);
    }

    protected function errorResponse(int $httpStatus = 400, string $errorCode = 'ERROR', string $message = 'Terjadi kesalahan.', array $errors = null)
    {
        $payload = [
            'status'     => 'error',
            'http_code'  => $httpStatus,
            'error_code' => $errorCode,
            'message'    => $message,
        ];
        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $httpStatus);
    }
}
