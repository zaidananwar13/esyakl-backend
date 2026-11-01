<?php

namespace Tests\Unit\Models;

use App\Models\Blog;
use Tests\TestCase;

class BlogModelTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $blog = new Blog();
        $this->assertInstanceOf(Blog::class, $blog);
    }

    /** @test */
    public function it_can_set_and_get_attributes()
    {
        $blog = new Blog();
        
        $blog->title = 'Test Blog';
        $blog->content = 'Test content';
        $blog->category = 'Technology';
        $blog->image = 'blog.jpg';

        $this->assertEquals('Test Blog', $blog->title);
        $this->assertEquals('Test content', $blog->content);
        $this->assertEquals('Technology', $blog->category);
        $this->assertEquals('blog.jpg', $blog->image);
    }

    /** @test */
    public function it_extends_model()
    {
        $blog = new Blog();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $blog);
    }
}
