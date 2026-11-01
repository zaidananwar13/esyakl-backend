<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\ProjectController;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);
        $this->assertInstanceOf(ProjectController::class, $controller);
    }

    public function test_project_class_method_exists()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);
        $this->assertTrue(method_exists($controller, 'projectClass'));
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'projectClass'));
        $this->assertTrue(method_exists($controller, 'submit'));
        $this->assertTrue(method_exists($controller, 'test'));
    }
}
