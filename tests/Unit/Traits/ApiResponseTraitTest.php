<?php

namespace Tests\Unit\Traits;

use App\Traits\ApiResponseTrait;
use Tests\TestCase;

class ApiResponseTraitTest extends TestCase
{
    use ApiResponseTrait;

    /**
     * @test
     */
    public function success_response_returns_correct_structure()
    {
        $response = $this->successResponse('Test', ['key' => 'value'], 'Success message');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('title', $content);
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals(200, $content['code']);
        $this->assertEquals('Success message', $content['message']);
        $this->assertEquals(['key' => 'value'], $content['data']);
    }

    /**
     * @test
     */
    public function success_response_uses_default_message()
    {
        $response = $this->successResponse('Test', null);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals('Retrieving data successful!', $content['message']);
    }

    /**
     * @test
     */
    public function success_response_includes_api_name_in_title()
    {
        $response = $this->successResponse('UserAPI', null);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertStringContainsString('UserAPI API', $content['title']);
    }

    /**
     * @test
     */
    public function success_response_accepts_custom_code()
    {
        $response = $this->successResponse('Test', null, 'Created', 201);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(201, $content['code']);
    }

    /**
     * @test
     */
    public function error_response_returns_correct_structure()
    {
        $response = $this->errorResponse('Test', 'Error message', 400);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('title', $content);
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals(400, $content['code']);
        $this->assertEquals('Error message', $content['message']);
    }

    /**
     * @test
     */
    public function error_response_uses_default_values()
    {
        $response = $this->errorResponse('Test');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $content['message']);
    }

    /**
     * @test
     */
    public function error_response_includes_data_when_provided()
    {
        $response = $this->errorResponse('Test', 'Error', 400, ['error_details' => 'Something']);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals(['error_details' => 'Something'], $content['data']);
    }

    /**
     * @test
     */
    public function error_response_excludes_data_when_null()
    {
        $response = $this->errorResponse('Test', 'Error', 400, null);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayNotHasKey('data', $content);
    }

    /**
     * @test
     */
    public function unauthorized_response_returns_401()
    {
        $response = $this->unauthorizedResponse('Test', 'Custom unauthorized message');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(401, $content['code']);
        $this->assertEquals('Custom unauthorized message', $content['message']);
    }

    /**
     * @test
     */
    public function unauthorized_response_uses_default_message()
    {
        $response = $this->unauthorizedResponse('Test');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals('Unauthorized', $content['message']);
    }

    /**
     * @test
     */
    public function forbidden_response_returns_403()
    {
        $response = $this->forbiddenResponse('Test', 'Custom forbidden message');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(403, $content['code']);
        $this->assertEquals('Custom forbidden message', $content['message']);
    }

    /**
     * @test
     */
    public function forbidden_response_uses_default_message()
    {
        $response = $this->forbiddenResponse('Test');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals('Access Forbidden', $content['message']);
    }

    /**
     * @test
     */
    public function not_found_response_returns_404()
    {
        $response = $this->notFoundResponse('Test', 'Resource not found');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(404, $content['code']);
        $this->assertEquals('Resource not found', $content['message']);
    }

    /**
     * @test
     */
    public function not_found_response_uses_default_message()
    {
        $response = $this->notFoundResponse('Test');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals('Not Found', $content['message']);
    }

    /**
     * @test
     */
    public function server_error_response_returns_500()
    {
        $response = $this->serverErrorResponse('Test', 'Internal error');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(500, $content['code']);
        $this->assertEquals('Internal error', $content['message']);
    }

    /**
     * @test
     */
    public function server_error_response_uses_default_message()
    {
        $response = $this->serverErrorResponse('Test');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals('Something went wrong', $content['message']);
    }

    /**
     * @test
     */
    public function conflict_response_returns_409()
    {
        $response = $this->conflictResponse('Test', 'Email already exists');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals(409, $content['code']);
        $this->assertEquals('Email already exists', $content['message']);
    }

    /**
     * @test
     */
    public function conflict_response_uses_default_message()
    {
        $response = $this->conflictResponse('Test');
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals('Already registered', $content['message']);
    }

    /**
     * @test
     */
    public function validation_error_response_returns_422()
    {
        $errors = ['The email field is required.', 'The name field is required.'];
        $response = $this->validationErrorResponse('Test', $errors);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals(422, $content['code']);
        $this->assertEquals(422, $content['status']);
        $this->assertEquals($errors, $content['message']);
    }

    /**
     * @test
     */
    public function validation_error_response_includes_title()
    {
        $response = $this->validationErrorResponse('UserAPI', ['Error']);
        
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('title', $content);
        $this->assertStringContainsString('UserAPI API', $content['title']);
    }

    /**
     * @test
     */
    public function all_response_methods_include_app_title_prefix()
    {
        $responses = [
            $this->successResponse('Test', null),
            $this->errorResponse('Test'),
            $this->unauthorizedResponse('Test'),
            $this->forbiddenResponse('Test'),
            $this->notFoundResponse('Test'),
            $this->serverErrorResponse('Test'),
            $this->conflictResponse('Test'),
            $this->validationErrorResponse('Test', ['error']),
        ];
        
        foreach ($responses as $response) {
            $content = json_decode($response->getContent(), true);
            $this->assertArrayHasKey('title', $content);
            $this->assertStringContainsString('Test API', $content['title']);
        }
    }
}
