<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionGradeController extends Controller
{
    public function update(Request $request, SchoolClass $class, Assignment $assignment, Submission $submission)
    {
        if ((int) $assignment->class_id !== (int) $class->id || (int) $submission->assignment_id !== (int) $assignment->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string'],
        ]);

        $submission->grade = $validated['grade'] ?? null;
        $submission->feedback = $validated['feedback'] ?? null;
        $submission->save();

        return back();
    }
}
