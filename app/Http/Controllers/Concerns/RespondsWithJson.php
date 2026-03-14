<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

trait RespondsWithJson
{
    /**
     * @param array<string, mixed>|null $data
     * @param array<string, mixed>|null $errors
     */
    protected function success(string $message, ?array $data = null, int $status = 200, ?array $errors = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $status);
    }

    /**
     * @param array<string, mixed>|null $data
     * @param array<string, mixed>|null $errors
     */
    protected function fail(string $message, int $status = 422, ?array $errors = null, ?array $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $status);
    }
}
