<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        $user->loadMissing('role');

        return view('users.show', [
            'user' => $user,
        ]);
    }
}
