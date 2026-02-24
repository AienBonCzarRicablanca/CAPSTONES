<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Support\Activity;
use App\Support\LibreOfficePreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    public function index(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $lessons = Lesson::query()
            ->where('class_id', $class->id)
            ->latest()
            ->paginate(10);

        return view('teacher.lessons.index', compact('class', 'lessons'));
    }

    public function create(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        return view('teacher.lessons.create', compact('class'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'materials' => ['nullable', 'array'],
            'materials.*' => [
                'file',
                'max:51200',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,audio/mpeg,audio/mp4,audio/wav,audio/ogg,application/zip,application/x-zip-compressed',
            ],
        ]);

        $lesson = Lesson::create([
            'class_id' => $class->id,
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
        ]);

        $uploadedCount = 0;
        foreach ($request->file('materials', []) as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('materials', 'public');
            $previewPath = LibreOfficePreview::makePdfPreviewIfSupported($path);
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            Material::create([
                'class_id' => $class->id,
                'lesson_id' => $lesson->id,
                'uploaded_by' => $request->user()->id,
                'title' => Str::limit($baseName ?: 'Attachment', 255, ''),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'preview_path' => $previewPath,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);

            $uploadedCount++;
        }

        Activity::log($request->user(), 'teacher.lesson.created', [
            'class_id' => $class->id,
            'lesson_id' => $lesson->id,
            'materials_uploaded' => $uploadedCount,
        ]);

        return redirect()->route('teacher.lessons.show', [$class, $lesson]);
    }

    public function show(Request $request, SchoolClass $class, Lesson $lesson)
    {
        if ((int) $lesson->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $lesson->load(['materials' => fn ($q) => $q->latest()]);

        return view('teacher.lessons.show', compact('class', 'lesson'));
    }

    public function destroy(Request $request, SchoolClass $class, Lesson $lesson)
    {
        if ((int) $lesson->class_id !== (int) $class->id) {
            abort(404);
        }
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        // Delete associated materials and their files
        foreach ($lesson->materials as $material) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            if ($material->preview_path) {
                Storage::disk('public')->delete($material->preview_path);
            }
            $material->delete();
        }

        $lesson->delete();

        Activity::log($request->user(), 'teacher.lesson.deleted', [
            'class_id' => $class->id,
            'lesson_id' => $lesson->id,
        ]);

        return redirect()->route('teacher.lessons.index', $class)
            ->with('success', 'Lesson deleted successfully.');
    }
}
