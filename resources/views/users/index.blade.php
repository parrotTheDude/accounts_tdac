@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">👥 Users</h1>

<!-- Filter Bar -->
<div class="flex flex-wrap items-center gap-4 mb-6">
  <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-4">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search name or email"
           class="px-3 py-2 border border-gray-300 rounded-md w-64">

    <select name="role" class="px-3 py-2 border border-gray-300 rounded-md">
      <option value="">All Roles</option>
      @foreach (auth()->user()->getAvailableRoles() as $value => $label)
        <option value="{{ $value }}" {{ request('role') === $value ? 'selected' : '' }}>
          {{ $label }}
        </option>
      @endforeach
    </select>

    <label class="inline-flex items-center space-x-2 text-sm">
        <input type="checkbox" name="show_archived" value="1" {{ request('show_archived') ? 'checked' : '' }}>
        <span>Show Archived Users</span>
    </label>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
      Filter
    </button>
  </form>

  @if(request()->has('search') || request()->has('role'))
    <a href="{{ route('users.index') }}"
       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-sm">
      Reset Filters
    </a>
  @endif
</div>

<!-- Users Table -->
<table class="w-full bg-white rounded-lg shadow overflow-hidden">
  <thead class="bg-gray-100 text-left text-sm text-gray-700">
    <tr>
      <th class="px-4 py-3">Name</th>
      <th class="px-4 py-3">Email</th>
      <th class="px-4 py-3">Role</th>
      <th class="px-4 py-3">Actions</th>
    </tr>
  </thead>
  <tbody class="text-sm text-gray-800 divide-y">
    @foreach ($users as $user)
      <tr>
        <td class="px-4 py-2">{{ $user->full_name }}
        @if ($user->isArchived())
            <span class="ml-2 bg-red-100 text-red-700 px-2 py-1 text-xs rounded-full">Archived</span>
        @endif
        </td>

        <!-- Email + Verified Badge -->
        <td class="px-4 py-2 flex items-center gap-2">
          {{ $user->email }}
          @if ($user->email_verified_at)
            <img src="{{ asset('icons/correct.svg') }}" alt="Verified" class="w-4 h-4" title="Verified">
          @endif
        </td>

        <!-- Role Badge -->
        <td class="px-4 py-2">
          <span class="px-2 py-1 text-xs rounded-full font-semibold capitalize
            @switch($user->user_type)
              @case('admin') bg-yellow-100 text-yellow-800 @break
              @case('superadmin') bg-red-100 text-red-800 @break
              @case('master') bg-purple-100 text-purple-800 @break
              @case('staff') bg-blue-100 text-blue-800 @break
              @case('participant') bg-green-100 text-green-800 @break
              @case('parent') bg-pink-100 text-pink-800 @break
              @case('external') bg-gray-200 text-gray-800 @break
              @default bg-gray-100 text-gray-700
            @endswitch">
            {{ $user->role_name }}
          </span>
        </td>

        <!-- Actions -->
        <td class="px-4 py-2">
          <a href="{{ route('users.edit', $user->id) }}"
             class="text-blue-600 hover:underline text-sm">
            Edit
          </a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

<!-- Pagination -->
<div class="mt-6">
  {{ $users->links() }}
</div>
@endsection