<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $comment = new Comment();
        $this->assertInstanceOf(Comment::class, $comment);
    }

    public function test_model_has_correct_table_name()
    {
        $comment = new Comment();
        $this->assertEquals('comment', $comment->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $comment = new Comment();
        $this->assertEquals('id_comment', $comment->getKeyName());
    }

    public function test_model_has_fillable_attributes()
    {
        $comment = new Comment();
        $fillable = $comment->getFillable();

        $this->assertContains('owner', $fillable);
        $this->assertContains('commented', $fillable);
        $this->assertContains('id_commented', $fillable);
        $this->assertContains('content', $fillable);
        $this->assertContains('time', $fillable);
    }

    public function test_model_has_timestamps_disabled()
    {
        $comment = new Comment();
        $this->assertFalse($comment->usesTimestamps());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Comment::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $comment = new Comment();
        $comment->owner = 1;
        $comment->commented = 'discussion';
        $comment->id_commented = 10;
        $comment->content = 'Test comment content';
        $comment->time = time();

        $this->assertEquals(1, $comment->owner);
        $this->assertEquals('discussion', $comment->commented);
        $this->assertEquals(10, $comment->id_commented);
        $this->assertEquals('Test comment content', $comment->content);
    }

    public function test_model_mass_assignment()
    {
        $comment = new Comment([
            'owner' => 1,
            'commented' => 'post',
            'id_commented' => 5,
            'content' => 'Mass assigned comment',
            'time' => time()
        ]);

        $this->assertEquals(1, $comment->owner);
        $this->assertEquals('post', $comment->commented);
    }
}
