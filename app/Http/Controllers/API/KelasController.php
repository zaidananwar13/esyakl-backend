<?php

namespace App\Http\Controllers\API;

use App\Helper as Help;
use App\Http\Controllers\Controller;
use App\Models\ClassStudent;
use App\Models\CourseReport;
use App\Models\Kelas;
use App\Models\User;
use App\Models\UserPurchase;
use App\Services\ClassService;
use App\Services\PaymentService;
use App\Services\PromoService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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

        // Legacy contract: always HTTP 200; the outcome is the 'code' in the body
        // (1919 = not enrolled, 403 = expired, 409 = already registered).
        if (!$result['enrolled']) {
            return $this->legacyResponse(self::API_NAME_AUTH, ['code' => $result['code'], 'message' => $result['message']], 200);
        }

        if ($result['expired']) {
            return $this->legacyResponse(self::API_NAME_AUTH, ['code' => 403, 'message' => $result['message']], 200);
        }

        return $this->legacyResponse(self::API_NAME_AUTH, ['code' => 409, 'message' => $result['message']], 200);
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

    /**
     * POST class/{id}/report: a student reports a problem with a class.
     * Restored from the Laravel 8 API (the route existed but the method was missing).
     */
    public function report(Request $request, $id)
    {
        $user = User::select('id_user')->where('api_token', $request->input('api_token'))->first();

        if ($user === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized');
        }

        $classStudent = ClassStudent::select('id_class_student')
            ->where('id_user', $user->id_user)
            ->where('id_class', (int) $id)
            ->first();

        if ($classStudent === null) {
            return response(['msg' => 'You have no access to this class!', 'code' => 401], 401);
        }

        $report = new CourseReport();
        $report->id_class_student = $classStudent->id_class_student;
        $report->status = $request->input('status');
        $report->description = $request->input('description');

        if ($report->save()) {
            return $this->successResponse('Report Class', null, 'Report Submitted!');
        }

        return $this->serverErrorResponse('Report Class', 'Oops something went wrong!');
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

        // Legacy contract: errors carry 'msg'; on success the class fields sit at the top level.
        if (!$result['success']) {
            return $this->legacyResponse(self::API_NAME, [
                'code' => $result['code'],
                'msg' => $result['message'],
                'message' => $result['message'],
            ], $result['code']);
        }

        $data = $result['data'];
        $data = is_object($data) && method_exists($data, 'toArray') ? $data->toArray() : (array) $data;

        return $this->legacySuccessResponse(self::API_NAME, $data, $result['message']);
    }

    public function checkoutClass(Request $request)
    {
        $user = User::select("id_user")->where("api_token", $request->input('api_token'))->first();

        if ($user === null) {
            return response(['msg' => 'You must login first!', 'code' => 401], 401);
        }

        // Ported from the Laravel 8 API (the refactor left this as a stub, which broke paid
        // purchases): apply the promo, create the pending purchase, then get a Midtrans Snap
        // token. POST finish-payment later verifies the order with Midtrans.
        $class = Kelas::select('id_class', 'title', 'price')
            ->where('id_class', (int) $request->input('class'))
            ->where('type', 'paid')
            ->first();

        if ($class === null) {
            return response(['msg' => 'Class is not found.', 'code' => 404], 404);
        }

        $price = (float) $class->price;
        $promo = app(PromoService::class)->getPromoDetails($request->input('code'));
        $promoId = null;

        if ($promo['success']) {
            $price = $promo['type'] === 'percent'
                ? $price - ($price * $promo['value'] / 100)
                : $price - $promo['value'];
            $promoId = $promo['id'];
        }

        $price = max(0, (int) round($price));

        $purchase = UserPurchase::create([
            'id_user_purchase' => (string) Str::uuid(),
            'id_user' => $user->id_user,
            'id_class' => $class->id_class,
            'id_promo' => $promoId,
            'expired_at' => Carbon::now()->addDay()->format('U'),
            'created_at' => Carbon::now()->format('U'),
            'updated_at' => Carbon::now()->format('U'),
        ]);

        if (!$purchase) {
            return $this->legacyResponse(self::API_NAME, ['code' => 500, 'msg' => 'Something went wrong :/'], 500);
        }

        $snap = app(PaymentService::class)->createSnapPayment($user->id_user, $class->id_class, $price);

        if (!$snap['success']) {
            report(new \RuntimeException('Snap token failed: ' . $snap['message']));

            return $this->legacyResponse(self::API_NAME, ['code' => $snap['code'], 'msg' => 'Payment gateway is unavailable, please try again later.'], $snap['code']);
        }

        // Legacy contract read by the checkout page.
        return $this->legacyResponse(self::API_NAME, [
            'code' => 200,
            'msg' => 'Purchases Created!',
            'price' => $price,
            'snap_token' => (string) $snap['token'],
        ], 200);
    }
}
