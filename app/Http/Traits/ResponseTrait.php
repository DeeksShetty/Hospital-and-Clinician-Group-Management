<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Generate a success JSON response.
     *
     * @param int $code
     * @param string|array $message
     * @param mixed|null $data
     * @return JsonResponse
     */
 
    protected function successResponse(
        int $code = 200,
        string $message = 'Success',
        mixed $data = null,

    ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Generate an error JSON response.
     *
     * @param int $code
     * @param string|array $message
     * @return JsonResponse
     */
    protected function errorResponse(int $code = 400, $message = 'Error', $data = null): JsonResponse
    {
        return response()->json([
            'errors' => true,
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
