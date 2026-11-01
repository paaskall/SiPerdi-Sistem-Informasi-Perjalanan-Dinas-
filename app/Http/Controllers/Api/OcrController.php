<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OcrController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // Base64 string
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-3.8-flash');
        
        // URL Endpoint Gemini API
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Bersihkan prefix base64 jika ada (misal: data:image/jpeg;base64,...)
        $base64Image = preg_replace('/^data:image\/(\w+);base64,/', '', $request->image);

        // Prompt instruksi ke Gemini
        $prompt = "Anda adalah sistem OCR cerdas untuk struk belanja Indonesia. 
        Baca gambar struk ini dan cari NOMINAL TOTAL yang harus dibayar. 
        Abaikan angka lain seperti uang tunai, kembalian, atau nomor telepon.
        Berikan jawaban HANYA dalam format JSON seperti ini: {\"total\": 150000, \"merchant\": \"Nama Toko\", \"tanggal\": \"2026-10-31\"}. 
        Jika tidak menemukan total, kembalikan {\"total\": 0}.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => 'image/jpeg',
                                    'data' => $base64Image,
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json', // Paksa output JSON
                ]
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Gagal memproses gambar.'], 500);
            }

            $result = $response->json();
            
            // Ambil teks balasan dari Gemini
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Parse JSON string menjadi array
            $data = json_decode($textResponse, true);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}