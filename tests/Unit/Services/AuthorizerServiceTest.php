<?php

namespace Tests\Unit\Services;

use App\Services\AuthorizerService;
use Mockery;
use Tests\TestCase;

class AuthorizerServiceTest extends TestCase
{
    protected AuthorizerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthorizerService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_unlocked_material_returns_no_access_when_class_student_not_found()
    {
        $result = $this->service->getUnlockedMaterial(99999, 99999);

        $this->assertFalse($result['success']);
        $this->assertEquals(403, $result['code']);
        $this->assertEquals('You have no access to this class', $result['message']);
    }

    public function test_unlock_material_returns_unauthorized_when_class_student_not_found()
    {
        $result = $this->service->unlockMaterial(99999, 99999, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(403, $result['code']);
        $this->assertEquals('Unauthorized.', $result['message']);
    }

    public function test_static_authorize_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'staticAuthorize'));
    }

    public function test_service_can_be_instantiated()
    {
        $service = new AuthorizerService();
        $this->assertInstanceOf(AuthorizerService::class, $service);
    }

    public function test_get_unlocked_material_returns_array()
    {
        $result = $this->service->getUnlockedMaterial(1, 1);
        $this->assertIsArray($result);
    }

    public function test_unlock_material_returns_array()
    {
        $result = $this->service->unlockMaterial(1, 1, 1);
        $this->assertIsArray($result);
    }

    public function test_get_unlocked_material_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'getUnlockedMaterial'));
    }

    public function test_unlock_material_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'unlockMaterial'));
    }
}
