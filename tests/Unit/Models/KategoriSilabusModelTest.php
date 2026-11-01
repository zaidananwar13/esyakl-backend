<?php

namespace Tests\Unit\Models;

use App\Models\Kategori_Silabus;
use Tests\TestCase;

class KategoriSilabusModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $kategoriSilabus = new Kategori_Silabus();
        $this->assertInstanceOf(Kategori_Silabus::class, $kategoriSilabus);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Kategori_Silabus::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $kategoriSilabus = new Kategori_Silabus();
        $kategoriSilabus->judul = 'Test Category';
        $kategoriSilabus->id_kelas = 1;

        $this->assertEquals('Test Category', $kategoriSilabus->judul);
        $this->assertEquals(1, $kategoriSilabus->id_kelas);
    }
}
