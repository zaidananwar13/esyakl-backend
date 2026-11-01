<?php

namespace Tests\Unit\Models;

use App\Models\LearningPath;
use Tests\TestCase;

class LearningPathModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $learningPath = new LearningPath();
        $this->assertInstanceOf(LearningPath::class, $learningPath);
    }

    public function test_model_has_correct_table_name()
    {
        $learningPath = new LearningPath();
        $this->assertEquals('learning_path', $learningPath->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $learningPath = new LearningPath();
        $this->assertEquals('id_learning_path', $learningPath->getKeyName());
    }

    public function test_model_has_unix_date_format()
    {
        $learningPath = new LearningPath();
        $this->assertEquals('U', $learningPath->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(LearningPath::class, 'factory'));
    }

    public function test_new_query_method_exists()
    {
        $learningPath = new LearningPath();
        $this->assertTrue(method_exists($learningPath, 'newQuery'));
    }

    public function test_model_has_timestamps_enabled()
    {
        $learningPath = new LearningPath();
        $this->assertTrue($learningPath->usesTimestamps());
    }

    public function test_can_set_attributes()
    {
        $learningPath = new LearningPath();
        $learningPath->name = 'Test Learning Path';
        $learningPath->desc = 'Test Description';

        $this->assertEquals('Test Learning Path', $learningPath->name);
        $this->assertEquals('Test Description', $learningPath->desc);
    }
}
