<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $project = new Project();
        $this->assertInstanceOf(Project::class, $project);
    }

    public function test_model_has_correct_table_name()
    {
        $project = new Project();
        $this->assertEquals('project', $project->getTable());
    }

    public function test_model_has_correct_primary_key()
    {
        $project = new Project();
        $this->assertEquals('id_project', $project->getKeyName());
    }

    public function test_model_has_fillable_attributes()
    {
        $project = new Project();
        $fillable = $project->getFillable();

        $this->assertContains('id_class', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('desc', $fillable);
        $this->assertContains('created_at', $fillable);
        $this->assertContains('updated_at', $fillable);
    }

    public function test_model_has_unix_date_format()
    {
        $project = new Project();
        $this->assertEquals('U', $project->getDateFormat());
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Project::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $project = new Project();
        $project->id_class = 1;
        $project->title = 'Test Project';
        $project->desc = 'Test Description';

        $this->assertEquals(1, $project->id_class);
        $this->assertEquals('Test Project', $project->title);
        $this->assertEquals('Test Description', $project->desc);
    }

    public function test_model_mass_assignment()
    {
        $project = new Project([
            'id_class' => 1,
            'title' => 'Mass assigned Project',
            'desc' => 'Mass assigned description'
        ]);

        $this->assertEquals(1, $project->id_class);
        $this->assertEquals('Mass assigned Project', $project->title);
    }
}
