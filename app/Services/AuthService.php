<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Mail\VerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthService
{
    public function resolveUserFromToken(?string $token): ?User
    {
        if (empty($token)) {
            return null;
        }

        return User::where('api_token', $token)->first();
    }

    public function getUserIdFromToken(?string $token): ?int
    {
        $user = $this->resolveUserFromToken($token);
        return $user?->id_user;
    }

    public function login(string $email, string $password): array
    {
        $user = User::select('id_user', 'name', 'email', 'email_verified_at', 'avatar_original', 'api_token', 'password')
            ->where('email', $email)
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Access Forbidden, Invalid email/password!'
            ];
        }

        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Access Forbidden, Invalid email/password!'
            ];
        }

        if ($user->email_verified_at === null) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Access Forbidden, Please verify your email first!'
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'user' => $user,
            'data' => [
                'api_token' => $user->api_token,
                'name' => $user->name,
                'email' => $user->email,
                'img' => $user->avatar_original,
            ]
        ];
    }

    public function register(array $data): array
    {
        if (User::where('email', $data['email'])->exists()) {
            return [
                'success' => false,
                'code' => 200,
                'message' => 'email sudah ada'
            ];
        }

        if ($data['password'] !== $data['repassword']) {
            return [
                'success' => false,
                'code' => 200,
                'message' => 'password ga sama'
            ];
        }

        if (strlen($data['password']) < 6) {
            return [
                'success' => false,
                'code' => 422,
                'message' => 'Invalid Input. Please check password length!'
            ];
        }

        $now = DateHelper::nowTimestamp();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->api_token = $this->generateApiToken();
        $user->avatar_original = 'user.png';
        $user->password = bcrypt($data['password']);
        $user->created_at = $now;
        $user->updated_at = $now;
        $user->save();

        $this->sendVerificationEmail($user);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'check your email :)',
            'user' => $user
        ];
    }

    public function generateApiToken(): string
    {
        return hash('sha256', Str::random(60));
    }

    public function generateVerificationToken(): string
    {
        return hash('sha256', Str::random(60));
    }

    public function sendVerificationEmail(User $user): void
    {
        $token = $this->generateVerificationToken();
        $expiryMinutes = config('app-settings.verification.token_expiry_minutes', 10);

        EmailVerification::create([
            'id_user' => $user->id_user,
            'email' => $user->email,
            'token' => $token,
            'expired_at' => DateHelper::nowTimestamp() + ($expiryMinutes * 60),
        ]);

        Mail::to($user->email)->send(new VerificationMail([
            'mailed' => $user->name,
            'content' => "Please click the link below before $expiryMinutes minutes passed",
            'link' => URL::to('/web/mail-verify/' . $token),
        ]));
    }

    public function resendVerificationEmail(User $user): void
    {
        EmailVerification::where('id_user', $user->id_user)->delete();
        $this->sendVerificationEmail($user);
    }

    public function verifyEmail(string $token): array
    {
        $verification = EmailVerification::where('token', $token)->first();

        if (!$verification) {
            return [
                'success' => false,
                'message' => 'wrong token'
            ];
        }

        $now = DateHelper::nowTimestamp();

        if ($verification->expired_at <= $now) {
            return [
                'success' => false,
                'message' => 'expired',
                'view' => 'mail.false-verified'
            ];
        }

        User::where('id_user', $verification->id_user)
            ->update(['email_verified_at' => $now]);

        return [
            'success' => true,
            'view' => 'mail.true-verified'
        ];
    }

    public function createFromGoogle(object $googleUser): User
    {
        $now = DateHelper::nowTimestamp();

        $user = new User();
        $user->name = $googleUser->name;
        $user->email = $googleUser->email;
        $user->google_id = $googleUser->id;
        $user->api_token = $this->generateApiToken();
        $user->avatar_original = $googleUser->avatar_original ?? 'user.png';
        $user->email_verified_at = $now;
        $user->password = null;
        $user->save();

        return $user;
    }

        public function handleOAuthLogin(object $oauthUser): array
    {
        $existingUser = User::select('id_user', 'name', 'email', 'avatar_original', 'password', 'api_token')
            ->where('email', $oauthUser->email)
            ->first();

        if ($existingUser) {
            return [
                'success' => true,
                'is_new' => false,
                'user' => $existingUser,
                'data' => [
                    'api_token' => $existingUser->api_token,
                    'name' => $existingUser->name,
                    'email' => $existingUser->email,
                    'img' => $existingUser->avatar_original,
                ]
            ];
        }

        $newUser = $this->createFromGoogle($oauthUser);

        return [
            'success' => true,
            'is_new' => true,
            'user' => $newUser,
            'data' => [
                'api_token' => $newUser->api_token,
                'name' => $newUser->name,
                'email' => $newUser->email,
                'img' => $newUser->avatar_original,
            ]
        ];
    }

        public function getOAuthRedirectUrl(): string
    {
        // SECURITY FIX: Use environment configuration instead of database toggle
        return config('app-settings.frontend_url', 'https://arabic-go.com') . '/student/my-class';
    }
}
