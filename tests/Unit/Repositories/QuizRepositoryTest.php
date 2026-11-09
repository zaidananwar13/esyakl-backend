<?php

namespace Tests\Unit\Repositories;

use PHPUnit\Framework\Attributes\Test;

use App\Repositories\Eloquent\QuizRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class QuizRepositoryTest extends TestCase
{
    protected QuizRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new QuizRepository();
    }

    #[Test]
    public function get_quiz_by_syllabus_method_exists()
    {
        $this->assertTrue(method_exists($this->repository, 'getQuizBySyllabus'));
    }

    #[Test]
    public function get_quiz_sections_returns_collection()
    {
        $this->assertTrue(method_exists($this->repository, 'getQuizSections'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getQuizSections');
        $this->assertEquals('Illuminate\Support\Collection', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_questions_returns_collection()
    {
        $this->assertTrue(method_exists($this->repository, 'getQuestions'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getQuestions');
        $this->assertEquals('Illuminate\Support\Collection', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_options_returns_collection()
    {
        $this->assertTrue(method_exists($this->repository, 'getOptions'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getOptions');
        $this->assertEquals('Illuminate\Support\Collection', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_quiz_history_method_exists()
    {
        $this->assertTrue(method_exists($this->repository, 'getQuizHistory'));
    }

    #[Test]
    public function save_quiz_attempt_returns_quiz_student()
    {
        $this->assertTrue(method_exists($this->repository, 'saveQuizAttempt'));
        
        $reflection = new \ReflectionMethod($this->repository, 'saveQuizAttempt');
        $this->assertEquals('App\Models\QuizStudent', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function save_quiz_answer_returns_quiz_answer()
    {
        $this->assertTrue(method_exists($this->repository, 'saveQuizAnswer'));
        
        $reflection = new \ReflectionMethod($this->repository, 'saveQuizAnswer');
        $this->assertEquals('App\Models\QuizAnswer', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_correct_answer_method_exists()
    {
        $this->assertTrue(method_exists($this->repository, 'getCorrectAnswer'));
    }

    #[Test]
    public function get_text_answer_returns_nullable_string()
    {
        $this->assertTrue(method_exists($this->repository, 'getTextAnswer'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getTextAnswer');
        $this->assertTrue($reflection->getReturnType()->allowsNull());
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'getQuizBySyllabus',
            'getQuizSections',
            'getQuestions',
            'getOptions',
            'getQuizHistory',
            'saveQuizAttempt',
            'saveQuizAnswer',
            'getCorrectAnswer',
            'getTextAnswer',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->repository, $method), "Method $method should exist");
        }
    }
}
