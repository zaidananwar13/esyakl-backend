<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\HarakatService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HarakatController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Harakat';

    protected HarakatService $harakatService;

    public function __construct(HarakatService $harakatService)
    {
        $this->harakatService = $harakatService;
    }

    /**
     * Vocalize Arabic text with complete harakat and I'rab breakdown.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function vocalize(Request $request): JsonResponse
    {
        $text = $request->input('text') ?? $request->input('arab') ?? '';
        $lang = $request->input('lang', 'Indonesian');

        if (empty(trim($text))) {
            return response()->json([
                'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | ' . self::API_NAME . ' API',
                'code' => 422,
                'status' => 422,
                'message' => 'Parameter teks bahasa Arab wajib diisi.',
                'text' => '',
                'aroef' => [],
                'data' => [
                    'text' => '',
                    'aroef' => []
                ]
            ], 422);
        }

        try {
            $result = $this->harakatService->vocalize($text, $lang);

            return response()->json([
                'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | ' . self::API_NAME . ' API',
                'code' => 200,
                'status' => 200,
                'message' => 'Pemberian harakat dan i\'rab berhasil diproses.',
                'text' => $result['text'],
                'aroef' => $result['aroef'],
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('HarakatController error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $rawMessage = $e->getMessage();
            $isTechnical = preg_match('/gemini|groq|openai|google|curl|http|token|key|exception|status|tier|demand|quota|\bai\b|error/i', $rawMessage);

            if ($isTechnical) {
                $userFriendlyMessage = ($lang === 'English')
                    ? 'Unable to process vocalization at the moment. Please try again later.'
                    : 'Gagal memproses harakat. Silakan coba beberapa saat lagi.';
            } else {
                $userFriendlyMessage = $rawMessage;
            }

            return response()->json([
                'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | ' . self::API_NAME . ' API',
                'code' => 500,
                'status' => 500,
                'message' => $userFriendlyMessage,
                'text' => '',
                'aroef' => [],
                'data' => [
                    'text' => '',
                    'aroef' => []
                ]
            ], 500);
        }
    }

    /**
     * Backward-compatibility handler for legacy GET/POST /api/get/all/{text?} requests.
     *
     * @param Request $request
     * @param string|null $text
     * @return JsonResponse
     */
    public function vocalizeLegacy(Request $request, ?string $text = null): JsonResponse
    {
        $inputText = $text ?? $request->input('text') ?? $request->input('arab') ?? '';
        $request->merge(['text' => $inputText]);

        return $this->vocalize($request);
    }
}
