<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\QuizService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class QuizProgressController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Quiz Progress';

    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index(Request $request)
    {
        $user = User::select("id_user")
            ->where("api_token", $request->input("api_token"))
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'User not found');
        }

        $quiz = $this->quizService->getQuizProgress($user->id_user);

        if ($quiz->count() > 0) {
            return $this->successResponse('Quiz', $quiz->toArray(), 'Retrieving data successful!');
        }

        return $this->notFoundResponse(self::API_NAME, 'Not Found');
    }

    public function isClear(Request $request)
    {
        $user = User::select("id_user")
            ->where("api_token", $request->input("api_token"))
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'User not found');
        }

        $kategoriSilabusId = (int) $request->input("y-key");

        if ($this->quizService->isQuizCompleted($user->id_user, $kategoriSilabusId)) {
            return $this->successResponse('Quiz', null, 'Quiz has been completed!');
        }

        return $this->errorResponse(self::API_NAME, 'Quiz not completed', 402);
    }
}
