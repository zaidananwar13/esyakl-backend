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
        $input = $request->all();
        $validator = Validator::make($input, [
            'note' => 'required',
            'api_token' => 'required',
            'project' => 'required',
            'project_file' => 'required|mimes:zip,rar,pdf,docx,txt|max:12048',
        ]);

        if ($validator->fails()) {
            // Legacy contract: validator errors were HTTP 401 {status: 401, message: [...]}.
            return $this->legacyResponse('Project', ['status' => 401, 'message' => $validator->errors()->all()], 401);
        }

        $result = $this->projectService->submitProject(
            $request->input("api_token"),
            $request->input("project"),
            $request->input("note"),
            $request->file('project_file')
        );

        if (!$result['success']) {
            return $this->errorResponse('Project', $result['message'], $result['code']);
        }

        return $this->successResponse('Project API', null, $result['message']);
    }

    public function test()
    {
        $action = 'nothing';
        return view('project.form', compact('action'));
    }
}
