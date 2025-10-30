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
            Log::info('Enviando arquivo para API Python', [
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'url' => "{$this->baseUrl}/api/v1/brasil"
            ]);
            
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])
            ->timeout($this->timeout)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->baseUrl}/api/v1/brasil", $options);

            Log::info('Resposta da API Python', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            // Verifica se a resposta é vazia
            if (empty($response->body())) {
                throw new Exception("API Python retornou resposta vazia");
            }

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMessage = $errorBody['detail'] ?? $response->body();
                throw new Exception("Erro na API Python: " . $errorMessage);
            }

            $data = $response->json();
            
            // Verifica se retornou dados
            if (empty($data)) {
                throw new Exception("API Python não retornou dados");
            }

            return $data;
            
        } catch (Exception $e) {
            Log::error('Erro ao processar arquivo na API Python', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}