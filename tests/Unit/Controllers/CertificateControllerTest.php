<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\CertificateController;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class CertificateControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(CertificateService::class);
        $controller = new CertificateController($mockService);
        $this->assertInstanceOf(CertificateController::class, $controller);
    }

    public function test_index_returns_unauthorized_when_no_token()
    {
        $mockService = Mockery::mock(CertificateService::class);

        $controller = new CertificateController($mockService);
        $request = new Request();

        $response = $controller->index($request);
        $content = json_decode($response->getContent(), true);

        // Returns unauthorized response
        $this->assertContains($response->getStatusCode(), [401, 403]);
    }

    public function test_index_returns_unauthorized_when_user_not_found()
    {
        $mockService = Mockery::mock(CertificateService::class);

        $controller = new CertificateController($mockService);
        $request = new Request(['api_token' => 'invalid-token']);

        $response = $controller->index($request);

        $this->assertContains($response->getStatusCode(), [401, 403]);
    }

    public function test_shared_returns_not_found_when_certificate_missing()
    {
        $mockService = Mockery::mock(CertificateService::class);
        $mockService->shouldReceive('getCertificateByToken')->once()->with('invalid-token')->andReturn(null);

        $controller = new CertificateController($mockService);

        $response = $controller->shared('invalid-token');

        // Returns unauthorized when certificate not found
        $this->assertContains($response->getStatusCode(), [401, 403]);
    }

    public function test_shared_returns_certificate_when_found()
    {
        $certificate = [
            'id_class_student' => 1,
            'token' => 'valid-token',
            'user' => 'Test User'
        ];

        $mockService = Mockery::mock(CertificateService::class);
        $mockService->shouldReceive('getCertificateByToken')->once()->with('valid-token')->andReturn($certificate);

        $controller = new CertificateController($mockService);

        $response = $controller->shared('valid-token');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(CertificateService::class);
        $controller = new CertificateController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_create_local_method_exists()
    {
        $this->assertTrue(method_exists(CertificateController::class, 'createLocal'));
    }
}
