@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">📜 Bulk Email History</h1>

  <table class="w-full bg-white rounded-lg shadow overflow-hidden">
    <thead class="bg-gray-100 text-left text-sm text-gray-700">
      <tr>
        <th class="px-4 py-3">Date</th>
        <th class="px-4 py-3">User</th>
        <th class="px-4 py-3">Template</th>
        <th class="px-4 py-3">List</th>
        <th class="px-4 py-3">Sent</th>
        <th class="px-4 py-3">Failed</th>
      </tr>
    </thead>
    <tbody class="text-sm text-gray-800 divide-y">
      @foreach ($emails as $email)
        <tr>
          <td class="px-4 py-2">{{ $email->created_at->format('M j, Y H:i') }}</td>
          <td class="px-4 py-2">{{ $email->user->full_name ?? 'Unknown' }}</td>
          <td class="px-4 py-2">{{ $email->template_name }}</td>
          <td class="px-4 py-2">{{ $email->list_name }}</td>
          <td class="px-4 py-2">{{ $email->emails_sent }}</td>
          <td class="px-4 py-2 text-red-600">{{ $email->failed_count }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-6">
    {{ $emails->links() }}
  </div>
@endsection