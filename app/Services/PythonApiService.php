<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Exception;

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

    public function processarArquivo(UploadedFile $file, array $options = []): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
            ->timeout($this->timeout)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->baseUrl}/processarArqu", $options);

            if ($response->failed()) {
                throw new Exception("Erro na API Python: " . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Erro ao processar arquivo na API Python', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw $e;
        }
    }
}