<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    public function scan(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $base64Image = preg_replace('/^data:image\/(\w+);base64,/', '', $request->image);
        
        $data = $gemini->scanStruk($base64Image);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}