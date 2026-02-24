<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Models\ReadingActivity;
use App\Support\Activity;
use Illuminate\Http\Request;

class ReadingActivityController extends Controller
{
    public function store(Request $request, LibraryItem $item)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:20'],
            'answers' => ['nullable', 'array'],
        ]);

        // Placeholder scoring: store answers, score left null (future AI/teacher scoring)
        ReadingActivity::create([
            'library_item_id' => $item->id,
            'student_id' => $request->user()->id,
            'type' => strtoupper($validated['type']),
            'answers' => $validated['answers'] ?? [],
            'completed_at' => now(),
        ]);

        Activity::log($request->user(), 'student.library.activity_submitted', [
            'library_item_id' => $item->id,
            'type' => strtoupper($validated['type']),
        ]);

        return back()->with('status', 'Activity submitted.');
    }
}
