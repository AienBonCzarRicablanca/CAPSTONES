<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReadingAnalyzeController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'text' => ['nullable', 'string'],
            'library_item_id' => ['nullable', 'integer'],
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'Not implemented yet. Connect Python/ML later.',
            'placeholder' => true,
        ], 501);
    }
}
