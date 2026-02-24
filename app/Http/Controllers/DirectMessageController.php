<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use App\Models\DirectThread;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DirectMessageController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        if (!$me) {
            abort(403);
        }

        // Get all direct message threads involving this user
        $threads = DirectThread::query()
            ->where(function ($q) use ($me) {
                $q->where('user_one_id', $me->id)
                    ->orWhere('user_two_id', $me->id);
            })
            ->with(['userOne', 'userTwo', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($thread) use ($me) {
                $otherUser = (int) $thread->user_one_id === (int) $me->id ? $thread->userTwo : $thread->userOne;
                $lastMessage = $thread->messages->first();
                
                return [
                    'type' => 'direct',
                    'id' => $otherUser->id,
                    'user' => $otherUser,
                    'name' => $otherUser->name,
                    'photo' => $otherUser->profile_photo_path,
                    'last_message' => $lastMessage?->body,
                    'last_message_at' => $thread->last_message_at,
                    'url' => route('messages.show', $otherUser),
                ];
            });

        // Get classes - for teachers, their teaching classes; for students, enrolled classes
        $classes = [];
        if ($me->isTeacher()) {
            $classes = $me->teachingClasses()
                ->withCount('students')
                ->with(['chatMessages' => fn($q) => $q->latest()->limit(1)])
                ->get()
                ->map(function ($class) {
                    $lastMessage = $class->chatMessages->first();
                    return [
                        'type' => 'class',
                        'id' => $class->id,
                        'name' => $class->name,
                        'photo' => $class->photo_path,
                        'last_message' => $lastMessage?->message,
                        'last_message_at' => $lastMessage?->created_at,
                        'url' => route('classes.chat.show', $class),
                        'members_count' => $class->students_count + 1, // +1 for teacher
                    ];
                });
        } elseif ($me->isStudent()) {
            $classes = $me->enrolledClasses()
                ->with(['teacher', 'chatMessages' => fn($q) => $q->latest()->limit(1)])
                ->get()
                ->map(function ($class) {
                    $lastMessage = $class->chatMessages->first();
                    return [
                        'type' => 'class',
                        'id' => $class->id,
                        'name' => $class->name,
                        'photo' => $class->photo_path,
                        'last_message' => $lastMessage?->message,
                        'last_message_at' => $lastMessage?->created_at,
                        'url' => route('classes.chat.show', $class),
                        'teacher_name' => $class->teacher->name,
                    ];
                });
        }

        // Combine and sort all conversations
        $conversations = $threads->concat($classes)
            ->sortByDesc('last_message_at')
            ->values();

        // Get all users (except current user) for starting new conversations
        $allUsers = User::query()
            ->where('id', '!=', $me->id)
            ->with('role')
            ->orderBy('name')
            ->get();

        return view('messages.index', [
            'conversations' => $conversations,
            'allUsers' => $allUsers,
        ]);
    }

    public function show(Request $request, User $user)
    {
        $me = $request->user();
        if (!$me) {
            abort(403);
        }

        if ((int) $me->id === (int) $user->id) {
            abort(404);
        }

        $thread = $this->findThread($me->id, $user->id);
        $messages = collect();

        if ($thread) {
            $messages = $thread->messages()
                ->with('sender')
                ->orderBy('id')
                ->get();
        }

        $user->loadMissing('role');

        return view('messages.show', [
            'otherUser' => $user,
            'thread' => $thread,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, User $user)
    {
        $me = $request->user();
        if (!$me) {
            abort(403);
        }

        if ((int) $me->id === (int) $user->id) {
            throw ValidationException::withMessages([
                'message' => 'You cannot message yourself.',
            ]);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'attachment' => [
                'nullable',
                'file',
                'max:' . (50 * 1024), // 50 MB
            ],
        ]);

        $messageText = $validated['message'] ?? '';

        // At least one of message or attachment must be present
        if (!$messageText && !$request->hasFile('attachment')) {
            throw ValidationException::withMessages(['message' => 'Please enter a message or attach a file.']);
        }

        $attachmentPath = null;
        $attachmentOriginalName = null;
        $attachmentMimeType = null;
        $attachmentSizeBytes = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('direct_message_attachments', 'public');
            $attachmentOriginalName = $file->getClientOriginalName();
            $attachmentMimeType = $file->getMimeType() ?: $file->getClientMimeType();
            $attachmentSizeBytes = $file->getSize();
        }

        $thread = $this->findOrCreateThread($me->id, $user->id);

        DirectMessage::create([
            'thread_id' => $thread->id,
            'sender_id' => $me->id,
            'body' => $messageText,
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'attachment_mime_type' => $attachmentMimeType,
            'attachment_size_bytes' => $attachmentSizeBytes,
        ]);

        $thread->forceFill(['last_message_at' => now()])->save();

        return redirect()->route('messages.show', $user);
    }

    private function normalizePair(int $a, int $b): array
    {
        return $a < $b ? [$a, $b] : [$b, $a];
    }

    private function findThread(int $a, int $b): ?DirectThread
    {
        [$one, $two] = $this->normalizePair($a, $b);

        return DirectThread::query()
            ->where('user_one_id', $one)
            ->where('user_two_id', $two)
            ->first();
    }

    private function findOrCreateThread(int $a, int $b): DirectThread
    {
        [$one, $two] = $this->normalizePair($a, $b);

        return DirectThread::firstOrCreate([
            'user_one_id' => $one,
            'user_two_id' => $two,
        ]);
    }
}
