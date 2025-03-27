@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">👥 Users</h1>

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
          <a href="#"
             class="text-blue-600 hover:underline text-sm">Edit</a>
          <a href="#"
             class="text-yellow-600 hover:underline text-sm">Unsubscribe</a>
          <a href="#"
             onclick="return confirm('Are you sure you want to delete this user?')"
             class="text-red-600 hover:underline text-sm">Delete</a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

<div class="mt-6">
  {{ $users->links() }}
</div>
@endsection