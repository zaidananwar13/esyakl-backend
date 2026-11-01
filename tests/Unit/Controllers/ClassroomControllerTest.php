<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\ClassroomController;
use App\Services\ClassroomService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ClassroomControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(ClassroomService::class);
        $controller = new ClassroomController($mockService);
        $this->assertInstanceOf(ClassroomController::class, $controller);
    }

    public function test_classroom_returns_unauthorized_when_no_token()
    {
        $mockService = Mockery::mock(ClassroomService::class);

        $controller = new ClassroomController($mockService);
        $request = new Request();

        $response = $controller->classroom($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_classroom_returns_unauthorized_when_no_class()
    {
        $mockService = Mockery::mock(ClassroomService::class);

        $controller = new ClassroomController($mockService);
        $request = new Request(['api_token' => 'token']);

        $response = $controller->classroom($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_classroom_returns_not_found_when_user_missing()
    {
        $mockService = Mockery::mock(ClassroomService::class);

        $controller = new ClassroomController($mockService);
        $request = new Request(['api_token' => 'invalid', 'class' => 1]);

        $response = $controller->classroom($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(ClassroomService::class);
        $controller = new ClassroomController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'forbiddenResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_classroom_method_exists()
    {
        $mockService = Mockery::mock(ClassroomService::class);
        $controller = new ClassroomController($mockService);

        $this->assertTrue(method_exists($controller, 'classroom'));
    }
}
