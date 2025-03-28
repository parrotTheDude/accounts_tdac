@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">🔗 Link User to {{ $participant->full_name }}</h1>

<form method="POST" action="{{ route('participants.links.store', $participant) }}" class="space-y-4 max-w-lg">
    @csrf

    <div>
        <label class="block font-medium text-gray-700 mb-1">Select User</label>
        <select name="related_user_id" class="w-full border rounded-md px-3 py-2" required>
            <option value="">-- Select a user --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ ucfirst($user->user_type) }}) - {{ $user->email }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block font-medium text-gray-700 mb-1">Relationship</label>
        <select name="relationship" class="w-full border rounded-md px-3 py-2" required>
            <option value="">Select relationship</option>
            <option value="parent">Parent</option>
            <option value="support_coordinator">Support Coordinator</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Link User</button>
</form>

@if($participant->parentLinks->count() || $participant->supportCoordinatorLinks->count())
    <div class="mt-8 bg-white p-6 rounded shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-800">🔗 Existing Links</h2>

        {{-- Parents --}}
        @if($participant->parentLinks->count())
            <h3 class="text-md font-semibold text-gray-600 mt-4">👨‍👩‍👧 Parents</h3>
            <ul class="space-y-2">
                @foreach($participant->parentLinks as $link)
                    <li>
                        <strong>{{ $link->relatedUser->full_name ?? 'Missing User' }}</strong> - {{ $link->relatedUser->email ?? 'N/A' }}
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Support Coordinators --}}
        @if($participant->supportCoordinatorLinks->count())
            <h3 class="text-md font-semibold text-gray-600 mt-4">🧑‍💼 Support Coordinators</h3>
            <ul class="space-y-2">
                @foreach($participant->supportCoordinatorLinks as $link)
                    <li>
                        <strong>{{ $link->relatedUser->full_name ?? 'Missing User' }}</strong> - {{ $link->relatedUser->email ?? 'N/A' }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

<a href="{{ route('users.edit', $participant) }}" class="block mt-4 text-sm text-gray-500 hover:underline">⬅️ Back to Participant</a>
@endsection