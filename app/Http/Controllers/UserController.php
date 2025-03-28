<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\VerificationToken;
use App\Services\PostmarkService;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $query = User::query()->latest();

        // Filter by allowed roles
        $allowedRoles = $currentUser->getAvailableRoles()->keys()->all();
        $query->whereIn('user_type', $allowedRoles);

        // Check if archived filter is applied
        if ($request->boolean('archived')) {
            $query->where('archived', true);
        } else {
            $query->where('archived', false);
        }

        // Optional search
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
        $this->authorize('update', $user);

        // Get distinct subscription list names from the DB
        $lists = \App\Models\Subscription::distinct()->pluck('list_name');

        // Get current subscriptions for the user
        $subscriptions = $user->subscriptions;

        return view('users.edit', compact('user', 'lists', 'subscriptions'));
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

        // First, get all known list names to avoid adding new unintended ones
        $validLists = \App\Models\Subscription::distinct()->pluck('list_name')->toArray();

        // Get selected subscriptions from form
        $selected = $request->input('subscriptions', []);

        // Loop through all valid lists
        foreach ($validLists as $listName) {
            $isSubscribed = in_array($listName, $selected);

            // Update or create subscription entry
            \App\Models\Subscription::updateOrCreate(
                ['user_id' => $user->id, 'list_name' => $listName],
                ['subscribed' => $isSubscribed]
            );
        }

        return redirect()->route('users.edit', $user->id)->with('success', 'User updated!');
    }

    public function updateSubscriptions(Request $request, User $user)
    {
        $this->authorize('edit-subscriptions', $user);

        $subscriptions = $request->input('subscriptions', []);

        // Delete existing
        $user->subscriptions()->delete();

        // Add new
        foreach ($subscriptions as $list) {
            $user->subscriptions()->create([
                'list_name' => $list,
                'subscribed' => true,
            ]);
        }

        return back()->with('success', 'Subscriptions updated.');
    }

    public function sendVerificationEmail(User $user, PostmarkService $postmark)
    {
        $this->authorize('update', $user);

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email is already verified.');
        }

        // Clear existing tokens
        VerificationToken::where('user_id', $user->id)->delete();

        $token = VerificationToken::create([
            'user_id' => $user->id,
            'token' => strtoupper(Str::random(6)),
            'expires_at' => now()->addHour(),
        ]);

        $url = route('verification.verify', $token->token);

        $postmark->sendVerificationEmail($user->email, $url);

        return back()->with('success', 'Verification email sent to user!');
    }

    public function archive(User $user)
    {
        $this->authorize('update', $user);
        $user->update(['archived_at' => now()]);
        return back()->with('success', 'User archived.');
    }

    public function unarchive(User $user)
    {
        $this->authorize('update', $user);
        $user->update(['archived_at' => null]);
        return back()->with('success', 'User unarchived.');
    }
}