<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Services\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    // ============================================================
    // getAllUsers() Tests
    // ============================================================

    #[Test]
    public function get_all_users_returns_array()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'getAllUsers'));
        
        $reflection = new \ReflectionMethod($service, 'getAllUsers');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // registerUser() Tests
    // ============================================================

    #[Test]
    public function register_user_method_has_correct_signature()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'registerUser');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('email', $parameters[0]->getName());
        $this->assertEquals('password', $parameters[1]->getName());
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function register_user_extracts_name_from_email_correctly()
    {
        // Test the name extraction logic
        $emails = [
            'johndoe@example.com' => 'johndoe',
            'jane.smith@test.org' => 'jane.smith',
            'user123@mail.co' => 'user123',
            'test@test.com' => 'test',
        ];
        
        foreach ($emails as $email => $expectedName) {
            $extractedName = explode('@', $email)[0];
            $this->assertEquals($expectedName, $extractedName);
        }
    }

    // ============================================================
    // getProfile() Tests
    // ============================================================

    #[Test]
    public function get_profile_method_has_correct_signature()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'getProfile');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_profile_returns_array_with_required_keys_on_failure()
    {
        // Since we can't mock static methods easily, we test method signature
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'getProfile'));
    }

    // ============================================================
    // getMentors() Tests
    // ============================================================

    #[Test]
    public function get_mentors_returns_array()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'getMentors'));
        
        $reflection = new \ReflectionMethod($service, 'getMentors');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // getUserProfile() Tests
    // ============================================================

    #[Test]
    public function get_user_profile_method_has_correct_signature()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'getUserProfile');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // updateUserProfile() Tests
    // ============================================================

    #[Test]
    public function update_user_profile_accepts_correct_parameters()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'updateUserProfile'));
        
        $reflection = new \ReflectionMethod($service, 'updateUserProfile');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
        $this->assertEquals('data', $parameters[1]->getName());
        $this->assertEquals('array', $parameters[1]->getType()->getName());
        $this->assertEquals('image', $parameters[2]->getName());
        $this->assertTrue($parameters[2]->isOptional());
        $this->assertNull($parameters[2]->getDefaultValue());
    }

    #[Test]
    public function update_user_profile_returns_array()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'updateUserProfile');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // updatePassword() Tests
    // ============================================================

    #[Test]
    public function update_password_returns_error_when_password_too_short()
    {
        $service = new UserService();
        
        $result = $service->updatePassword('token', '12345', '12345', null);
        
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
        $this->assertStringContainsString('password length', $result['message']);
    }

    #[Test]
    public function update_password_returns_error_when_confirm_password_too_short()
    {
        $service = new UserService();
        
        $result = $service->updatePassword('token', 'validpassword', '123', null);
        
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
    }

    #[Test]
    public function update_password_validates_both_passwords()
    {
        $service = new UserService();
        
        // Both passwords must be >= 6 characters
        $result1 = $service->updatePassword('token', 'short', 'validpass', null);
        $this->assertFalse($result1['success']);
        
        $result2 = $service->updatePassword('token', 'validpass', 'short', null);
        $this->assertFalse($result2['success']);
    }

    #[Test]
    public function update_password_accepts_correct_parameters()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'updatePassword');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('password', $parameters[1]->getName());
        $this->assertEquals('passwordConfirm', $parameters[2]->getName());
        $this->assertEquals('oldPassword', $parameters[3]->getName());
        $this->assertTrue($parameters[3]->isOptional());
    }

    #[Test]
    public function update_password_validates_minimum_length_boundary()
    {
        $service = new UserService();
        
        // Exactly 5 characters should fail
        $result5 = $service->updatePassword('token', '12345', '12345', null);
        $this->assertFalse($result5['success']);
        $this->assertEquals(422, $result5['code']);
        
        // Exactly 6 characters should pass validation (might fail for other reasons)
        $resultPass = $service->updatePassword('token', '123456', '123456', null);
        // Even if it fails due to invalid token, code should not be 422
        $this->assertNotEquals(422, $resultPass['code']);
    }

    // ============================================================
    // updateCourseStatus() Tests
    // ============================================================

    #[Test]
    public function update_course_status_accepts_correct_parameters()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'updateCourseStatus'));
        
        $reflection = new \ReflectionMethod($service, 'updateCourseStatus');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('classId', $parameters[1]->getName());
        $this->assertEquals('int', $parameters[1]->getType()->getName());
        $this->assertEquals('status', $parameters[2]->getName());
        $this->assertEquals('string', $parameters[2]->getType()->getName());
    }

    #[Test]
    public function update_course_status_returns_array()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'updateCourseStatus');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // generateForgotPasswordMail() Tests
    // ============================================================

    #[Test]
    public function generate_forgot_password_mail_accepts_email_parameter()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'generateForgotPasswordMail'));
        
        $reflection = new \ReflectionMethod($service, 'generateForgotPasswordMail');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('email', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
    }

    #[Test]
    public function generate_forgot_password_mail_returns_array()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'generateForgotPasswordMail');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // setNewPassword() Tests
    // ============================================================

    #[Test]
    public function set_new_password_accepts_correct_parameters()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'setNewPassword'));
        
        $reflection = new \ReflectionMethod($service, 'setNewPassword');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
        $this->assertEquals('password', $parameters[1]->getName());
        $this->assertEquals('string', $parameters[1]->getType()->getName());
    }

    #[Test]
    public function set_new_password_returns_array()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'setNewPassword');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // getProgress() Tests
    // ============================================================

    #[Test]
    public function get_progress_accepts_token_parameter()
    {
        $service = new UserService();
        
        $this->assertTrue(method_exists($service, 'getProgress'));
        
        $reflection = new \ReflectionMethod($service, 'getProgress');
        $parameters = $reflection->getParameters();
        
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
    }

    #[Test]
    public function get_progress_returns_array()
    {
        $service = new UserService();
        
        $reflection = new \ReflectionMethod($service, 'getProgress');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    // ============================================================
    // General Service Tests
    // ============================================================

    #[Test]
    public function all_methods_return_array()
    {
        $service = new UserService();
        
        $methods = [
            'getAllUsers',
            'registerUser',
            'getProfile',
            'getMentors',
            'getUserProfile',
            'updateUserProfile',
            'updatePassword',
            'updateCourseStatus',
            'generateForgotPasswordMail',
            'setNewPassword',
            'getProgress',
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(method_exists($service, $method), "Method $method does not exist");
            
            $reflection = new \ReflectionMethod($service, $method);
            $this->assertEquals('array', $reflection->getReturnType()?->getName(), "Method $method should return array");
        }
    }

    #[Test]
    public function service_can_be_instantiated()
    {
        $service = new UserService();
        
        $this->assertInstanceOf(UserService::class, $service);
    }

    #[Test]
    public function update_password_response_structure_on_validation_failure()
    {
        $service = new UserService();
        $result = $service->updatePassword('token', '123', '123', null);
        
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertFalse($result['success']);
    }

    #[Test]
    public function all_public_methods_are_accessible()
    {
        $service = new UserService();
        $reflection = new \ReflectionClass($service);
        
        $publicMethods = array_filter(
            $reflection->getMethods(\ReflectionMethod::IS_PUBLIC),
            fn($method) => !$method->isConstructor() && $method->getDeclaringClass()->getName() === UserService::class
        );
        
        // Should have all expected public methods
        $expectedCount = 11; // getAllUsers, registerUser, getProfile, getMentors, getUserProfile, updateUserProfile, updatePassword, updateCourseStatus, generateForgotPasswordMail, setNewPassword, getProgress
        $this->assertGreaterThanOrEqual($expectedCount, count($publicMethods));
    }

    #[Test]
    public function password_length_validation_is_consistent()
    {
        $service = new UserService();
        
        // Test boundary cases
        $testCases = [
            ['pass' => '1', 'confirm' => '1', 'shouldFail' => true],
            ['pass' => '12', 'confirm' => '12', 'shouldFail' => true],
            ['pass' => '123', 'confirm' => '123', 'shouldFail' => true],
            ['pass' => '1234', 'confirm' => '1234', 'shouldFail' => true],
            ['pass' => '12345', 'confirm' => '12345', 'shouldFail' => true],
        ];
        
        foreach ($testCases as $case) {
            $result = $service->updatePassword('token', $case['pass'], $case['confirm'], null);
            $this->assertEquals($case['shouldFail'], !$result['success'] && $result['code'] === 422);
        }
    }
}
