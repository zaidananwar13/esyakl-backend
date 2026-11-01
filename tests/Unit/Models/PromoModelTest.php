<?php

namespace Tests\Unit\Models;

use App\Models\Promo;
use Tests\TestCase;

class PromoModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $promo = new Promo();
        $this->assertInstanceOf(Promo::class, $promo);
    }

    public function test_model_has_correct_table_name()
    {
        $promo = new Promo();
        $this->assertEquals('promo', $promo->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $promo = new Promo();
        $this->assertEquals('id_promo', $promo->getKeyName());
    }

    public function test_model_has_fillable_attributes()
    {
        $promo = new Promo();
        $fillable = $promo->getFillable();

        $this->assertContains('code', $fillable);
        $this->assertContains('percentage', $fillable);
        $this->assertContains('value', $fillable);
        $this->assertContains('expired_at', $fillable);
        $this->assertContains('created_at', $fillable);
        $this->assertContains('updated_at', $fillable);
    }

    public function test_model_has_unix_date_format()
    {
        $promo = new Promo();
        $this->assertEquals('U', $promo->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Promo::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $promo = new Promo();
        $promo->code = 'TEST50';
        $promo->percentage = true;
        $promo->value = 50;

        $this->assertEquals('TEST50', $promo->code);
        $this->assertTrue($promo->percentage);
        $this->assertEquals(50, $promo->value);
    }

    public function test_model_mass_assignment()
    {
        $promo = new Promo([
            'code' => 'DISCOUNT20',
            'percentage' => true,
            'value' => 20,
            'expired_at' => time() + 86400
        ]);

        $this->assertEquals('DISCOUNT20', $promo->code);
        $this->assertEquals(20, $promo->value);
    }
}
