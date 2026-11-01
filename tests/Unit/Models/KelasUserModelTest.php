<?php

namespace Tests\Unit\Models;

use App\Models\Kelas_User;
use Tests\TestCase;

class KelasUserModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $kelasUser = new Kelas_User();
        $this->assertInstanceOf(Kelas_User::class, $kelasUser);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Kelas_User::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $kelasUser = new Kelas_User();
        $kelasUser->id_user = 1;
        $kelasUser->id_kelas = 5;

        $this->assertEquals(1, $kelasUser->id_user);
        $this->assertEquals(5, $kelasUser->id_kelas);
    }
}
