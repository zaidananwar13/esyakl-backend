<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Quiz;
use Tests\TestCase;

class QuizModelTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated()
    {
        $quiz = new Quiz();
        $this->assertInstanceOf(Quiz::class, $quiz);
    }

    #[Test]
    public function it_can_set_and_get_attributes()
    {
        $quiz = new Quiz();
        
        $quiz->id_syllabus = 1;
        $quiz->title = 'Test Quiz';
        $quiz->pass_grade = 70;

        $this->assertEquals(1, $quiz->id_syllabus);
        $this->assertEquals('Test Quiz', $quiz->title);
        $this->assertEquals(70, $quiz->pass_grade);
    }

    #[Test]
    public function it_extends_model()
    {
        $quiz = new Quiz();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $quiz);
    }
}
