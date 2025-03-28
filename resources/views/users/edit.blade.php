@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">🛠️ Edit User: {{ $user->full_name }}</h1>

  <x-badge :label="ucfirst($user->user_type)" :type="$user->user_type" />

  @if (session('success'))
    <div class="mb-4 text-green-700 bg-green-100 border border-green-300 px-4 py-3 rounded shadow-sm">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded shadow-sm">
      <strong>Error:</strong> {{ $errors->first() }}
    </div>
  @endif

  <!-- Main form starts -->
  <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6 max-w-lg">
    @csrf
    @method('PUT')

    <!-- Name Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block font-medium text-gray-700 mb-1">First Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}"
               class="w-full border rounded-md px-3 py-2">
      </div>
      <div>
        <label class="block font-medium text-gray-700 mb-1">Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
               class="w-full border rounded-md px-3 py-2">
      </div>
    </div>

    <!-- Email with status -->
    <div>
      <label class="block font-medium text-gray-700 mb-1 flex items-center gap-2">
        Email
        <span class="inline-block w-24">
          @if ($user->hasVerifiedEmail())
            <img src="{{ asset('icons/correct.svg') }}" alt="Verified" class="w-4 h-4 inline-block">
          @else
            <button type="button"
                    onclick="document.getElementById('sendVerificationForm').submit()"
                    class="text-sm text-blue-600 hover:underline">
              Verify Email
            </button>
          @endif
        </span>
      </label>
      <input type="email" value="{{ $user->email }}" disabled
             class="w-full border bg-gray-100 px-3 py-2 cursor-not-allowed">
    </div>

    <!-- Role -->
    <div>
      <label class="block font-medium text-gray-700 mb-1">Role</label>
      <select name="user_type" id="user-role" class="w-full border rounded-md px-3 py-2">
        @foreach (auth()->user()->getAvailableRoles() as $value => $label)
          <option value="{{ $value }}" {{ old('user_type', $user->user_type) === $value ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- Participant Type -->
    <div id="participant-type-section" class="{{ in_array($user->user_type, ['participant', 'parent']) ? '' : 'hidden' }}">
      <label class="block font-medium text-gray-700 mb-1">Participant Type</label>
      <select name="gender" class="w-full border rounded-md px-3 py-2">
        <option value="">Not specified</option>
        @foreach (['male', 'female', 'teen', 'child'] as $type)
          <option value="{{ $type }}" {{ old('gender', $user->gender) === $type ? 'selected' : '' }}>
            {{ ucfirst($type) }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- Engangement Status -->
    <div>
        <label class="block font-medium text-gray-700 mb-1">Engagement Status</label>
        <select name="engagement_status" class="w-full border rounded-md px-3 py-2">
            <option value="">Not Set</option>
            <option value="engaged" {{ old('engagement_status', $user->engagement_status) === 'engaged' ? 'selected' : '' }}>Engaged</option>
            <option value="limited" {{ old('engagement_status', $user->engagement_status) === 'limited' ? 'selected' : '' }}>Limited</option>
            <option value="unengaged" {{ old('engagement_status', $user->engagement_status) === 'unengaged' ? 'selected' : '' }}>Unengaged</option>
        </select>
    </div>

    <!-- Manage Subscriptions -->
    <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">🗂️ Manage Subscriptions</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach ($lists as $list)
          @php
            $subscribed = $user->subscriptions->contains(function ($sub) use ($list) {
              return $sub->list_name === $list && $sub->subscribed;
            });
          @endphp

          <label class="flex items-center space-x-2">
            <input type="checkbox" name="subscriptions[]" value="{{ $list }}" {{ $subscribed ? 'checked' : '' }}
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200">
            <span>{{ ucfirst(str_replace('_', ' ', $list)) }}</span>
          </label>
        @endforeach
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-between items-center mt-6">
      <a href="{{ route('users.index') }}"
        class="text-sm text-gray-600 hover:underline flex items-center gap-1">
          ⬅️ Back to Users
      </a>

      <div class="flex gap-2">
          <button type="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
              💾 Save Changes
          </button>
      </div>
  </div>
  </form>
  <!-- Main form ends -->

  <!-- Hidden Verification Form -->
  <form id="sendVerificationForm" action="{{ route('users.sendVerification', $user->id) }}" method="POST" class="hidden">
    @csrf
  </form>

  @if($user->user_type === 'participant')
    <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">👥 Linked Users</h2>
        
        @if ($user->participantLinks?->count())
            <ul class="divide-y divide-gray-200 text-sm">
                @foreach($user->participantLinks as $link)
                <li class="py-2 flex justify-between items-center">
                    <div>
                        <strong>{{ $link->relatedUser->full_name }}</strong>
                        <x-badge :label="ucfirst($link->relatedUser->user_type)" :type="$link->relatedUser->user_type" class="ml-2" />
                        <span class="ml-1 text-xs text-gray-500">({{ ucfirst($link->relationship) }})</span>
                    </div>
                    <form method="POST" action="{{ route('participants.links.unlink', [$user, $link->relatedUser]) }}" onsubmit="return confirm('Unlink this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 text-xs hover:underline">🗑 Unlink</button>
                    </form>
                </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-600">No linked users yet.</p>
        @endif

        <a href="{{ route('participants.links.create', $user) }}"
           class="mt-3 inline-block bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
            ➕ Link Parent / Support Coordinator
        </a>
    </div>
@endif

@if($user->user_type === 'parent' || $user->user_type === 'support_coordinator')
    <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">👥 Linked Participants</h2>

        @if ($user->linkedParticipants?->count())
            <ul class="divide-y divide-gray-200 text-sm">
                @foreach($user->linkedParticipants as $participant)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                          <strong>{{ $participant->full_name }}</strong>
                          <x-badge :label="ucfirst($participant->user_type)" :type="$participant->user_type" class="ml-2" />
                        </div>
                        <form method="POST" action="{{ route('participants.links.unlink', [$participant, $user]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-xs">🗑 Unlink</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-600">No linked participants yet.</p>
        @endif
    </div>
@endif

  <!-- Current Subscriptions View (separate from form) -->
  @if ($subscriptions->count())
    <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 Current Subscription Status</h2>
      <ul class="divide-y divide-gray-200 text-sm">
        @foreach ($subscriptions as $sub)
          <li class="py-2 flex justify-between items-center">
            <span>{{ ucfirst($sub->list_name) }}</span>
            <span class="{{ $sub->subscribed ? 'text-green-600' : 'text-red-600' }}">
              {{ $sub->subscribed ? 'Subscribed' : 'Unsubscribed' }}
            </span>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (!$user->isArchived())
    <form action="{{ route('users.archive', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this user?')" class="inline">
        @csrf
        <button type="submit" class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">Archive</button>
    </form>
@else
    <form action="{{ route('users.unarchive', $user) }}" method="POST" onsubmit="return confirm('Restore this user?')" class="inline">
        @csrf
        <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs">Unarchive</button>
    </form>
@endif

  <script>
    const roleSelect = document.getElementById('user-role');
    const participantSection = document.getElementById('participant-type-section');

    function toggleParticipantSection() {
      const selectedRole = roleSelect.value;
      if (['participant', 'parent'].includes(selectedRole)) {
        participantSection.classList.remove('hidden');
      } else {
        participantSection.classList.add('hidden');
      }
    }

    roleSelect.addEventListener('change', toggleParticipantSection);
    window.addEventListener('DOMContentLoaded', toggleParticipantSection);
  </script>
@endsection