<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\PromoService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Payment';

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create a Snap payment token.
     * SECURITY FIX: Price is now fetched from database, not from client request.
     */
    public function createSnapPayment(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'class' => 'required|integer|exists:class,id_class',
            'promo_code' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(self::API_NAME, $validator->errors()->all());
        }

        $user = User::select("id_user")
            ->where("api_token", $request->input('api_token'))
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'You must login first!');
        }

        $classId = (int) $request->input('class');

        // SECURITY FIX: Get price from database, NOT from client request
        $class = Kelas::select('id_class', 'title', 'price', 'type')
            ->where('id_class', $classId)
            ->first();

        if (!$class) {
            return $this->notFoundResponse(self::API_NAME, 'Class not found');
        }

        if ($class->type !== 'paid') {
            return $this->errorResponse(self::API_NAME, 'This class does not require payment', 400);
        }

        // Calculate final price (server-side)
        $finalPrice = $class->price;

        // Apply promo code if provided (validated on server)
        if ($request->filled('promo_code')) {
            $promoResult = $this->applyPromoCode($request->input('promo_code'), $class->price, $user->id_user);
            if ($promoResult['valid']) {
                $finalPrice = $promoResult['final_price'];
            }
        }

        // Ensure price is at least 1 (minimum for payment gateway)
        $finalPrice = max(1, (int) $finalPrice);

        $result = $this->paymentService->createSnapPayment($user->id_user, $classId, $finalPrice);

        if (!$result['success']) {
            if ($result['code'] === 404) {
                return $this->notFoundResponse(self::API_NAME, $result['message']);
            }
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            return $this->serverErrorResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, [
            'token' => $result['token'],
            'amount' => $finalPrice,
        ], 'Payment token created');
    }

    /**
     * Handle payment completion callback.
     */
    public function handlePayment(Request $request)
    {
        $user = User::select("id_user", "name", "email", "phone_number")
            ->where("api_token", $request->input('api_token'))
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse(self::API_NAME, 'You must login first!');
        }

        $paymentData = [
            'order_id' => $request->input('order_id'),
            'transaction_time' => $request->input('transaction_time'),
            'payment_type' => $request->input('payment_type'),
            'gross_amount' => $request->input('gross_amount'),
        ];

        $result = $this->paymentService->handlePayment($user->id_user, $paymentData);

        if (!$result['success']) {
            return $this->serverErrorResponse(self::API_NAME, $result['message']);
        }

        $response = KelasController::authPremiumKelas($result['class_id'], $user->id_user);

        return $this->successResponse(self::API_NAME, $response, $response['message'] ?? 'Payment processed');
    }

    /**
     * Apply promo code and calculate discount (server-side validation).
     */
    private function applyPromoCode(string $code, int $originalPrice, int $userId): array
    {
        try {
            $promoService = app(PromoService::class);
            return $promoService->validateAndApply($code, $originalPrice, $userId);
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'final_price' => $originalPrice,
                'message' => 'Invalid promo code',
            ];
        }
    }
}
