<?php

namespace Tests\Unit\Models;

use App\Models\StudentLogActivity;
use Tests\TestCase;

class StudentLogActivityModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $studentLogActivity = new StudentLogActivity();
        $this->assertInstanceOf(StudentLogActivity::class, $studentLogActivity);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(StudentLogActivity::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $studentLogActivity = new StudentLogActivity();
        $studentLogActivity->id_user = 1;
        $studentLogActivity->activity = 'Login';

        $this->assertEquals(1, $studentLogActivity->id_user);
        $this->assertEquals('Login', $studentLogActivity->activity);
    }
}
