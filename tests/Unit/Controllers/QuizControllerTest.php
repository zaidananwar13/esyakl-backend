<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\QuizController;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizController($mockService);
        $this->assertInstanceOf(QuizController::class, $controller);
    }

    public function test_index_returns_unauthorized_when_user_not_found()
    {
        $mockService = Mockery::mock(QuizService::class);

        $controller = new QuizController($mockService);
        $request = new Request(['api_token' => 'invalid', 'silabus' => 1]);

        $response = $controller->index($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_submit_returns_unauthorized_when_user_not_found()
    {
        $mockService = Mockery::mock(QuizService::class);

        $controller = new QuizController($mockService);
        $request = new Request(['api_token' => 'invalid', 'id_syllabus' => 1]);

        $response = $controller->submit($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_freekey_returns_success()
    {
        $quizKey = ['key' => 'sample-key'];

        $mockService = Mockery::mock(QuizService::class);
        $mockService->shouldReceive('getQuizKey')->once()->andReturn($quizKey);

        $controller = new QuizController($mockService);
        $request = new Request();

        $response = $controller->freekey($request, 1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_sample_returns_success()
    {
        $sampleQuiz = ['id' => 1, 'questions' => []];

        $mockService = Mockery::mock(QuizService::class);
        $mockService->shouldReceive('getSampleQuiz')->once()->andReturn($sampleQuiz);

        $controller = new QuizController($mockService);

        $response = $controller->sample();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_get_option_returns_options()
    {
        $options = [
            ['id' => 1, 'text' => 'Option A'],
            ['id' => 2, 'text' => 'Option B']
        ];

        $mockService = Mockery::mock(QuizService::class);
        $mockService->shouldReceive('getOptionById')->once()->with(1)->andReturn($options);

        $controller = new QuizController($mockService);

        $response = $controller->getOption(1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_freekey_with_null_syllabus_from_request()
    {
        $mockService = Mockery::mock(QuizService::class);
        $mockService->shouldReceive('getQuizKey')->once()->with(null)->andReturn([]);

        $controller = new QuizController($mockService);
        $request = new Request();

        $response = $controller->freekey($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(QuizService::class);
        $controller = new QuizController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }
}
