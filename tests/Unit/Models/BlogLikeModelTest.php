<?php

namespace Tests\Unit\Models;

use App\Models\BlogLike;
use Tests\TestCase;

class BlogLikeModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $blogLike = new BlogLike();
        $this->assertInstanceOf(BlogLike::class, $blogLike);
    }

    public function test_model_has_correct_table_name()
    {
        $blogLike = new BlogLike();
        $this->assertEquals('blog_like', $blogLike->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $blogLike = new BlogLike();
        $this->assertEquals('id_blog_like', $blogLike->getKeyName());
    }

    public function test_model_has_unix_date_format()
    {
        $blogLike = new BlogLike();
        $this->assertEquals('U', $blogLike->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(BlogLike::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $blogLike = new BlogLike();
        $blogLike->id_blog = 1;
        $blogLike->id_user = 5;

        $this->assertEquals(1, $blogLike->id_blog);
        $this->assertEquals(5, $blogLike->id_user);
    }
}
