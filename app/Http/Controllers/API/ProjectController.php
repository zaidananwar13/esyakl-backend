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
        $result = $this->projectService->getProjectByClass(
            $request->input('api_token'),
            $class_id
        );

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
            return $this->validationErrorResponse('Project API', $validator->errors()->all());
        }

        $result = $this->projectService->submitProject(
            $request->input("api_token"),
            $request->input("project"),
            $request->input("note"),
            $request->file('project_file')
        );

        if (!$result['success']) {
            return $this->errorResponse('Project API', $result['code'], $result['message']);
        }

        return $this->successResponse('Project API', null, $result['message']);
    }

    public function test()
    {
        $action = 'nothing';
        return view('project.form', compact('action'));
    }
}
