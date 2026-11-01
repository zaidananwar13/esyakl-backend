<?php

namespace Tests\Unit\Models;

use App\Models\LectureRating;
use Tests\TestCase;

class LectureRatingModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $lectureRating = new LectureRating();
        $this->assertInstanceOf(LectureRating::class, $lectureRating);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(LectureRating::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $lectureRating = new LectureRating();
        $lectureRating->id_lecture = 1;
        $lectureRating->rating = 5;

        $this->assertEquals(1, $lectureRating->id_lecture);
        $this->assertEquals(5, $lectureRating->rating);
    }
}
