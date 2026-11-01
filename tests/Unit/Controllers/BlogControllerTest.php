<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\BlogController;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(BlogService::class);
        $controller = new BlogController($mockService);
        $this->assertInstanceOf(BlogController::class, $controller);
    }

    public function test_index_returns_not_found_when_no_blogs()
    {
        $mockService = Mockery::mock(BlogService::class);
        $mockService->shouldReceive('getAllBlogs')->once()->andReturn([]);

        $controller = new BlogController($mockService);
        $request = new Request();

        $response = $controller->index($request);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_index_returns_blogs_when_found()
    {
        $blogs = [
            ['id' => 1, 'title' => 'Test Blog'],
            ['id' => 2, 'title' => 'Test Blog 2']
        ];

        $mockService = Mockery::mock(BlogService::class);
        $mockService->shouldReceive('getAllBlogs')->once()->andReturn($blogs);

        $controller = new BlogController($mockService);
        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_index_with_id_returns_blog()
    {
        $blog = ['id' => 1, 'title' => 'Test Blog'];

        $mockService = Mockery::mock(BlogService::class);
        $mockService->shouldReceive('getBlogById')->once()->with(1, null)->andReturn($blog);

        $controller = new BlogController($mockService);
        $request = new Request();

        $response = $controller->index($request, 1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_index_with_id_returns_not_found_when_blog_missing()
    {
        $mockService = Mockery::mock(BlogService::class);
        $mockService->shouldReceive('getBlogById')->once()->andReturn(null);

        $controller = new BlogController($mockService);
        $request = new Request();

        $response = $controller->index($request, 99999);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(BlogService::class);
        $controller = new BlogController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }
}
