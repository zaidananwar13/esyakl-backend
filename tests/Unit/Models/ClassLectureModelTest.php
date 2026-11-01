<?php

namespace Tests\Unit\Models;

use App\Models\ClassLecture;
use Tests\TestCase;

class ClassLectureModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $classLecture = new ClassLecture();
        $this->assertInstanceOf(ClassLecture::class, $classLecture);
    }

    public function test_model_has_correct_table_name()
    {
        $classLecture = new ClassLecture();
        $this->assertEquals('class_lecture', $classLecture->getTable());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ClassLecture::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $classLecture = new ClassLecture();
        $classLecture->id_class = 1;
        $classLecture->id_user = 5;

        $this->assertEquals(1, $classLecture->id_class);
        $this->assertEquals(5, $classLecture->id_user);
    }
}
