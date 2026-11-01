<?php

namespace Tests\Unit\Models;

use App\Models\Overview;
use Tests\TestCase;

class OverviewModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $overview = new Overview();
        $this->assertInstanceOf(Overview::class, $overview);
    }

    public function test_model_has_correct_table_name()
    {
        $overview = new Overview();
        $this->assertEquals('overview', $overview->getTable());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Overview::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $overview = new Overview();
        $overview->title = 'Test Title';
        $overview->desc = 'Test Description';

        $this->assertEquals('Test Title', $overview->title);
        $this->assertEquals('Test Description', $overview->desc);
    }
}
