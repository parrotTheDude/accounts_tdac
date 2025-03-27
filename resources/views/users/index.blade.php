@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">👥 Users</h1>

<form method="GET" action="{{ route('users.index') }}" class="flex items-center gap-4 mb-6">
  <input type="text" name="search" value="{{ request('search') }}"
         placeholder="Search name or email"
         class="px-3 py-2 border rounded-md w-64">

  <select name="role" class="px-3 py-2 border rounded-md">
    <option value="">All Roles</option>
    @foreach (\App\Models\User::ROLES as $value => $label)
      <option value="{{ $value }}" {{ request('role') === $value ? 'selected' : '' }}>
        {{ $label }}
      </option>
    @endforeach
  </select>

  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Filter</button>
</form>

<table class="w-full bg-white rounded-lg shadow overflow-hidden">
  <thead class="bg-gray-100 text-left text-sm text-gray-700">
    <tr>
      <th class="px-4 py-3">Name</th>
      <th class="px-4 py-3">Email</th>
      <th class="px-4 py-3">Role</th>
      <th class="px-4 py-3">Subscriptions</th>
      <th class="px-4 py-3">Actions</th>
    </tr>
  </thead>
  <tbody class="text-sm text-gray-800 divide-y">
    @foreach ($users as $user)
      <tr>
        <td class="px-4 py-2">{{ $user->full_name }}</td>
        <td class="px-4 py-2">{{ $user->email }}</td>
        <td class="px-4 py-2">
          <span class="px-2 py-1 text-xs rounded-full font-semibold
            @if($user->user_type === 'admin') bg-yellow-100 text-yellow-800
            @elseif($user->user_type === 'superadmin') bg-red-100 text-red-800
            @elseif($user->user_type === 'master') bg-purple-100 text-purple-800
            @else bg-gray-100 text-gray-800 @endif">
            {{ ucfirst($user->user_type) }}
          </span>
        </td>
        <td class="px-4 py-2">{{ $user->subscriptions_count }}</td>
        <td class="px-4 py-2 space-x-2">
    <a href="{{ route('users.edit', $user->id) }}"
      class="text-blue-600 hover:underline text-sm">Edit</a>
    {{-- Placeholder for Unsubscribe/Delete --}}
  </td>
      </tr>
    @endforeach
  </tbody>
</table>

<div class="mt-6">
  {{ $users->links() }}
</div>
@endsection