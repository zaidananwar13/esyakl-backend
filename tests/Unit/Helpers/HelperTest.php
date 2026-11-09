<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;

use App\Helper;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HelperTest extends TestCase
{
    #[Test]
    public function sec_to_hour_converts_seconds_correctly()
    {
        // 3600 seconds = 1 hour
        $result = Helper::secToHour(3600);
        $this->assertEquals(1.0, $result);
        
        // 7200 seconds = 2 hours
        $result = Helper::secToHour(7200);
        $this->assertEquals(2.0, $result);
        
        // 1800 seconds = 0.5 hours
        $result = Helper::secToHour(1800);
        $this->assertEquals(0.5, $result);
    }

    #[Test]
    public function sec_to_hour_handles_zero()
    {
        $result = Helper::secToHour(0);
        $this->assertEquals(0.0, $result);
    }

    #[Test]
    public function sec_to_hour_rounds_to_one_decimal()
    {
        // 5400 seconds = 1.5 hours
        $result = Helper::secToHour(5400);
        $this->assertEquals(1.5, $result);
        
        // 4000 seconds = ~1.11 hours, rounded to 1.1
        $result = Helper::secToHour(4000);
        $this->assertEquals(1.1, $result);
    }

    #[Test]
    public function time_elapsed_string_returns_human_readable_format()
    {
        // Test that the method exists and returns a string
        $result = Helper::time_elapsed_string(Carbon::now()->subMinutes(5));
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function time_elapsed_string_handles_recent_times()
    {
        $result = Helper::time_elapsed_string(Carbon::now()->subSeconds(30));
        
        $this->assertIsString($result);
    }

    #[Test]
    public function time_elapsed_string_handles_past_dates()
    {
        $result = Helper::time_elapsed_string(Carbon::now()->subDays(7));
        
        $this->assertIsString($result);
        // The result can be in any language based on locale settings
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function unauthorized_returns_401_response()
    {
        $response = Helper::unauthorized('TestAPI');
        
        $this->assertEquals(401, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('title', $content);
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals(401, $content['code']);
        $this->assertEquals('Unauthorized!', $content['message']);
        $this->assertNull($content['data']);
    }

    #[Test]
    public function unauthorized_includes_api_name_in_title()
    {
        $response = Helper::unauthorized('UserAPI');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertStringContainsString('UserAPI', $content['title']);
    }

    #[Test]
    public function generate_quiz_structures_quiz_data_correctly()
    {
        $params = [
            'id_quiz_1' => 1,
            'pilihan_1' => 'A',
            'id_quiz_2' => 2,
            'pilihan_2' => 'B',
            'id_quiz_3' => 3,
            'pilihan_3' => 'C',
        ];
        
        $result = Helper::generateQuiz($params);
        
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        
        foreach ($result as $item) {
            $this->assertArrayHasKey('id_quiz', $item);
            $this->assertArrayHasKey('pilihan', $item);
        }
    }

    #[Test]
    public function generate_quiz_handles_empty_params()
    {
        $result = Helper::generateQuiz([]);
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function helper_class_exists()
    {
        $this->assertTrue(class_exists(Helper::class));
    }

    #[Test]
    public function helper_has_all_expected_methods()
    {
        $expectedMethods = [
            'setCookie',
            'checkSilabusAccess',
            'checkSilabusAccessUser',
            'checkKelasAccessUser',
            'generateQuiz',
            'time_elapsed_string',
            'unauthorized',
            'updateUserLog',
            'secToHour',
            'recordUserActivity',
        ];
        
        foreach ($expectedMethods as $method) {
            $this->assertTrue(
                method_exists(Helper::class, $method),
                "Helper should have method: $method"
            );
        }
    }

    #[Test]
    public function helper_methods_are_static()
    {
        $reflection = new \ReflectionClass(Helper::class);
        $methods = [
            'setCookie',
            'checkSilabusAccess',
            'checkSilabusAccessUser',
            'checkKelasAccessUser',
            'generateQuiz',
            'time_elapsed_string',
            'unauthorized',
            'updateUserLog',
            'secToHour',
            'recordUserActivity',
        ];
        
        foreach ($methods as $methodName) {
            $method = $reflection->getMethod($methodName);
            $this->assertTrue($method->isStatic(), "Method $methodName should be static");
        }
    }

    #[Test]
    public function helper_uses_api_response_trait()
    {
        $traits = class_uses(Helper::class);
        
        $this->assertContains(\App\Traits\ApiResponseTrait::class, $traits);
    }
}
