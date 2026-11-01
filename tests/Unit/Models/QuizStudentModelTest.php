<?php

namespace Tests\Unit\Models;

use App\Models\QuizStudent;
use Tests\TestCase;

class QuizStudentModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $quizStudent = new QuizStudent();
        $this->assertInstanceOf(QuizStudent::class, $quizStudent);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(QuizStudent::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $quizStudent = new QuizStudent();
        $quizStudent->id_quiz = 1;
        $quizStudent->id_class_student = 5;
        $quizStudent->grade = 85.5;

        $this->assertEquals(1, $quizStudent->id_quiz);
        $this->assertEquals(5, $quizStudent->id_class_student);
        $this->assertEquals(85.5, $quizStudent->grade);
    }
}
