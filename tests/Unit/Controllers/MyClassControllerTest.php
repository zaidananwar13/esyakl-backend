<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\MyClassController;
use App\Services\MyClassService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class MyClassControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(MyClassService::class);
        $controller = new MyClassController($mockService);
        $this->assertInstanceOf(MyClassController::class, $controller);
    }

    public function test_controller_with_point_service()
    {
        $mockMyClassService = Mockery::mock(MyClassService::class);
        $mockPointService = Mockery::mock(PointService::class);
        $controller = new MyClassController($mockMyClassService, $mockPointService);
        $this->assertInstanceOf(MyClassController::class, $controller);
    }

    public function test_index_returns_forbidden_when_no_token()
    {
        $mockService = Mockery::mock(MyClassService::class);

        $controller = new MyClassController($mockService);
        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_index_returns_not_found_when_user_missing()
    {
        $mockService = Mockery::mock(MyClassService::class);

        $controller = new MyClassController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->index($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_progress_returns_not_found_when_user_missing()
    {
        $mockService = Mockery::mock(MyClassService::class);

        $controller = new MyClassController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->progress($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(MyClassService::class);
        $controller = new MyClassController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'forbiddenResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(MyClassService::class);
        $controller = new MyClassController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'progress'));
    }
}
