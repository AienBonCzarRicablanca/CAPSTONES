<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Models\Submission;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::query()
            ->where('teacher_id', $request->user()->id)
            ->withCount(['students', 'assignments', 'quizzes'])
            ->latest()
            ->paginate(10);

        return view('teacher.progress.index', compact('classes'));
    }

    public function show(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $class->load(['students' => fn ($q) => $q->orderBy('name')]);

        $assignmentIds = $class->assignments()->pluck('id');
        $assignmentsTotal = $assignmentIds->count();
        $submissionsByStudent = Submission::query()
            ->whereIn('assignment_id', $assignmentIds)
            ->select(['student_id', 'assignment_id'])
            ->get()
            ->groupBy('student_id')
            ->map(fn ($rows) => $rows->unique('assignment_id')->count());

        $quizIds = $class->quizzes()->pluck('id');
        $questions = QuizQuestion::query()
            ->whereIn('quiz_id', $quizIds)
            ->get(['id', 'points']);

        $quizMaxPoints = (int) $questions->sum('points');
        $questionIds = $questions->pluck('id');

        $quizPointsByStudent = QuizAnswer::query()
            ->whereIn('quiz_question_id', $questionIds)
            ->select(['student_id', 'awarded_points'])
            ->get()
            ->groupBy('student_id')
            ->map(fn ($rows) => (int) $rows->sum(fn ($r) => (int) ($r->awarded_points ?? 0)));

        return view('teacher.progress.class', compact(
            'class',
            'assignmentsTotal',
            'submissionsByStudent',
            'quizMaxPoints',
            'quizPointsByStudent'
        ));
    }
}
