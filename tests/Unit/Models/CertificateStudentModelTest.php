<?php

namespace Tests\Unit\Models;

use App\Models\CertificateStudent;
use Tests\TestCase;

class CertificateStudentModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $certificateStudent = new CertificateStudent();
        $this->assertInstanceOf(CertificateStudent::class, $certificateStudent);
    }

    public function test_model_has_correct_table_name()
    {
        $certificateStudent = new CertificateStudent();
        $this->assertEquals('class_certificate', $certificateStudent->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $certificateStudent = new CertificateStudent();
        $this->assertEquals('id_class_certificate', $certificateStudent->getKeyName());
    }

    public function test_model_has_unix_date_format()
    {
        $certificateStudent = new CertificateStudent();
        $this->assertEquals('U', $certificateStudent->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(CertificateStudent::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $certificateStudent = new CertificateStudent();
        $certificateStudent->id_class_student = 1;
        $certificateStudent->token = 'abc123';
        $certificateStudent->file = 'certificate.pdf';

        $this->assertEquals(1, $certificateStudent->id_class_student);
        $this->assertEquals('abc123', $certificateStudent->token);
        $this->assertEquals('certificate.pdf', $certificateStudent->file);
    }
}
