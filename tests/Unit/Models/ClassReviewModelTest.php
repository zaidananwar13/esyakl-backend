<?php

namespace Tests\Unit\Models;

use App\Models\ClassReview;
use Tests\TestCase;

class ClassReviewModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $classReview = new ClassReview();
        $this->assertInstanceOf(ClassReview::class, $classReview);
    }

    public function test_model_has_correct_table_name()
    {
        $classReview = new ClassReview();
        $this->assertEquals('class_review', $classReview->getTable());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ClassReview::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $classReview = new ClassReview();
        $classReview->id_class_student = 1;
        $classReview->star = 5;
        $classReview->content = 'Great class!';

        $this->assertEquals(1, $classReview->id_class_student);
        $this->assertEquals(5, $classReview->star);
        $this->assertEquals('Great class!', $classReview->content);
    }
}
