<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:customer,staff,driver,admin',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User account created.');
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

    public function deactivate(User $user)
    {
        $this->ensureAdmin();

        abort_if($user->id === Auth::id(), 403, 'Hindi mo puwedeng i-deactivate ang sarili mong account.');

        abort_unless(in_array($user->role, ['staff', 'driver']), 403, 'Staff at driver accounts lang ang puwedeng i-deactivate.');

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('success', $user->name . ' has been deactivated.');
    }

    public function activate(User $user)
    {
        $this->ensureAdmin();

        abort_unless(in_array($user->role, ['staff', 'driver']), 403, 'Staff at driver accounts lang ang puwedeng i-activate.');

        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')->with('success', $user->name . ' has been activated.');
    }
}