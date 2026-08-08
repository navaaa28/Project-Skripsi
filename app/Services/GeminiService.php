<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiService
{
    public function analyze(array $payload): ?array
    {
        $providers = $this->providerOrder();

        foreach ($providers as $provider) {
            $result = match ($provider) {
                'gemini' => $this->analyzeWithGemini($payload),
                'groq' => $this->analyzeWithGroq($payload),
                default => null,
            };

            if ($result !== null) {
                Log::info('AI analysis provider succeeded.', ['provider' => $provider]);
                return $result;
            }

            Log::warning('AI analysis provider failed, trying next provider.', ['provider' => $provider]);
        }

        return null;
    }

    private function analyzeWithGemini(array $payload): ?array
    {
        $apiKey = config('ai.gemini_api_key');
        $model = config('ai.gemini_model');
        $prompt = config('ai.system_prompt');

        if (!$apiKey || !$model || !$prompt) {
            Log::warning('Gemini config missing.');
            return null;
        }

        $text = $prompt . "\n\nINPUT JSON:\n" . json_encode($payload, JSON_UNESCAPED_UNICODE);

        $modelName = str_starts_with($model, 'models/') ? $model : "models/{$model}";
        $url = "https://generativelanguage.googleapis.com/v1beta/{$modelName}:generateContent?key={$apiKey}";

        try {
            $response = Http::connectTimeout((int) config('ai.gemini_connect_timeout', 10))
                ->timeout((int) config('ai.gemini_timeout', 30))
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $text],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'topP' => 0.9,
                        'responseMimeType' => 'application/json',
                    ],
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Gemini API connection timeout', [
                'message' => $exception->getMessage(),
                'model' => $modelName,
            ]);

            return null;
        } catch (RequestException $exception) {
            Log::warning('Gemini API request failed', [
                'message' => $exception->getMessage(),
                'model' => $modelName,
            ]);

            return null;
        } catch (Throwable $exception) {
            Log::error('Gemini API unexpected error', [
                'message' => $exception->getMessage(),
                'model' => $modelName,
            ]);

            return null;
        }

        if (!$response->ok()) {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        $raw = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (!$raw) {
            Log::error('Gemini API empty response', ['body' => $response->json()]);
            return null;
        }

        $decoded = $this->decodeJsonResponse($raw);
        if (!$decoded) {
            Log::error('Gemini API invalid JSON');
            return null;
        }

        Log::info('Gemini API parsed', [
            'keys' => array_keys($decoded),
            'has_minat' => array_key_exists('minat', $decoded),
            'has_bakat' => array_key_exists('bakat', $decoded),
        ]);

        return $decoded;
    }

    private function analyzeWithGroq(array $payload): ?array
    {
        $apiKey = config('ai.groq_api_key');
        $model = config('ai.groq_model');
        $prompt = config('ai.system_prompt');

        if (!$apiKey || !$model || !$prompt) {
            Log::warning('Groq config missing.');
            return null;
        }

        $text = "INPUT JSON:\n" . json_encode($payload, JSON_UNESCAPED_UNICODE);

        try {
            $response = Http::withToken($apiKey)
                ->connectTimeout((int) config('ai.groq_connect_timeout', 10))
                ->timeout((int) config('ai.groq_timeout', 30))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $prompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                    'temperature' => 0.2,
                    'top_p' => 0.9,
                    'response_format' => [
                        'type' => 'json_object',
                    ],
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Groq API connection timeout', [
                'message' => $exception->getMessage(),
                'model' => $model,
            ]);

            return null;
        } catch (RequestException $exception) {
            Log::warning('Groq API request failed', [
                'message' => $exception->getMessage(),
                'model' => $model,
            ]);

            return null;
        } catch (Throwable $exception) {
            Log::error('Groq API unexpected error', [
                'message' => $exception->getMessage(),
                'model' => $model,
            ]);

            return null;
        }

        if (!$response->ok()) {
            Log::error('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        $raw = data_get($response->json(), 'choices.0.message.content');
        if (!$raw) {
            Log::error('Groq API empty response', ['body' => $response->json()]);
            return null;
        }

        $decoded = $this->decodeJsonResponse($raw);
        if (!$decoded) {
            Log::error('Groq API invalid JSON');
            return null;
        }

        Log::info('Groq API parsed', [
            'keys' => array_keys($decoded),
            'has_minat' => array_key_exists('minat', $decoded),
            'has_bakat' => array_key_exists('bakat', $decoded),
        ]);

        return $decoded;
    }

    private function decodeJsonResponse(mixed $raw): ?array
    {
        $raw = trim((string) $raw);
        if (str_contains($raw, '```')) {
            $raw = preg_replace('/^```(json)?|```$/m', '', $raw);
            $raw = trim($raw);
        }

        $decoded = json_decode($raw, true);

        if (!is_array($decoded) || !$this->hasRequiredKeys($decoded)) {
            return null;
        }

        return $decoded;
    }

    private function hasRequiredKeys(array $decoded): bool
    {
        foreach (['minat', 'bakat', 'analisis_tren', 'ringkasan_non_akademik', 'saran_pengembangan', 'tips_peningkatan'] as $key) {
            if (!array_key_exists($key, $decoded)) {
                return false;
            }
        }

        return is_array($decoded['minat']) && is_array($decoded['bakat']);
    }

    private function providerOrder(): array
    {
        $primary = strtolower((string) config('ai.primary_provider', 'gemini'));
        $fallback = strtolower((string) config('ai.fallback_provider', 'groq'));

        return collect([$primary, $fallback])
            ->filter(fn($provider) => in_array($provider, ['gemini', 'groq'], true))
            ->unique()
            ->values()
            ->all();
    }
}
