<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Support\Activity;
use App\Support\LibreOfficePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function store(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $now = now();
        $isLate = (bool) ($assignment->due_at && $now->greaterThan($assignment->due_at));
        if ($isLate && !$assignment->allow_late) {
            return back()->withErrors([
                'submit' => 'Submission is closed. The due date has already passed.',
            ]);
        }

        $existingSubmission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $request->user()->id)
            ->first();

        if ($existingSubmission?->submitted_at) {
            return back()->withErrors([
                'submit' => 'You already submitted this assignment. Use Undo Submit to make changes before the due date.',
            ]);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string'],
            'attachment' => [
                'nullable',
                'file',
                'max:51200',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,audio/mpeg,audio/mp4,audio/wav,audio/ogg,application/zip,application/x-zip-compressed',
            ],
        ]);

        $attachmentPath = $existingSubmission?->attachment_path;
        $attachmentPreviewPath = $existingSubmission?->attachment_preview_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            if ($attachmentPreviewPath) {
                Storage::disk('public')->delete($attachmentPreviewPath);
            }
            $attachmentPath = $request->file('attachment')->store('submissions', 'public');
            $attachmentPreviewPath = LibreOfficePreview::makePdfPreviewIfSupported($attachmentPath);
        }

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $request->user()->id],
            [
                'content' => $validated['content'] ?? $existingSubmission?->content ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_preview_path' => $attachmentPreviewPath,
                'submitted_at' => $now,
                'is_late' => $isLate,
            ]
        );

        Activity::log($request->user(), 'student.assignment.submitted', [
            'class_id' => $class->id,
            'assignment_id' => $assignment->id,
        ]);

        return back();
    }

    public function unsubmit(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }
        $enrolled = $request->user()->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$enrolled) {
            abort(403);
        }

        $submission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $request->user()->id)
            ->first();

        if (!$submission || !$submission->submitted_at) {
            return back();
        }

        if ($assignment->due_at && now()->greaterThanOrEqualTo($assignment->due_at)) {
            return back()->withErrors([
                'submit' => 'Undo is not available. The due date has already passed.',
            ]);
        }

        $submission->forceFill([
            'submitted_at' => null,
            'is_late' => false,
        ])->save();

        Activity::log($request->user(), 'student.assignment.unsubmitted', [
            'class_id' => $class->id,
            'assignment_id' => $assignment->id,
        ]);

        return back();
    }
}
