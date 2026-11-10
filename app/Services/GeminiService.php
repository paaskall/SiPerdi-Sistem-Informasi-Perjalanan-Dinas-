<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? '';
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    public function scanStruk(string $filePath, string $mimeType = 'image/jpeg'): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('API Key Gemini belum disetting di .env');
        }

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception('File struk tidak ditemukan atau tidak bisa dibaca.');
        }

        $imageData = base64_encode(file_get_contents($filePath));

        $prompt = <<<PROMPT
Anda adalah sistem OCR cerdas untuk membaca struk/bukti pembayaran di Indonesia.
Tugas Anda adalah mengekstrak informasi dari gambar struk yang diberikan.

Kembalikan HANYA format JSON yang valid (tanpa teks pembuka, penutup, atau markdown ```json).
Format JSON harus seperti ini:
{
  "nominal": 150000,
  "merchant": "Nama Toko",
  "tanggal": "2024-05-20"
}

Aturan:
1. "nominal" harus berupa ANGKA BULAT (integer). Jangan pakai titik, koma, atau "Rp". Jika ada desimal, abaikan.
2. "merchant" adalah nama toko/vendor. Jika tidak ada, isi "Tidak Diketahui".
3. "tanggal" format YYYY-MM-DD. Jika tidak ada, isi null.
4. JIKA gambar BUKAN struk atau nominal tidak bisa dibaca, kembalikan: {"nominal": 0, "merchant": "Gagal Membaca", "tanggal": null}
PROMPT;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'responseMimeType' => 'application/json'
            ]
        ];

        try {
            $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;
            $response = Http::timeout(30)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                throw new Exception('Koneksi ke Gemini API gagal. Cek kuota atau API Key.');
            }

            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($textResponse)) {
                throw new Exception('Gemini mengembalikan respon kosong.');
            }

            $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($textResponse));
            $data = json_decode(trim($cleanJson), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gagal parse JSON: ' . $textResponse);
                throw new Exception('Format balasan dari Gemini tidak valid.');
            }

            if (!isset($data['nominal']) || $data['nominal'] <= 0) {
                throw new Exception('Tidak dapat mendeteksi nominal pada struk. Silakan input manual.');
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new Exception('Koneksi ke server Gemini timeout. Coba lagi.');
        } catch (Exception $e) {
            throw $e; // Lempar error ke Livewire
        }
    }
}