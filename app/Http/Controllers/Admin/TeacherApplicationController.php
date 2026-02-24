<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TeacherApplication;
use App\Support\Activity;
use Illuminate\Http\Request;

class TeacherApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherApplication::query()->with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->string('status')));
        }

        $applications = $query->paginate(25)->withQueryString();

        return view('admin.teacher_applications.index', compact('applications'));
    }

    public function approve(Request $request, TeacherApplication $application)
    {
        if ($application->status !== 'PENDING') {
            return back();
        }

        $teacherRoleId = Role::where('name', 'TEACHER')->value('id');
        $application->user->update(['role_id' => $teacherRoleId]);

        $application->update([
            'status' => 'APPROVED',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        Activity::log($request->user(), 'teacher_application.approved', ['user_id' => $application->user_id]);

        return back();
    }

    public function reject(Request $request, TeacherApplication $application)
    {
        if ($application->status !== 'PENDING') {
            return back();
        }

        $application->update([
            'status' => 'REJECTED',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        Activity::log($request->user(), 'teacher_application.rejected', ['user_id' => $application->user_id]);

        return back();
    }
}
