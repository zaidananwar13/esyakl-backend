<?php

namespace Tests\Unit\Models;

use App\Models\FEAuthorizer;
use Tests\TestCase;

class FEAuthorizerModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $feAuthorizer = new FEAuthorizer();
        $this->assertInstanceOf(FEAuthorizer::class, $feAuthorizer);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(FEAuthorizer::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $feAuthorizer = new FEAuthorizer();
        $feAuthorizer->id_user = 1;
        $feAuthorizer->id_kelas = 5;
        $feAuthorizer->unlocked = 3;

        $this->assertEquals(1, $feAuthorizer->id_user);
        $this->assertEquals(5, $feAuthorizer->id_kelas);
        $this->assertEquals(3, $feAuthorizer->unlocked);
    }
}
