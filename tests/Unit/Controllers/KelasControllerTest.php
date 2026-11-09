<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\Attributes\Test;

use App\Http\Controllers\API\KelasController;
use App\Services\ClassService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class KelasControllerTest extends TestCase
{
    protected $mockService;
    protected KelasController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(ClassService::class);
        $this->controller = new KelasController($this->mockService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function kelas_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'kelas'));
    }

    #[Test]
    public function search_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'search'));
    }

    #[Test]
    public function my_class_method_exists()
    {
        $this->assertTrue(method_exists($this->controller, 'myClass'));
    }

    #[Test]
    public function controller_uses_api_response_trait()
    {
        $this->assertTrue(
            in_array(\App\Traits\ApiResponseTrait::class, class_uses($this->controller))
        );
    }

    #[Test]
    public function all_public_methods_exist()
    {
        $methods = [
            'index',
            'kelas',
            'filter',
            'search',
            'authKelas',
            'checkKelas',
            'completeClass',
            'myClass',
            'update',
            'review',
            'rateLecture',
            'checkoutInfoClass',
            'checkoutClass',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->controller, $method), "Method $method should exist");
        }
    }

    #[Test]
    public function auth_premium_kelas_static_method_exists()
    {
        $this->assertTrue(method_exists(KelasController::class, 'authPremiumKelas'));
    }
}
