<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\PointStudentController;
use Tests\TestCase;

class PointStudentControllerTest extends TestCase
{
    public function test_controller_can_be_instantiated()
    {
        $controller = new PointStudentController();
        $this->assertInstanceOf(PointStudentController::class, $controller);
    }

    public function test_submit_point_static_method_exists()
    {
        $this->assertTrue(method_exists(PointStudentController::class, 'submitPoint'));
    }

    public function test_submit_point_returns_boolean()
    {
        // Since we can't easily control the database, just verify the method is callable
        $result = PointStudentController::submitPoint(99999);
        $this->assertIsBool($result);
    }

    public function test_get_point_service_method_exists()
    {
        $reflection = new \ReflectionClass(PointStudentController::class);
        $method = $reflection->getMethod('getPointService');
        
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isProtected());
    }
}
