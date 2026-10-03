<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private function ensureAdmin()
    {
        abort_unless(Auth::user()->role === 'admin', 403, 'Only admins can manage users.');
    }

    public function index()
    {
        $this->ensureAdmin();
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'role' => 'required|in:customer,staff,driver,admin',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User role updated.');
    }
}