<?php

namespace Tests\Unit\Repositories;

use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    protected UserRepository $repository;
    protected $mockModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockModel = Mockery::mock(User::class);
        $this->repository = new UserRepository($this->mockModel);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_find_user_by_id()
    {
        $userId = 1;
        $expectedUser = new User();
        $expectedUser->id_user = $userId;
        $expectedUser->name = 'Test User';
        $expectedUser->email = 'test@example.com';

        $this->mockModel
            ->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn($expectedUser);

        $result = $this->repository->findById($userId);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userId, $result->id_user);
        $this->assertEquals('Test User', $result->name);
    }

    #[Test]
    public function it_returns_null_when_user_not_found_by_id()
    {
        $userId = 999;

        $this->mockModel
            ->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(null);

        $result = $this->repository->findById($userId);

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_find_user_by_token()
    {
        $token = 'valid_api_token_123';
        $expectedUser = new User();
        $expectedUser->api_token = $token;
        $expectedUser->name = 'Token User';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn($expectedUser);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('api_token', $token)
            ->andReturn($builderMock);

        $result = $this->repository->findByToken($token);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($token, $result->api_token);
    }

    #[Test]
    public function it_returns_null_when_user_not_found_by_token()
    {
        $token = 'invalid_token';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn(null);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('api_token', $token)
            ->andReturn($builderMock);

        $result = $this->repository->findByToken($token);

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_find_user_by_email()
    {
        $email = 'user@example.com';
        $expectedUser = new User();
        $expectedUser->email = $email;
        $expectedUser->name = 'Email User';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn($expectedUser);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('email', $email)
            ->andReturn($builderMock);

        $result = $this->repository->findByEmail($email);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->email);
    }

    #[Test]
    public function it_returns_null_when_user_not_found_by_email()
    {
        $email = 'nonexistent@example.com';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn(null);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('email', $email)
            ->andReturn($builderMock);

        $result = $this->repository->findByEmail($email);

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'hashed_password',
            'api_token' => 'new_token_123',
        ];

        // Test that the create method exists and has correct signature
        $reflection = new \ReflectionClass(UserRepository::class);
        $this->assertTrue($reflection->hasMethod('create'));
        
        $method = $reflection->getMethod('create');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('data', $parameters[0]->getName());
        $this->assertEquals('array', $parameters[0]->getType()->getName());
    }

    #[Test]
    public function it_can_update_user()
    {
        $userId = 1;
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('update')
            ->once()
            ->with($updateData)
            ->andReturn(1);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('id_user', $userId)
            ->andReturn($builderMock);

        $result = $this->repository->update($userId, $updateData);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_update_fails()
    {
        $userId = 999;
        $updateData = ['name' => 'Updated Name'];

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('update')
            ->once()
            ->with($updateData)
            ->andReturn(0);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('id_user', $userId)
            ->andReturn($builderMock);

        $result = $this->repository->update($userId, $updateData);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_find_users_by_type()
    {
        $type = 'teacher';
        $expectedUsers = new Collection([
            (object)['name' => 'Teacher 1', 'type' => 'teacher'],
            (object)['name' => 'Teacher 2', 'type' => 'teacher'],
        ]);

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('get')->once()->andReturn($expectedUsers);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('type', $type)
            ->andReturn($builderMock);

        $result = $this->repository->findByType($type);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_empty_collection_when_no_users_found_by_type()
    {
        $type = 'nonexistent_type';
        $expectedUsers = new Collection([]);

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('get')->once()->andReturn($expectedUsers);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('type', $type)
            ->andReturn($builderMock);

        $result = $this->repository->findByType($type);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_can_find_user_with_specific_columns()
    {
        $userId = 1;
        $columns = ['id_user', 'name', 'email'];
        $expectedUser = new User();
        $expectedUser->id_user = $userId;
        $expectedUser->name = 'Test User';
        $expectedUser->email = 'test@example.com';

        $builderMock1 = Mockery::mock();
        $builderMock2 = Mockery::mock();
        
        $builderMock2->shouldReceive('first')->once()->andReturn($expectedUser);
        $builderMock1->shouldReceive('where')
            ->once()
            ->with('id_user', $userId)
            ->andReturn($builderMock2);

        $this->mockModel
            ->shouldReceive('select')
            ->once()
            ->with($columns)
            ->andReturn($builderMock1);

        $result = $this->repository->findWithColumns($userId, $columns);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userId, $result->id_user);
    }

    #[Test]
    public function it_can_find_user_with_default_columns()
    {
        $userId = 1;
        $expectedUser = new User();
        $expectedUser->id_user = $userId;

        $builderMock1 = Mockery::mock();
        $builderMock2 = Mockery::mock();
        
        $builderMock2->shouldReceive('first')->once()->andReturn($expectedUser);
        $builderMock1->shouldReceive('where')
            ->once()
            ->with('id_user', $userId)
            ->andReturn($builderMock2);

        $this->mockModel
            ->shouldReceive('select')
            ->once()
            ->with(['*'])
            ->andReturn($builderMock1);

        $result = $this->repository->findWithColumns($userId);

        $this->assertInstanceOf(User::class, $result);
    }

    #[Test]
    public function it_returns_null_when_find_with_columns_user_not_found()
    {
        $userId = 999;
        $columns = ['id_user', 'name'];

        $builderMock1 = Mockery::mock();
        $builderMock2 = Mockery::mock();
        
        $builderMock2->shouldReceive('first')->once()->andReturn(null);
        $builderMock1->shouldReceive('where')
            ->once()
            ->with('id_user', $userId)
            ->andReturn($builderMock2);

        $this->mockModel
            ->shouldReceive('select')
            ->once()
            ->with($columns)
            ->andReturn($builderMock1);

        $result = $this->repository->findWithColumns($userId, $columns);

        $this->assertNull($result);
    }

    #[Test]
    public function repository_implements_interface()
    {
        $this->assertInstanceOf(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            $this->repository
        );
    }

    #[Test]
    public function repository_has_model_property()
    {
        $reflection = new \ReflectionClass($this->repository);
        $property = $reflection->getProperty('model');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(User::class, $property->getValue($this->repository));
    }

    #[Test]
    public function find_by_id_calls_model_find()
    {
        $userId = 5;
        
        $this->mockModel
            ->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(null);

        $result = $this->repository->findById($userId);
        
        $this->assertNull($result);
    }

    #[Test]
    public function find_by_token_uses_where_and_first()
    {
        $token = 'test_token';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn(null);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('api_token', $token)
            ->andReturn($builderMock);

        $result = $this->repository->findByToken($token);
        
        $this->assertNull($result);
    }

    #[Test]
    public function find_by_email_uses_where_and_first()
    {
        $email = 'test@example.com';

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('first')->once()->andReturn(null);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('email', $email)
            ->andReturn($builderMock);

        $result = $this->repository->findByEmail($email);
        
        $this->assertNull($result);
    }

    #[Test]
    public function update_returns_true_when_rows_affected_greater_than_zero()
    {
        $userId = 1;
        $updateData = ['name' => 'New Name'];

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('update')
            ->once()
            ->with($updateData)
            ->andReturn(5); // 5 rows affected

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('id_user', $userId)
            ->andReturn($builderMock);

        $result = $this->repository->update($userId, $updateData);

        $this->assertTrue($result);
    }

    #[Test]
    public function find_by_type_returns_collection()
    {
        $type = 'student';
        $expectedUsers = new \Illuminate\Database\Eloquent\Collection([
            (object)['name' => 'Student 1'],
            (object)['name' => 'Student 2'],
            (object)['name' => 'Student 3'],
        ]);

        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('get')->once()->andReturn($expectedUsers);

        $this->mockModel
            ->shouldReceive('where')
            ->once()
            ->with('type', $type)
            ->andReturn($builderMock);

        $result = $this->repository->findByType($type);

        $this->assertCount(3, $result);
    }
}
