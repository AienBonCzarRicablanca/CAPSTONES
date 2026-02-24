<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class QuizResultController extends Controller
{
    public function show(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $students = $class->students()->orderBy('name')->get();

        $questions = QuizQuestion::query()
            ->where('quiz_id', $quiz->id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $maxPoints = (int) $questions->sum('points');

        $answers = QuizAnswer::query()
            ->whereIn('quiz_question_id', $questions->pluck('id'))
            ->whereIn('student_id', $students->pluck('id'))
            ->get();

        $answersByStudent = $answers
            ->groupBy('student_id')
            ->map(fn ($rows) => $rows->keyBy('quiz_question_id'));

        $scoresByStudent = $students->mapWithKeys(function ($student) use ($answersByStudent) {
            $rows = $answersByStudent->get($student->id, collect());
            $score = (int) $rows->sum(fn ($a) => (int) ($a->awarded_points ?? 0));
            return [$student->id => $score];
        });

        return view('teacher.quizzes.results', compact(
            'class',
            'quiz',
            'students',
            'questions',
            'answersByStudent',
            'scoresByStudent',
            'maxPoints'
        ));
    }
}
