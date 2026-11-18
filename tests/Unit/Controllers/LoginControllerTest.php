<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\Attributes\Test;

use App\Http\Controllers\API\LoginController;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    protected $mockService;
    protected LoginController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\DB::beginTransaction();
        $this->mockService = Mockery::mock(AuthService::class);
        $this->controller = new LoginController($this->mockService);
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\DB::rollBack();
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function login_returns_validation_error_when_email_is_null()
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => null,
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function login_returns_validation_error_when_password_is_null()
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'test@example.com',
            'password' => null,
        ]);

        $response = $this->controller->login($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function login_returns_validation_error_when_email_too_short()
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'a@b.c',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function login_returns_validation_error_when_password_too_short()
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'test@example.com',
            'password' => '1234567',
        ]);

        $response = $this->controller->login($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function login_returns_forbidden_on_invalid_credentials()
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->mockService
            ->shouldReceive('login')
            ->once()
            ->with('invalid@example.com', 'wrongpassword')
            ->andReturn([
                'success' => false,
                'code' => 403,
                'message' => 'Access Forbidden, Invalid email/password!',
            ]);

        $response = $this->controller->login($request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function login_supports_username_field()
    {
        $token = \Illuminate\Support\Str::random(60);
        $id = \Illuminate\Support\Facades\DB::table('user')->insertGetId([
            'name' => 'Test User',
            'email' => 'testuser_' . uniqid() . '@example.com',
            'api_token' => $token,
            'type' => 'student',
            'created_at' => time(),
            'updated_at' => time(),
        ], 'id_user');
        $user = \App\Models\User::find($id);

        $request = Request::create('/api/login', 'POST', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $this->mockService
            ->shouldReceive('login')
            ->once()
            ->with('testuser', 'password123')
            ->andReturn([
                'success' => true,
                'code' => 200,
                'user' => $user,
                'data' => [
                    'api_token' => $token,
                    'name' => 'Test User',
                    'email' => $user->email,
                    'img' => null,
                ],
            ]);

        $response = $this->controller->login($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function login_allows_short_valid_username()
    {
        $token = \Illuminate\Support\Str::random(60);
        $id = \Illuminate\Support\Facades\DB::table('user')->insertGetId([
            'name' => 'Zai',
            'email' => 'zai_' . uniqid() . '@example.com',
            'api_token' => $token,
            'type' => 'student',
            'created_at' => time(),
            'updated_at' => time(),
        ], 'id_user');
        $user = \App\Models\User::find($id);

        $request = Request::create('/api/login', 'POST', [
            'username' => 'zai',
            'password' => 'password123',
        ]);

        $this->mockService
            ->shouldReceive('login')
            ->once()
            ->with('zai', 'password123')
            ->andReturn([
                'success' => true,
                'code' => 200,
                'user' => $user,
                'data' => [
                    'api_token' => $token,
                    'name' => 'Zai',
                    'email' => $user->email,
                    'img' => null,
                ],
            ]);

        $response = $this->controller->login($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function register_email_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'registerEmail'));
    }

    #[Test]
    public function verify_email_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'verifyEmail'));
    }

    #[Test]
    public function create_email_verifier_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'createEmailVerifier'));
    }

    #[Test]
    public function redirect_to_provider_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'redirectToProvider'));
    }

    #[Test]
    public function handle_provider_callback_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'handleProviderCallback'));
    }

    #[Test]
    public function controller_uses_api_response_trait()
    {
        $this->assertTrue(
            in_array(\App\Traits\ApiResponseTrait::class, class_uses($this->controller))
        );
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'login',
            'regis',
            'registerEmail',
            'verifyEmail',
            'createEmailVerifier',
            'redirectToProvider',
            'handleProviderCallback',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->controller, $method), "Method $method should exist");
        }
    }
}
