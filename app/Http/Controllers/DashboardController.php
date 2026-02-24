<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    public function admin()
    {
        $stats = [
            'users' => User::count(),
            'teachers' => User::whereHas('role', fn ($q) => $q->where('name', 'TEACHER'))->count(),
            'students' => User::whereHas('role', fn ($q) => $q->where('name', 'STUDENT'))->count(),
        ];

        return view('dashboards.admin', compact('stats'));
    }

    public function teacher()
    {
        return view('dashboards.teacher');
    }

    public function student()
    {
        return view('dashboards.student');
    }
}
