<?php

namespace Tests\Unit\Models;

use App\Models\Sub_Kategori_Silabus;
use Tests\TestCase;

class SubKategoriSilabusModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $subKategoriSilabus = new Sub_Kategori_Silabus();
        $this->assertInstanceOf(Sub_Kategori_Silabus::class, $subKategoriSilabus);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Sub_Kategori_Silabus::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $subKategoriSilabus = new Sub_Kategori_Silabus();
        $subKategoriSilabus->id_kategori_silabus = 1;
        $subKategoriSilabus->judul = 'Test Sub Category';
        $subKategoriSilabus->konten = 'Content here';

        $this->assertEquals(1, $subKategoriSilabus->id_kategori_silabus);
        $this->assertEquals('Test Sub Category', $subKategoriSilabus->judul);
        $this->assertEquals('Content here', $subKategoriSilabus->konten);
    }
}
