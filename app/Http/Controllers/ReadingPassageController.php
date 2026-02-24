<?php

namespace App\Http\Controllers;

use App\Models\ReadingPassage;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;

class ReadingPassageController extends Controller
{
    /**
     * Display listing of reading passages (teacher view)
     */
    public function index(Request $request)
    {
        $query = ReadingPassage::query()
            ->with('category', 'assessments')
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        // Filter by language
        if ($request->filled('language')) {
            $query->language($request->language);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->difficulty($request->difficulty);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $passages = $query->paginate(15);

        return view('teacher.passages.index', compact('passages'));
    }

    /**
     * Show form to create new passage
     */
    public function create()
    {
        $categories = LibraryCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('teacher.passages.create', compact('categories'));
    }

    /**
     * Store newly created passage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'language' => 'required|in:English,Tagalog',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'category_id' => 'nullable|exists:library_categories,id',
            'expected_wpm' => 'nullable|integer|min:30|max:200',
            'time_limit_seconds' => 'nullable|integer|min:30',
            'comprehension_questions' => 'nullable|json',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        // Auto-calculate word count
        $validated['word_count'] = ReadingPassage::calculateWordCount($validated['content']);

        // Set defaults
        $validated['expected_wpm'] = $validated['expected_wpm'] ?? 60;
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $passage = ReadingPassage::create($validated);

        return redirect()
            ->route('teacher.passages.show', $passage)
            ->with('success', 'Reading passage created successfully!');
    }

    /**
     * Display specific passage
     */
    public function show(ReadingPassage $passage)
    {
        $passage->load(['category', 'assessments' => function($query) {
            $query->completed()->latest()->limit(10);
        }]);

        // Calculate statistics
        $stats = [
            'total_attempts' => $passage->assessments()->completed()->count(),
            'avg_accuracy' => $passage->assessments()->completed()->avg('accuracy_score') ?? 0,
            'avg_wpm' => $passage->assessments()->completed()->avg('words_per_minute') ?? 0,
            'avg_fluency' => $passage->assessments()->completed()->avg('fluency_score') ?? 0
        ];

        return view('teacher.passages.show', compact('passage', 'stats'));
    }

    /**
     * Show form to edit passage
     */
    public function edit(ReadingPassage $passage)
    {
        $categories = LibraryCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('teacher.passages.edit', compact('passage', 'categories'));
    }

    /**
     * Update passage
     */
    public function update(Request $request, ReadingPassage $passage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'language' => 'required|in:English,Tagalog',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'category_id' => 'nullable|exists:library_categories,id',
            'expected_wpm' => 'nullable|integer|min:30|max:200',
            'time_limit_seconds' => 'nullable|integer|min:30',
            'comprehension_questions' => 'nullable|json',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0'
        ]);

        // Recalculate word count if content changed
        if ($validated['content'] !== $passage->content) {
            $validated['word_count'] = ReadingPassage::calculateWordCount($validated['content']);
        }

        // Update is_active
        $validated['is_active'] = $request->has('is_active');

        $passage->update($validated);

        return redirect()
            ->route('teacher.passages.show', $passage)
            ->with('success', 'Reading passage updated successfully!');
    }

    /**
     * Delete passage
     */
    public function destroy(ReadingPassage $passage)
    {
        // Check if passage has assessments
        $assessmentCount = $passage->assessments()->count();
        
        if ($assessmentCount > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete passage with {$assessmentCount} assessment(s). Archive it instead.");
        }

        $passage->delete();

        return redirect()
            ->route('teacher.passages.index')
            ->with('success', 'Reading passage deleted successfully!');
    }

    /**
     * Toggle passage active status
     */
    public function toggleStatus(ReadingPassage $passage)
    {
        $passage->update([
            'is_active' => !$passage->is_active
        ]);

        $status = $passage->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Passage {$status} successfully!");
    }

    /**
     * Duplicate passage
     */
    public function duplicate(ReadingPassage $passage)
    {
        $newPassage = $passage->replicate();
        $newPassage->title = $passage->title . ' (Copy)';
        $newPassage->is_active = false;
        $newPassage->save();

        return redirect()
            ->route('teacher.passages.edit', $newPassage)
            ->with('success', 'Passage duplicated! Edit and activate when ready.');
    }
}
