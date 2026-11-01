<?php

namespace Tests\Unit\Models;

use App\Models\ProjectResult;
use Tests\TestCase;

class ProjectResultModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $projectResult = new ProjectResult();
        $this->assertInstanceOf(ProjectResult::class, $projectResult);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ProjectResult::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $projectResult = new ProjectResult();
        $projectResult->id_project_student = 1;
        $projectResult->file = 'submission.zip';
        $projectResult->status = 2;

        $this->assertEquals(1, $projectResult->id_project_student);
        $this->assertEquals('submission.zip', $projectResult->file);
        $this->assertEquals(2, $projectResult->status);
    }
}
