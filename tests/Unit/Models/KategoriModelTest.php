<?php

namespace Tests\Unit\Models;

use App\Models\Kategori;
use Tests\TestCase;

class KategoriModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $kategori = new Kategori();
        $this->assertInstanceOf(Kategori::class, $kategori);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Kategori::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $kategori = new Kategori();
        $kategori->level = 'Beginner';

        $this->assertEquals('Beginner', $kategori->level);
    }
}
