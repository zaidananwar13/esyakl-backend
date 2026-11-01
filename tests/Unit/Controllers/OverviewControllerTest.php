<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\OverviewController;
use App\Services\OverviewService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class OverviewControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(OverviewService::class);
        $controller = new OverviewController($mockService);
        $this->assertInstanceOf(OverviewController::class, $controller);
    }

    public function test_faq_returns_success_response()
    {
        $faqData = collect([
            ['title' => 'Question 1', 'content' => 'Answer 1'],
            ['title' => 'Question 2', 'content' => 'Answer 2'],
        ]);

        $mockService = Mockery::mock(OverviewService::class);
        $mockService->shouldReceive('getFaq')->once()->with('English')->andReturn($faqData);

        $controller = new OverviewController($mockService);
        $request = new Request();

        $response = $controller->faq($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_faq_with_indonesian_language()
    {
        $faqData = collect([['title' => 'Pertanyaan', 'content' => 'Jawaban']]);

        $mockService = Mockery::mock(OverviewService::class);
        $mockService->shouldReceive('getFaq')->once()->with('Indonesian')->andReturn($faqData);

        $controller = new OverviewController($mockService);
        $request = new Request(['lang' => 'Indonesian']);

        $response = $controller->faq($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(OverviewService::class);
        $controller = new OverviewController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_index_method_exists()
    {
        $mockService = Mockery::mock(OverviewService::class);
        $controller = new OverviewController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
    }
}
