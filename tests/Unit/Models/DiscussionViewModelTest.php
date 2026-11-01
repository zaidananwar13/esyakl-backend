<?php

namespace Tests\Unit\Models;

use App\Models\DiscussionView;
use Tests\TestCase;

class DiscussionViewModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $discussionView = new DiscussionView();
        $this->assertInstanceOf(DiscussionView::class, $discussionView);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(DiscussionView::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $discussionView = new DiscussionView();
        $discussionView->id_discussion = 1;
        $discussionView->id_user = 5;

        $this->assertEquals(1, $discussionView->id_discussion);
        $this->assertEquals(5, $discussionView->id_user);
    }
}
