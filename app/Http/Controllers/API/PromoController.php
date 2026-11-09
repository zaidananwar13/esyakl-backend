<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PromoService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Promo';

    protected PromoService $promoService;

    public function __construct(PromoService $promoService)
    {
        $this->promoService = $promoService;
    }

    public function index(Request $request)
    {
        $user = User::select("id_user")
            ->where("api_token", $request->input('api_token'))
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'You must login first!');
        }

        $code = $request->input('code');

        // Legacy contract (the checkout page reads 'msg', 'code', 'type' and 'value'):
        //   invalid/missing code -> HTTP 401 {code: 401, msg: "Promo Coupon Invalid"}
        //   expired              -> HTTP 200 {code: 419, msg: "Promo Coupon Expired"}
        //   valid                -> HTTP 200 {type, value} at the top level
        if (!$code || !is_string($code)) {
            return $this->legacyResponse(self::API_NAME, ['code' => 401, 'msg' => 'Promo Coupon Invalid', 'message' => 'Promo Coupon Invalid'], 401);
        }

        $result = $this->promoService->validatePromoCode($code);

        if (!$result['success']) {
            if ($result['code'] === 410) {
                return $this->legacyResponse(self::API_NAME, ['code' => 419, 'msg' => 'Promo Coupon Expired', 'message' => 'Promo Coupon Expired'], 200);
            }

            return $this->legacyResponse(self::API_NAME, ['code' => 401, 'msg' => 'Promo Coupon Invalid', 'message' => 'Promo Coupon Invalid'], 401);
        }

        return $this->legacySuccessResponse(self::API_NAME, [
            'type' => $result['type'],
            'value' => $result['value']
        ], 'Promo code validated');
    }
    
    public static function getPromo($code = null): array
    {
        $service = app(PromoService::class);
        $result = $service->getPromoDetails($code);

        if (!$result['success']) {
            return [
                'code' => $result['code'],
                'msg' => $result['message'],
            ];
        }

        return [
            'code' => $result['code'],
            'id' => $result['id'],
            'type' => $result['type'],
            'value' => $result['value'],
        ];
    }
}
