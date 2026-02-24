<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatAttachmentController extends Controller
{
    public function show(Request $request, SchoolClass $class, ChatMessage $message)
    {
        if ((int) $message->class_id !== (int) $class->id) {
            abort(404);
        }

        $user = $request->user();
        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
            abort(404);
        }

        $absolutePath = Storage::disk('public')->path($message->attachment_path);
        $name = $message->attachment_original_name ?: basename($message->attachment_path);
        $mime = $message->attachment_mime_type ?: null;
        if (!$mime || $mime === 'application/octet-stream') {
            $detected = Storage::disk('public')->mimeType($message->attachment_path);
            if (is_string($detected) && $detected !== '') {
                $mime = $detected;
            }
        }

        $headers = [
            'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
        ];
        if ($mime) {
            $headers['Content-Type'] = $mime;
        }

        return response()->file($absolutePath, $headers);
    }
}
