<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\SchoolClass;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    public function index(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $quizzes = Quiz::query()
            ->where('class_id', $class->id)
            ->latest()
            ->paginate(10);

        return view('teacher.quizzes.index', compact('class', 'quizzes'));
    }

    public function create(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        return view('teacher.quizzes.create', compact('class'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'available_from' => ['required', 'date', 'after_or_equal:now'],
            'due_at' => ['required', 'date', 'after:available_from'],
        ]);

        $quiz = Quiz::create([
            'class_id' => $class->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'available_from' => $validated['available_from'],
            'due_at' => $validated['due_at'],
        ]);

        Activity::log($request->user(), 'teacher.quiz.created', [
            'class_id' => $class->id,
            'quiz_id' => $quiz->id,
        ]);

        return redirect()->route('teacher.quizzes.show', [$class, $quiz]);
    }

    public function show(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $quiz->load(['questions' => fn ($q) => $q->orderBy('order')->orderBy('id')]);

        return view('teacher.quizzes.show', compact('class', 'quiz'));
    }

    public function publish(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $count = (int) $quiz->questions()->count();
        if ($count < 1) {
            throw ValidationException::withMessages([
                'questions' => 'Add at least 1 question before publishing.',
            ]);
        }

        if (!$quiz->is_published) {
            $quiz->forceFill(['is_published' => true])->save();

            Activity::log($request->user(), 'teacher.quiz.published', [
                'class_id' => $class->id,
                'quiz_id' => $quiz->id,
            ]);
        }

        return redirect()->route('teacher.quizzes.index', $class);
    }

    public function destroy(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        // Delete all quiz questions and submissions
        $quiz->questions()->delete();
        $quiz->submissions()->delete();
        $quiz->delete();

        Activity::log($request->user(), 'teacher.quiz.deleted', [
            'class_id' => $class->id,
            'quiz_id' => $quiz->id,
        ]);

        return redirect()->route('teacher.quizzes.index', $class)
            ->with('success', 'Quiz deleted successfully.');
    }
}
