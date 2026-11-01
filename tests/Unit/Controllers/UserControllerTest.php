<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\UserController;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected $mockService;
    protected UserController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(UserService::class);
        $this->controller = new UserController($this->mockService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function index_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'index'));
    }

    /**
     * @test
     */
    public function register_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'register'));
    }

    /**
     * @test
     */
    public function profile_returns_success_response_with_valid_token()
    {
        $request = Request::create('/api/profile', 'GET', ['api_token' => 'valid_token']);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok.',
            'data' => ['name' => 'Test User', 'email' => 'test@example.com'],
        ];

        $this->mockService
            ->shouldReceive('getProfile')
            ->once()
            ->with('valid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->profile($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function profile_returns_unauthorized_response_when_user_not_found()
    {
        $request = Request::create('/api/profile', 'GET', ['api_token' => 'invalid_token']);

        $serviceResult = [
            'success' => false,
            'code' => 401,
            'message' => 'Unauthorized',
        ];

        $this->mockService
            ->shouldReceive('getProfile')
            ->once()
            ->with('invalid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->profile($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function profile_returns_forbidden_response_when_token_is_empty()
    {
        $request = Request::create('/api/profile', 'GET', ['api_token' => '']);

        $serviceResult = [
            'success' => false,
            'code' => 403,
            'message' => 'Access Forbidden! What are you looking for?',
        ];

        $this->mockService
            ->shouldReceive('getProfile')
            ->once()
            ->with('')
            ->andReturn($serviceResult);

        $response = $this->controller->profile($request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function get_mentor_returns_success_response()
    {
        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok.',
            'data' => [
                ['name' => 'Mentor 1', 'type' => 'teacher'],
                ['name' => 'Mentor 2', 'type' => 'teacher'],
            ],
        ];

        $this->mockService
            ->shouldReceive('getMentors')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->getMentor();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_returns_success_response_with_valid_token()
    {
        $request = Request::create('/api/user/profile', 'GET', ['api_token' => 'valid_token']);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. User Data Retrieved',
            'data' => ['my-profile' => [], 'courses' => []],
        ];

        $this->mockService
            ->shouldReceive('getUserProfile')
            ->once()
            ->with('valid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->userProfile($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_returns_unauthorized_when_user_not_found()
    {
        $request = Request::create('/api/user/profile', 'GET', ['api_token' => 'invalid_token']);

        $serviceResult = [
            'success' => false,
            'code' => 401,
            'message' => 'Unauthorized',
        ];

        $this->mockService
            ->shouldReceive('getUserProfile')
            ->once()
            ->with('invalid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->userProfile($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_update_returns_validation_error_when_name_missing()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'email' => 'test@example.com',
            'language' => 1,
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_update_returns_validation_error_when_email_missing()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'name' => 'Test User',
            'language' => 1,
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_update_returns_validation_error_when_language_missing()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_update_returns_success_with_valid_data()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'language' => 1,
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update data successfull',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('updateUserProfile')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_profile_update_returns_conflict_when_email_taken()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'language' => 1,
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 409,
            'message' => 'Email is already taken',
        ];

        $this->mockService
            ->shouldReceive('updateUserProfile')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(409, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_password_update_returns_validation_error_when_password_missing()
    {
        $request = Request::create('/api/user/password/update', 'POST', [
            'api_token' => 'valid_token',
            'password_confirm' => 'newpassword',
        ]);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_password_update_returns_validation_error_when_confirm_missing()
    {
        $request = Request::create('/api/user/password/update', 'POST', [
            'api_token' => 'valid_token',
            'password' => 'newpassword',
        ]);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_password_update_requires_old_password_when_provided()
    {
        $request = Request::create('/api/user/password/update', 'POST', [
            'api_token' => 'valid_token',
            'password' => 'newpassword',
            'password_confirm' => 'newpassword',
            'old_password' => 'oldpassword',
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update password successfull',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('updatePassword')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_password_update_returns_error_when_password_validation_fails()
    {
        $request = Request::create('/api/user/password/update', 'POST', [
            'api_token' => 'valid_token',
            'password' => 'short',
            'password_confirm' => 'short',
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 500,
            'message' => 'Invalid password',
        ];

        $this->mockService
            ->shouldReceive('updatePassword')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_course_update_returns_validation_error_when_status_missing()
    {
        $request = Request::create('/api/user/course/update', 'POST', [
            'api_token' => 'valid_token',
            'class' => 1,
        ]);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_course_update_returns_validation_error_when_class_missing()
    {
        $request = Request::create('/api/user/course/update', 'POST', [
            'api_token' => 'valid_token',
            'status' => 'On',
        ]);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_course_update_returns_success_with_valid_data()
    {
        $request = Request::create('/api/user/course/update', 'POST', [
            'api_token' => 'valid_token',
            'class' => 1,
            'status' => 'On',
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update course successfull',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('updateCourseStatus')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_course_update_returns_error_on_failure()
    {
        $request = Request::create('/api/user/course/update', 'POST', [
            'api_token' => 'valid_token',
            'class' => 1,
            'status' => 'On',
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 500,
            'message' => 'Something went wrong :(',
        ];

        $this->mockService
            ->shouldReceive('updateCourseStatus')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function edit_user_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'editUser'));
    }

    /**
     * @test
     */
    public function generate_forgot_mail_returns_not_found_when_user_not_exists()
    {
        $request = Request::create('/api/forgot-password', 'POST', [
            'email' => 'nonexistent@example.com',
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 404,
            'message' => 'User not found',
        ];

        $this->mockService
            ->shouldReceive('generateForgotPasswordMail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn($serviceResult);

        $response = $this->controller->generateForgotMail($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function generate_forgot_mail_returns_success_when_user_exists()
    {
        $request = Request::create('/api/forgot-password', 'POST', [
            'email' => 'existing@example.com',
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Check your email.',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('generateForgotPasswordMail')
            ->once()
            ->with('existing@example.com')
            ->andReturn($serviceResult);

        $response = $this->controller->generateForgotMail($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function new_password_returns_unauthorized_when_token_invalid()
    {
        $request = Request::create('/api/new-password/invalid_token', 'POST', [
            'password' => 'newpassword',
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 404,
            'message' => 'Invalid token',
        ];

        $this->mockService
            ->shouldReceive('setNewPassword')
            ->once()
            ->with('invalid_token', 'newpassword')
            ->andReturn($serviceResult);

        $response = $this->controller->newPassword($request, 'invalid_token');

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function new_password_returns_success_when_token_valid()
    {
        $request = Request::create('/api/new-password/valid_token', 'POST', [
            'password' => 'newpassword',
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Update password success.',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('setNewPassword')
            ->once()
            ->with('valid_token', 'newpassword')
            ->andReturn($serviceResult);

        $response = $this->controller->newPassword($request, 'valid_token');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function progress_returns_success_with_valid_token()
    {
        $request = Request::create('/api/progress', 'GET', ['api_token' => 'valid_token']);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Progress retrieved successfully',
            'data' => [
                'today_spent' => 2.5,
                'certificates' => 3,
                'score' => 100,
                'total_courses' => 5,
            ],
        ];

        $this->mockService
            ->shouldReceive('getProgress')
            ->once()
            ->with('valid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->progress($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function progress_returns_unauthorized_when_user_not_found()
    {
        $request = Request::create('/api/progress', 'GET', ['api_token' => 'invalid_token']);

        $serviceResult = [
            'success' => false,
            'code' => 401,
            'message' => 'Unauthorized',
        ];

        $this->mockService
            ->shouldReceive('getProgress')
            ->once()
            ->with('invalid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->progress($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function controller_uses_api_response_trait()
    {
        $this->assertTrue(
            in_array(
                \App\Traits\ApiResponseTrait::class,
                class_uses($this->controller)
            )
        );
    }

    /**
     * @test
     */
    public function controller_has_user_service_dependency()
    {
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('userService');
        
        $this->assertTrue($property->isProtected());
    }

    /**
     * @test
     */
    public function controller_defines_api_name_constants()
    {
        $reflection = new \ReflectionClass($this->controller);
        
        $this->assertTrue($reflection->hasConstant('API_NAME'));
        $this->assertTrue($reflection->hasConstant('API_NAME_PROFILE'));
        $this->assertTrue($reflection->hasConstant('API_NAME_MENTOR'));
    }

    /**
     * @test
     */
    public function user_profile_update_returns_server_error_on_service_failure()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'language' => 1,
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 500,
            'message' => 'Something went wrong :)',
        ];

        $this->mockService
            ->shouldReceive('updateUserProfile')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_password_update_returns_server_error_when_service_fails()
    {
        $request = Request::create('/api/user/password/update', 'POST', [
            'api_token' => 'valid_token',
            'password' => 'validpassword',
            'password_confirm' => 'validpassword',
        ]);

        $serviceResult = [
            'success' => false,
            'code' => 500,
            'message' => 'Server error occurred',
        ];

        $this->mockService
            ->shouldReceive('updatePassword')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_password_update_without_old_password_calls_service_with_null()
    {
        $request = Request::create('/api/user/password/update', 'POST', [
            'api_token' => 'valid_token',
            'password' => 'newpassword',
            'password_confirm' => 'newpassword',
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update password successfull',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('updatePassword')
            ->once()
            ->with('valid_token', 'newpassword', 'newpassword', null)
            ->andReturn($serviceResult);

        $response = $this->controller->userPasswordUpdate($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function get_mentor_returns_empty_array_when_no_mentors()
    {
        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok.',
            'data' => [],
        ];

        $this->mockService
            ->shouldReceive('getMentors')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->getMentor();

        $this->assertEquals(200, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertEmpty($content['data']);
    }

    /**
     * @test
     */
    public function profile_with_full_data_returns_complete_structure()
    {
        $request = Request::create('/api/profile', 'GET', ['api_token' => 'valid_token']);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok.',
            'data' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'joined' => 'Joined since 1 year ago',
                'class_on_progress' => 2,
                'completed_classes' => 5,
                'class_progress' => [],
                'class_completed' => [],
                'class_expired' => [],
                'project' => [],
                'event_today' => [],
            ],
        ];

        $this->mockService
            ->shouldReceive('getProfile')
            ->once()
            ->with('valid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->profile($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('Test User', $content['data']['name']);
    }

    /**
     * @test
     */
    public function user_course_update_with_off_status()
    {
        $request = Request::create('/api/user/course/update', 'POST', [
            'api_token' => 'valid_token',
            'class' => 1,
            'status' => 'Off',
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update course successfull',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('updateCourseStatus')
            ->once()
            ->with('valid_token', 1, 'Off')
            ->andReturn($serviceResult);

        $response = $this->controller->userCourseUpdate($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function all_public_methods_exist()
    {
        $expectedMethods = [
            'index',
            'register',
            'registerPost',
            'profile',
            'getMentor',
            'userProfile',
            'userProfileUpdate',
            'userPasswordUpdate',
            'userCourseUpdate',
            'editUser',
            'generateForgotMail',
            'newPassword',
            'progress',
        ];

        foreach ($expectedMethods as $method) {
            $this->assertTrue(
                method_exists($this->controller, $method),
                "Method $method does not exist"
            );
        }
    }

    /**
     * @test
     */
    public function constructor_injects_user_service()
    {
        $mockService = Mockery::mock(UserService::class);
        $controller = new UserController($mockService);
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('userService');
        $property->setAccessible(true);
        
        $this->assertSame($mockService, $property->getValue($controller));
    }

    /**
     * @test
     */
    public function validation_errors_return_all_missing_fields()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
        ]);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(422, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertIsArray($content['message']);
    }

    /**
     * @test
     */
    public function user_profile_update_with_all_optional_fields()
    {
        $request = Request::create('/api/user/profile/update', 'POST', [
            'api_token' => 'valid_token',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'language' => 1,
            'username' => 'testuser',
            'about' => 'About me',
            'phone_number' => '1234567890',
            'country' => 1,
        ]);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update data successfull',
            'data' => null,
        ];

        $this->mockService
            ->shouldReceive('updateUserProfile')
            ->once()
            ->andReturn($serviceResult);

        $response = $this->controller->userProfileUpdate($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function progress_returns_complete_data_structure()
    {
        $request = Request::create('/api/progress', 'GET', ['api_token' => 'valid_token']);

        $serviceResult = [
            'success' => true,
            'code' => 200,
            'message' => 'Progress retrieved successfully',
            'data' => [
                'today_spent' => 2.5,
                'certificates' => 3,
                'score' => 100,
                'total_courses' => 5,
                'course' => [
                    'in_progress' => 2,
                    'completed' => 3,
                    'yet_to_start' => 0,
                ],
                'monthly_spent' => [
                    'Jan' => 10.5,
                    'Feb' => 15.2,
                ],
            ],
        ];

        $this->mockService
            ->shouldReceive('getProgress')
            ->once()
            ->with('valid_token')
            ->andReturn($serviceResult);

        $response = $this->controller->progress($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('course', $content['data']);
        $this->assertArrayHasKey('monthly_spent', $content['data']);
    }
}
