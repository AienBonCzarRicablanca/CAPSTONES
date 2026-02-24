<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::query()
            ->whereHas('role', fn ($q) => $q->where('name', 'TEACHER'))
            ->latest()
            ->paginate(15);

        return view('admin.users.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.users.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $teacherRoleId = Role::where('name', 'TEACHER')->value('id');

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $teacherRoleId,
        ]);

        return redirect()->route('admin.teachers.index');
    }

    public function edit(User $teacher)
    {
        return view('admin.users.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$teacher->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        if (!empty($validated['password'])) {
            $teacher->password = Hash::make($validated['password']);
        }
        $teacher->save();

        return redirect()->route('admin.teachers.index');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return back();
    }
}
