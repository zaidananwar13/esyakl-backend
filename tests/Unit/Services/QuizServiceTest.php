<?php

namespace Tests\Unit\Services;

use App\Services\QuizService;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    protected QuizService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuizService();
    }

    /** @test */
    public function get_quiz_for_syllabus_returns_nullable_array()
    {
        $this->assertTrue(method_exists($this->service, 'getQuizForSyllabus'));
        
        $reflection = new \ReflectionMethod($this->service, 'getQuizForSyllabus');
        $this->assertTrue($reflection->getReturnType()->allowsNull());
    }

    /** @test */
    public function get_quiz_for_syllabus_accepts_correct_parameters()
    {
        $reflection = new \ReflectionMethod($this->service, 'getQuizForSyllabus');
        $params = $reflection->getParameters();
        
        $this->assertEquals('syllabusId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('userId', $params[1]->getName());
        $this->assertEquals('int', $params[1]->getType()->getName());
    }

    /** @test */
    public function get_quiz_sections_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getQuizSections'));
        
        $reflection = new \ReflectionMethod($this->service, 'getQuizSections');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_questions_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getQuestions'));
        
        $reflection = new \ReflectionMethod($this->service, 'getQuestions');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_options_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'getOptions'));
    }

    /** @test */
    public function get_quiz_history_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'getQuizHistory'));
    }

    /** @test */
    public function submit_quiz_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'submitQuiz'));
        
        $reflection = new \ReflectionMethod($this->service, 'submitQuiz');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function calculate_score_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'calculateScore'));
        
        $reflection = new \ReflectionMethod($this->service, 'calculateScore');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_quiz_key_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getQuizKey'));
        
        $reflection = new \ReflectionMethod($this->service, 'getQuizKey');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_sample_quiz_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getSampleQuiz'));
        
        $reflection = new \ReflectionMethod($this->service, 'getSampleQuiz');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_option_by_id_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'getOptionById'));
    }

    /** @test */
    public function get_quiz_progress_method_exists()
    {
        $this->assertTrue(method_exists($this->service, 'getQuizProgress'));
    }

    /** @test */
    public function is_quiz_completed_returns_bool()
    {
        $this->assertTrue(method_exists($this->service, 'isQuizCompleted'));
        
        $reflection = new \ReflectionMethod($this->service, 'isQuizCompleted');
        $this->assertEquals('bool', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function all_public_methods_exist()
    {
        $methods = [
            'getQuizForSyllabus',
            'getQuizSections',
            'getQuestions',
            'getOptions',
            'getQuizHistory',
            'submitQuiz',
            'calculateScore',
            'saveQuizAttempt',
            'getQuizKey',
            'getSampleQuiz',
            'getOptionById',
            'getQuizProgress',
            'isQuizCompleted',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->service, $method), "Method $method should exist");
        }
    }
}
