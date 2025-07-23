<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');
    }

    public function analyzeTicket($ticketDescription)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Eres un experto en soporte técnico y resolución de problemas. Analiza el siguiente problema técnico y proporciona posibles soluciones paso a paso. Sigue este formato:

                        1. Diagnóstico del problema
                        - Puntos clave identificados
                        - Posibles causas

                        2. Soluciones propuestas
                        - Solución 1 (más probable)
                            * Pasos detallados
                            * Consideraciones
                        - Solución 2 (alternativa)
                            * Pasos detallados
                            * Consideraciones

                        3. Prevención
                        - Recomendaciones para evitar el problema en el futuro
                        - Buenas prácticas

                        Problema a analizar: " . $ticketDescription
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $response->json()['content'][0]['text'];
            }

            Log::error('Error en la llamada a Claude', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error al analizar ticket con Claude: ' . $e->getMessage());
            return null;
        }
    }
}
