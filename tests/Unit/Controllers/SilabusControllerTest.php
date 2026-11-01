<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\SilabusController;
use App\Services\SyllabusService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class SilabusControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $controller = new SilabusController($mockService);
        $this->assertInstanceOf(SilabusController::class, $controller);
    }

    public function test_silabus_method_exists()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $controller = new SilabusController($mockService);
        $this->assertTrue(method_exists($controller, 'silabus'));
    }

    public function test_index_method_exists()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $controller = new SilabusController($mockService);
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_auth_kategori_returns_unauthorized()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $mockService->shouldReceive('authKategori')
            ->once()
            ->andReturn(['success' => false, 'code' => 401, 'message' => 'Unauthorized']);

        $controller = new SilabusController($mockService);
        $request = new Request(['api_token' => 'invalid', 'silabus' => 1, 'material' => 1]);

        $response = $controller->authKategori($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_auth_kategori_returns_success()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $mockService->shouldReceive('authKategori')
            ->once()
            ->andReturn(['success' => true, 'message' => 'Success']);

        $controller = new SilabusController($mockService);
        $request = new Request(['api_token' => 'valid', 'silabus' => 1, 'material' => 1]);

        $response = $controller->authKategori($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_auth_sub_kategori_returns_unauthorized()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $mockService->shouldReceive('authSubKategori')
            ->once()
            ->andReturn(['success' => false, 'code' => 401, 'message' => 'Unauthorized']);

        $controller = new SilabusController($mockService);
        $request = new Request(['api_token' => 'invalid', 'silabus' => 1, 'material' => 1]);

        $response = $controller->authSubKategori($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_auth_sub_kategori_returns_success()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $mockService->shouldReceive('authSubKategori')
            ->once()
            ->andReturn(['success' => true, 'message' => 'Success']);

        $controller = new SilabusController($mockService);
        $request = new Request(['api_token' => 'valid', 'silabus' => 1, 'material' => 1]);

        $response = $controller->authSubKategori($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $controller = new SilabusController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(SyllabusService::class);
        $controller = new SilabusController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'silabus'));
        $this->assertTrue(method_exists($controller, 'authKategori'));
        $this->assertTrue(method_exists($controller, 'authSubKategori'));
    }
}
