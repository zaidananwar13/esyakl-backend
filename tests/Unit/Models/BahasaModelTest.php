<?php

namespace Tests\Unit\Models;

use App\Models\Bahasa;
use Tests\TestCase;

class BahasaModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $bahasa = new Bahasa();
        $this->assertInstanceOf(Bahasa::class, $bahasa);
    }

    public function test_model_has_correct_table_name()
    {
        $bahasa = new Bahasa();
        $this->assertEquals('bahasa', $bahasa->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $bahasa = new Bahasa();
        $this->assertEquals('id_bahasa', $bahasa->getKeyName());
    }

    public function test_model_has_fillable_attributes()
    {
        $bahasa = new Bahasa();
        $fillable = $bahasa->getFillable();

        $this->assertContains('bahasa', $fillable);
        $this->assertContains('created_at', $fillable);
        $this->assertContains('updated_at', $fillable);
    }

    public function test_model_has_unix_date_format()
    {
        $bahasa = new Bahasa();
        $this->assertEquals('U', $bahasa->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Bahasa::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $bahasa = new Bahasa();
        $bahasa->bahasa = 'English';

        $this->assertEquals('English', $bahasa->bahasa);
    }

    public function test_model_mass_assignment()
    {
        $bahasa = new Bahasa(['bahasa' => 'Indonesian']);
        $this->assertEquals('Indonesian', $bahasa->bahasa);
    }
}
