<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Success response with data
     *
     * @param mixed  $data
     * @param string $message
     * @param int    $code
     * @return JsonResponse
     */
    public static function success($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response with custom message and optional errors
     *
     * @param string $message
     * @param array|null $errors
     * @param int    $code
     * @return JsonResponse
     */
    public static function error($message = 'Error', $errors = null, $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Not Found response
     *
     * @param string $message
     * @return JsonResponse
     */
    public static function notFound($message = 'Resource not found'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Validation error response
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    public static function validationError($errors, $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, $errors, 422);
    }
}
