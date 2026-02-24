<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Support\LibreOfficePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $assignments = Assignment::query()
            ->where('class_id', $class->id)
            ->latest()
            ->paginate(10);

        return view('teacher.assignments.index', compact('class', 'assignments'));
    }

    public function create(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        return view('teacher.assignments.create', compact('class'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:HOMEWORK,ACTIVITY'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
            'allow_late' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:51200'],
        ]);

        $dueAt = Carbon::parse($validated['due_at'])->endOfDay();

        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentMimeType = null;
        $attachmentSizeBytes = null;
        $attachmentPreviewPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('assignment_attachments', 'public');
            $attachmentOriginalName = $file->getClientOriginalName();
            $attachmentMimeType = $file->getClientMimeType();
            $attachmentSizeBytes = $file->getSize();
            $attachmentPreviewPath = LibreOfficePreview::makePdfPreviewIfSupported($attachmentPath);
        }

        $assignment = Assignment::create([
            'class_id' => $class->id,
            'created_by' => $request->user()->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_at' => $dueAt,
            'allow_late' => $request->boolean('allow_late'),
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_mime_type' => $attachmentMimeType,
            'attachment_size_bytes' => $attachmentSizeBytes,
            'attachment_preview_path' => $attachmentPreviewPath,
        ]);

        return redirect()->route('teacher.assignments.show', [$class, $assignment]);
    }

    public function show(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $assignment->load(['submissions' => fn ($q) => $q->with('student')->latest()]);

        return view('teacher.assignments.show', compact('class', 'assignment'));
    }

    public function destroy(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        // Delete assignment attachment
        if ($assignment->attachment_path) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }
        if ($assignment->attachment_preview_path) {
            Storage::disk('public')->delete($assignment->attachment_preview_path);
        }

        // Delete all submission attachments
        foreach ($assignment->submissions as $submission) {
            if ($submission->attachment_path) {
                Storage::disk('public')->delete($submission->attachment_path);
            }
            if ($submission->attachment_preview_path) {
                Storage::disk('public')->delete($submission->attachment_preview_path);
            }
        }

        $assignment->delete();

        return redirect()->route('teacher.assignments.index', $class)
            ->with('success', 'Assignment deleted successfully.');
    }
}
