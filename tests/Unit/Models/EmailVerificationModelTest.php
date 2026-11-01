<?php

namespace Tests\Unit\Models;

use App\Models\EmailVerification;
use Tests\TestCase;

class EmailVerificationModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $emailVerification = new EmailVerification();
        $this->assertInstanceOf(EmailVerification::class, $emailVerification);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(EmailVerification::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $emailVerification = new EmailVerification();
        $emailVerification->email = 'test@example.com';
        $emailVerification->token = 'verification-token';

        $this->assertEquals('test@example.com', $emailVerification->email);
        $this->assertEquals('verification-token', $emailVerification->token);
    }
}
