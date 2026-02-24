<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()->with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
