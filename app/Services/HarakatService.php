<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HarakatService
{
    /**
     * Maximum allowed character length for Arabic vocalization text.
     */
    protected const MAX_TEXT_LENGTH = 1000;

    /**
     * Cache duration in seconds (7 days).
     */
    protected const CACHE_TTL = 604800;

    /**
     * Vocalize Arabic text and provide I'rab breakdown using configured providers.
     *
     * @param string $text Raw Arabic text
     * @param string $lang Target language ('Indonesian' or 'English')
     * @return array Array with 'text' and 'aroef'
     * @throws Exception
     */
    public function vocalize(string $text, string $lang = 'Indonesian'): array
    {
        $text = trim($text);

        if (empty($text)) {
            throw new Exception('Teks bahasa Arab tidak boleh kosong.');
        }

        if (mb_strlen($text) > self::MAX_TEXT_LENGTH) {
            throw new Exception('Panjang teks melebihi batas maksimal ' . self::MAX_TEXT_LENGTH . ' karakter.');
        }

        $cacheKey = 'harakat_vocalize:' . md5($lang . ':' . $text);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $geminiKey = config('services.gemini.api_key');
        $groqKey = config('services.groq.api_key');
        $openaiKey = config('services.openai.api_key');

        if (empty($geminiKey) && empty($groqKey) && empty($openaiKey)) {
            throw new Exception('API Key AI (GEMINI_API_KEY atau GROQ_API_KEY) belum dikonfigurasi di file .env.');
        }

        $data = null;
        $errors = [];

        // 1. Try Groq if configured
        if (!empty($groqKey)) {
            try {
                $data = $this->callGroq($text, $lang, $groqKey);
            } catch (Exception $e) {
                Log::warning('Groq Harakat failed: ' . $e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        // 2. Try Gemini with automatic multi-model fallback on demand spikes
        if (!$data && !empty($geminiKey)) {
            try {
                $data = $this->callGemini($text, $lang, $geminiKey);
            } catch (Exception $e) {
                Log::warning('Gemini Harakat failed: ' . $e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        // 3. Try OpenAI if configured
        if (!$data && !empty($openaiKey)) {
            try {
                $data = $this->callOpenAI($text, $lang, $openaiKey);
            } catch (Exception $e) {
                Log::warning('OpenAI Harakat failed: ' . $e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        if (!$data) {
            throw new Exception(implode(' | ', $errors) ?: 'Gagal memproses harakat. Silakan coba lagi.');
        }

        // Cache successful result
        Cache::put($cacheKey, $data, self::CACHE_TTL);

        return $data;
    }

    /**
     * Call Google Gemini API with automatic model fallback cascading.
     */
    protected function callGemini(string $text, string $lang, string $apiKey): array
    {
        $systemInstruction = "You are an expert Arabic grammarian and linguist specializing in Nahwu, Sharaf, and I'rab. "
            . "Your task is to take unvocalized or partially vocalized Arabic text, apply accurate full Tashkeel (harakat: fatḥah, kasrah, ḍammah, sukūn, shaddah, tanwīn), "
            . "and provide a word-by-word grammatical I'rab breakdown. "
            . "Language for explanation and translation: {$lang}. "
            . "Strictly output a valid JSON object matching the requested schema.";

        $prompt = "Analyze and vocalize this Arabic text:\n\"{$text}\"\n\n"
            . "Respond strictly with JSON schema:\n"
            . "{\n"
            . "  \"text\": \"Full sentence with complete and accurate harakat\",\n"
            . "  \"aroef\": [\n"
            . "    {\n"
            . "      \"arab\": \"Word with harakat\",\n"
            . "      \"jenis\": \"Isim / Fi'il / Huruf\",\n"
            . "      \"hukum\": \"Marfu' / Manshub / Majrur / Majzum / Mabni\",\n"
            . "      \"tanda\": \"Dhammah / Fathah / Kasrah / Sukun / dsb\",\n"
            . "      \"penjelasan\": \"Penjelasan posisi dan kaidah i'rab dalam bahasa {$lang}\",\n"
            . "      \"arti\": \"Arti kata dalam bahasa {$lang}\"\n"
            . "    }\n"
            . "  ]\n"
            . "}";

        // Cascade list of models to try in order
        $models = array_values(array_unique(array_filter([
            config('services.gemini.model', 'gemini-3.5-flash-lite'),
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
        ])));

        $lastException = null;

        foreach ($models as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            try {
                $response = Http::timeout(25)->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $systemInstruction . "\n\n" . $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.1,
                    ]
                ]);

                // High demand spike or rate limit on this model -> try next fallback model immediately
                if ($response->status() === 503 || $response->status() === 429) {
                    Log::warning("Gemini model {$model} unavailable ({$response->status()}), trying fallback model...");
                    continue;
                }

                if ($response->failed()) {
                    $errorData = $response->json();
                    $errorMessage = $errorData['error']['message'] ?? ('HTTP error ' . $response->status());

                    if (str_contains($errorMessage, 'free tier is not available for this project')) {
                        throw new Exception("Google Cloud Project ini tidak mengizinkan Free Tier. Gunakan akun Gmail pribadi (@gmail.com) di aistudio.google.com atau aktifkan billing.");
                    }

                    if (str_contains(strtolower($errorMessage), 'demand') || str_contains(strtolower($errorMessage), 'unavailable') || str_contains(strtolower($errorMessage), 'busy')) {
                        Log::warning("Gemini model {$model} high demand, trying fallback model...");
                        continue;
                    }

                    throw new Exception($errorMessage);
                }

                $result = $response->json();
                $rawContent = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                return $this->parseJsonOutput($rawContent);
            } catch (Exception $e) {
                $lastException = $e;
                if (str_contains($e->getMessage(), 'Google Cloud Project')) {
                    throw $e;
                }
                Log::warning("Gemini model {$model} exception: " . $e->getMessage() . ", trying fallback model...");
            }
        }

        if ($lastException) {
            if (str_contains(strtolower($lastException->getMessage()), 'demand') || str_contains(strtolower($lastException->getMessage()), 'unavailable')) {
                throw new Exception("Layanan sedang mengalami lonjakan pengguna sementara. Silakan coba klik tombol Beri Harakat beberapa saat lagi.");
            }
            throw $lastException;
        }

        throw new Exception("Layanan sedang tidak dapat diakses saat ini. Silakan coba lagi nanti.");
    }

    /**
     * Call Groq Cloud API (Llama 3.3 70B) for ultrafast vocalization.
     */
    protected function callGroq(string $text, string $lang, string $apiKey): array
    {
        $model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        $systemInstruction = "You are an expert Arabic grammarian and linguist specializing in Nahwu, Sharaf, and I'rab. "
            . "Apply accurate full Tashkeel and provide word-by-word I'rab breakdown in {$lang}. Respond strictly in JSON format.";

        $prompt = "Analyze and vocalize this Arabic text:\n\"{$text}\"\n\n"
            . "Respond strictly with JSON schema:\n"
            . "{\n"
            . "  \"text\": \"Full sentence with complete and accurate harakat\",\n"
            . "  \"aroef\": [\n"
            . "    {\n"
            . "      \"arab\": \"Word with harakat\",\n"
            . "      \"jenis\": \"Isim / Fi'il / Huruf\",\n"
            . "      \"hukum\": \"Marfu' / Manshub / Majrur / Majzum / Mabni\",\n"
            . "      \"tanda\": \"Dhammah / Fathah / Kasrah / Sukun / dsb\",\n"
            . "      \"penjelasan\": \"Penjelasan posisi dan kaidah i'rab dalam bahasa {$lang}\",\n"
            . "      \"arti\": \"Arti kata dalam bahasa {$lang}\"\n"
            . "    }\n"
            . "  ]\n"
            . "}";

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->post($endpoint, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemInstruction],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.1,
            ]);

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? ('HTTP error ' . $response->status());
            throw new Exception("Groq Error: {$errorMessage}");
        }

        $result = $response->json();
        $rawContent = $result['choices'][0]['message']['content'] ?? '';

        return $this->parseJsonOutput($rawContent);
    }

    /**
     * Call OpenAI API for vocalization.
     */
    protected function callOpenAI(string $text, string $lang, string $apiKey): array
    {
        $model = config('services.openai.model', 'gpt-4o-mini');
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        $systemInstruction = "You are an expert Arabic grammarian and linguist specializing in Nahwu, Sharaf, and I'rab. "
            . "Apply accurate full Tashkeel and provide word-by-word I'rab breakdown in {$lang}. Respond strictly in JSON format.";

        $prompt = "Analyze and vocalize this Arabic text:\n\"{$text}\"\n\n"
            . "Respond strictly with JSON schema:\n"
            . "{\n"
            . "  \"text\": \"Full sentence with complete and accurate harakat\",\n"
            . "  \"aroef\": [\n"
            . "    {\n"
            . "      \"arab\": \"Word with harakat\",\n"
            . "      \"jenis\": \"Isim / Fi'il / Huruf\",\n"
            . "      \"hukum\": \"Marfu' / Manshub / Majrur / Majzum / Mabni\",\n"
            . "      \"tanda\": \"Dhammah / Fathah / Kasrah / Sukun / dsb\",\n"
            . "      \"penjelasan\": \"Penjelasan posisi dan kaidah i'rab dalam bahasa {$lang}\",\n"
            . "      \"arti\": \"Arti kata dalam bahasa {$lang}\"\n"
            . "    }\n"
            . "  ]\n"
            . "}";

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->post($endpoint, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemInstruction],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.1,
            ]);

        if ($response->failed()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? ('HTTP error ' . $response->status());
            throw new Exception("OpenAI Error: {$errorMessage}");
        }

        $result = $response->json();
        $rawContent = $result['choices'][0]['message']['content'] ?? '';

        return $this->parseJsonOutput($rawContent);
    }

    /**
     * Clean and parse JSON output from LLM.
     *
     * @throws Exception
     */
    protected function parseJsonOutput(string $rawContent): array
    {
        if (empty($rawContent)) {
            throw new Exception('Sistem tidak mengembalikan hasil teks yang valid.');
        }

        $cleanedJson = preg_replace('/^```(?:json)?\s*/i', '', trim($rawContent));
        $cleanedJson = preg_replace('/\s*```$/', '', $cleanedJson);

        $parsed = json_decode($cleanedJson, true);
        if (!is_array($parsed) || !isset($parsed['text'])) {
            Log::error('Invalid JSON structure from service', ['raw' => $rawContent]);
            throw new Exception('Format respons tidak sesuai struktur yang diharapkan.');
        }

        return [
            'text' => (string) ($parsed['text'] ?? ''),
            'aroef' => (array) ($parsed['aroef'] ?? []),
        ];
    }
}
