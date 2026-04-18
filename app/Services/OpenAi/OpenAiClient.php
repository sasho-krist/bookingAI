<?php

namespace App\Services\OpenAi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiClient
{
    public function chatCompletion(array $messages, ?array $responseFormat = null): string
    {
        $key = config('openai.api_key');
        if (! is_string($key) || $key === '') {
            throw new RuntimeException('Missing OPENAI_API_KEY in environment.');
        }

        $payload = [
            'model' => config('openai.model'),
            'messages' => $messages,
        ];

        if ($responseFormat !== null) {
            $payload['response_format'] = $responseFormat;
        }

        $org = config('openai.organization');
        $url = rtrim((string) config('openai.base_url'), '/').'/chat/completions';

        $request = Http::withToken($key)
            ->acceptJson()
            ->timeout(120)
            ->asJson();

        $caFile = config('openai.ca_file');
        if (is_string($caFile) && $caFile !== '') {
            $request = $request->withOptions(['verify' => $caFile]);
        } else {
            $request = $request->withOptions(['verify' => (bool) config('openai.verify_ssl', true)]);
        }

        if (is_string($org) && $org !== '') {
            $request = $request->withHeaders(['OpenAI-Organization' => $org]);
        }

        try {
            $response = $request->post($url, $payload);
            $response->throw();
        } catch (ConnectionException $e) {
            throw new RuntimeException('OpenAI connection failed: '.$e->getMessage(), 0, $e);
        } catch (RequestException $e) {
            $response = $e->response;
            $body = $response?->json();
            $message = is_array($body) ? ($body['error']['message'] ?? $response?->body() ?? '') : ($response?->body() ?? '');
            throw new RuntimeException('OpenAI request failed: '.$message, 0, $e);
        }

        $content = $response->json('choices.0.message.content');
        if (! is_string($content)) {
            throw new RuntimeException('Unexpected OpenAI response shape.');
        }

        return $content;
    }
}
