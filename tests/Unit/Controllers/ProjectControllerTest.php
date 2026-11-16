<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\API\ProjectController;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_can_be_instantiated()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);
        $this->assertInstanceOf(ProjectController::class, $controller);
    }

    public function test_project_class_method_exists()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);
        $this->assertTrue(method_exists($controller, 'projectClass'));
    }

    public function test_uses_api_response_trait()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);

        $this->assertTrue(method_exists($controller, 'successResponse'));
        $this->assertTrue(method_exists($controller, 'unauthorizedResponse'));
        $this->assertTrue(method_exists($controller, 'notFoundResponse'));
    }

    public function test_all_methods_exist()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);

        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'projectClass'));
        $this->assertTrue(method_exists($controller, 'submit'));
        $this->assertTrue(method_exists($controller, 'test'));
    }

    public function test_submit_returns_validation_error_when_required_fields_missing()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);

        $request = Request::create('/api/project/submit', 'POST', []);
        $response = $controller->submit($request);

        $this->assertEquals(401, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertEquals(401, $body['status']);
        $this->assertIsArray($body['message']);
        $this->assertNotEmpty($body['message']);
    }

    public function test_submit_returns_validation_error_when_file_type_invalid()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $controller = new ProjectController($mockService);

        $file = UploadedFile::fake()->create('danger.exe', 100, 'application/x-msdownload');
        $request = Request::create(
            '/api/project/submit',
            'POST',
            ['api_token' => 'token', 'project' => 1, 'note' => 'Note'],
            [],
            ['project_file' => $file]
        );

        $response = $controller->submit($request);

        $this->assertEquals(401, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertEquals(401, $body['status']);
        $this->assertStringContainsString('file of type: zip, rar, pdf, docx, txt', implode(' ', $body['message']));
    }

    public function test_submit_accepts_pdf_variant_mimes_and_returns_success_contract()
    {
        $mimes = [
            'application/pdf',
            'application/x-pdf',
            'application/acrobat',
            'applications/vnd.pdf',
        ];

        foreach ($mimes as $mime) {
            $mockService = Mockery::mock(ProjectService::class);
            $mockService->shouldReceive('submitProject')
                ->once()
                ->with('token123', 6, 'Note text', Mockery::type(UploadedFile::class))
                ->andReturn([
                    'success' => true,
                    'code' => 200,
                    'message' => 'Upload Project Successful',
                ]);

            $controller = new ProjectController($mockService);

            $file = UploadedFile::fake()->create('document.pdf', 50, $mime);
            $request = Request::create(
                '/api/project/submit',
                'POST',
                ['api_token' => 'token123', 'project' => 6, 'note' => 'Note text'],
                [],
                ['project_file' => $file]
            );

            $response = $controller->submit($request);

            $this->assertEquals(200, $response->getStatusCode());
            $body = json_decode($response->getContent(), true);
            $this->assertSame(200, $body['status']);
            $this->assertSame('Upload Project Successful', $body['message']);
        }
    }

    public function test_submit_returns_error_contract_matching_failure_code()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $mockService->shouldReceive('submitProject')
            ->once()
            ->andReturn([
                'success' => false,
                'code' => 404,
                'message' => 'Project not found',
            ]);

        $controller = new ProjectController($mockService);

        $file = UploadedFile::fake()->create('document.pdf', 50, 'application/pdf');
        $request = Request::create(
            '/api/project/submit',
            'POST',
            ['api_token' => 'token123', 'project' => 999, 'note' => 'Note text'],
            [],
            ['project_file' => $file]
        );

        $response = $controller->submit($request);

        $this->assertEquals(404, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame(404, $body['status']);
        $this->assertSame('Project not found', $body['message']);
    }

    public function test_submit_accepts_rar_files_with_x_rar_and_rar_mime()
    {
        $rarMimes = ['application/x-rar', 'application/rar', 'application/x-rar-compressed', 'application/vnd.rar'];

        foreach ($rarMimes as $mime) {
            $mockService = Mockery::mock(ProjectService::class);
            $mockService->shouldReceive('submitProject')
                ->once()
                ->andReturn([
                    'success' => true,
                    'code' => 200,
                    'message' => 'Upload Project Successful',
                ]);

            $controller = new ProjectController($mockService);

            $file = UploadedFile::fake()->create('submission.rar', 50, $mime);
            $request = Request::create(
                '/api/project/submit',
                'POST',
                ['api_token' => 'token123', 'project' => 6, 'note' => 'RAR note'],
                [],
                ['project_file' => $file]
            );

            $response = $controller->submit($request);

            $this->assertEquals(200, $response->getStatusCode());
            $body = json_decode($response->getContent(), true);
            $this->assertSame(200, $body['status']);
        }
    }

    public function test_submit_accepts_file_when_client_mime_type_is_allowed()
    {
        $mockService = Mockery::mock(ProjectService::class);
        $mockService->shouldReceive('submitProject')
            ->once()
            ->andReturn([
                'success' => true,
                'code' => 200,
                'message' => 'Upload Project Successful',
            ]);

        $controller = new ProjectController($mockService);

        // Create temporary real file
        $temp = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($temp, 'sample raw content');
        $file = new UploadedFile($temp, 'document.pdf', 'applications/vnd.pdf', null, true);

        $request = Request::create(
            '/api/project/submit',
            'POST',
            ['api_token' => 'token123', 'project' => 6, 'note' => 'Client MIME test'],
            [],
            ['project_file' => $file]
        );

        $response = $controller->submit($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame(200, $body['status']);

        @unlink($temp);
    }
}

