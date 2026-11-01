<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MigrasiController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Migrasi Login';

    /**
     * Migration mode switching endpoint.
     * SECURITY: This endpoint is now disabled for security reasons.
     * Application mode should be configured via environment variables.
     *
     * @deprecated Use APP_MODE environment variable instead
     */
    public function index(Request $request, $mode = null)
    {
        // SECURITY FIX: This endpoint is disabled
        // Mode should be configured via environment variables (APP_MODE)
        // To change mode, update .env file: APP_MODE=local or APP_MODE=production
        
        return $this->forbiddenResponse(
            self::API_NAME,
            'This endpoint is disabled for security reasons. Please configure APP_MODE in environment variables.'
        );
    }
}
