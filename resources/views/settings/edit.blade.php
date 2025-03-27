@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-2">User Settings</h1>

<div class="flex items-center gap-2 text-gray-600 text-sm mb-6">
  <span>{{ $user->full_name }}</span>
  <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
    {{ $user->role_name }}
    </span>
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
    <label class="block font-medium text-gray-700 mb-1">Gender</label>
    <select name="gender" class="w-full border rounded-md px-3 py-2">
      <option value="">-- Select Gender --</option>
      @foreach (['male', 'female', 'nonbinary', 'other'] as $gender)
        <option value="{{ $gender }}" {{ $user->gender === $gender ? 'selected' : '' }}>
          {{ ucfirst($gender) }}
        </option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block font-medium text-gray-700 mb-1">Email</label>
    <input type="email" value="{{ $user->email }}" disabled
           class="w-full bg-gray-100 border rounded-md px-3 py-2 cursor-not-allowed">
  </div>

  <div>
    <label class="block font-medium text-gray-700 mb-1">New Password</label>
    <input type="password" name="password" class="w-full border rounded-md px-3 py-2">
    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
  </div>

  <div>
    <label class="block font-medium text-gray-700 mb-1">Confirm Password</label>
    <input type="password" name="password_confirmation" class="w-full border rounded-md px-3 py-2">
  </div>

  <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
    Save Changes
  </button>
</form>
@endsection