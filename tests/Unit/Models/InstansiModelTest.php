<?php

namespace Tests\Unit\Models;

use App\Models\Instansi;
use Tests\TestCase;

class InstansiModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $instansi = new Instansi();
        $this->assertInstanceOf(Instansi::class, $instansi);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Instansi::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $instansi = new Instansi();
        $instansi->name = 'Test Institution';

        $this->assertEquals('Test Institution', $instansi->name);
    }
}
