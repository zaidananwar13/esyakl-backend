<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\PromoController;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class PromoControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(PromoService::class);
        $controller = new PromoController($mockService);
        $this->assertInstanceOf(PromoController::class, $controller);
    }

    public function test_index_returns_unauthorized_when_no_user()
    {
        $mockService = Mockery::mock(PromoService::class);

        $controller = new PromoController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->index($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_get_promo_static_method_exists()
    {
        $this->assertTrue(method_exists(PromoController::class, 'getPromo'));
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(PromoService::class);
        $controller = new PromoController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
    }

    public function test_index_method_exists()
    {
        $mockService = Mockery::mock(PromoService::class);
        $controller = new PromoController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
    }
}
