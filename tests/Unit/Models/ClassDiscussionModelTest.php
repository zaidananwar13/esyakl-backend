<?php

namespace Tests\Unit\Models;

use App\Models\ClassDiscussion;
use Tests\TestCase;

class ClassDiscussionModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $classDiscussion = new ClassDiscussion();
        $this->assertInstanceOf(ClassDiscussion::class, $classDiscussion);
    }

    public function test_model_has_correct_table_name()
    {
        $classDiscussion = new ClassDiscussion();
        $this->assertEquals('class_discussion', $classDiscussion->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $classDiscussion = new ClassDiscussion();
        $this->assertEquals('id_class_discussion', $classDiscussion->getKeyName());
    }

    public function test_model_has_fillable_attributes()
    {
        $classDiscussion = new ClassDiscussion();
        $fillable = $classDiscussion->getFillable();

        $this->assertContains('owner', $fillable);
        $this->assertContains('id_class', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('content', $fillable);
    }

    public function test_model_has_unix_date_format()
    {
        $classDiscussion = new ClassDiscussion();
        $this->assertEquals('U', $classDiscussion->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ClassDiscussion::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $classDiscussion = new ClassDiscussion();
        $classDiscussion->owner = 1;
        $classDiscussion->title = 'Test Discussion';
        $classDiscussion->content = 'Content here';

        $this->assertEquals(1, $classDiscussion->owner);
        $this->assertEquals('Test Discussion', $classDiscussion->title);
        $this->assertEquals('Content here', $classDiscussion->content);
    }

    public function test_model_mass_assignment()
    {
        $classDiscussion = new ClassDiscussion([
            'owner' => 1,
            'id_class' => 5,
            'title' => 'Mass Assigned',
            'content' => 'Content'
        ]);

        $this->assertEquals('Mass Assigned', $classDiscussion->title);
    }
}
