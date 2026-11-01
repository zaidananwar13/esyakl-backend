<?php

namespace Tests\Unit\Models;

use App\Models\Migrasi;
use Tests\TestCase;

class MigrasiModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $migrasi = new Migrasi();
        $this->assertInstanceOf(Migrasi::class, $migrasi);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Migrasi::class, 'factory'));
    }
}
