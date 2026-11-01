<?php

namespace App\Http\Controllers\API;

use App\Helper as Help;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ClassService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Kelas';
    protected const API_NAME_AUTH = 'Silabus Auth';
    protected const API_NAME_SILABUS = 'Silabus';

    protected ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index(Request $request, $id = null)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $userId = null;
        $token = $request->input("api_token");

        if ($token !== null) {
            $user = User::select("id_user")->where("api_token", $token)->first();
            $userId = $user ? $user->id_user : null;
        }

        $classDetails = $this->classService->getClassDetails((int)$id, $userId);

        if ($classDetails === null) {
            return $this->notFoundResponse(self::API_NAME, 'Not Found');
        }

        return $this->successResponse(self::API_NAME, [$classDetails], 'Retrieving data successful!');
    }

    public function kelas()
    {
        $classList = $this->classService->getClassList();

        if (count($classList) > 0) {
            return $this->successResponse(self::API_NAME, $classList, 'Retrieving data successful!');
        }

        return $this->notFoundResponse(self::API_NAME, 'Not Found');
    }

    public function filter(Request $request, $keywords = null)
    {
        $filters = json_decode($request->getContent(), true);
        $filters = $filters['filters'] ?? [];

        $filtered = $this->classService->filterClasses($filters, $keywords);

        if (count($filtered) > 0) {
            return $this->successResponse(self::API_NAME, $filtered, 'Retrieving data successful!');
        }

        return $this->notFoundResponse(self::API_NAME, 'Not Found');
    }

    public function search(Request $request, $keyword)
    {
        if ($keyword === null) {
            return $this->notFoundResponse('Search', 'Not Found');
        }

        $results = $this->classService->searchClasses($keyword);

        if (count($results) > 0) {
            return $this->successResponse(self::API_NAME, $results, 'Retrieving data successful!');
        }

        return $this->notFoundResponse('Search', 'Not Found');
    }

    public function authKelas(Request $request)
    {
        $req = $request->all();

        $user = User::select('id_user', "api_token")
            ->where('api_token', $req['api_token'])
            ->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME_AUTH, 'Unauthorized');
        }

        $result = $this->classService->enrollUserToClass((int)$req['kelas'], $user->id_user);

        if (!$result['success']) {
            if ($result['code'] === 403) {
                return $this->forbiddenResponse(self::API_NAME, $result['message']);
            }
            return $this->conflictResponse(self::API_NAME_AUTH, $result['message']);
        }

        Help::recordUserActivity($user->id_user, 'Join Class', $req['kelas']);
        return $this->successResponse(self::API_NAME_AUTH, null, $result['message']);
    }

    public static function authPremiumKelas($class, $userId)
    {
        $classService = app(ClassService::class);

        $result = $classService->enrollUserToClass((int)$class, (int)$userId, true);

        if (!$result['success']) {
            return [
                'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Silabus Auth API',
                'code' => $result['code'],
                'message' => $result['message'],
            ];
        }

        Help::recordUserActivity($userId, 'Join Premium Class', $class);

        return [
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Silabus Auth API',
            'code' => 200,
            'message' => 'Payment Success, Class Register success!',
        ];
    }

    public function checkKelas(Request $request)
    {
        $req = $request->all();

        $user = User::select('id_user')
            ->where('api_token', $req['api_token'])
            ->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME_AUTH, 'Unauthorized');
        }

        $result = $this->classService->checkEnrollment((int)$req['kelas'], $user->id_user);

        if (!$result['enrolled']) {
            return $this->errorResponse(self::API_NAME_AUTH, $result['message'], $result['code']);
        }

        if ($result['expired']) {
            return $this->forbiddenResponse(self::API_NAME_AUTH, $result['message']);
        }

        return $this->conflictResponse(self::API_NAME_AUTH, $result['message']);
    }

    public function completeClass(Request $request)
    {
        $user = User::select("id_user")->where("api_token", $request->input("api_token"))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME_AUTH, 'Unauthorized');
        }

        $result = $this->classService->completeClass((int)$request->input("class"), $user->id_user);

        if (!$result['success']) {
            if ($result['code'] === 200) {
                return $this->successResponse('Kelas Auth', null, $result['message']);
            }
            return $this->unauthorizedResponse(self::API_NAME_AUTH, $result['message']);
        }

        if (CertificateController::createLocal($result['class_student'])) {
            Help::recordUserActivity($user->id_user, 'Finish Class', $request->input("class"));
            return $this->successResponse('Kelas Auth', null, $result['message']);
        }

        return $this->unauthorizedResponse(self::API_NAME_AUTH, 'Unauthorized');
    }

    public function myClass(Request $request)
    {
        return $this->notFoundResponse(self::API_NAME, "User's Class Not Found");
    }

    public function update(Request $request, $id_silabus)
    {
        $user = User::where("api_token", $request->input("api_token"))->first();

        if ($user === null) {
            return Help::unauthorized("Kelas");
        }

        $result = $this->classService->updateClassHistory($user->id_user, (int)$id_silabus);

        if (!$result['success']) {
            return Help::unauthorized("Kelas");
        }

        return $this->successResponse(self::API_NAME, null, $result['message']);
    }

    public function review(Request $request, $id)
    {
        $user = User::select("id_user")->where("api_token", $request->input('api_token'))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME_SILABUS, 'Access Prohibited');
        }

        $result = $this->classService->submitReview(
            $user->id_user,
            (int)$id,
            (int)$request->input('star'),
            $request->input('comment')
        );

        if (!$result['success']) {
            return $this->unauthorizedResponse(self::API_NAME_SILABUS, $result['message']);
        }

        return $this->successResponse(self::API_NAME_SILABUS, null, $result['message']);
    }

    public function rateLecture(Request $request)
    {
        $user = User::select("id_user")->where("api_token", $request->input('api_token'))->first();

        if ($user === null) {
            return $this->serverErrorResponse('Lecture', 'Oops something went wrong!');
        }

        $result = $this->classService->rateLecture(
            $user->id_user,
            (int)$request->input('class'),
            $request->input('postType'),
            (int)$request->input('post_id'),
            $request->input('status')
        );

        if (!$result['success']) {
            return $this->serverErrorResponse('Lecture', $result['message']);
        }

        return $this->successResponse('Lecture', null, $result['message']);
    }

    public function checkoutInfoClass(Request $request)
    {
        $user = User::select("id_user")->where("api_token", $request->input('api_token'))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'You must login first!');
        }

        $result = $this->classService->getCheckoutInfo($user->id_user, (int)$request->input('class'));

        if (!$result['success']) {
            return $this->errorResponse(self::API_NAME, $result['message'], $result['code']);
        }

        return $this->successResponse(self::API_NAME, $result['data'], $result['message']);
    }

    public function checkoutClass(Request $request)
    {
        $user = User::select("id_user")->where("api_token", $request->input('api_token'))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'You must login first!');
        }

        // Payment processing logic would go here
        // This is typically handled by PaymentController
        return $this->successResponse(self::API_NAME, null, 'Checkout initiated');
    }
}
