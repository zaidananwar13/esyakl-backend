<?php

namespace Tests\Unit\Services;

use App\Services\PromoService;
use Mockery;
use Tests\TestCase;

class PromoServiceTest extends TestCase
{
    protected PromoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PromoService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validate_promo_code_returns_error_when_not_found()
    {
        $result = $this->service->validatePromoCode('INVALID_CODE');

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Promo Coupon Invalid', $result['message']);
    }

    public function test_validate_promo_code_returns_array()
    {
        $result = $this->service->validatePromoCode('TEST');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    public function test_get_promo_details_returns_error_when_code_is_null()
    {
        $result = $this->service->getPromoDetails(null);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Promo Coupon Invalid', $result['message']);
    }

    public function test_get_promo_details_returns_error_when_not_found()
    {
        $result = $this->service->getPromoDetails('INVALID_CODE');

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Promo Coupon Invalid', $result['message']);
    }

    public function test_get_promo_details_returns_array()
    {
        $result = $this->service->getPromoDetails('TEST');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new PromoService();
        $this->assertInstanceOf(PromoService::class, $service);
    }

    public function test_validate_promo_code_with_empty_string()
    {
        $result = $this->service->validatePromoCode('');
        $this->assertFalse($result['success']);
    }

    public function test_get_promo_details_with_empty_string()
    {
        $result = $this->service->getPromoDetails('');
        $this->assertFalse($result['success']);
    }
}
