<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextToSpeechController extends Controller
{
    /**
     * Generate speech audio using VoiceRSS API (FREE - No Credit Card Required!)
     */
    public function synthesize(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'language' => 'required|in:en-US,fil-PH'
        ]);

        $apiKey = config('services.voicerss.api_key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'VoiceRSS API key not configured. Get free key at: https://www.voicerss.org/registration.aspx'
            ], 500);
        }

        try {
            // Map language codes
            $langCode = $request->language === 'fil-PH' ? 'fil-ph' : 'en-us';
            
            // Make request to VoiceRSS API
            $response = Http::asForm()->post('https://api.voicerss.org/', [
                'key' => $apiKey,
                'src' => $request->text,
                'hl' => $langCode,
                'c' => 'MP3',           // Audio format
                'f' => '44khz_16bit_stereo', // High quality
                'r' => '-2',            // Speed: -10 (slowest) to 10 (fastest)
                'ssml' => 'false'
            ]);

            if ($response->successful()) {
                // VoiceRSS returns audio file directly (not JSON)
                $audioContent = base64_encode($response->body());
                
                return response()->json([
                    'success' => true,
                    'audioContent' => $audioContent,
                    'voice' => $langCode === 'fil-ph' ? 'Filipino (Female)' : 'English US (Female)',
                    'provider' => 'VoiceRSS'
                ]);
            } else {
                $errorBody = $response->body();
                Log::error('VoiceRSS API Error', [
                    'status' => $response->status(),
                    'body' => $errorBody
                ]);

                // Check for common errors
                if (strpos($errorBody, 'ERROR: The subscription does not support SSML') !== false) {
                    return response()->json(['error' => 'SSML not supported in free tier'], 400);
                } elseif (strpos($errorBody, 'ERROR: The request rate is over') !== false) {
                    return response()->json(['error' => 'Daily limit reached (350 requests/day). Try again tomorrow.'], 429);
                } elseif (strpos($errorBody, 'ERROR: The API key is invalid') !== false) {
                    return response()->json(['error' => 'Invalid API key. Please check your VoiceRSS API key.'], 401);
                }

                return response()->json([
                    'error' => 'Failed to generate speech: ' . $errorBody
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('TTS Exception', ['message' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
