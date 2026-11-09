<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassStudent;
use App\Models\User;
use App\Services\ClassroomService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Classroom';

    protected ClassroomService $classroomService;

    public function __construct(ClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
    }

    public function classroom(Request $request)
    {
        $token = $request->input('api_token');
        $classId = $request->input('class');

        if ($token === null || $classId === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'Access Forbidden! What are you looking for?');
        }

        $user = User::select('id_user', 'name', 'email', 'created_at')
            ->where('api_token', $token)
            ->first();

        if (!$user) {
            return $this->notFoundResponse(self::API_NAME, 'User not found');
        }

        $classStudent = ClassStudent::select('id_class_student', 'id_class', 'id_user', 'completed_at', 'created_at', 'updated_at')
            ->where('id_user', $user->id_user)
            ->where('id_class', $classId)
            ->orderBy('id_class_student', 'desc')
            ->first();

        if (!$classStudent) {
            return $this->forbiddenResponse(self::API_NAME, 'You have no access to this class!');
        }

        $data = $this->classroomService->getClassroomData($user, $classStudent, $classId, $token);

        // Handle expired case
        if (isset($data['expired']) && $data['expired'] === true) {
            // Legacy contract: {status: "expired", id_class} at the top level, HTTP 200.
            return $this->legacySuccessResponse(self::API_NAME, [
                'status' => $data['status'],
                'id_class' => $data['id_class'],
            ], 'expired');
        }

        // Handle class not found
        if (isset($data['error']) && $data['error'] === 'class_not_found') {
            return $this->notFoundResponse(self::API_NAME, 'Your Class is Found in Nowhere? :/.');
        }

        return $this->successResponse(self::API_NAME, $data, 'Ok.');
    }
}
