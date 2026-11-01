<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LearningPathService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LearningPathController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Learning Path';

    private LearningPathService $learningPathService;

    public function __construct(LearningPathService $learningPathService)
    {
        $this->learningPathService = $learningPathService;
    }

    public function index(Request $request, $id = null)
    {
        $userId = null;
        if ($request->input('api_token')) {
            $user = User::where('api_token', $request->input('api_token'))->first();
            $userId = $user?->id_user;
        }

        if ($id !== null) {
            $path = $this->learningPathService->getPathById((int) $id, $userId);

            if (!$path) {
                return $this->notFoundResponse(self::API_NAME, 'The learning path is found nowhere.');
            }

            if (empty($path['classes'])) {
                return $this->notFoundResponse(self::API_NAME, 'There are no classes on this path yet :(.');
            }

            return $this->successResponse(self::API_NAME, $path, "This is an API for Arabic-Go's Learning Path.");
        }

        $lang = $request->input('lang') ?? 'English';
        $paths = $this->learningPathService->getAllPaths($lang);

        return $this->successResponse(self::API_NAME, $paths, "This is an API for Arabic-Go's Learning Path.");
    }
}
