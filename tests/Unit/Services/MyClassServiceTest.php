<?php

namespace Tests\Unit\Services;

use App\Services\MyClassService;
use Mockery;
use Tests\TestCase;

class MyClassServiceTest extends TestCase
{
    protected MyClassService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MyClassService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_user_dashboard_returns_array()
    {
        $result = $this->service->getUserDashboard(
            1,
            'Test User',
            'test@example.com',
            now()->toDateTimeString()
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('class_on_progress', $result);
        $this->assertArrayHasKey('completed_classes', $result);
    }

    public function test_get_user_progress_returns_array()
    {
        $result = $this->service->getUserProgress(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('today_spent', $result);
        $this->assertArrayHasKey('certificates', $result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('total_courses', $result);
        $this->assertArrayHasKey('course', $result);
        $this->assertArrayHasKey('monthly_spent', $result);
    }

    public function test_process_progress_classes_returns_array_with_classes_and_projects()
    {
        $result = $this->service->processProgressClasses(collect([]), 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('classes', $result);
        $this->assertArrayHasKey('projects', $result);
    }

    public function test_process_completed_classes_returns_array()
    {
        $result = $this->service->processCompletedClasses(collect([]), 1);
        $this->assertIsArray($result);
    }

    public function test_process_expired_classes_returns_array()
    {
        $result = $this->service->processExpiredClasses(1);
        $this->assertIsArray($result);
    }

    public function test_get_project_status_returns_no_project_when_not_found()
    {
        $result = $this->service->getProjectStatus(99999, 99999);

        $this->assertIsArray($result);
        $this->assertEquals('No Project Yet', $result['project_class']);
        $this->assertNull($result['project']);
    }

    public function test_extract_projects_returns_message_when_empty()
    {
        $result = $this->service->extractProjects([]);
        $this->assertEquals("You haven't taken a project yet", $result);
    }

    public function test_extract_projects_returns_array_when_has_projects()
    {
        $projects = [['id' => 1, 'title' => 'Project 1']];
        $result = $this->service->extractProjects($projects);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function test_calculate_today_spent_returns_string()
    {
        $result = $this->service->calculateTodaySpent(1);
        $this->assertIsString($result);
    }

    public function test_calculate_monthly_spent_returns_array()
    {
        $result = $this->service->calculateMonthlySpent(1);
        $this->assertIsArray($result);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new MyClassService();
        $this->assertInstanceOf(MyClassService::class, $service);
    }

    public function test_get_user_dashboard_returns_joined_string()
    {
        $result = $this->service->getUserDashboard(
            1,
            'Test User',
            'test@example.com',
            '2024-01-01 00:00:00'
        );

        $this->assertStringContainsString('Joined since', $result['joined']);
    }

    public function test_get_user_progress_returns_course_structure()
    {
        $result = $this->service->getUserProgress(1);

        $this->assertArrayHasKey('in_progress', $result['course']);
        $this->assertArrayHasKey('completed', $result['course']);
        $this->assertArrayHasKey('yet_to_start', $result['course']);
    }
}
