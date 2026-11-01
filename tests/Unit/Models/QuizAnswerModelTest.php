<?php

namespace Tests\Unit\Models;

use App\Models\QuizAnswer;
use Tests\TestCase;

class QuizAnswerModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $quizAnswer = new QuizAnswer();
        $this->assertInstanceOf(QuizAnswer::class, $quizAnswer);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(QuizAnswer::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $quizAnswer = new QuizAnswer();
        $quizAnswer->id_quiz_student = 1;
        $quizAnswer->id_option = 5;
        $quizAnswer->answer = 'Test Answer';

        $this->assertEquals(1, $quizAnswer->id_quiz_student);
        $this->assertEquals(5, $quizAnswer->id_option);
        $this->assertEquals('Test Answer', $quizAnswer->answer);
    }
}
