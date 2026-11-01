<?php

namespace Tests\Unit\Services;

use App\Models\Kelas;
use App\Models\LearningPath;
use App\Models\LearningPathClass;
use App\Services\LearningPathService;
use Mockery;
use Tests\TestCase;

class LearningPathServiceTest extends TestCase
{
    protected LearningPathService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LearningPathService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_paths_returns_array()
    {
        $result = $this->service->getAllPaths('English');
        $this->assertIsArray($result);
    }

    public function test_get_all_paths_with_indonesian_lang()
    {
        $result = $this->service->getAllPaths('Indonesian');
        $this->assertIsArray($result);
    }

    public function test_get_path_by_id_returns_null_when_not_found()
    {
        $result = $this->service->getPathById(99999);
        $this->assertNull($result);
    }

    public function test_get_path_by_id_with_user_id()
    {
        $result = $this->service->getPathById(1, 1);
        // Returns null if path doesn't exist, array if it does
        $this->assertTrue($result === null || is_array($result));
    }

    public function test_get_path_by_id_without_user_id()
    {
        $result = $this->service->getPathById(1);
        $this->assertTrue($result === null || is_array($result));
    }

    public function test_get_all_paths_default_english()
    {
        $result = $this->service->getAllPaths();
        $this->assertIsArray($result);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new LearningPathService();
        $this->assertInstanceOf(LearningPathService::class, $service);
    }
}
