<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Support\Activity;
use Illuminate\Http\Request;

class JoinClassController extends Controller
{
    public function create()
    {
        return view('student.classes.join');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'join_code' => ['required', 'string', 'max:12'],
        ]);

        $code = strtoupper(trim($validated['join_code']));

        $class = SchoolClass::where('join_code', $code)->first();
        if (!$class) {
            return back()->withErrors(['join_code' => 'Invalid join code.']);
        }

        $existing = $request->user()
            ->classMemberships()
            ->where('classes.id', $class->id)
            ->first();

        if ($existing && (string) ($existing->pivot?->status ?? '') === 'approved') {
            return back()->withErrors(['join_code' => 'You are already enrolled in this class.']);
        }
        if ($existing && (string) ($existing->pivot?->status ?? '') === 'pending') {
            return back()->withErrors(['join_code' => 'Your join request is already pending.']);
        }

        $now = now();

        if ($class->is_private) {
            $request->user()->classMemberships()->syncWithoutDetaching([
                $class->id => [
                    'status' => 'pending',
                    'requested_at' => $now,
                    'joined_at' => null,
                    'decided_at' => null,
                    'decided_by' => null,
                ],
            ]);

            Activity::log($request->user(), 'student.class.join_requested', ['class_id' => $class->id]);

            return redirect()
                ->route('student.classes.index')
                ->with('status', 'Join request sent. Please wait for the teacher to accept.');
        }

        $request->user()->classMemberships()->syncWithoutDetaching([
            $class->id => [
                'status' => 'approved',
                'joined_at' => $now,
                'requested_at' => $now,
                'decided_at' => $now,
                'decided_by' => $class->teacher_id,
            ],
        ]);

        Activity::log($request->user(), 'student.class.joined', ['class_id' => $class->id]);

        return redirect()->route('student.classes.index');
    }

    public function index(Request $request)
    {
        $classes = $request->user()->enrolledClasses()
            ->with('teacher')
            ->latest('class_student.created_at')
            ->paginate(10);
        return view('student.classes.index', compact('classes'));
    }
}
