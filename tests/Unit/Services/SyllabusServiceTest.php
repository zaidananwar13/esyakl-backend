<?php

namespace Tests\Unit\Services;

use App\Services\SyllabusService;
use Mockery;
use Tests\TestCase;

class SyllabusServiceTest extends TestCase
{
    protected SyllabusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SyllabusService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_authorizer_status_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->getAuthorizerStatus('invalid-token', 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_update_fe_authorizer_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->updateFeAuthorizer('invalid-token', 1, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_get_syllabus_by_class_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->getSyllabusByClass('invalid-token', 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_get_syllabus_material_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->getSyllabusMaterial('invalid-token', 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized User', $result['message']);
    }

    public function test_auth_kategori_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->authKategori('invalid-token', 1, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_auth_sub_kategori_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->authSubKategori('invalid-token', 1, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new SyllabusService();
        $this->assertInstanceOf(SyllabusService::class, $service);
    }

    public function test_get_authorizer_status_returns_array()
    {
        $result = $this->service->getAuthorizerStatus('token', 1);
        $this->assertIsArray($result);
    }

    public function test_update_fe_authorizer_returns_array()
    {
        $result = $this->service->updateFeAuthorizer('token', 1, 1);
        $this->assertIsArray($result);
    }

    public function test_get_syllabus_by_class_returns_array()
    {
        $result = $this->service->getSyllabusByClass('token', 1);
        $this->assertIsArray($result);
    }

    public function test_get_syllabus_material_returns_array()
    {
        $result = $this->service->getSyllabusMaterial('token', 1);
        $this->assertIsArray($result);
    }
}
