<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\LearningPathController;
use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class LearningPathControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(LearningPathService::class);
        $controller = new LearningPathController($mockService);
        $this->assertInstanceOf(LearningPathController::class, $controller);
    }

    public function test_index_method_exists()
    {
        $mockService = Mockery::mock(LearningPathService::class);
        $controller = new LearningPathController($mockService);
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(LearningPathService::class);
        $controller = new LearningPathController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }
}
