@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-2">User Settings</h1>

<div class="flex items-center gap-2 text-gray-600 text-sm mb-6">
  <x-badge :label="$user->role_name" :type="$user->user_type" />
</div>

@if (session('success'))
  <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4 shadow">
    ✅ {{ session('success') }}
  </div>
@endif

<form method="POST" action="{{ route('settings.update') }}" class="space-y-6 max-w-lg">
  @csrf

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

  <div>
    <label class="block font-medium text-gray-700 mb-1 flex items-center gap-2">
        Email
        @if ($user->email_verified_at)
        <img src="{{ asset('icons/correct.svg') }}" alt="Verified" class="w-4 h-4">
        @else
        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Unverified</span>
        @endif
    </label>

    <div class="flex items-center gap-4">
        <input type="email" value="{{ $user->email }}" disabled
            class="w-full bg-gray-100 border rounded-md px-3 py-2 cursor-not-allowed">

        @if (!$user->email_verified_at)
        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit"
                    class="text-sm text-blue-600 hover:underline">
            Verify Email
            </button>
        </form>
        @endif
    </div>
    </div>

  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
    Save Changes
  </button>
</form>
@endsection