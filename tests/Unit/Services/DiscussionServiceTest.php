<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Services\DiscussionService;
use Tests\TestCase;

class DiscussionServiceTest extends TestCase
{
    protected DiscussionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DiscussionService();
    }

    #[Test]
    public function get_classes_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getClasses'));
        
        $reflection = new \ReflectionMethod($this->service, 'getClasses');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function mark_as_viewed_returns_bool()
    {
        $this->assertTrue(method_exists($this->service, 'markAsViewed'));
        
        $reflection = new \ReflectionMethod($this->service, 'markAsViewed');
        $this->assertEquals('bool', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_discussions_by_class_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getDiscussionsByClass'));
        
        $reflection = new \ReflectionMethod($this->service, 'getDiscussionsByClass');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function create_discussion_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'createDiscussion'));
        
        $reflection = new \ReflectionMethod($this->service, 'createDiscussion');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function create_discussion_accepts_correct_parameters()
    {
        $reflection = new \ReflectionMethod($this->service, 'createDiscussion');
        $params = $reflection->getParameters();
        
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertEquals('classId', $params[1]->getName());
        $this->assertEquals('title', $params[2]->getName());
        $this->assertEquals('content', $params[3]->getName());
    }

    #[Test]
    public function add_comment_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'addComment'));
        
        $reflection = new \ReflectionMethod($this->service, 'addComment');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function like_post_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'likePost'));
        
        $reflection = new \ReflectionMethod($this->service, 'likePost');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function delete_comment_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'deleteComment'));
        
        $reflection = new \ReflectionMethod($this->service, 'deleteComment');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function delete_comment_accepts_user_id_and_comment_id()
    {
        $reflection = new \ReflectionMethod($this->service, 'deleteComment');
        $params = $reflection->getParameters();
        
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('commentId', $params[1]->getName());
        $this->assertEquals('int', $params[1]->getType()->getName());
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'getClasses',
            'markAsViewed',
            'getDiscussionsByClass',
            'createDiscussion',
            'addComment',
            'likePost',
            'deleteComment',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->service, $method), "Method $method should exist");
        }
    }
}
