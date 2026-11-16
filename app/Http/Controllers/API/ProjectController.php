<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Project';

    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        $result = $this->projectService->getAllProjects();

        return $this->successResponse(self::API_NAME, $result['data'], $result['message']);
    }

    public function projectClass(Request $request, $class_id)
    {
        $token = $request->bearerToken() ?: $request->input('api_token');

        if (!is_string($token) || $token === '') {
            return $this->unauthorizedResponse(self::API_NAME, 'You must login first!');
        }

        $result = $this->projectService->getProjectByClass($token, (int) $class_id);

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            return $this->notFoundResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, $result['data'], $result['message']);
    }

    public function submit(Request $request)
    {
        $allowedExtensions = ['zip', 'rar', 'pdf', 'docx', 'doc', 'txt'];
        $allowedMimes = [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/vnd.rar',
            'application/x-rar',
            'application/rar',
            'application/pdf',
            'application/x-pdf',
            'application/acrobat',
            'applications/vnd.pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'text/plain',
        ];

        $input = $request->all();
        $validator = Validator::make($input, [
            'note' => 'required',
            'api_token' => 'required',
            'project' => 'required',
            'project_file' => [
                'required',
                'file',
                'max:12048',
                function ($attribute, $value, $fail) use ($allowedExtensions, $allowedMimes) {
                    if (!$value instanceof \Illuminate\Http\UploadedFile) {
                        return;
                    }
                    $ext = strtolower($value->getClientOriginalExtension());
                    $mime = $value->getMimeType();
                    $clientMime = $value->getClientMimeType();
                    $mimeValid = in_array($mime, $allowedMimes, true) || in_array($clientMime, $allowedMimes, true);
                    if (!in_array($ext, $allowedExtensions, true) || !$mimeValid) {
                        $fail('The ' . str_replace('_', ' ', $attribute) . ' must be a file of type: zip, rar, pdf, docx, txt.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            // Legacy contract: validator errors were HTTP 401 {status: 401, message: [...]}.
            return $this->legacyResponse(self::API_NAME, ['status' => 401, 'message' => $validator->errors()->all()], 401);
        }

        $result = $this->projectService->submitProject(
            $request->input("api_token"),
            $request->input("project"),
            $request->input("note"),
            $request->file('project_file')
        );

        if (!$result['success']) {
            return $this->legacyResponse(self::API_NAME, [
                'status' => $result['code'],
                'message' => $result['message'],
            ], $result['code']);
        }

        return $this->legacyResponse(self::API_NAME, [
            'status' => 200,
            'message' => $result['message'],
        ], 200);
    }

    public function test()
    {
        $action = 'nothing';
        return view('project.form', compact('action'));
    }
}
