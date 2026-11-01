<?php

namespace Tests\Unit\Models;

use App\Models\UserLog;
use Tests\TestCase;

class UserLogModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $userLog = new UserLog();
        $this->assertInstanceOf(UserLog::class, $userLog);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(UserLog::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $userLog = new UserLog();
        $userLog->id_user = 1;
        $userLog->status = true;
        $userLog->time = now()->toDateTimeString();

        $this->assertEquals(1, $userLog->id_user);
        $this->assertTrue($userLog->status);
    }
}
