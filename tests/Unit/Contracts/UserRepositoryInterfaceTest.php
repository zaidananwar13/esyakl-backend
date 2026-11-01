<?php

namespace Tests\Unit\Contracts;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use Tests\TestCase;

class UserRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function interface_defines_find_by_id_method()
    {
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        $this->assertTrue($reflection->hasMethod('findById'));
        
        $method = $reflection->getMethod('findById');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('id', $parameters[0]->getName());
        $this->assertEquals('int', $parameters[0]->getType()->getName());
    }

    /**
     * @test
     */
    public function interface_defines_find_by_token_method()
    {
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        $this->assertTrue($reflection->hasMethod('findByToken'));
        
        $method = $reflection->getMethod('findByToken');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('token', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
    }

    /**
     * @test
     */
    public function interface_defines_find_by_email_method()
    {
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        $this->assertTrue($reflection->hasMethod('findByEmail'));
        
        $method = $reflection->getMethod('findByEmail');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('email', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
    }

    /**
     * @test
     */
    public function interface_defines_create_method()
    {
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        $this->assertTrue($reflection->hasMethod('create'));
        
        $method = $reflection->getMethod('create');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('data', $parameters[0]->getName());
        $this->assertEquals('array', $parameters[0]->getType()->getName());
    }

    /**
     * @test
     */
    public function interface_defines_update_method()
    {
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        $this->assertTrue($reflection->hasMethod('update'));
        
        $method = $reflection->getMethod('update');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertEquals('id', $parameters[0]->getName());
        $this->assertEquals('int', $parameters[0]->getType()->getName());
        $this->assertEquals('data', $parameters[1]->getName());
        $this->assertEquals('array', $parameters[1]->getType()->getName());
    }

    /**
     * @test
     */
    public function interface_update_method_returns_bool()
    {
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        $method = $reflection->getMethod('update');
        
        $this->assertEquals('bool', $method->getReturnType()->getName());
    }

    /**
     * @test
     */
    public function user_repository_implements_interface()
    {
        $interfaces = class_implements(UserRepository::class);
        
        $this->assertContains(UserRepositoryInterface::class, $interfaces);
    }

    /**
     * @test
     */
    public function interface_has_all_required_methods()
    {
        $requiredMethods = ['findById', 'findByToken', 'findByEmail', 'create', 'update'];
        $reflection = new \ReflectionClass(UserRepositoryInterface::class);
        
        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "Interface should define method: $method"
            );
        }
    }
}
