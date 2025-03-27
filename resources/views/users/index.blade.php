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
  <thead>
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
      <td class="px-4 py-2">{{ $user->full_name }}</td>
      <td class="px-4 py-2">{{ $user->email }}</td>
      <td class="px-4 py-2">
        <form method="POST" action="{{ route('users.updateRole', $user->id) }}" class="flex items-center gap-2">
          @csrf
          @method('PATCH')
          <select name="user_type" onchange="this.form.submit()"
                  class="text-sm border-gray-300 rounded px-2 py-1">
            @foreach (\App\Models\User::ROLES as $key => $label)
              <option value="{{ $key }}" {{ $user->user_type === $key ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </form>
      </td>
      <td class="px-4 py-2">
        <a href="{{ route('users.edit', $user->id) }}" class="text-sm text-blue-600 hover:underline">
          Edit
        </a>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

<div class="mt-6">
  {{ $users->links() }}
</div>
@endsection