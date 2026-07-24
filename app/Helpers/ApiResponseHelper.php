<?php

namespace App\Helpers;

class ApiResponseHelper
{
    public static function success(mixed $data = null, string $message = 'ok', int $code = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'request_id' => (string) \Illuminate\Support\Str::uuid(),
            'data' => $data,
        ]);
    }

    public static function error(int $code = 1001001, string $message = 'error', mixed $data = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'request_id' => (string) \Illuminate\Support\Str::uuid(),
            'data' => $data,
        ], $code >= 1001000 ? 200 : 400);
    }
}
