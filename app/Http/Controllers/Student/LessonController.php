<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request, SchoolClass $class)
    {
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $lessons = Lesson::query()
            ->where('class_id', $class->id)
            ->latest()
            ->paginate(10);

        return view('student.lessons.index', compact('class', 'lessons'));
    }

    public function show(Request $request, SchoolClass $class, Lesson $lesson)
    {
        if ((int) $lesson->class_id !== (int) $class->id) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $lesson->load(['materials' => fn ($q) => $q->latest()]);

        return view('student.lessons.show', compact('class', 'lesson'));
    }
}
