<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthorizerService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FEAuthController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Authorizer';

    protected AuthorizerService $authorizerService;

    public function __construct(AuthorizerService $authorizerService)
    {
        $this->authorizerService = $authorizerService;
    }

    public function index(Request $request)
    {
        $token = $request->input("api_token");
        $classId = $request->input("class");

        if ($token === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized!');
        }

        $user = User::select("id_user")
            ->where("api_token", $token)
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized!');
        }

        $result = $this->authorizerService->getUnlockedMaterial($user->id_user, (int) $classId);

        if (!$result['success']) {
            if ($result['code'] === 403) {
                return $this->forbiddenResponse(self::API_NAME, $result['message']);
            }
            return $this->notFoundResponse(self::API_NAME, $result['message']);
        }

        // Legacy contract: id_class is echoed back exactly as the client sent it (a string).
        $data = $result['data'];
        $data['id_class'] = $classId;

        return $this->successResponse(self::API_NAME, (object) $data, 'Retrieving data successful!');
    }

    public function feAuthorizer(Request $request)
    {
        $token = $request->input("api_token");
        $classId = $request->input("class");
        $material = $request->input("material");

        if ($token === null) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized!');
        }

        $user = User::select("id_user")
            ->where("api_token", $token)
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'Unauthorized!');
        }

        $result = $this->authorizerService->unlockMaterial($user->id_user, (int) $classId, (int) $material);

        if (!$result['success']) {
            if ($result['code'] === 403) {
                return $this->forbiddenResponse(self::API_NAME, $result['message']);
            }
            return $this->unauthorizedResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, null, $result['message']);
    }

    public static function staticFEAuthorizer($token, $class, $material)
    {
        if ($token === null) {
            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::select("id_user")
            ->where("api_token", $token)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Create service instance and delegate
        $service = app(AuthorizerService::class);
        $result = $service->staticAuthorize($user->id_user, (int) $class, (int) $material);

        return response()->json([
            'status' => $result['success'],
            'code' => 200,
            'message' => $result['message']
        ]);
    }
}
