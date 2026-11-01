<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use Tests\TestCase;

class CountryModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $country = new Country();
        $this->assertInstanceOf(Country::class, $country);
    }

    public function test_model_has_correct_table_name()
    {
        $country = new Country();
        $this->assertEquals('country', $country->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $country = new Country();
        $this->assertEquals('id_country', $country->getKeyName());
    }

    public function test_model_has_unix_date_format()
    {
        $country = new Country();
        $this->assertEquals('U', $country->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Country::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $country = new Country();
        $country->name = 'Indonesia';
        $country->code = 'ID';

        $this->assertEquals('Indonesia', $country->name);
        $this->assertEquals('ID', $country->code);
    }
}
