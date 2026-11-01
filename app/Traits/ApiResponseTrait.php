<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse(string $apiName, $data, string $message = 'Retrieving data successful!', int $code = 200)
    {
        $response = [
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . " | $apiName API",
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
        
        return response($response, $code);
    }

    protected function errorResponse(string $apiName, string $message = 'Not Found', int $code = 404, $data = null)
    {
        $response = [
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . " | $apiName API",
            'code' => $code,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return response($response, $code);
    }

    protected function unauthorizedResponse(string $apiName, string $message = 'Unauthorized')
    {
        return $this->errorResponse($apiName, $message, 401);
    }

    protected function forbiddenResponse(string $apiName, string $message = 'Access Forbidden')
    {
        return $this->errorResponse($apiName, $message, 403);
    }

    protected function notFoundResponse(string $apiName, string $message = 'Not Found')
    {
        return $this->errorResponse($apiName, $message, 404);
    }

    protected function serverErrorResponse(string $apiName, string $message = 'Something went wrong')
    {
        return $this->errorResponse($apiName, $message, 500);
    }

    protected function conflictResponse(string $apiName, string $message = 'Already registered')
    {
        return $this->errorResponse($apiName, $message, 409);
    }

    protected function validationErrorResponse(string $apiName, array $messages)
    {
        $response = [
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . " | $apiName API",
            'code' => 422,
            'status' => 422,
            'message' => $messages
        ];
        
        return response($response, 422);
    }
}
