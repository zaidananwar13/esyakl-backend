<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\HarakatController;
use App\Services\HarakatService;
use Exception;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class HarakatControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(HarakatService::class);
        $controller = new HarakatController($mockService);
        $this->assertInstanceOf(HarakatController::class, $controller);
    }

    public function test_vocalize_returns_422_when_text_empty()
    {
        $mockService = Mockery::mock(HarakatService::class);
        $controller = new HarakatController($mockService);

        $request = new Request(['text' => '']);
        $response = $controller->vocalize($request);

        $this->assertEquals(422, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals(422, $data['code']);
        $this->assertEmpty($data['text']);
        $this->assertIsArray($data['aroef']);
    }

    public function test_vocalize_returns_success_response()
    {
        $expectedResult = [
            'text' => 'فِي الْمَسْجِدِ',
            'aroef' => [
                [
                    'arab' => 'فِي',
                    'jenis' => 'Huruf',
                    'hukum' => 'Mabni',
                    'tanda' => 'Sukun',
                    'penjelasan' => 'Huruf Jar',
                    'arti' => 'Di'
                ],
                [
                    'arab' => 'الْمَسْجِدِ',
                    'jenis' => 'Isim',
                    'hukum' => 'Majrur',
                    'tanda' => 'Kasrah',
                    'penjelasan' => 'Isim majrur',
                    'arti' => 'Masjid'
                ]
            ]
        ];

        $mockService = Mockery::mock(HarakatService::class);
        $mockService->shouldReceive('vocalize')
            ->once()
            ->with('في المسجد', 'Indonesian')
            ->andReturn($expectedResult);

        $controller = new HarakatController($mockService);
        $request = new Request(['text' => 'في المسجد', 'lang' => 'Indonesian']);

        $response = $controller->vocalize($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals(200, $data['code']);
        $this->assertEquals('فِي الْمَسْجِدِ', $data['text']);
        $this->assertCount(2, $data['aroef']);
        $this->assertEquals('فِي', $data['aroef'][0]['arab']);
    }

    public function test_vocalize_handles_service_exception_gracefully()
    {
        $mockService = Mockery::mock(HarakatService::class);
        $mockService->shouldReceive('vocalize')
            ->once()
            ->andThrow(new Exception('AI quota exceeded'));

        $controller = new HarakatController($mockService);
        $request = new Request(['text' => 'في المسجد']);

        $response = $controller->vocalize($request);

        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals(500, $data['code']);
        $this->assertEquals('Gagal memproses harakat. Silakan coba beberapa saat lagi.', $data['message']);
    }

    public function test_vocalize_legacy_passes_url_parameter()
    {
        $expectedResult = [
            'text' => 'أَنَا خَالِدٌ',
            'aroef' => []
        ];

        $mockService = Mockery::mock(HarakatService::class);
        $mockService->shouldReceive('vocalize')
            ->once()
            ->with('أنا خالد', 'Indonesian')
            ->andReturn($expectedResult);

        $controller = new HarakatController($mockService);
        $request = new Request();

        $response = $controller->vocalizeLegacy($request, 'أنا خالد');

        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals('أَنَا خَالِدٌ', $data['text']);
    }
}
