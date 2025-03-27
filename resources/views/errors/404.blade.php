@extends('layouts.public')

@section('content')
<div class="text-center mt-20">
  <h1 class="text-5xl font-bold text-gray-600 mb-4">404</h1>
  <p class="text-xl text-gray-700 mb-6">🤷 Page not found.</p>

  <a href="{{ route('dashboard') }}"
     class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm font-medium">
    ⬅️ Back to Dashboard
  </a>
</div>
@endsection