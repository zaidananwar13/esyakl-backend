<?php

namespace Tests\Unit\Models;

use App\Models\QuizProgress;
use Tests\TestCase;

class QuizProgressModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $quizProgress = new QuizProgress();
        $this->assertInstanceOf(QuizProgress::class, $quizProgress);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(QuizProgress::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $quizProgress = new QuizProgress();
        $quizProgress->id_user = 1;
        $quizProgress->id_quiz = 5;
        $quizProgress->progress = 50;

        $this->assertEquals(1, $quizProgress->id_user);
        $this->assertEquals(5, $quizProgress->id_quiz);
        $this->assertEquals(50, $quizProgress->progress);
    }
}
