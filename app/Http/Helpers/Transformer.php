<?php

namespace App\Helpers;

class Transformer
{
    /**
     * Success Json Response format.
     *
     * @param   string  $message
     * @param   mixed  $data
     * @param   int  $statusCode
     * @param   array   $headers
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public static function success(string $message, $data = null, $statusCode = 200, array $headers = [])
    {
        return response()->json([
            'ok' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode, $headers);
    }

    /**
     * Failed Json Response format.
     *
     * @param   string  $message
     * @param   mixed  $data
     * @param   int  $statusCode
     * @param   array   $headers
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public static function failed(string $message, $data = null, $statusCode = 500, array $headers = [])
    {
        return response()->json([
            'ok' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode, $headers);
    }
}
