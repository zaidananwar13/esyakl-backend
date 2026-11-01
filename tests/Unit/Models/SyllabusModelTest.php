<?php

namespace Tests\Unit\Models;

use App\Models\Syllabus;
use Tests\TestCase;

class SyllabusModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $syllabus = new Syllabus();
        $this->assertInstanceOf(Syllabus::class, $syllabus);
    }

    public function test_model_has_correct_table_name()
    {
        $syllabus = new Syllabus();
        $this->assertEquals('syllabus', $syllabus->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $syllabus = new Syllabus();
        $this->assertEquals('id_syllabus', $syllabus->getKeyName());
    }

    public function test_model_has_unix_date_format()
    {
        $syllabus = new Syllabus();
        $this->assertEquals('U', $syllabus->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Syllabus::class, 'factory'));
    }

    public function test_model_has_timestamps_enabled()
    {
        $syllabus = new Syllabus();
        $this->assertTrue($syllabus->usesTimestamps());
    }

    public function test_can_set_attributes()
    {
        $syllabus = new Syllabus();
        $syllabus->title = 'Test Syllabus';
        $syllabus->content = 'Test Content';

        $this->assertEquals('Test Syllabus', $syllabus->title);
        $this->assertEquals('Test Content', $syllabus->content);
    }
}
