<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\QuizProgressController;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class QuizProgressControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizProgressController($mockService);
        $this->assertInstanceOf(QuizProgressController::class, $controller);
    }

    public function test_index_method_exists()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizProgressController($mockService);
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_is_clear_method_exists()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizProgressController($mockService);
        $this->assertTrue(method_exists($controller, 'isClear'));
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizProgressController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizProgressController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'isClear'));
    }
}
