<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MyClassService;
use App\Services\PointService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MyClassController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'User';

    protected MyClassService $myClassService;
    protected ?PointService $pointService;

    public function __construct(MyClassService $myClassService, ?PointService $pointService = null)
    {
        $this->myClassService = $myClassService;
        $this->pointService = $pointService;
    }

    public function index(Request $request)
    {
        if ($request->input('api_token') === null) {
            return $this->forbiddenResponse(self::API_NAME, 'Access Forbidden! What are you looking for?');
        }

        $user = User::select('id_user', 'name', 'email', 'created_at')
            ->where('api_token', $request->input('api_token'))
            ->first();

        if (!$user) {
            return $this->notFoundResponse(self::API_NAME, 'User not found');
        }

        $profile = $this->myClassService->getUserDashboard(
            $user->id_user,
            $user->name,
            $user->email,
            $user->created_at
        );

        return $this->successResponse(self::API_NAME, $profile, 'Ok.');
    }

    public function progress(Request $request)
    {
        $user = User::select('id_user')
            ->where('api_token', $request->input('api_token'))
            ->first();

        if (!$user) {
            return $this->notFoundResponse(self::API_NAME, 'User not found');
        }

        // Calculate and submit points if PointService is available
        if ($this->pointService) {
            $this->pointService->calculateAndSubmitPoints($user->id_user);
        } else {
            // Fallback to static method for backward compatibility
            PointStudentController::submitPoint($user->id_user);
        }

        $progressData = $this->myClassService->getUserProgress($user->id_user);

        return $this->successResponse(self::API_NAME, $progressData, 'Success');
    }
}
