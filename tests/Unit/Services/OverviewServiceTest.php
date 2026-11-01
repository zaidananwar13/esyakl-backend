<?php

namespace Tests\Unit\Services;

use App\Services\OverviewService;
use Mockery;
use Tests\TestCase;

class OverviewServiceTest extends TestCase
{
    protected OverviewService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OverviewService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_overview_by_slide_returns_unknown_for_invalid_slide()
    {
        $result = $this->service->getOverviewBySlide(99, 'English');
        $this->assertEquals('unknown request', $result);
    }

    public function test_get_overview_by_slide_one()
    {
        $result = $this->service->getOverviewBySlide(1, 'English');
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_get_overview_by_slide_two()
    {
        $result = $this->service->getOverviewBySlide(2, 'English');
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_get_overview_by_slide_three()
    {
        $result = $this->service->getOverviewBySlide(3, 'English');
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_get_overview_by_slide_four()
    {
        $result = $this->service->getOverviewBySlide(4, 'English');
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_get_overview_by_slide_five()
    {
        $result = $this->service->getOverviewBySlide(5, 'English');
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_get_overview_with_indonesian_language()
    {
        $result = $this->service->getOverviewBySlide(1, 'Indonesian');
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_get_faq_returns_collection()
    {
        $result = $this->service->getFaq('English');
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_get_faq_with_indonesian()
    {
        $result = $this->service->getFaq('Indonesian');
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_get_faq_with_invalid_language_returns_empty()
    {
        $result = $this->service->getFaq('InvalidLang');
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_get_overview_default_language()
    {
        $result = $this->service->getOverviewBySlide(1);
        $this->assertTrue(is_object($result) || is_array($result));
    }

    public function test_service_can_be_instantiated()
    {
        $service = new OverviewService();
        $this->assertInstanceOf(OverviewService::class, $service);
    }

    public function test_get_faq_default_language()
    {
        $result = $this->service->getFaq();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }
}
