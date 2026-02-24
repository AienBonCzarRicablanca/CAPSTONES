<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $now = now();

        $classIds = $user->enrolledClasses()->pluck('classes.id');

        $assignments = Assignment::query()
            ->whereIn('class_id', $classIds)
            ->with('class')
            ->with(['submissions' => fn ($q) => $q->where('student_id', $user->id)])
            ->orderBy('due_at')
            ->get();

        $quizzes = Quiz::query()
            ->whereIn('class_id', $classIds)
            ->where('is_published', true)
            ->with('class')
            ->with(['submissions' => fn ($q) => $q->where('student_id', $user->id)])
            ->orderByRaw('due_at is null, due_at asc')
            ->get();

        $ongoing = [];
        $due = [];
        $completed = [];

        foreach ($assignments as $assignment) {
            $submission = $assignment->submissions->first();
            $isCompleted = (bool) ($submission?->submitted_at);
            if ($isCompleted) {
                $completed[] = [
                    'kind' => 'ACTIVITY',
                    'title' => $assignment->title,
                    'class_name' => $assignment->class?->name,
                    'due_at' => $assignment->due_at,
                    'submitted_at' => $submission->submitted_at,
                    'is_late' => (bool) $submission->is_late,
                    'url' => route('student.assignments.show', [$assignment->class, $assignment]),
                ];
                continue;
            }

            $isDue = (bool) ($assignment->due_at && $assignment->due_at->lessThanOrEqualTo($now));
            $target = $isDue ? 'due' : 'ongoing';

            ${$target}[] = [
                'kind' => 'ACTIVITY',
                'title' => $assignment->title,
                'class_name' => $assignment->class?->name,
                'due_at' => $assignment->due_at,
                'allow_late' => (bool) $assignment->allow_late,
                'url' => route('student.assignments.show', [$assignment->class, $assignment]),
            ];
        }

        foreach ($quizzes as $quiz) {
            $quizSubmission = $quiz->submissions->first();
            $isCompleted = (bool) ($quizSubmission?->submitted_at);
            if ($isCompleted) {
                $completed[] = [
                    'kind' => 'QUIZ',
                    'title' => $quiz->title,
                    'class_name' => $quiz->class?->name,
                    'due_at' => $quiz->due_at,
                    'submitted_at' => $quizSubmission->submitted_at,
                    'url' => route('student.quizzes.result', [$quiz->class, $quiz]),
                ];
                continue;
            }

            $isDue = (bool) ($quiz->due_at && $quiz->due_at->lessThanOrEqualTo($now));
            $target = $isDue ? 'due' : 'ongoing';

            ${$target}[] = [
                'kind' => 'QUIZ',
                'title' => $quiz->title,
                'class_name' => $quiz->class?->name,
                'due_at' => $quiz->due_at,
                'url' => route('student.quizzes.take', [$quiz->class, $quiz]),
            ];
        }

        return view('student.todo.index', compact('ongoing', 'due', 'completed'));
    }
}
