@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">🔗 Link User to {{ $participant->full_name }}</h1>

@if (session('success'))
    <div class="mb-4 text-green-700 bg-green-100 border border-green-300 px-4 py-3 rounded shadow-sm">
        ✅ {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('participants.links.store', $participant) }}" class="space-y-4 max-w-lg" id="link-form">
    @csrf

    <div>
        <label class="block font-medium text-gray-700 mb-1">Search User</label>
        <input type="text" id="user-search" placeholder="Type name or email..." class="w-full border rounded-md px-3 py-2">
    </div>

    <div id="user-list" class="border rounded-md p-2 max-h-60 overflow-y-auto space-y-2 bg-white">
        @forelse($users as $user)
            <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded user-item">
                <input type="radio" name="related_user_id" value="{{ $user->id }}" required>
                <div>
                    <strong class="user-name">{{ $user->full_name }}</strong> 
                    <span class="text-xs text-gray-500 user-role">({{ ucfirst($user->user_type) }})</span><br>
                    <small class="user-email">{{ $user->email }}</small>
                </div>
            </label>
        @empty
            <p class="text-sm text-gray-500">No users available for linking.</p>
        @endforelse
    </div>

    <div>
        <label class="block font-medium text-gray-700 mb-1">Relationship</label>
        <select name="relationship" class="w-full border rounded-md px-3 py-2" required>
            <option value="">Select relationship</option>
            <option value="parent">Parent</option>
            <option value="support_coordinator">Support Coordinator</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md w-full">Link User</button>
</form>

{{-- Existing Links --}}
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

<script>
// Client-side search filter
document.getElementById('user-search').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.user-item').forEach(item => {
        const name = item.querySelector('.user-name').textContent.toLowerCase();
        const email = item.querySelector('.user-email').textContent.toLowerCase();
        item.style.display = name.includes(query) || email.includes(query) ? '' : 'none';
    });
});
</script>
@endsection