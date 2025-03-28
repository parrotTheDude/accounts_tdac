@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">🔗 Link User to {{ $participant->full_name }}</h1>

<form method="POST" action="{{ route('participants.links.store', $participant) }}" class="space-y-4 max-w-lg" id="link-form">
    @csrf

    <div>
        <label class="block font-medium text-gray-700 mb-1">Search User</label>
        <input type="text" id="user-search" class="w-full border rounded-md px-3 py-2" placeholder="Search name or email...">
    </div>

    <div id="search-results" class="space-y-2"></div>

    <input type="hidden" name="related_user_id" id="selected-user">

    <div>
        <label class="block font-medium text-gray-700 mb-1">Relationship</label>
        <select name="relationship" class="w-full border rounded-md px-3 py-2" required>
            <option value="">Select relationship</option>
            <option value="parent">Parent</option>
            <option value="support_coordinator">Support Coordinator</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md" disabled id="link-btn">Link User</button>
</form>

@if($participant->linkedParents()->count() || $participant->linkedSupportCoordinators()->count())
    <div class="mt-8 bg-white p-6 rounded shadow-md space-y-4">
        <h2 class="text-lg font-semibold text-gray-800">🔗 Existing Links</h2>

        {{-- Parents --}}
        @if($participant->linkedParents()->count())
            <h3 class="text-md font-semibold text-gray-600 mt-4">👨‍👩‍👧 Parents</h3>
            <ul class="space-y-2">
                @foreach($participant->linkedParents() as $parent)
                    <li class="flex items-center justify-between bg-gray-50 p-2 rounded">
                        <div>
                            <strong>{{ $parent->full_name }}</strong> - {{ $parent->email }}
                            <span class="text-xs bg-gray-200 px-2 py-1 rounded ml-2">{{ ucfirst($parent->user_type) }}</span>
                        </div>
                        <button data-id="{{ $parent->id }}" class="unlink-btn text-red-600 hover:underline text-sm">
                            🗑 Unlink
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Support Coordinators --}}
        @if($participant->linkedCoordinators()->count())
            <h3 class="text-md font-semibold text-gray-600 mt-4">🧑‍💼 Support Coordinators</h3>
            <ul class="space-y-2">
                @foreach($participant->linkedCoordinators() as $sc)
                    <li class="flex items-center justify-between bg-gray-50 p-2 rounded">
                        <div>
                            <strong>{{ $sc->full_name }}</strong> - {{ $sc->email }}
                            <span class="text-xs bg-gray-200 px-2 py-1 rounded ml-2">{{ ucfirst($sc->user_type) }}</span>
                        </div>
                        <button data-id="{{ $sc->id }}" class="unlink-btn text-red-600 hover:underline text-sm">
                            🗑 Unlink
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

<a href="{{ route('users.edit', $participant) }}" class="block mt-4 text-sm text-gray-500 hover:underline">⬅️ Back to Participant</a>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('user-search');
    const searchResults = document.getElementById('search-results');
    const selectedUserInput = document.getElementById('selected-user');
    const linkButton = document.getElementById('link-btn');
    
    searchInput.addEventListener('input', async function() {
        const query = this.value;
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        searchResults.innerHTML = '<div class="text-sm text-gray-500">Searching...</div>';

        const response = await fetch(`/users/search?q=${encodeURIComponent(query)}&participant_id={{ $participant->id }}`);
        const users = await response.json();

        if (users.length === 0) {
            searchResults.innerHTML = '<div class="text-sm text-gray-500">No users found.</div>';
            return;
        }

        searchResults.innerHTML = '';
        users.forEach(user => {
            const div = document.createElement('div');
            div.className = 'p-2 border rounded hover:bg-gray-50 cursor-pointer';
            div.innerHTML = `<strong>${user.full_name}</strong> (${user.user_type})<br><small>${user.email}</small>`;
            div.addEventListener('click', () => {
                selectedUserInput.value = user.id;
                searchInput.value = `${user.full_name} (${user.user_type})`;
                searchResults.innerHTML = '';
                searchInput.disabled = true;
                linkButton.disabled = false;
            });
            searchResults.appendChild(div);
        });
    });
});

document.querySelectorAll('.unlink-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to unlink this user?')) return;

        fetch("{{ url('/participants/' . $participant->id . '/links') }}/" + btn.dataset.id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(res => res.json())
        .then(data => {
            if(data.success) {
                btn.closest('li').remove();
            } else {
                alert('Failed to unlink.');
            }
        });
    });
});
</script>
@endsection