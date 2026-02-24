<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::query()
            ->where('teacher_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('teacher.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('teacher.classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $class = SchoolClass::create([
            'teacher_id' => $request->user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'join_code' => strtoupper(Str::random(8)),
        ]);

        return redirect()->route('teacher.classes.show', $class);
    }

    public function show(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $class->loadCount('students');
        $class->load(['students' => fn ($q) => $q->orderBy('name')]);
        $class->load(['pendingStudents' => fn ($q) => $q->orderBy('name')]);

        return view('teacher.classes.show', ['class' => $class]);
    }

    public function approveRequest(Request $request, SchoolClass $class, User $student)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $now = now();

        DB::table('class_student')
            ->where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->update([
                'status' => 'approved',
                'joined_at' => $now,
                'decided_at' => $now,
                'decided_by' => $request->user()->id,
                'updated_at' => $now,
            ]);

        return back();
    }

    public function rejectRequest(Request $request, SchoolClass $class, User $student)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $now = now();

        DB::table('class_student')
            ->where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->update([
                'status' => 'rejected',
                'joined_at' => null,
                'decided_at' => $now,
                'decided_by' => $request->user()->id,
                'updated_at' => $now,
            ]);

        return back();
    }

    public function addStudent(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'student_email' => ['required', 'email'],
        ]);

        $student = User::query()
            ->where('email', $validated['student_email'])
            ->first();

        if (!$student || !$student->isStudent()) {
            throw ValidationException::withMessages([
                'student_email' => 'Student not found.',
            ]);
        }

        $exists = DB::table('class_student')
            ->where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->exists();

        $now = now();

        if (!$exists) {
            DB::table('class_student')->insert([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'joined_at' => $now,
                'status' => 'approved',
                'requested_at' => $now,
                'decided_at' => $now,
                'decided_by' => $request->user()->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return back();
        }

        DB::table('class_student')
            ->where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->update([
                'status' => 'approved',
                'joined_at' => $now,
                'decided_at' => $now,
                'decided_by' => $request->user()->id,
                'updated_at' => $now,
            ]);

        return back();
    }

    public function edit(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        return view('teacher.classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_private' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'file', 'max:5120', 'mimetypes:image/jpeg,image/png,image/gif,image/webp'],
        ]);

        if ($request->hasFile('photo')) {
            if ($class->photo_path) {
                Storage::disk('public')->delete($class->photo_path);
            }
            $class->photo_path = $request->file('photo')->store('class_photos', 'public');
        }

        $class->name = $validated['name'];
        $class->description = $validated['description'] ?? null;
        $class->is_private = $request->boolean('is_private');
        $class->save();

        return redirect()->route('teacher.classes.show', $class);
    }

    public function regenerateCode(Request $request, SchoolClass $class)
    {
        if ((int) $class->teacher_id !== (int) $request->user()->id) {
            abort(403);
        }

        $class->update(['join_code' => strtoupper(Str::random(8))]);

        return back();
    }
}
