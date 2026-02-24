<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $classes = $request->user()->enrolledClasses()
            ->with('teacher')
            ->withCount(['assignments', 'quizzes'])
            ->latest('class_student.created_at')
            ->paginate(10);

        return view('student.progress.index', compact('classes'));
    }

    public function show(Request $request, SchoolClass $class)
    {
        $student = $request->user();
        $enrolled = $student->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $assignments = Assignment::query()
            ->where('class_id', $class->id)
            ->with(['submissions' => fn ($q) => $q->where('student_id', $student->id)])
            ->latest()
            ->get();

        $quizzes = Quiz::query()
            ->where('class_id', $class->id)
            ->with(['questions' => fn ($q) => $q->orderBy('order')->orderBy('id')])
            ->latest()
            ->get();

        $answers = QuizAnswer::query()
            ->where('student_id', $student->id)
            ->whereIn('quiz_question_id', $quizzes->flatMap(fn ($q) => $q->questions->pluck('id')))
            ->get()
            ->keyBy('quiz_question_id');

        return view('student.progress.class', compact('class', 'assignments', 'quizzes', 'answers'));
    }
}
