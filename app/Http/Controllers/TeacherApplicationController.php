<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\TeacherApplication;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherApplicationController extends Controller
{
    public function create()
    {
        return view('teacher_applications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $studentRoleId = Role::where('name', 'STUDENT')->value('id');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $studentRoleId,
        ]);

        TeacherApplication::create([
            'user_id' => $user->id,
            'status' => 'PENDING',
        ]);

        Activity::log($user, 'teacher_application.submitted');

        return redirect()->route('login')->with('status', 'Teacher application submitted. You can log in while waiting for admin approval.');
    }
}
