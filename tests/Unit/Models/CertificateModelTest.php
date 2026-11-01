<?php

namespace Tests\Unit\Models;

use App\Models\Certificate;
use Tests\TestCase;

class CertificateModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $certificate = new Certificate();
        $this->assertInstanceOf(Certificate::class, $certificate);
    }

    public function test_model_has_correct_table_name()
    {
        $certificate = new Certificate();
        $this->assertEquals('certificate', $certificate->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $certificate = new Certificate();
        $this->assertEquals('id_certificate', $certificate->getKeyName());
    }

    public function test_model_has_unix_date_format()
    {
        $certificate = new Certificate();
        $this->assertEquals('U', $certificate->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Certificate::class, 'factory'));
    }

    public function test_model_has_timestamps_enabled()
    {
        $certificate = new Certificate();
        $this->assertTrue($certificate->usesTimestamps());
    }

    public function test_can_set_attributes()
    {
        $certificate = new Certificate();
        $certificate->template = 'template_1';
        $certificate->status = 'active';

        $this->assertEquals('template_1', $certificate->template);
        $this->assertEquals('active', $certificate->status);
    }
}
