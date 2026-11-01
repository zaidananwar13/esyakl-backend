<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\KategoriController;
use App\Services\KategoriService;
use Mockery;
use Tests\TestCase;

class KategoriControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(KategoriService::class);
        $controller = new KategoriController($mockService);
        $this->assertInstanceOf(KategoriController::class, $controller);
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(KategoriService::class);
        $controller = new KategoriController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_index_method_exists()
    {
        $mockService = Mockery::mock(KategoriService::class);
        $controller = new KategoriController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
    }
}
