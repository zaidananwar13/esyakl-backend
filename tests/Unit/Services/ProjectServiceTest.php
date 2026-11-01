<?php

namespace Tests\Unit\Services;

use App\Services\ProjectService;
use Mockery;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    protected ProjectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_projects_returns_success_response()
    {
        $result = $this->service->getAllProjects();

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Project found.', $result['message']);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_get_project_by_class_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->getProjectByClass('invalid-token', 1);

        // Returns error when project not found or user unauthorized
        $this->assertFalse($result['success']);
        $this->assertContains($result['code'], [401, 404]);
    }

    public function test_get_project_by_class_returns_not_found_when_project_missing()
    {
        $result = $this->service->getProjectByClass('test-token', 99999);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('Project not found.', $result['message']);
    }

    public function test_submit_project_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->submitProject('invalid-token', 1, 'test note', null);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new ProjectService();
        $this->assertInstanceOf(ProjectService::class, $service);
    }

    public function test_get_all_projects_returns_array()
    {
        $result = $this->service->getAllProjects();
        $this->assertIsArray($result);
    }

    public function test_get_project_by_class_returns_array()
    {
        $result = $this->service->getProjectByClass('token', 1);
        $this->assertIsArray($result);
    }

    public function test_submit_project_returns_array()
    {
        $result = $this->service->submitProject('token', 1, 'note', null);
        $this->assertIsArray($result);
    }
}
