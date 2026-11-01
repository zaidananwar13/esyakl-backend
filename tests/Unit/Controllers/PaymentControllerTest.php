<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\PaymentController;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(PaymentService::class);
        $controller = new PaymentController($mockService);
        $this->assertInstanceOf(PaymentController::class, $controller);
    }

    public function test_create_snap_payment_returns_unauthorized_when_no_user()
    {
        $mockService = Mockery::mock(PaymentService::class);

        $controller = new PaymentController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->createSnapPayment($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_handle_payment_returns_unauthorized_when_no_user()
    {
        $mockService = Mockery::mock(PaymentService::class);

        $controller = new PaymentController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->handlePayment($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(PaymentService::class);
        $controller = new PaymentController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(PaymentService::class);
        $controller = new PaymentController($mockService);

        $this->assertTrue(method_exists($controller, 'createSnapPayment'));
        $this->assertTrue(method_exists($controller, 'handlePayment'));
    }
}
