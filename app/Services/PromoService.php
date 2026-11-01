<?php

namespace App\Services;

use App\Models\Promo;
use Carbon\Carbon;

class PromoService
{
    public function validatePromoCode(string $code): array
    {
        $promo = Promo::select('percentage', 'value', 'expired_at')
            ->where('code', $code)
            ->first();

        if (!$promo) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Promo code not found'
            ];
        }

        if ($promo->expired_at < Carbon::now()->format('U')) {
            return [
                'success' => false,
                'code' => 410,
                'message' => 'Promo code has expired'
            ];
        }

        return [
            'success' => true,
            'type' => $promo->percentage ? 'percent' : 'decimal',
            'value' => $promo->value,
        ];
    }

    public function getPromoDetails(?string $code): array
    {
        if (!$code) {
            return [
                'success' => false,
                'code' => 400,
                'message' => 'Promo code is required'
            ];
        }

        $promo = Promo::select('id_promo', 'percentage', 'value')
            ->where('code', $code)
            ->where('expired_at', '>', Carbon::now()->format('U'))
            ->first();

        if (!$promo) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Promo code not found or expired'
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'id' => $promo->id_promo,
            'type' => $promo->percentage ? 'percent' : 'decimal',
            'value' => $promo->value,
        ];
    }

    public function validateAndApply(string $code, int $originalPrice, int $userId): array
    {
        $result = $this->validatePromoCode($code);

        if (!$result['success']) {
            return [
                'valid' => false,
                'final_price' => $originalPrice,
                'discount' => 0,
                'message' => $result['message']
            ];
        }

        $finalPrice = $originalPrice;
        $discount = 0;

        if ($result['type'] === 'percent') {
            // Percentage discount
            $discount = (int) (($originalPrice * $result['value']) / 100);
            $finalPrice = $originalPrice - $discount;
        } else {
            // Fixed amount discount
            $discount = min($result['value'], $originalPrice); // Don't exceed original price
            $finalPrice = $originalPrice - $discount;
        }

        // Ensure price doesn't go below minimum (1)
        $finalPrice = max(1, $finalPrice);

        return [
            'valid' => true,
            'final_price' => $finalPrice,
            'discount' => $discount,
            'original_price' => $originalPrice,
            'message' => 'Promo code applied successfully'
        ];
    }
}
