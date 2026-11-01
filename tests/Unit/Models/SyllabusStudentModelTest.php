<?php

namespace Tests\Unit\Models;

use App\Models\SyllabusStudent;
use Tests\TestCase;

class SyllabusStudentModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $syllabusStudent = new SyllabusStudent();
        $this->assertInstanceOf(SyllabusStudent::class, $syllabusStudent);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(SyllabusStudent::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $syllabusStudent = new SyllabusStudent();
        $syllabusStudent->id_class_student = 1;
        $syllabusStudent->id_syllabus = 5;

        $this->assertEquals(1, $syllabusStudent->id_class_student);
        $this->assertEquals(5, $syllabusStudent->id_syllabus);
    }
}
