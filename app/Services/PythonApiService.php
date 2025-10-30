<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonApiService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.python_api.url');
        $this->apiKey = config('services.python_api.key');
        $this->timeout = 300;
    }

    private function getHeaders(): array
    {
        return [
            'X-API-Key' => $this->apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Python API health check falhou', ['error' => $e->getMessage()]);
            return false;
        }
    }
}