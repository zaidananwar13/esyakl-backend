<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\User;
use App\Models\UserPurchase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Midtrans\Config as MidTransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

        protected function configureMidtrans(): void
    {
        MidTransConfig::$serverKey = config('services.midtrans.server_key');
        MidTransConfig::$isProduction = config('services.midtrans.is_production', true);
        MidTransConfig::$isSanitized = true;
        MidTransConfig::$is3ds = true;
    }

        public function createSnapPayment(int $userId, int $classId, int $price): array
    {
        $user = User::select("id_user", "name", "email", "phone_number")
            ->where("id_user", $userId)
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'You must login first!'
            ];
        }

        $class = Kelas::select('id_class', 'title')
            ->where('id_class', $classId)
            ->where('type', 'paid')
            ->first();

        if (!$class) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Class not found!'
            ];
        }

        $nameParts = $this->parseUserName($user->name);

        $params = [
            'transaction_details' => [
                'order_id' => Carbon::now()->format('U') . '$' . $class->id_class,
                'gross_amount' => $price,
            ],
            'customer_details' => [
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'email' => $user->email,
                'phone' => $user->phone_number,
            ],
            'item_details' => [
                [
                    'id' => Str::uuid(),
                    'price' => $price,
                    'quantity' => 1,
                    'name' => $class->title,
                ],
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return [
                'success' => true,
                'token' => $snapToken
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ];
        }
    }

        public function handlePayment(int $userId, array $paymentData): array
    {
        $orderId = $paymentData['order_id'] ?? '';
        $orderParts = explode('$', $orderId);

        if (count($orderParts) < 2) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Invalid order ID format'
            ];
        }

        if (!ctype_digit($orderParts[0]) || !ctype_digit($orderParts[1])) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Invalid order ID format'
            ];
        }

        $classId = (int) $orderParts[1];

        // SECURITY: an order can only ever unlock one purchase.
        $alreadyUsed = UserPurchase::where('id_order', (int) $orderParts[0])
            ->where(function ($q) use ($userId, $classId) {
                $q->where('id_user', '!=', $userId)->orWhere('id_class', '!=', $classId);
            })
            ->exists();

        if ($alreadyUsed) {
            return [
                'success' => false,
                'code' => 409,
                'message' => 'This order has already been used'
            ];
        }

        // SECURITY: never trust the client's claim that a payment succeeded.
        // Ask Midtrans (server-to-server) for the real transaction status.
        $verification = $this->verifyTransaction($orderId);

        if (!$verification['success']) {
            return $verification;
        }

        $status = $verification['status'];
        $datetime = Carbon::parse($status->transaction_time ?? now())->format('U');

        $updateData = [
            'id_order' => (int) $orderParts[0],
            'payment_method' => (string) ($status->payment_type ?? ''),
            'value' => (float) ($status->gross_amount ?? 0),
            'expired_at' => Carbon::now()->addDays(1)->format('U'),
            'created_at' => $datetime,
            'updated_at' => $datetime
        ];

        $updated = UserPurchase::where('id_user', $userId)
            ->where('id_class', $classId)
            ->update($updateData);

        if (!$updated) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Something went wrong :/'
            ];
        }

        return [
            'success' => true,
            'class_id' => $classId
        ];
    }

    /**
     * Verify a transaction directly with Midtrans.
     * Only "settlement", or "capture" with fraud_status "accept", counts as paid.
     */
    public function verifyTransaction(string $orderId): array
    {
        if (empty(config('services.midtrans.server_key'))) {
            return [
                'success' => false,
                'code' => 503,
                'message' => 'Payment gateway is not configured'
            ];
        }

        try {
            $status = $this->fetchTransactionStatus($orderId);
        } catch (\Throwable $e) {
            report($e);

            return [
                'success' => false,
                'code' => 402,
                'message' => 'Unable to verify payment'
            ];
        }

        $transactionStatus = $status->transaction_status ?? null;
        $fraudStatus = $status->fraud_status ?? null;
        $paid = $transactionStatus === 'settlement'
            || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

        if (!$paid || (string) ($status->order_id ?? '') !== $orderId) {
            return [
                'success' => false,
                'code' => 402,
                'message' => 'Payment has not been completed'
            ];
        }

        return [
            'success' => true,
            'status' => $status
        ];
    }

    /**
     * Server-to-server status lookup (separate method so tests can stub it).
     */
    protected function fetchTransactionStatus(string $orderId): object
    {
        return (object) Transaction::status($orderId);
    }

        private function parseUserName(string $fullName): array
    {
        $parts = explode(' ', $fullName);
        $firstName = $parts[0];
        $lastName = '';

        if (count($parts) > 1) {
            $lastName = implode(' ', array_slice($parts, 1));
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
    }
}
