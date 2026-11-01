<?php

namespace Tests\Unit\Models;

use App\Models\ForgotPassword;
use Tests\TestCase;

class ForgotPasswordModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $forgotPassword = new ForgotPassword();
        $this->assertInstanceOf(ForgotPassword::class, $forgotPassword);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ForgotPassword::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $forgotPassword = new ForgotPassword();
        $forgotPassword->email = 'test@example.com';
        $forgotPassword->token = 'reset-token';

        $this->assertEquals('test@example.com', $forgotPassword->email);
        $this->assertEquals('reset-token', $forgotPassword->token);
    }
}
