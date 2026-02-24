<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $classes = collect();
        $users = collect();

        if ($q !== '') {
            $classes = SchoolClass::query()
                ->where('is_private', false)
                ->where('name', 'like', '%' . $q . '%')
                ->with(['teacher' => fn ($t) => $t->select('id', 'name', 'role_id')->with('role')])
                ->orderBy('name')
                ->limit(20)
                ->get();

            $users = User::query()
                ->where('name', 'like', '%' . $q . '%')
                ->whereKeyNot(optional($request->user())->id)
                ->with('role')
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        return view('search.index', [
            'q' => $q,
            'classes' => $classes,
            'users' => $users,
        ]);
    }

    public function api(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '' || strlen($q) < 1) {
            return response()->json([
                'classes' => [],
                'users' => [],
            ]);
        }

        $classes = SchoolClass::query()
            ->where('is_private', false)
            ->where('name', 'like', '%' . $q . '%')
            ->with(['teacher' => fn ($t) => $t->select('id', 'name', 'role_id')->with('role')])
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'join_code' => $class->join_code,
                    'teacher_name' => $class->teacher?->name ?? 'N/A',
                ];
            });

        $users = User::query()
            ->where('name', 'like', '%' . $q . '%')
            ->whereKeyNot(optional($request->user())->id)
            ->with('role')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role?->name,
                    'profile_photo_url' => $user->profile_photo_path ? Storage::url($user->profile_photo_path) : null,
                    'profile_url' => route('users.show', $user),
                    'messages_url' => route('messages.show', $user),
                ];
            });

        return response()->json([
            'classes' => $classes,
            'users' => $users,
        ]);
    }
}
