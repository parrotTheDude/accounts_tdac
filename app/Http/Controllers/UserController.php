<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $query = User::withCount('subscriptions')->latest();

        // Apply role filtering: only allow current user to see roles they are allowed to
        $allowedRoles = array_keys($currentUser->getAvailableRoles());
        $query->whereIn('user_type', $allowedRoles);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Optional role filter (dropdown)
        if ($request->filled('role') && in_array($request->role, $allowedRoles)) {
            $query->where('user_type', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'user_type' => ['required', 'string', 'in:' . implode(',', array_keys(User::ROLES))],
        ]);

        $user->update(['user_type' => $request->user_type]);

        return back()->with('success', 'Role updated.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,nonbinary,other',
            'user_type' => 'required|in:' . implode(',', array_keys(User::ROLES)),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'user_type' => $validated['user_type'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => \Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}