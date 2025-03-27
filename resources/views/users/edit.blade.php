@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit User: {{ $user->full_name }}</h1>

  @if (session('success'))
    <div class="mb-4 text-green-700 bg-green-100 border border-green-300 px-4 py-3 rounded">
      ✅ {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded">
      <strong>Error:</strong> {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6 max-w-lg">
    @csrf
    @method('PUT')

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
      <label class="block font-medium text-gray-700 mb-1">Email</label>
      <input type="email" value="{{ $user->email }}" disabled
             class="w-full border bg-gray-100 px-3 py-2 cursor-not-allowed">
    </div>

    <div>
      <label class="block font-medium text-gray-700 mb-1">Role</label>
      <select name="user_type" class="w-full border rounded-md px-3 py-2">
        @foreach (\App\Models\User::ROLES as $value => $label)
          <option value="{{ $value }}" {{ old('user_type', $user->user_type) === $value ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block font-medium text-gray-700 mb-1">Gender</label>
      <select name="gender" class="w-full border rounded-md px-3 py-2">
        <option value="">Not specified</option>
        @foreach (['male', 'female', 'nonbinary', 'other'] as $gender)
          <option value="{{ $gender }}" {{ old('gender', $user->gender) === $gender ? 'selected' : '' }}>
            {{ ucfirst($gender) }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="flex justify-between items-center">
      <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:underline">⬅️ Back to users</a>
      <button type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
        Save Changes
      </button>
    </div>
  </form>
@endsection