<?php

namespace Tests\Unit\Models;

use App\Models\UserPurchase;
use Tests\TestCase;

class UserPurchaseModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $userPurchase = new UserPurchase();
        $this->assertInstanceOf(UserPurchase::class, $userPurchase);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(UserPurchase::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $userPurchase = new UserPurchase();
        $userPurchase->id_user = 1;
        $userPurchase->id_class = 5;
        $userPurchase->value = 100000;

        $this->assertEquals(1, $userPurchase->id_user);
        $this->assertEquals(5, $userPurchase->id_class);
        $this->assertEquals(100000, $userPurchase->value);
    }
}
