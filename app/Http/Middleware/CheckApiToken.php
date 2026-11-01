<?php

namespace App\Http\Middleware;

use App\Helper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     * Validates API token from either Bearer header or request input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // SECURITY: Prefer Bearer token from Authorization header
        $token = $this->extractToken($request);

        if (empty($token)) {
            return $this->unauthorizedResponse('No authentication token provided');
        }

        // Validate token format (should be SHA256 hash - 64 hex characters)
        if (!$this->isValidTokenFormat($token)) {
            return $this->unauthorizedResponse('Invalid token format');
        }

        $user = User::where('api_token', $token)->first();

        if ($user === null) {
            return $this->unauthorizedResponse('Invalid or expired token');
        }

        // Store user in request for later use
        $request->merge(['authenticated_user' => $user]);
        $request->setUserResolver(fn() => $user);

        Helper::updateUserLog(false, $user->id_user);

        return $next($request);
    }

    /**
     * Extract token from request (Bearer header preferred, then input)
     */
    private function extractToken(Request $request): ?string
    {
        // First, try Bearer token from Authorization header (preferred)
        $bearerToken = $request->bearerToken();
        if (!empty($bearerToken)) {
            return trim($bearerToken);
        }

        // Fallback to request input for backward compatibility
        $inputToken = $request->input('api_token');
        if (!empty($inputToken)) {
            return trim($inputToken);
        }

        return null;
    }

    /**
     * Validate token format (SHA256 = 64 hex characters)
     */
    private function isValidTokenFormat(string $token): bool
    {
        return preg_match('/^[a-f0-9]{64}$/i', $token) === 1;
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Auth API',
            'code' => 401,
            'message' => $message,
        ], 401);
    }
}
