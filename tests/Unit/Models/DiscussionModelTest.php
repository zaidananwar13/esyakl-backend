<?php

namespace Tests\Unit\Models;

use App\Models\ClassDiscussion;
use Tests\TestCase;

class DiscussionModelTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $discussion = new ClassDiscussion();
        $this->assertInstanceOf(ClassDiscussion::class, $discussion);
    }

    /** @test */
    public function it_can_set_and_get_attributes()
    {
        $discussion = new ClassDiscussion();
        
        $discussion->owner = 1;
        $discussion->id_class = 2;
        $discussion->title = 'Test Discussion';
        $discussion->content = 'Test content';

        $this->assertEquals(1, $discussion->owner);
        $this->assertEquals(2, $discussion->id_class);
        $this->assertEquals('Test Discussion', $discussion->title);
        $this->assertEquals('Test content', $discussion->content);
    }

    /** @test */
    public function it_extends_model()
    {
        $discussion = new ClassDiscussion();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $discussion);
    }
}
