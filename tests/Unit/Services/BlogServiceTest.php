<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Services\BlogService;
use Tests\TestCase;

class BlogServiceTest extends TestCase
{
    protected BlogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BlogService();
    }

    #[Test]
    public function get_all_blogs_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getAllBlogs'));
        
        $reflection = new \ReflectionMethod($this->service, 'getAllBlogs');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function get_all_blogs_accepts_nullable_user_id()
    {
        $reflection = new \ReflectionMethod($this->service, 'getAllBlogs');
        $params = $reflection->getParameters();
        
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertTrue($params[0]->allowsNull());
    }

    #[Test]
    public function get_blog_by_id_returns_nullable_array()
    {
        $this->assertTrue(method_exists($this->service, 'getBlogById'));
        
        $reflection = new \ReflectionMethod($this->service, 'getBlogById');
        $this->assertTrue($reflection->getReturnType()->allowsNull());
    }

    #[Test]
    public function get_blog_by_id_accepts_blog_id_and_optional_user_id()
    {
        $reflection = new \ReflectionMethod($this->service, 'getBlogById');
        $params = $reflection->getParameters();
        
        $this->assertEquals('blogId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('userId', $params[1]->getName());
        $this->assertTrue($params[1]->allowsNull());
    }

    #[Test]
    public function like_blog_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'likeBlog'));
        
        $reflection = new \ReflectionMethod($this->service, 'likeBlog');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function like_blog_accepts_blog_id_and_user_id()
    {
        $reflection = new \ReflectionMethod($this->service, 'likeBlog');
        $params = $reflection->getParameters();
        
        $this->assertEquals('blogId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
        $this->assertEquals('userId', $params[1]->getName());
        $this->assertEquals('int', $params[1]->getType()->getName());
    }

    #[Test]
    public function get_categories_returns_array()
    {
        $this->assertTrue(method_exists($this->service, 'getCategories'));
        
        $reflection = new \ReflectionMethod($this->service, 'getCategories');
        $this->assertEquals('array', $reflection->getReturnType()->getName());
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'getAllBlogs',
            'getBlogById',
            'likeBlog',
            'getCategories',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->service, $method), "Method $method should exist");
        }
    }
}
