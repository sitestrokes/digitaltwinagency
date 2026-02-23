<?php

namespace App\Services;

class OpenAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.openai.com/v1';
    protected int    $timeout = 45;
    protected array  $lastUsage = [];

    public function __construct(string $apiKey, string $model = 'gpt-4.1-nano')
    {
        $this->apiKey = $apiKey;
        $this->model  = $model;
    }

    /**
     * Validate API key by listing models endpoint
     */
    public function validateKey(): bool
    {
        try {
            $response = $this->curlGet('/models');
            return isset($response['data']) && is_array($response['data']);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Generate a proposal using OpenAI chat completions
     */
    public function generateProposal(array $params): string
    {
        $messages = $this->buildProposalMessages($params);

        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'max_tokens'  => 1500,
            'temperature' => 0.7,
        ];

        $response = $this->curlPost('/chat/completions', $payload);

        if (isset($response['error'])) {
            $errorMsg  = $response['error']['message'] ?? 'Unknown OpenAI error';
            $errorCode = $response['error']['code'] ?? '';

            if ($errorCode === 'invalid_api_key') {
                throw new \RuntimeException('invalid_api_key: Your OpenAI API key is invalid.');
            }
            if (str_contains($errorMsg, 'quota')) {
                throw new \RuntimeException('quota_exceeded: Your OpenAI quota has been exceeded.');
            }
            if (str_contains($errorMsg, 'rate')) {
                throw new \RuntimeException('rate_limit: Rate limit reached. Please wait a moment.');
            }

            throw new \RuntimeException('openai_error: ' . $errorMsg);
        }

        $content = $response['choices'][0]['message']['content'] ?? '';
        if (empty($content)) {
            throw new \RuntimeException('empty_response: OpenAI returned an empty response.');
        }

        if (isset($response['usage'])) {
            $this->lastUsage = $response['usage'];
        }

        return $content;
    }

    public function getLastUsage(): array
    {
        return $this->lastUsage;
    }

    protected function buildProposalMessages(array $p): array
    {
        $servicesList = is_array($p['services'] ?? null)
            ? implode(', ', $p['services'])
            : ($p['services'] ?? 'AI Digital Twin, Social Videos, Website Greeter');

        $painPointsList = is_array($p['pain_points'] ?? null) && count($p['pain_points'])
            ? implode("\n- ", $p['pain_points'])
            : 'No specific pain points noted';

        return [
            [
                'role'    => 'system',
                'content' => 'You are a top-tier digital agency proposal writer specializing in AI Digital Twin services for local businesses. Write professional, persuasive proposals that convert prospects into clients. Respond with clean HTML only — no markdown code blocks, no backticks, no explanations outside the HTML. Use only these HTML tags: h2, h3, p, ul, li, strong, em, table, thead, tbody, tr, th, td, div, br. No inline styles.',
            ],
            [
                'role'    => 'user',
                'content' => "Write a professional Digital Twin agency service proposal with these details:

Agency Name: {$p['agency_name']}
Client Business: {$p['client_name']}
Industry/Niche: {$p['niche']}
Contact Person: {$p['contact_name']}
Recommended Package: {$p['tier']} at \${$p['price']}/month
Services Included: {$servicesList}
Known Client Pain Points:
- {$painPointsList}
Special Notes: " . ($p['notes'] ?: 'None') . "

Structure the proposal with these exact sections:
1. Opening paragraph: Dear {$p['contact_name']}, (acknowledge the opportunity, mention {$p['client_name']} and {$p['niche']})
2. <h3>The Challenge</h3> (2-3 sentences about pain points specific to {$p['niche']} businesses — no video content, staff won't go on camera, competitors using video)
3. <h3>Our Solution: AI Digital Twins</h3> (explain how an AI avatar creates unlimited video content without anyone on camera, fraction of traditional cost)
4. <h3>{$p['tier']} Package — What's Included</h3> (HTML table: Service column | Status column, each service row marked '✓ Included')
5. <h3>Your Investment</h3> (state \${$p['price']}/month, compare to traditional video production cost \$2,000-\$10,000 per video, show ROI)
6. <h3>Next Steps</h3> (numbered list: 1. 15-min onboarding call → 2. Build your Digital Twin in 3-5 days → 3. Launch and deliver content)
7. Closing (professional close, valid 14 days, signed by {$p['agency_name']})

Write 400-600 words total. Make it specific to {$p['niche']}, compelling, and action-oriented.",
            ],
        ];
    }

    protected function curlPost(string $endpoint, array $payload): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body  = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            throw new \RuntimeException('curl_error: Network error connecting to OpenAI — ' . $error);
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('parse_error: Could not parse OpenAI response.');
        }

        return $decoded;
    }

    protected function curlGet(string $endpoint): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body  = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($errno) {
            throw new \RuntimeException('curl_error: Network error connecting to OpenAI.');
        }

        return json_decode($body, true) ?? [];
    }
}
