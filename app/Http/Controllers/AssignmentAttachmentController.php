<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Support\LibreOfficePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssignmentAttachmentController extends Controller
{
    public function preview(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }

        if (!$assignment->attachment_path) {
            abort(404);
        }

        $user = $request->user();

        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        if (!$assignment->attachment_preview_path) {
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($assignment->attachment_path);
            if ($previewPath) {
                $assignment->forceFill(['attachment_preview_path' => $previewPath])->save();
            }
        }

        return view('assignments.attachment-preview', compact('class', 'assignment'));
    }

    public function show(Request $request, SchoolClass $class, Assignment $assignment)
    {
        if ((int) $assignment->class_id !== (int) $class->id) {
            abort(404);
        }

        if (!$assignment->attachment_path) {
            abort(404);
        }

        $user = $request->user();

        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        // Check if mime type is viewable in browser
        $mime = $assignment->attachment_mime_type ?: 'application/octet-stream';
        $viewableTypes = [
            'application/pdf',
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'video/mp4', 'video/webm', 'video/ogg',
            'text/plain', 'text/html', 'text/css', 'text/javascript',
        ];
        
        $isViewable = in_array($mime, $viewableTypes) || str_starts_with($mime, 'image/') || str_starts_with($mime, 'video/');
        
        // For non-viewable files, try to use PDF preview if available
        $usePreview = !$isViewable && $assignment->attachment_preview_path;
        
        // If no preview exists but file is not viewable, generate one
        if (!$isViewable && !$assignment->attachment_preview_path) {
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($assignment->attachment_path);
            if ($previewPath) {
                $assignment->forceFill(['attachment_preview_path' => $previewPath])->save();
                $usePreview = true;
            }
        }

        $relativePath = $usePreview ? $assignment->attachment_preview_path : $assignment->attachment_path;
        $contentType = $usePreview ? 'application/pdf' : $mime;
        $filename = $assignment->attachment_original_name ?: 'attachment';
        
        if ($usePreview) {
            $base = pathinfo((string) $filename, PATHINFO_FILENAME);
            $filename = ($base ?: 'preview') . '.pdf';
        }

        return Storage::disk('public')->response($relativePath, $filename, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . addslashes((string) $filename) . '"',
        ]);
    }
}
