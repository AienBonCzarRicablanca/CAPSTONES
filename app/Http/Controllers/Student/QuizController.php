<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizSubmission;
use App\Models\SchoolClass;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request, SchoolClass $class)
    {
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $studentId = $request->user()->id;

        $quizzes = Quiz::query()
            ->where('class_id', $class->id)
            ->where('is_published', true)
            ->with(['submissions' => fn ($q) => $q->where('student_id', $studentId)])
            ->latest()
            ->paginate(10);

        return view('student.quizzes.index', compact('class', 'quizzes'));
    }

    public function take(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if (!$quiz->is_published) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $studentId = $request->user()->id;
        $alreadySubmitted = QuizSubmission::query()
            ->where('quiz_id', $quiz->id)
            ->where('student_id', $studentId)
            ->exists();
        if ($alreadySubmitted) {
            return redirect()->route('student.quizzes.result', [$class, $quiz]);
        }

        $quiz->load(['questions' => fn ($q) => $q->orderBy('order')->orderBy('id')]);

        return view('student.quizzes.take', compact('class', 'quiz'));
    }

    public function submit(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if (!$quiz->is_published) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $quiz->load(['questions']);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $studentId = $request->user()->id;

        $alreadySubmitted = QuizSubmission::query()
            ->where('quiz_id', $quiz->id)
            ->where('student_id', $studentId)
            ->exists();
        if ($alreadySubmitted) {
            return redirect()->route('student.quizzes.result', [$class, $quiz]);
        }

        DB::transaction(function () use ($quiz, $studentId, $validated) {
            foreach ($quiz->questions as $question) {
                $raw = $validated['answers'][$question->id] ?? null;
                $answerText = is_array($raw) ? json_encode($raw) : (is_null($raw) ? null : (string) $raw);

                $isCorrect = null;
                $awarded = null;

                if (in_array($question->type, ['MCQ', 'TRUE_FALSE'], true) && $question->correct_answer !== null) {
                    $isCorrect = trim(strtoupper((string) $raw)) === trim(strtoupper((string) $question->correct_answer));
                    $awarded = $isCorrect ? (int) $question->points : 0;
                }

                if ($question->type === 'IDENTIFICATION' && $question->correct_answer !== null) {
                    $given = preg_replace('/\s+/', ' ', trim((string) $raw));
                    $expected = preg_replace('/\s+/', ' ', trim((string) $question->correct_answer));
                    $isCorrect = strtoupper((string) $given) === strtoupper((string) $expected);
                    $awarded = $isCorrect ? (int) $question->points : 0;
                }

                if ($question->type === 'MATCHING' && is_array($question->options) && count($question->options) > 0) {
                    $studentMap = is_array($raw) ? $raw : [];
                    $pairs = collect($question->options)->values();
                    $total = (int) $pairs->count();
                    $correctCount = 0;

                    foreach ($pairs as $idx => $pair) {
                        $expected = (string) ($pair['right'] ?? '');
                        $given = (string) ($studentMap[$idx] ?? '');
                        if ($expected !== '' && $given !== '' && strtoupper(trim($expected)) === strtoupper(trim($given))) {
                            $correctCount++;
                        }
                    }

                    $isCorrect = $total > 0 ? ($correctCount === $total) : null;
                    $awarded = $total > 0 ? (int) floor(((int) $question->points) * ($correctCount / $total)) : null;
                }

                QuizAnswer::updateOrCreate(
                    ['quiz_question_id' => $question->id, 'student_id' => $studentId],
                    ['answer_text' => $answerText, 'is_correct' => $isCorrect, 'awarded_points' => $awarded]
                );
            }

            QuizSubmission::create([
                'quiz_id' => $quiz->id,
                'student_id' => $studentId,
                'submitted_at' => now(),
            ]);
        });

        Activity::log($request->user(), 'student.quiz.submitted', [
            'class_id' => $class->id,
            'quiz_id' => $quiz->id,
        ]);

        return redirect()->route('student.quizzes.result', [$class, $quiz]);
    }

    public function result(Request $request, SchoolClass $class, Quiz $quiz)
    {
        if ((int) $quiz->class_id !== (int) $class->id) {
            abort(404);
        }
        if (!$quiz->is_published) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $studentId = $request->user()->id;

        $submission = QuizSubmission::query()
            ->where('quiz_id', $quiz->id)
            ->where('student_id', $studentId)
            ->first();
        if (!$submission) {
            return redirect()->route('student.quizzes.take', [$class, $quiz]);
        }

        $quiz->load(['questions.answers' => fn ($q) => $q->where('student_id', $studentId)]);

        $max = $quiz->questions->sum('points');
        $score = $quiz->questions->sum(function ($q) {
            $ans = $q->answers->first();
            return (int) ($ans?->awarded_points ?? 0);
        });

        return view('student.quizzes.result', compact('class', 'quiz', 'score', 'max', 'submission'));
    }
}
