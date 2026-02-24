<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\SchoolClass;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function show(Request $request, SchoolClass $class)
    {
        // Basic access: teacher of class OR enrolled student
        $user = $request->user();
        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        $messages = ChatMessage::query()
            ->where('class_id', $class->id)
            ->whereNull('recipient_id')
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('chat.class', compact('class', 'messages'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        $user = $request->user();
        $isTeacher = (int) $class->teacher_id === (int) $user->id;
        $isStudent = $user->enrolledClasses()->where('classes.id', $class->id)->exists();
        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'attachment' => [
                'nullable',
                'file',
                'max:51200',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,audio/mpeg,audio/mp4,audio/wav,audio/ogg,application/zip,application/x-zip-compressed',
            ],
        ]);

        $messageText = $request->filled('message') ? trim((string) $validated['message']) : null;
        if (!$messageText && !$request->hasFile('attachment')) {
            return back()->withErrors(['message' => 'Type a message or attach a file.']);
        }

        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentMimeType = null;
        $attachmentSizeBytes = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat_attachments', 'public');
            $attachmentOriginalName = $file->getClientOriginalName();
            $attachmentMimeType = $file->getMimeType() ?: $file->getClientMimeType();
            $attachmentSizeBytes = $file->getSize();
        }

        ChatMessage::create([
            'class_id' => $class->id,
            'sender_id' => $user->id,
            'recipient_id' => null,
            'message' => $messageText,
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_mime_type' => $attachmentMimeType,
            'attachment_size_bytes' => $attachmentSizeBytes,
        ]);

        Activity::log($user, 'class.chat.sent', ['class_id' => $class->id]);

        return back();
    }
}
