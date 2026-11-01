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

        if (!$code || !is_string($code)) {
            return $this->validationErrorResponse(self::API_NAME, ['Promo code is required']);
        }

        $result = $this->promoService->validatePromoCode($code);

        if (!$result['success']) {
            return $this->errorResponse(self::API_NAME, $result['message'], $result['code']);
        }

        return $this->successResponse(self::API_NAME, [
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
