<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function analyze(array $payload): ?array
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
        $response = Http::timeout(30)->post($url, [
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

        if (!$response->ok()) {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        $raw = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (!$raw) {
            Log::error('Gemini API empty response', ['body' => $response->json()]);
            return null;
        }

        $raw = trim((string) $raw);
        if (str_contains($raw, '```')) {
            $raw = preg_replace('/^```(json)?|```$/m', '', $raw);
            $raw = trim($raw);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            Log::error('Gemini API invalid JSON', ['raw' => $raw]);
            return null;
        }

        Log::info('Gemini API parsed', [
            'keys' => array_keys($decoded),
            'has_minat' => array_key_exists('minat', $decoded),
            'has_bakat' => array_key_exists('bakat', $decoded),
        ]);

        return $decoded;
    }
}
