<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\FEAuthController;
use App\Services\AuthorizerService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class FEAuthControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(AuthorizerService::class);
        $controller = new FEAuthController($mockService);
        $this->assertInstanceOf(FEAuthController::class, $controller);
    }

    public function test_index_returns_unauthorized_when_no_token()
    {
        $mockService = Mockery::mock(AuthorizerService::class);

        $controller = new FEAuthController($mockService);
        $request = new Request();

        $response = $controller->index($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_index_returns_unauthorized_when_user_not_found()
    {
        $mockService = Mockery::mock(AuthorizerService::class);

        $controller = new FEAuthController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->index($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_fe_authorizer_returns_unauthorized_when_no_token()
    {
        $mockService = Mockery::mock(AuthorizerService::class);

        $controller = new FEAuthController($mockService);
        $request = new Request();

        $response = $controller->feAuthorizer($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_fe_authorizer_returns_unauthorized_when_user_not_found()
    {
        $mockService = Mockery::mock(AuthorizerService::class);

        $controller = new FEAuthController($mockService);
        $request = new Request(['api_token' => 'invalid']);

        $response = $controller->feAuthorizer($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_static_fe_authorizer_method_exists()
    {
        $this->assertTrue(method_exists(FEAuthController::class, 'staticFEAuthorizer'));
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(AuthorizerService::class);
        $controller = new FEAuthController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'forbiddenResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(AuthorizerService::class);
        $controller = new FEAuthController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'feAuthorizer'));
    }
}
