<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\MigrasiController;
use Tests\TestCase;

class MigrasiControllerTest extends TestCase
{
    public function test_controller_can_be_instantiated()
    {
        $controller = new MigrasiController();
        $this->assertInstanceOf(MigrasiController::class, $controller);
    }

    public function test_uses_api_response_trait()
    {
        $controller = new MigrasiController();

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'errorResponse'));
    }

    public function test_index_method_exists()
    {
        $controller = new MigrasiController();
        $this->assertTrue(method_exists($controller, 'index'));
    }

    public function test_api_name_constant_exists()
    {
        $reflection = new \ReflectionClass(MigrasiController::class);
        $constants = $reflection->getConstants();

        $this->assertArrayHasKey('API_NAME', $constants);
        $this->assertEquals('Migrasi Login', $constants['API_NAME']);
    }

    public function test_controller_extends_base_controller()
    {
        $controller = new MigrasiController();
        $this->assertInstanceOf(\App\Http\Controllers\Controller::class, $controller);
    }
}
