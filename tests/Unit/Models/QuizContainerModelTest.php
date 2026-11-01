<?php

namespace Tests\Unit\Models;

use App\Models\QuizContainer;
use Tests\TestCase;

class QuizContainerModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $quizContainer = new QuizContainer();
        $this->assertInstanceOf(QuizContainer::class, $quizContainer);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(QuizContainer::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $quizContainer = new QuizContainer();
        $quizContainer->id_quiz = 1;

        $this->assertEquals(1, $quizContainer->id_quiz);
    }
}
