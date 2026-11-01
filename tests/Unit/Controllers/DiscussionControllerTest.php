<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\DiscussionController;
use App\Services\DiscussionService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class DiscussionControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(DiscussionService::class);
        $controller = new DiscussionController($mockService);
        $this->assertInstanceOf(DiscussionController::class, $controller);
    }

    public function test_classes_returns_success()
    {
        $classes = [
            ['id' => 1, 'name' => 'Class 1'],
            ['id' => 2, 'name' => 'Class 2']
        ];

        $mockService = Mockery::mock(DiscussionService::class);
        $mockService->shouldReceive('getClasses')->once()->andReturn($classes);

        $controller = new DiscussionController($mockService);

        $response = $controller->classes();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_classes_returns_empty_array()
    {
        $mockService = Mockery::mock(DiscussionService::class);
        $mockService->shouldReceive('getClasses')->once()->andReturn([]);

        $controller = new DiscussionController($mockService);

        $response = $controller->classes();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_comment_returns_unauthorized_when_no_user()
    {
        $mockService = Mockery::mock(DiscussionService::class);

        $controller = new DiscussionController($mockService);
        $request = new Request(['api_token' => 'invalid', 'id_comment' => 1]);

        $response = $controller->deleteCommentDiscussion($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(DiscussionService::class);
        $controller = new DiscussionController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'errorResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }
}
