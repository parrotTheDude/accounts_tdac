@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Subscriptions</h1>

  <div class="bg-white shadow-md rounded-lg p-6">
    <p class="text-gray-600">Manage all mailing list subscriptions here.</p>

    <div class="mt-4">
      <ul class="list-disc ml-6 text-gray-700">
        <li>View who is subscribed to what</li>
        <li>Manually add/remove subscribers</li>
        <li>Track unsubscriptions</li>
      </ul>
    </div>
  </div>
@endsection