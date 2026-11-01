<?php

namespace Tests\Unit\Repositories;

use App\Models\Kelas;
use App\Repositories\Eloquent\ClassRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ClassRepositoryTest extends TestCase
{
    protected ClassRepository $repository;
    protected $mockModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockModel = Mockery::mock(Kelas::class);
        $this->repository = new ClassRepository($this->mockModel);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function find_by_id_returns_class_when_found()
    {
        $expectedClass = new Kelas();
        $expectedClass->id_class = 1;
        $expectedClass->title = 'Test Class';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn($expectedClass);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('id_class', 1)
            ->andReturn($builderMock);

        $result = $this->repository->findById(1);

        $this->assertInstanceOf(Kelas::class, $result);
        $this->assertEquals(1, $result->id_class);
    }

    /** @test */
    public function find_by_id_returns_null_when_not_found()
    {
        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn(null);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('id_class', 999)
            ->andReturn($builderMock);

        $result = $this->repository->findById(999);

        $this->assertNull($result);
    }

    /** @test */
    public function find_all_returns_collection()
    {
        $expectedCollection = new Collection([
            (object)['id_class' => 1, 'title' => 'Class 1'],
            (object)['id_class' => 2, 'title' => 'Class 2'],
        ]);

        $this->mockModel
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->repository->findAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    /** @test */
    public function get_student_count_returns_integer()
    {
        $this->assertTrue(method_exists($this->repository, 'getStudentCount'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getStudentCount');
        $this->assertEquals('int', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_reviews_returns_collection()
    {
        $this->assertTrue(method_exists($this->repository, 'getReviews'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getReviews');
        $this->assertEquals('Illuminate\Support\Collection', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function get_syllabus_returns_collection()
    {
        $this->assertTrue(method_exists($this->repository, 'getSyllabus'));
        
        $reflection = new \ReflectionMethod($this->repository, 'getSyllabus');
        $this->assertEquals('Illuminate\Support\Collection', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function calculate_rating_returns_float()
    {
        $this->assertTrue(method_exists($this->repository, 'calculateRating'));
        
        $reflection = new \ReflectionMethod($this->repository, 'calculateRating');
        $this->assertEquals('float', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function is_user_enrolled_returns_bool()
    {
        $this->assertTrue(method_exists($this->repository, 'isUserEnrolled'));
        
        $reflection = new \ReflectionMethod($this->repository, 'isUserEnrolled');
        $this->assertEquals('bool', $reflection->getReturnType()->getName());
    }

    /** @test */
    public function find_with_columns_method_exists()
    {
        $this->assertTrue(method_exists($this->repository, 'findWithColumns'));
    }

    /** @test */
    public function get_learning_path_method_exists()
    {
        $this->assertTrue(method_exists($this->repository, 'getLearningPath'));
    }

    /** @test */
    public function repository_implements_interface()
    {
        $this->assertInstanceOf(
            \App\Repositories\Contracts\ClassRepositoryInterface::class,
            $this->repository
        );
    }

    /** @test */
    public function all_public_methods_exist()
    {
        $methods = [
            'findById',
            'findAll',
            'getStudentCount',
            'getReviews',
            'getSyllabus',
            'calculateRating',
            'isUserEnrolled',
            'findWithColumns',
            'getLearningPath',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->repository, $method), "Method $method should exist");
        }
    }
}
