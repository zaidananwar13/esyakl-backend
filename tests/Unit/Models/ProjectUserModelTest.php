<?php

namespace Tests\Unit\Models;

use App\Models\ProjectUser;
use Tests\TestCase;

class ProjectUserModelTest extends TestCase
{
    public function test_model_can_be_instantiated()
    {
        $projectUser = new ProjectUser();
        $this->assertInstanceOf(ProjectUser::class, $projectUser);
    }

    public function test_model_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(ProjectUser::class, 'factory'));
    }

    public function test_can_set_attributes()
    {
        $projectUser = new ProjectUser();
        $projectUser->id_user = 1;
        $projectUser->id_project = 5;
        $projectUser->poin = 85;

        $this->assertEquals(1, $projectUser->id_user);
        $this->assertEquals(5, $projectUser->id_project);
        $this->assertEquals(85, $projectUser->poin);
    }
}
