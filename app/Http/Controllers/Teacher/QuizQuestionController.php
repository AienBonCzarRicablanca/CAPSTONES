<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\SchoolClass;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuizQuestionController extends Controller
{
    public function store(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }
        if ($quiz->is_published) {
            throw ValidationException::withMessages([
                'quiz' => 'This quiz is already published and locked. You can no longer add questions.',
            ]);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:MCQ,TRUE_FALSE,SHORT,IDENTIFICATION,MATCHING'],
            'question_text' => ['required', 'string'],
            'options' => ['nullable'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'pairs_left' => ['nullable', 'array'],
            'pairs_left.*' => ['nullable', 'string', 'max:255'],
            'pairs_right' => ['nullable', 'array'],
            'pairs_right.*' => ['nullable', 'string', 'max:255'],
            'correct_answer' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $type = (string) $validated['type'];
        $options = null;

        if ($type === 'MCQ') {
            $rawOptions = $validated['options'] ?? null;

            if (is_string($rawOptions)) {
                $lines = collect(preg_split('/\r\n|\r|\n/', $rawOptions))
                    ->map(fn ($s) => trim((string) $s))
                    ->filter()
                    ->values();
                $options = $lines->all();
            } elseif (is_array($rawOptions)) {
                $options = collect($rawOptions)
                    ->map(fn ($s) => trim((string) $s))
                    ->filter()
                    ->values()
                    ->all();
            } else {
                $options = [];
            }

            if (count($options) < 2) {
                throw ValidationException::withMessages([
                    'options' => 'Please add at least 2 options for Multiple Choice.',
                ]);
            }

            $correct = trim((string) ($validated['correct_answer'] ?? ''));
            if ($correct === '' || !in_array($correct, $options, true)) {
                throw ValidationException::withMessages([
                    'correct_answer' => 'Please select a correct option that matches one of the choices.',
                ]);
            }
        }

        if ($type === 'TRUE_FALSE') {
            $correct = strtoupper(trim((string) ($validated['correct_answer'] ?? '')));
            if (!in_array($correct, ['TRUE', 'FALSE'], true)) {
                throw ValidationException::withMessages([
                    'correct_answer' => 'Correct answer must be TRUE or FALSE.',
                ]);
            }
            $validated['correct_answer'] = $correct;
        }

        if ($type === 'MATCHING') {
            $left = $validated['pairs_left'] ?? [];
            $right = $validated['pairs_right'] ?? [];

            $max = max(count($left), count($right));
            $pairs = [];
            for ($i = 0; $i < $max; $i++) {
                $l = trim((string) ($left[$i] ?? ''));
                $r = trim((string) ($right[$i] ?? ''));
                if ($l === '' && $r === '') {
                    continue;
                }
                if ($l === '' || $r === '') {
                    throw ValidationException::withMessages([
                        'pairs_left' => 'Each matching pair must have both Left and Right filled.',
                    ]);
                }
                $pairs[] = ['left' => $l, 'right' => $r];
            }

            if (count($pairs) < 1) {
                throw ValidationException::withMessages([
                    'pairs_left' => 'Please add at least 1 matching pair.',
                ]);
            }

            $options = $pairs;
            $validated['correct_answer'] = null;
        }

        if (in_array($type, ['SHORT'], true)) {
            $validated['correct_answer'] = null;
        }

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'type' => $type,
            'question_text' => $validated['question_text'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'] ?? null,
            'points' => $validated['points'],
            'order' => $validated['order'] ?? 0,
        ]);

        Activity::log($request->user(), 'teacher.quiz.question_created', [
            'class_id' => $class->id,
            'quiz_id' => $quiz->id,
        ]);

        return back();
    }

    public function destroy(Request $request, SchoolClass $class, Quiz $quiz, QuizQuestion $question)
    {
        if ((int) $quiz->class_id !== (int) $class->id || (int) $question->quiz_id !== (int) $quiz->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }
        if ($quiz->is_published) {
            throw ValidationException::withMessages([
                'quiz' => 'This quiz is already published and locked. You can no longer delete questions.',
            ]);
        }

        $question->delete();
        return back();
    }
}
