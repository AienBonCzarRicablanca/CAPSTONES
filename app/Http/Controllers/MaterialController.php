<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Support\Activity;
use App\Support\LibreOfficePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MaterialController extends Controller
{
    public function store(Request $request, SchoolClass $class, Lesson $lesson)
    {
        if ((int) $lesson->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => [
                'required',
                'file',
                'max:51200',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,audio/mpeg,audio/mp4,audio/wav,audio/ogg,application/zip,application/x-zip-compressed',
            ],
        ]);

        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($path);

        Material::create([
            'class_id' => $class->id,
            'lesson_id' => $lesson->id,
            'uploaded_by' => $request->user()->id,
            'title' => $validated['title'],
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'preview_path' => $previewPath,
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        Activity::log($request->user(), 'teacher.material.uploaded', [
            'class_id' => $class->id,
            'lesson_id' => $lesson->id,
        ]);

        return back();
    }

    public function preview(Request $request, Material $material)
    {
        $class = SchoolClass::findOrFail($material->class_id);
        $user = $request->user();

        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        if (!$material->preview_path) {
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($material->file_path);
            if ($previewPath) {
                $material->forceFill(['preview_path' => $previewPath])->save();
            }
        }

        return view('materials.preview', compact('material', 'class'));
    }

    public function stream(Request $request, Material $material)
    {
        $class = SchoolClass::findOrFail($material->class_id);
        $user = $request->user();

        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        // Check if mime type is viewable in browser
        $mime = $material->mime_type ?: 'application/octet-stream';
        $viewableTypes = [
            'application/pdf',
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'video/mp4', 'video/webm', 'video/ogg',
            'text/plain', 'text/html', 'text/css', 'text/javascript',
        ];
        
        $isViewable = in_array($mime, $viewableTypes) || str_starts_with($mime, 'image/') || str_starts_with($mime, 'video/');
        
        // For non-viewable files, try to use PDF preview if available
        $usePreview = !$isViewable && $material->preview_path;
        
        // If no preview exists but file is not viewable, generate one
        if (!$isViewable && !$material->preview_path) {
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($material->file_path);
            if ($previewPath) {
                $material->forceFill(['preview_path' => $previewPath])->save();
                $usePreview = true;
            }
        }

        $relativePath = $usePreview ? $material->preview_path : $material->file_path;
        $contentType = $usePreview ? 'application/pdf' : $mime;
        $filename = $material->original_name;
        
        if ($usePreview) {
            $base = pathinfo((string) $material->original_name, PATHINFO_FILENAME);
            $filename = ($base ?: 'preview') . '.pdf';
        }

        return Storage::disk('public')->response($relativePath, $filename, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . addslashes((string) $filename) . '"',
        ]);
    }
}
