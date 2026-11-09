<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Services\ClassService;
use Tests\TestCase;

class ClassServiceTest extends TestCase
{
    protected ClassService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClassService();
    }

    #[Test]
    public function get_class_details_returns_nullable_array()
    {
        $this->assertTrue(method_exists($this->service, 'getClassDetails'));
        
        $reflection = new \ReflectionMethod($this->service, 'getClassDetails');
        $this->assertTrue($reflection->getReturnType()->allowsNull());
    }

    #[Test]
    public function get_class_details_accepts_correct_parameters()
    {
        $reflection = new \ReflectionMethod($this->service, 'getClassDetails');
        $params = $reflection->getParameters();
        
        $this->assertEquals('classId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('userId', $params[1]->getName());
        $this->assertTrue($params[1]->allowsNull());
    }

    #[Test]
    public function get_class_list_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getClassList'));
        
        $reflection = new \ReflectionMethod($this->service, 'getClassList');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function enroll_user_to_class_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'enrollUserToClass'));
        
        $reflection = new \ReflectionMethod($this->service, 'enrollUserToClass');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function enroll_user_to_class_accepts_correct_parameters()
    {
        $reflection = new \ReflectionMethod($this->service, 'enrollUserToClass');
        $params = $reflection->getParameters();
        
        $this->assertEquals('classId', $params[0]->getName());
        $this->assertEquals('userId', $params[1]->getName());
        $this->assertEquals('isPremium', $params[2]->getName());
        $this->assertTrue($params[2]->isOptional());
        $this->assertFalse($params[2]->getDefaultValue());
    }

    #[Test]
    public function check_enrollment_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'checkEnrollment'));
        
        $reflection = new \ReflectionMethod($this->service, 'checkEnrollment');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function calculate_class_progress_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'calculateClassProgress'));
        
        $reflection = new \ReflectionMethod($this->service, 'calculateClassProgress');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function complete_class_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'completeClass'));
        
        $reflection = new \ReflectionMethod($this->service, 'completeClass');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function search_classes_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'searchClasses'));
        
        $reflection = new \ReflectionMethod($this->service, 'searchClasses');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function filter_classes_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'filterClasses'));
        
        $reflection = new \ReflectionMethod($this->service, 'filterClasses');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function submit_review_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'submitReview'));
        
        $reflection = new \ReflectionMethod($this->service, 'submitReview');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function update_class_history_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'updateClassHistory'));
        
        $reflection = new \ReflectionMethod($this->service, 'updateClassHistory');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function rate_lecture_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'rateLecture'));
        
        $reflection = new \ReflectionMethod($this->service, 'rateLecture');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_checkout_info_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getCheckoutInfo'));
        
        $reflection = new \ReflectionMethod($this->service, 'getCheckoutInfo');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_active_enrollment_accepts_correct_parameters()
    {
        $reflection = new \ReflectionMethod($this->service, 'getActiveEnrollment');
        $params = $reflection->getParameters();
        
        $this->assertEquals('classId', $params[0]->getName());
        $this->assertEquals('userId', $params[1]->getName());
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'getClassDetails',
            'getClassList',
            'enrollUserToClass',
            'getActiveEnrollment',
            'checkEnrollment',
            'calculateClassProgress',
            'completeClass',
            'searchClasses',
            'filterClasses',
            'submitReview',
            'updateClassHistory',
            'rateLecture',
            'getCheckoutInfo',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->service, $method), "Method $method should exist");
        }
    }
}
