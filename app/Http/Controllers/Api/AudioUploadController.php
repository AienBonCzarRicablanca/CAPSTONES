<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AudioUploadController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'audio' => ['required', 'file', 'max:51200'],
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'Not implemented yet. Will store audio and return URL later.',
            'placeholder' => true,
        ], 501);
    }
}
