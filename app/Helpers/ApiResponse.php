<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiResponse
{
    public static function success($data = null, $message = 'Berhasil', $code = 200)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($message = 'Terjadi kesalahan', $error = null, $code = 500)
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'error' => $error,
        ], $code);
    }

    public static function handleException(\Exception $e)
    {
        // Jika exception merupakan ModelNotFoundException, berikan response 404
        if ($e instanceof ModelNotFoundException) {
            return self::error('Data tidak ditemukan: ' . $e->getMessage(), 404);
        }

        // Tangani exception umum lainnya dengan response 500
        return self::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
    }
}
