<?php

namespace Tests\Unit\Models;

use App\Models\PointStudent;
use Tests\TestCase;

class PointStudentModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $pointStudent = new PointStudent();
        $this->assertInstanceOf(PointStudent::class, $pointStudent);
    }

    public function test_model_has_correct_table_name()
    {
        $pointStudent = new PointStudent();
        $this->assertEquals('point_student', $pointStudent->getTable());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(PointStudent::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $pointStudent = new PointStudent();
        $pointStudent->id_user = 1;
        $pointStudent->points = 100;
        $pointStudent->status = 1;

        $this->assertEquals(1, $pointStudent->id_user);
        $this->assertEquals(100, $pointStudent->points);
        $this->assertEquals(1, $pointStudent->status);
    }
}
