<?php

namespace App\Helpers;

trait ApiResponse
{
    protected function successResponse($data = null, string $message = '', int $status = 200 ,  $success = true)
    {
       
        return response()->json([
            'status' => true,
            'message' => $message,
            "data" => $data,
            "success" => $success
        ], $status);
    }

    protected function errorResponse(string $message = '', int $status = 400, $errors = null ,  $success = false)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
            "success" => $success
        ], $status);
    }

    protected function validationErrorResponse(array $errors, string $message = 'Validation failed')
    {
        return $this->errorResponse($message, 422, $errors);
    }
}