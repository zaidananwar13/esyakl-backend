<?php

namespace Tests\Unit\Models;

use App\Models\SilabusChecker;
use Tests\TestCase;

class SilabusCheckerModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $silabusChecker = new SilabusChecker();
        $this->assertInstanceOf(SilabusChecker::class, $silabusChecker);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(SilabusChecker::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $silabusChecker = new SilabusChecker();
        $silabusChecker->id_user = 1;
        $silabusChecker->id_kategori_silabus = 5;
        $silabusChecker->id_sub_kategori_silabus = 10;

        $this->assertEquals(1, $silabusChecker->id_user);
        $this->assertEquals(5, $silabusChecker->id_kategori_silabus);
        $this->assertEquals(10, $silabusChecker->id_sub_kategori_silabus);
    }
}
