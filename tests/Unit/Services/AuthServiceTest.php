<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Services\AuthService;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthService();
    }

    #[Test]
    public function resolve_user_from_token_returns_null_for_empty_token()
    {
        $result = $this->service->resolveUserFromToken('');
        $this->assertNull($result);
        
        $result = $this->service->resolveUserFromToken(null);
        $this->assertNull($result);
    }

    #[Test]
    public function get_user_id_from_token_returns_null_for_empty_token()
    {
        $result = $this->service->getUserIdFromToken('');
        $this->assertNull($result);
    }

    #[Test]
    public function login_method_exists_and_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'login'));
        
        $reflection = new \ReflectionMethod($this->service, 'login');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function register_method_exists_and_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'register'));
        
        $reflection = new \ReflectionMethod($this->service, 'register');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function generate_api_token_returns_string()
    {
        $token = $this->service->generateApiToken();
        
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // sha256 hash is 64 characters
    }

    #[Test]
    public function generate_api_token_returns_unique_tokens()
    {
        $token1 = $this->service->generateApiToken();
        $token2 = $this->service->generateApiToken();
        
        $this->assertNotEquals($token1, $token2);
    }

    #[Test]
    public function generate_verification_token_returns_string()
    {
        $token = $this->service->generateVerificationToken();
        
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));
    }

    #[Test]
    public function verify_email_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'verifyEmail'));
        
        $reflection = new \ReflectionMethod($this->service, 'verifyEmail');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function handle_oauth_login_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'handleOAuthLogin'));
        
        $reflection = new \ReflectionMethod($this->service, 'handleOAuthLogin');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_oauth_redirect_url_returns_string()
    {
        $url = $this->service->getOAuthRedirectUrl();
        
        $this->assertIsString($url);
        $this->assertStringContainsString('my-class', $url);
    }

    #[Test]
    public function send_verification_email_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'sendVerificationEmail'));
    }

    #[Test]
    public function resend_verification_email_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'resendVerificationEmail'));
    }

    #[Test]
    public function create_from_google_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'createFromGoogle'));
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'resolveUserFromToken',
            'getUserIdFromToken',
            'login',
            'register',
            'generateApiToken',
            'generateVerificationToken',
            'sendVerificationEmail',
            'resendVerificationEmail',
            'verifyEmail',
            'createFromGoogle',
            'handleOAuthLogin',
            'getOAuthRedirectUrl',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->service, $method), "Method $method should exist");
        }
    }
}
