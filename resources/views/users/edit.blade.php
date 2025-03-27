@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">🛠️ Edit User: {{ $user->full_name }}</h1>

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

  <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6 max-w-lg">
    @csrf
    @method('PUT')

    <!-- Name -->
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

    <!-- Email -->
    <div>
      <label class="block font-medium text-gray-700 mb-1">Email</label>
      <input type="email" value="{{ $user->email }}" disabled
             class="w-full border bg-gray-100 px-3 py-2 cursor-not-allowed">
    </div>

    <!-- Role (restrict options based on current user rank) -->
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

    <!-- Participant Type (only for participant/parent) -->
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

    <!-- Actions -->
    <div class="flex justify-between items-center mt-6">
      <a href="{{ route('users.index') }}"
         class="text-sm text-gray-600 hover:underline flex items-center gap-1">
         ⬅️ Back to Users
      </a>
      <button type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
        💾 Save Changes
      </button>
    </div>
  </form>

  @if ($subscriptions->count())
  <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">🗂️ Subscriptions</h2>
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

<hr class="my-8">

<h2 class="text-xl font-bold text-gray-800 mb-4">Email Subscriptions</h2>

<form method="POST" action="{{ route('users.updateSubscriptions', $user) }}" class="space-y-4">
  @csrf

  @php
    $userSubs = $user->subscriptions->pluck('list_name')->toArray();
    $allLists = ['newsletter', 'calendar-release', 'bonus-event', 'teens-social', 'service-agreements', 'participant-info'];
  @endphp

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    @foreach ($allLists as $list)
      <label class="flex items-center space-x-2">
        <input type="checkbox" name="subscriptions[]" value="{{ $list }}"
          {{ in_array($list, $userSubs) ? 'checked' : '' }}
          class="form-checkbox text-blue-600">
        <span class="capitalize text-sm">{{ str_replace('-', ' ', $list) }}</span>
      </label>
    @endforeach
  </div>

  <button type="submit"
          class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
    Save Subscriptions
  </button>
</form>

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