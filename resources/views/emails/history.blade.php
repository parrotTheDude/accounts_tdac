@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">📜 Bulk Email History</h1>

  <form action="{{ route('emails.index') }}" method="GET" class="mb-6">
    <button type="submit"
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium px-4 py-2 rounded-md shadow">
      ⬅️ Back to Email Templates
    </button>
  </form>

  <div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-800">
      <thead class="bg-gray-100 text-gray-700">
        <tr>
          <th class="px-4 py-3">Date</th>
          <th class="px-4 py-3">User</th>
          <th class="px-4 py-3">Template</th>
          <th class="px-4 py-3">List</th>
          <th class="px-4 py-3">Sent</th>
          <th class="px-4 py-3">Failed</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach ($emails as $email)
          <tr>
            <td class="px-4 py-2">{{ $email->created_at->format('M j, Y H:i') }}</td>
            <td class="px-4 py-2">{{ $email->user->full_name ?? 'Unknown' }}</td>
            <td class="px-4 py-2">{{ $email->template_name }}</td>
            <td class="px-4 py-2">{{ $email->list_name }}</td>
            <td class="px-4 py-2">{{ number_format($email->emails_sent) }}</td>
            <td class="px-4 py-2 text-red-600">{{ number_format($email->failed_count) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $emails->links() }}
  </div>
@endsection