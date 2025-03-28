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

    {{-- Button to switch to archived users --}}
    <a href="{{ route('users.index', ['archived' => 1]) }}"
       class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 text-sm flex items-center">
        🗄️ Show Archived Users
    </a>
</div>

<!-- Users Table -->
<table class="w-full bg-white rounded-lg shadow overflow-hidden">
    <thead class="bg-gray-100 text-left text-sm text-gray-700">
        <tr>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Role</th>
            <th class="px-4 py-3">Engagement</th>
            <th class="px-4 py-3">Actions</th>
        </tr>
    </thead>
    <tbody class="text-sm text-gray-800 divide-y">
        @foreach ($users as $user)
            <tr>
                <!-- Name -->
                <td class="px-4 py-2">
                    {{ $user->full_name }}
                    @if ($user->isArchived())
                        <x-badge label="Archived" type="archived" />
                    @endif
                </td>

                <!-- Email -->
                <td class="px-4 py-2 flex items-center gap-2">
                    {{ $user->email }}
                    @if ($user->email_verified_at)
                        <img src="{{ asset('icons/correct.svg') }}" alt="Verified" class="w-4 h-4" title="Verified">
                    @endif
                </td>

                <!-- Role -->
                <td class="px-4 py-2">
                    <x-badge :label="$user->role_name" :type="$user->user_type" />
                </td>

                <!-- Engagement -->
                <td class="px-4 py-2">
                    <x-badge :label="ucfirst($user->engagement_status ?? 'Not Set')" :type="$user->engagement_status ?? 'unknown'" />
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