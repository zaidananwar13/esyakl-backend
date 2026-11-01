<?php

namespace Tests\Unit\Models;

use App\Models\LikePost;
use Tests\TestCase;

class LikePostModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $likePost = new LikePost();
        $this->assertInstanceOf(LikePost::class, $likePost);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(LikePost::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $likePost = new LikePost();
        $likePost->id_user = 1;
        $likePost->id_post = 5;

        $this->assertEquals(1, $likePost->id_user);
        $this->assertEquals(5, $likePost->id_post);
    }
}
