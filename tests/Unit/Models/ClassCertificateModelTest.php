<?php

namespace Tests\Unit\Models;

use App\Models\ClassCertificate;
use Tests\TestCase;

class ClassCertificateModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $classCertificate = new ClassCertificate();
        $this->assertInstanceOf(ClassCertificate::class, $classCertificate);
    }

    public function test_model_has_correct_table_name()
    {
        $classCertificate = new ClassCertificate();
        $this->assertEquals('class_certificate', $classCertificate->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $classCertificate = new ClassCertificate();
        $this->assertEquals('id_class_certificate', $classCertificate->getKeyName());
    }

    public function test_model_has_fillable_attributes()
    {
        $classCertificate = new ClassCertificate();
        $fillable = $classCertificate->getFillable();

        $this->assertContains('id_certificate', $fillable);
        $this->assertContains('id_class_student', $fillable);
        $this->assertContains('file', $fillable);
        $this->assertContains('token', $fillable);
        $this->assertContains('created_at', $fillable);
        $this->assertContains('updated_at', $fillable);
    }

    public function test_model_has_unix_date_format()
    {
        $classCertificate = new ClassCertificate();
        $this->assertEquals('U', $classCertificate->getDateFormat());
    }

    public function test_model_has_incrementing_enabled()
    {
        $classCertificate = new ClassCertificate();
        $this->assertTrue($classCertificate->getIncrementing());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ClassCertificate::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $classCertificate = new ClassCertificate();
        $classCertificate->id_class_student = 1;
        $classCertificate->file = 'cert.pdf';
        $classCertificate->token = 'token123';

        $this->assertEquals(1, $classCertificate->id_class_student);
        $this->assertEquals('cert.pdf', $classCertificate->file);
        $this->assertEquals('token123', $classCertificate->token);
    }

    public function test_model_mass_assignment()
    {
        $classCertificate = new ClassCertificate([
            'id_certificate' => 1,
            'id_class_student' => 5,
            'file' => 'certificate.pdf',
            'token' => 'abc123'
        ]);

        $this->assertEquals(5, $classCertificate->id_class_student);
        $this->assertEquals('certificate.pdf', $classCertificate->file);
    }
}
