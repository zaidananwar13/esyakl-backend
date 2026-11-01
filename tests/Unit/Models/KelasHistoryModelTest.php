<?php

namespace Tests\Unit\Models;

use App\Models\KelasHistory;
use Tests\TestCase;

class KelasHistoryModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $kelasHistory = new KelasHistory();
        $this->assertInstanceOf(KelasHistory::class, $kelasHistory);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(KelasHistory::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $kelasHistory = new KelasHistory();
        $kelasHistory->id_user = 1;
        $kelasHistory->id_kategori_silabus = 5;

        $this->assertEquals(1, $kelasHistory->id_user);
        $this->assertEquals(5, $kelasHistory->id_kategori_silabus);
    }
}
