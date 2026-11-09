<?php

namespace App\Http\Controllers\API;

use App\Helper as Help;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Authorizer';

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        // Legacy contract: string message + status 422 (the frontend shows the message as-is).
        if ($request->input('email') === null || $request->input('password') === null) {
            return $this->legacyResponse(self::API_NAME, ['code' => 422, 'status' => 422, 'message' => 'Invalid Input'], 422);
        }

        if (strlen($request->input('email')) < 7 || strlen($request->input('password')) < 8) {
            return $this->legacyResponse(self::API_NAME, [
                'code' => 422,
                'status' => 422,
                'message' => 'Invalid Input. Please check your email or password length!',
            ], 422);
        }

        $result = $this->authService->login($request->input('email'), $request->input('password'));

        if (!$result['success']) {
            return $this->forbiddenResponse(self::API_NAME, $result['message']);
        }

        $user = $result['user'];

        // NOTE: the API is token based (api_token). The former
        // auth()->attempt($user->toArray()) call queried
        // "email_verified_at = '<ISO date>'" against an integer column and
        // crashed every login on PostgreSQL, so it has been removed.
        Help::setCookie('ssid', Hash::make($user->name));
        Help::updateUserLog(true, $user->id_user);

        // api_token/name/email/img also at the top level: the current frontend reads them there.
        return $this->legacySuccessResponse(self::API_NAME, $result['data'], 'Login successful');
    }

    public function regis()
    {
        return view('test/register');
    }

    public function registerEmail(Request $request)
    {
        $result = $this->authService->register([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'repassword' => $request->input('repassword'),
        ]);

        if (!$result['success']) {
            if ($result['code'] === 422) {
                return $this->legacyResponse(self::API_NAME, ['code' => 422, 'status' => 422, 'message' => $result['message']], 422);
            }
            // Legacy contract: "email sudah ada" / "password ga sama" were HTTP 200 with code 200.
            return $this->legacyResponse(self::API_NAME, ['code' => 200, 'message' => $result['message']], 200);
        }

        return $this->successResponse(self::API_NAME, null, $result['message']);
    }

    public function verifyEmail(Request $request, $token)
    {
        $result = $this->authService->verifyEmail($token);

        if (!$result['success']) {
            if ($result['message'] === 'wrong token') {
                return "wrong token";
            }
            return view($result['view'] ?? 'mail.false-verified');
        }

        return view($result['view'] ?? 'mail.true-verified');
    }

    public function createEmailVerifier(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'User not found');
        }

        $this->authService->resendVerificationEmail($user);

        return $this->successResponse(self::API_NAME, null, 'Success, Check your E-Mail');
    }

    public function redirectToProvider()
    {
        $url = url('/auth/callback');

        return Socialite::driver('google')
            ->with(["redirect_uri" => $url])
            ->stateless()->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $oauthUser = Socialite::driver('google')->stateless()->user();

        $result = $this->authService->handleOAuthLogin($oauthUser);

        if (!$result['success']) {
            return redirect()->to('/login')->withErrors(['OAuth login failed']);
        }

        $user = $result['user'];

        Help::updateUserLog(true, $user->id_user);

        // auth()->attempt($user->toArray()) removed - see login() above.
        Help::setCookie('ssid', Hash::make($user->name));

        $redirectUrl = $this->authService->getOAuthRedirectUrl();

        // Legacy flow (default): the current frontend reads the credentials from the query
        // string, exactly like the Laravel 8 version's auto-submitted GET form did.
        if (config('app-settings.oauth.legacy_redirect', true)) {
            return redirect()->away($redirectUrl . '?' . http_build_query([
                'api_token' => $result['data']['api_token'],
                'name' => $result['data']['name'],
                'email' => $result['data']['email'],
                'img' => $result['data']['img'],
            ]));
        }

        // SECURITY FIX: Use secure token exchange instead of passing credentials in URL
        // Generate a short-lived exchange code
        $exchangeCode = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\Cache::put(
            "oauth_exchange:{$exchangeCode}",
            [
                'api_token' => $result['data']['api_token'],
                'name' => $result['data']['name'],
                'email' => $result['data']['email'],
                'img' => $result['data']['img'],
            ],
            now()->addMinutes(5)
        );

        // Only pass the exchange code in URL, not sensitive data
        return redirect()->away($redirectUrl . '?code=' . $exchangeCode);
    }

    /**
     * Exchange OAuth code for user data.
     * SECURITY: This endpoint allows the frontend to securely retrieve user data
     * using the exchange code instead of receiving it via URL parameters.
     */
    public function exchangeOAuthCode(Request $request)
    {
        $code = $request->input('code');

        if (empty($code) || strlen($code) !== 64) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid exchange code'
            ], 401);
        }

        $data = \Illuminate\Support\Facades\Cache::pull("oauth_exchange:{$code}");

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Exchange code expired or invalid'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
