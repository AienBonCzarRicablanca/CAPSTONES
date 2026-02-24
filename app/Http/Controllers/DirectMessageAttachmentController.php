<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use App\Models\DirectThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectMessageAttachmentController extends Controller
{
    public function show(Request $request, User $user, DirectMessage $message)
    {
        $me = $request->user();
        if (!$me) {
            abort(403);
        }

        // Check if this message belongs to a thread involving the current user
        $thread = DirectThread::find($message->thread_id);
        if (!$thread) {
            abort(404);
        }

        $isParticipant = (int) $thread->user_one_id === (int) $me->id || (int) $thread->user_two_id === (int) $me->id;
        if (!$isParticipant) {
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
