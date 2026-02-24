<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Support\LibreOfficePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SubmissionAttachmentController extends Controller
{
    public function preview(Request $request, SchoolClass $class, Assignment $assignment, Submission $submission)
    {
        $this->authorizeAccess($request, $class, $assignment, $submission);

        if (!$submission->attachment_preview_path) {
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($submission->attachment_path);
            if ($previewPath) {
                $submission->forceFill(['attachment_preview_path' => $previewPath])->save();
            }
        }

        $mime = null;
        if ($submission->attachment_preview_path) {
            $mime = 'application/pdf';
        } elseif ($submission->attachment_path && Storage::disk('public')->exists($submission->attachment_path)) {
            $mime = Storage::disk('public')->mimeType($submission->attachment_path);
        }

        return view('submissions.attachment-preview', [
            'class' => $class,
            'assignment' => $assignment,
            'submission' => $submission,
            'mime' => $mime,
        ]);
    }

    public function stream(Request $request, SchoolClass $class, Assignment $assignment, Submission $submission)
    {
        $this->authorizeAccess($request, $class, $assignment, $submission);

        $disk = Storage::disk('public');
        if (!$disk->exists($submission->attachment_path)) {
            abort(404);
        }

        // Check if mime type is viewable in browser
        $mime = $disk->mimeType($submission->attachment_path) ?: 'application/octet-stream';
        $viewableTypes = [
            'application/pdf',
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'video/mp4', 'video/webm', 'video/ogg',
            'text/plain', 'text/html', 'text/css', 'text/javascript',
        ];
        
        $isViewable = in_array($mime, $viewableTypes) || str_starts_with($mime, 'image/') || str_starts_with($mime, 'video/');
        
        // For non-viewable files, try to use PDF preview if available
        $usePreview = !$isViewable && $submission->attachment_preview_path;
        
        // If no preview exists but file is not viewable, generate one
        if (!$isViewable && !$submission->attachment_preview_path) {
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($submission->attachment_path);
            if ($previewPath) {
                $submission->forceFill(['attachment_preview_path' => $previewPath])->save();
                $usePreview = true;
            }
        }

        $path = $usePreview ? $submission->attachment_preview_path : $submission->attachment_path;
        $contentType = $usePreview ? 'application/pdf' : $mime;
        $filename = basename($submission->attachment_path) ?: 'attachment';

        if ($usePreview) {
            $filename = 'preview.pdf';
        }

        return Storage::disk('public')->response($path, $filename, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
        ]);
    }

    private function authorizeAccess(Request $request, SchoolClass $class, Assignment $assignment, Submission $submission): void
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }

        if ((int) $submission->assignment_id !== (int) $assignment->id) {
            abort(404);
        }

        if (!$submission->attachment_path) {
            abort(404);
        }

        $user = $request->user();
        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isOwnerStudent = (int) $submission->student_id === (int) $user->id;

        if (!$isTeacher && !$isOwnerStudent) {
            abort(403);
        }
    }
}
