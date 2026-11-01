<?php

namespace App\Services;

use App\Helper;
use App\Models\ClassStudent;
use App\Models\Project;
use App\Models\ProjectResult;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    public function getAllProjects(): array
    {
        $projects = Project::all();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Project found.',
            'data' => $projects
        ];
    }

    public function getProjectByClass(string $token, int $classId): array
    {
        $project = DB::table('project')
            ->where('id_class', $classId)
            ->first();

        if (!$project) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Project not found.'
            ];
        }

        $project->expired = new Carbon($project->created_at);
        $project->created_at = new Carbon($project->created_at);
        $project->updated_at = new Carbon($project->updated_at);
        $appraisal = $project->appraisal;

        $user = User::where('api_token', $token)->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Access Prohibited'
            ];
        }

        $projectUser = ProjectUser::where('id_user', $user->id_user)
            ->where('id_project', $project->id_project)
            ->first();

        $project->expired = $project->expired->addMonths(2);
        $project->status = ($projectUser !== null) ? 'Turned in' : 'No File Attached';

        if ($projectUser !== null) {
            $project->lectureNote = [
                'status' => ($projectUser->point > 79) ? 'Approved' : 'Disapprove',
                'note' => $projectUser->note,
            ];
        }

        $project->expired = $project->expired->format('d M Y - h:i:s');
        $project->appraisal = $appraisal;
        unset($project->created_at);
        unset($project->updated_at);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Project Found',
            'data' => $project
        ];
    }

    public function submitProject(string $token, int $projectClassId, string $note, $file): array
    {
        $user = User::where("api_token", $token)->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        $filename = "";
        if ($file) {
            $validation = $this->validateProjectFile($file);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'code' => 422,
                    'message' => $validation['message']
                ];
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
            
            $file->storeAs('', $filename, 'project');
        }

        $classStudent = ClassStudent::select('id_class_student')
            ->where('id_user', $user->id_user)
            ->where('id_class', $projectClassId)
            ->where('expired_at', '>=', Carbon::now()->format('U'))
            ->first();

        if ($classStudent === null) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Class enrollment not found or expired'
            ];
        }

        $projectModel = Project::where('id_class', $projectClassId)->first();

        if ($projectModel === null) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Project not found'
            ];
        }

        $project = ProjectUser::select("id_project_student")
            ->where('id_project', $projectModel->id_project)
            ->where('id_class_student', $classStudent->id_class_student)
            ->first();

        if ($project === null) {
            $project = new ProjectUser();
            $project->note = $note;
            $project->id_user = $user->id_user;
            $project->id_class_student = $classStudent->id_class_student;
            $project->id_project = $projectModel->id_project;
            $project->expired = Carbon::now()->addMonth(2)->format('U');
            $project->save();
        } else {
            ProjectUser::where('id_project', $projectModel->id_project)
                ->where('id_class_student', $classStudent->id_class_student)
                ->update([
                    'poin' => null
                ]);
        }

        $projectResult = new ProjectResult();
        $projectResult->id_project_student = $project->id_project_student;
        $projectResult->file = $filename;
        $projectResult->save();

        Helper::recordUserActivity($user->id_user, 'Submit Project', $projectClassId);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Upload Project Successful'
        ];
    }

    private function validateProjectFile($file): array
    {
        $allowedMimes = [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/vnd.rar',
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'text/plain',
        ];

        $allowedExtensions = ['zip', 'rar', 'pdf', 'docx', 'doc', 'txt'];
        $maxSize = config('app-settings.uploads.project_file.max_size', 12 * 1024 * 1024);

        // Check file size
        if ($file->getSize() > $maxSize) {
            return [
                'valid' => false,
                'message' => 'File size exceeds maximum allowed (12MB)'
            ];
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes, true)) {
            return [
                'valid' => false,
                'message' => 'Invalid file type. Allowed: ZIP, RAR, PDF, DOCX, TXT'
            ];
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            return [
                'valid' => false,
                'message' => 'Invalid file extension'
            ];
        }

        return ['valid' => true, 'message' => 'OK'];
    }
}
