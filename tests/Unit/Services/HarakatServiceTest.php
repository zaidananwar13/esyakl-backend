<?php

namespace Tests\Unit\Services;

use App\Services\HarakatService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HarakatServiceTest extends TestCase
{
    protected HarakatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HarakatService();
        Cache::flush();
    }

    public function test_vocalize_throws_exception_when_text_empty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Teks bahasa Arab tidak boleh kosong.');

        $this->service->vocalize('   ');
    }

    public function test_vocalize_throws_exception_when_text_exceeds_max_length()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Panjang teks melebihi batas maksimal');

        $longText = str_repeat('عربي ', 300);
        $this->service->vocalize($longText);
    }

    public function test_vocalize_throws_exception_when_api_keys_missing()
    {
        Config::set('services.gemini.api_key', '');
        Config::set('services.groq.api_key', '');
        Config::set('services.openai.api_key', '');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('API Key AI (GEMINI_API_KEY atau GROQ_API_KEY) belum dikonfigurasi');

        $this->service->vocalize('في المسجد');
    }

    public function test_vocalize_returns_cached_data_if_available()
    {
        Config::set('services.gemini.api_key', 'dummy-key');

        $cachedData = [
            'text' => 'فِي الْمَسْجِدِ',
            'aroef' => [
                ['arab' => 'فِي', 'jenis' => 'Huruf', 'hukum' => 'Mabni', 'tanda' => 'Sukun', 'penjelasan' => 'Huruf Jar', 'arti' => 'Di']
            ]
        ];

        $cacheKey = 'harakat_vocalize:' . md5('Indonesian:في المسجد');
        Cache::put($cacheKey, $cachedData, 3600);

        Http::fake(); // Should not be called

        $result = $this->service->vocalize('في المسجد', 'Indonesian');

        $this->assertEquals($cachedData, $result);
        Http::assertNothingSent();
    }

    public function test_vocalize_calls_gemini_api_and_parses_json_response()
    {
        Config::set('services.gemini.api_key', 'test-api-key');
        Config::set('services.gemini.model', 'gemini-2.5-flash');

        $geminiJsonPayload = json_encode([
            'text' => 'أَنَا ذَاهِبٌ إِلَى الْمَسْجِدِ',
            'aroef' => [
                [
                    'arab' => 'أَنَا',
                    'jenis' => 'Isim',
                    'hukum' => 'Mabni',
                    'tanda' => 'Sukun',
                    'penjelasan' => 'Dhamir munfashil mubtada',
                    'arti' => 'Saya'
                ]
            ]
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $geminiJsonPayload]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->vocalize('انا ذاهب الى المسجد', 'Indonesian');

        $this->assertEquals('أَنَا ذَاهِبٌ إِلَى الْمَسْجِدِ', $result['text']);
        $this->assertCount(1, $result['aroef']);
        $this->assertEquals('أَنَا', $result['aroef'][0]['arab']);
    }

    public function test_vocalize_handles_api_failure_response()
    {
        Config::set('services.gemini.api_key', 'test-api-key');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'error' => [
                    'code' => 403,
                    'message' => 'The Gemini API free tier is not available for this project.'
                ]
            ], 403)
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Google Cloud Project ini tidak mengizinkan Free Tier');

        $this->service->vocalize('في المسجد');
    }
}
