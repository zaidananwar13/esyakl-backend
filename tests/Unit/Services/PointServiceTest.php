<?php

namespace Tests\Unit\Services;

use App\Services\PointService;
use Mockery;
use Tests\TestCase;

class PointServiceTest extends TestCase
{
    protected PointService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PointService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_and_submit_points_returns_false_when_no_pending_points()
    {
        $result = $this->service->calculateAndSubmitPoints(99999);
        $this->assertFalse($result);
    }

    public function test_get_total_points_returns_integer()
    {
        $result = $this->service->getTotalPoints(1);
        $this->assertIsInt($result);
    }

    public function test_get_total_points_for_user_without_points()
    {
        $result = $this->service->getTotalPoints(99999);
        $this->assertEquals(0, $result);
    }

    public function test_get_pending_points_returns_collection()
    {
        $result = $this->service->getPendingPoints(1);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function test_get_pending_points_for_user_without_pending()
    {
        $result = $this->service->getPendingPoints(99999);
        $this->assertTrue($result->isEmpty());
    }

    public function test_service_can_be_instantiated()
    {
        $service = new PointService();
        $this->assertInstanceOf(PointService::class, $service);
    }

    public function test_get_bracket_returns_correct_values()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getBracket');
        $method->setAccessible(true);

        // Test various point ranges
        $this->assertEquals(20, $method->invoke($this->service, 0));
        $this->assertEquals(20, $method->invoke($this->service, 10));
        $this->assertEquals(20, $method->invoke($this->service, 20));
        $this->assertEquals(40, $method->invoke($this->service, 21));
        $this->assertEquals(40, $method->invoke($this->service, 40));
        $this->assertEquals(60, $method->invoke($this->service, 41));
        $this->assertEquals(60, $method->invoke($this->service, 60));
        $this->assertEquals(80, $method->invoke($this->service, 61));
        $this->assertEquals(80, $method->invoke($this->service, 80));
        $this->assertEquals(100, $method->invoke($this->service, 81));
        $this->assertEquals(100, $method->invoke($this->service, 100));
    }

    public function test_get_bracket_above_100_returns_100()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getBracket');
        $method->setAccessible(true);

        $this->assertEquals(100, $method->invoke($this->service, 150));
    }
}
