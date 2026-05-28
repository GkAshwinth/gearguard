<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function success(string $message, $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Request was successful.',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'An error has occurred...',
            'message' => $message,
        ], $code);
    }
}
