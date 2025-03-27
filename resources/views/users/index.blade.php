@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Users</h1>

  <div class="bg-white shadow-md rounded-lg p-6">
    <p class="text-gray-600">This is where you'll manage all registered users.</p>
    
    <div class="mt-4">
      <ul class="list-disc ml-6 text-gray-700">
        <li>View and search users</li>
        <li>Assign roles (admin, customer, etc)</li>
        <li>Reset passwords, edit user info</li>
        <li>Delete or deactivate accounts</li>
      </ul>
    </div>
  </div>
@endsection