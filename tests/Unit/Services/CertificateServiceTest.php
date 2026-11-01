<?php

namespace Tests\Unit\Services;

use App\Services\CertificateService;
use Mockery;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    protected CertificateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CertificateService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_user_certificates_returns_array()
    {
        $result = $this->service->getUserCertificates(1);
        $this->assertIsArray($result);
    }

    public function test_get_certificate_by_class_returns_null_when_not_found()
    {
        $result = $this->service->getCertificateByClass(99999, 99999);
        $this->assertNull($result);
    }

    public function test_get_certificate_by_token_returns_null_when_not_found()
    {
        $result = $this->service->getCertificateByToken('invalid-token');
        $this->assertNull($result);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new CertificateService();
        $this->assertInstanceOf(CertificateService::class, $service);
    }

    public function test_get_user_certificates_for_user_without_certificates()
    {
        $result = $this->service->getUserCertificates(99999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_certificate_by_class_returns_null_for_invalid_user()
    {
        $result = $this->service->getCertificateByClass(0, 1);
        $this->assertNull($result);
    }

    public function test_get_certificate_by_class_returns_null_for_invalid_class()
    {
        $result = $this->service->getCertificateByClass(1, 0);
        $this->assertNull($result);
    }

    public function test_get_certificate_by_token_with_empty_token()
    {
        $result = $this->service->getCertificateByToken('');
        $this->assertNull($result);
    }
}
