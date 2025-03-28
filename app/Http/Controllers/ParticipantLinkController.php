<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ParticipantLink;
use Illuminate\Http\Request;

class ParticipantLinkController extends Controller
{
    public function create(User $participant)
    {
        // Only allow linking to participant type users
        if ($participant->user_type !== 'participant') {
            abort(403, 'Only participants can have links.');
        }

        // Get linkable users
        $users = User::whereIn('user_type', ['parent', 'support_coordinator', 'external'])
                    ->whereNull('archived_at')
                    ->get();

        return view('participant_links.create', compact('participant', 'users'));
    }

    public function store(Request $request, User $participant)
    {
        $validated = $request->validate([
            'related_user_id' => ['required', 'exists:users,id'],
            'relationship' => ['required', 'in:parent,support_coordinator'],
        ]);
    
        // Check if link already exists
        if (ParticipantLink::where('participant_id', $participant->id)->where('linked_user_id', $validated['related_user_id'])->exists()) {
            return back()->with('error', 'This user is already linked to the participant.');
        }
    
        // Create link
        ParticipantLink::create([
            'participant_id' => $participant->id,
            'linked_user_id' => $validated['related_user_id'],
            'relationship' => $validated['relationship'],
        ]);
    
        return redirect()
            ->route('users.edit', $participant)
            ->with('success', 'User successfully linked.');
    }

    public function unlink(User $participant, User $related)
    {
        ParticipantLink::where('participant_id', $participant->id)
            ->where('linked_user_id', $related->id)
            ->delete();

        return back()->with('success', 'Link removed.');
    }
}