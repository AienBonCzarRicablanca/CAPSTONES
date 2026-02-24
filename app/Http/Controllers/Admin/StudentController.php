<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'STUDENT'))
            ->latest()
            ->paginate(15);

        return view('admin.users.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.users.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $studentRoleId = Role::where('name', 'STUDENT')->value('id');

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $studentRoleId,
        ]);

        return redirect()->route('admin.students.index');
    }

    public function edit(User $student)
    {
        return view('admin.users.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$student->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $student->name = $validated['name'];
        $student->email = $validated['email'];
        if (!empty($validated['password'])) {
            $student->password = Hash::make($validated['password']);
        }
        $student->save();

        return redirect()->route('admin.students.index');
    }

    public function destroy(User $student)
    {
        $student->delete();
        return back();
    }
}
