<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Support\Activity;
use Illuminate\Http\Request;

class QuizAnswerReviewController extends Controller
{
    public function update(Request $request, SchoolClass $class, Quiz $quiz, QuizQuestion $question, QuizAnswer $answer)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $question->quiz_id !== (int) $quiz->id) {
            abort(404);
        }
        if ((int) $answer->quiz_question_id !== (int) $question->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'awarded_points' => ['required', 'integer', 'min:0', 'max:'.$question->points],
            'is_correct' => ['nullable', 'boolean'],
        ]);

        $answer->awarded_points = (int) $validated['awarded_points'];
        $answer->is_correct = $request->has('is_correct') ? (bool) $validated['is_correct'] : null;
        $answer->save();

        Activity::log($request->user(), 'teacher.quiz.answer_reviewed', [
            'class_id' => $class->id,
            'quiz_id' => $quiz->id,
            'question_id' => $question->id,
            'student_id' => $answer->student_id,
        ]);

        return back();
    }
}
