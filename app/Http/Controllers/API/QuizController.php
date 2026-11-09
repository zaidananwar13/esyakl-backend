<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\QuizService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Quiz';

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

        $syllabusId = $request->input("silabus");

        $quizData = $this->quizService->getQuizForSyllabus((int) $syllabusId, $user->id_user);

        if (!$quizData || empty($quizData['quizzes'])) {
            return $this->notFoundResponse(self::API_NAME, 'Not Found');
        }

        return $this->successResponse(self::API_NAME, $quizData, 'Retrieving data successful!');
    }

    public function submit(Request $request)
    {
        $user = User::select("id_user")
            ->where("api_token", $request->input("api_token"))
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'User not found');
        }

        $syllabusId = (int) $request->input('id_syllabus');
        $result = $this->quizService->submitQuiz($user->id_user, $syllabusId, $request->all());

        if (!$result['success']) {
            if ($result['code'] === 403) {
                return $this->forbiddenResponse(self::API_NAME, $result['message']);
            }
            if ($result['code'] === 404) {
                return $this->notFoundResponse(self::API_NAME, $result['message']);
            }
            return $this->serverErrorResponse(self::API_NAME, $result['message']);
        }

        // Legacy contract: correct/question/grade/status at the top level (also under 'data').
        return $this->legacySuccessResponse(self::API_NAME, [
            'correct' => $result['correct'],
            'question' => $result['question'],
            'grade' => $result['grade'],
            'status' => $result['status']
        ], $result['message']);
    }

    /**
     * Get quiz key/answers.
     * SECURITY FIX: This endpoint now requires admin authentication.
     * Quiz answers should never be publicly accessible.
     */
    public function freekey(Request $request, $syl = null)
    {
        // SECURITY: Check for admin privileges
        $token = $request->bearerToken() ?: $request->input('api_token');
        // SECURITY: never query with a null token (it would match "api_token IS NULL").
        $user = $request->user() ?? (is_string($token) && $token !== ''
            ? User::where('api_token', $token)->first()
            : null);

        if (!$user || $user->type !== 'admin') {
            return $this->forbiddenResponse(
                self::API_NAME,
                'Access denied. This endpoint requires administrator privileges.'
            );
        }

        $syllabusId = $syl ?? $request->input('id_syllabus');
        $quizKey = $this->quizService->getQuizKey($syllabusId ? (int) $syllabusId : null);

        return $this->successResponse(self::API_NAME, $quizKey, 'Quiz data retrieved');
    }

    public function sample()
    {
        $sampleQuiz = $this->quizService->getSampleQuiz();

        return $this->successResponse(self::API_NAME, $sampleQuiz, 'Sample quiz retrieved');
    }

    public function getOption($id)
    {
        $options = $this->quizService->getOptionById((int) $id);

        return $this->successResponse(self::API_NAME, $options, 'Options retrieved');
    }
}
