<?php

namespace Tests\Unit\Services;

use App\Models\ClassStudent;
use App\Models\Kelas;
use App\Models\User;
use App\Services\ClassroomService;
use Mockery;
use Tests\TestCase;

class ClassroomServiceTest extends TestCase
{
    protected ClassroomService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClassroomService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_class_teachers_returns_array()
    {
        $result = $this->service->getClassTeachers(1);
        $this->assertIsArray($result);
    }

    public function test_get_class_teachers_for_nonexistent_class()
    {
        $result = $this->service->getClassTeachers(99999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_certificate_status_returns_on_progress_when_not_found()
    {
        $result = $this->service->getCertificateStatus(99999);

        $this->assertIsArray($result);
        $this->assertEquals('on-progress', $result['status']);
        $this->assertEquals("You haven't finish this class yet", $result['message']);
    }

    public function test_get_class_progress_returns_array()
    {
        $mockClassStudent = new ClassStudent();
        $mockClassStudent->id_class = 1;
        $mockClassStudent->id_class_student = 1;

        $result = $this->service->getClassProgress($mockClassStudent);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('progress', $result);
        $this->assertArrayHasKey('progress_temp', $result);
        $this->assertArrayHasKey('last_material', $result);
        $this->assertArrayHasKey('status', $result);
    }

    public function test_get_materials_with_quizzes_returns_array()
    {
        $mockClassStudent = new ClassStudent();
        $mockClassStudent->id_class = 1;
        $mockClassStudent->id_class_student = 1;

        $result = $this->service->getMaterialsWithQuizzes($mockClassStudent, 80);
        $this->assertIsArray($result);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new ClassroomService();
        $this->assertInstanceOf(ClassroomService::class, $service);
    }

    public function test_get_quiz_histories_returns_collection()
    {
        $mockClassStudent = new ClassStudent();
        $mockClassStudent->id_class_student = 1;

        $result = $this->service->getQuizHistories($mockClassStudent, 1, 80);
        
        // Returns a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_get_project_data_returns_no_project_when_not_found()
    {
        $mockClass = new Kelas();
        $mockClass->id_class = 99999;
        $mockClass->duration = 60;

        $mockClassStudent = new ClassStudent();
        $mockClassStudent->id_class_student = 99999;
        $mockClassStudent->created_at = now();
        $mockClassStudent->updated_at = now();

        $result = $this->service->getProjectData($mockClass, $mockClassStudent, 'test-token');
        
        $this->assertIsArray($result);
        $this->assertEquals('No Project Yet.', $result['data']);
    }
}
