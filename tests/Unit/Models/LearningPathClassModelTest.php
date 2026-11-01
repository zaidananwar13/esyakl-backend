<?php

namespace Tests\Unit\Models;

use App\Models\LearningPathClass;
use Tests\TestCase;

class LearningPathClassModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $learningPathClass = new LearningPathClass();
        $this->assertInstanceOf(LearningPathClass::class, $learningPathClass);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(LearningPathClass::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $learningPathClass = new LearningPathClass();
        $learningPathClass->id_learning_path = 1;
        $learningPathClass->id_class = 5;

        $this->assertEquals(1, $learningPathClass->id_learning_path);
        $this->assertEquals(5, $learningPathClass->id_class);
    }
}
