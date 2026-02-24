<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request, SchoolClass $class)
    {
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $assignments = Assignment::query()
            ->where('class_id', $class->id)
            ->latest()
            ->paginate(10);

        return view('student.assignments.index', compact('class', 'assignments'));
    }

    public function show(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $submission = $assignment->submissions()->where('student_id', $request->user()->id)->first();

        return view('student.assignments.show', compact('class', 'assignment', 'submission'));
    }
}
