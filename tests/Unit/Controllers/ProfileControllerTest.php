<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\Attributes\Test;

use App\Http\Controllers\API\ProfileController;
use App\Services\UserService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    protected $mockService;
    protected ProfileController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(UserService::class);
        $this->controller = new ProfileController($this->mockService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function user_profile_returns_unauthorized_when_no_token()
    {
        $request = Request::create('/api/profile', 'POST', []);

        $response = $this->controller->userProfile($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    #[Test]
    public function user_profile_returns_success_with_valid_token()
    {
        $request = Request::create('/api/profile', 'POST', ['api_token' => 'valid_token']);

        $this->mockService
            ->shouldReceive('getUserProfile')
            ->once()
            ->with('valid_token')
            ->andReturn([
                'success' => true,
                'code' => 200,
                'message' => 'Ok',
                'data' => ['my-profile' => [], 'courses' => []],
            ]);

        $response = $this->controller->userProfile($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function user_profile_update_returns_validation_error_when_name_missing()
    {
        $request = Request::create('/api/profile/update', 'POST', [
            'email' => 'test@example.com',
            'language' => 1,
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function user_profile_update_returns_validation_error_when_email_missing()
    {
        $request = Request::create('/api/profile/update', 'POST', [
            'name' => 'Test User',
            'language' => 1,
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function user_profile_update_returns_validation_error_when_language_missing()
    {
        $request = Request::create('/api/profile/update', 'POST', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function user_password_update_returns_validation_error_when_password_short()
    {
        $request = Request::create('/api/profile/update/password', 'POST', [
            'password' => '12345',
            'password_confirm' => '12345',
        ]);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function user_course_update_returns_validation_error_when_status_missing()
    {
        $request = Request::create('/api/profile/update/course', 'POST', [
            'class' => 1,
        ]);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function user_course_update_returns_validation_error_when_class_missing()
    {
        $request = Request::create('/api/profile/update/course', 'POST', [
            'status' => 'On',
        ]);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    #[Test]
    public function generate_forgot_mail_returns_success()
    {
        $request = Request::create('/api/forgot-password', 'POST', [
            'email' => 'existing@example.com',
        ]);

        $this->mockService
            ->shouldReceive('generateForgotPasswordMail')
            ->once()
            ->with('existing@example.com')
            ->andReturn([
                'success' => true,
                'code' => 200,
                'message' => 'Check your email.',
            ]);

        $response = $this->controller->generateForgotMail($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function generate_forgot_mail_returns_not_found()
    {
        $request = Request::create('/api/forgot-password', 'POST', [
            'email' => 'nonexistent@example.com',
        ]);

        $this->mockService
            ->shouldReceive('generateForgotPasswordMail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn([
                'success' => false,
                'code' => 404,
                'message' => 'User not found',
            ]);

        $response = $this->controller->generateForgotMail($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    #[Test]
    public function new_password_returns_success()
    {
        $request = Request::create('/api/forgot-password/valid_token', 'POST', [
            'password' => 'newpassword',
        ]);

        $this->mockService
            ->shouldReceive('setNewPassword')
            ->once()
            ->with('valid_token', 'newpassword')
            ->andReturn([
                'success' => true,
                'code' => 200,
                'message' => 'Password updated',
            ]);

        $response = $this->controller->newPassword($request, 'valid_token');

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function new_password_returns_unauthorized_on_invalid_token()
    {
        $request = Request::create('/api/forgot-password/invalid_token', 'POST', [
            'password' => 'newpassword',
        ]);

        $this->mockService
            ->shouldReceive('setNewPassword')
            ->once()
            ->with('invalid_token', 'newpassword')
            ->andReturn([
                'success' => false,
                'code' => 401,
                'message' => 'Invalid token',
            ]);

        $response = $this->controller->newPassword($request, 'invalid_token');

        $this->assertEquals(401, $response->getStatusCode());
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
            'userProfile',
            'userProfileUpdate',
            'userPasswordUpdate',
            'userCourseUpdate',
            'generateForgotMail',
            'newPassword',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->controller, $method), "Method $method should exist");
        }
    }
}
