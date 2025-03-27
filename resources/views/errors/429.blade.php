@extends('layouts.dashboard')

@section('content')
<div class="text-center mt-20">
  <h1 class="text-5xl font-bold text-orange-500 mb-4">429</h1>
  <p class="text-xl text-gray-700 mb-6">🚨 Too many requests. Please slow down.</p>

  <a href="{{ route('dashboard') }}"
     class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md text-sm font-medium">
    ⏳ Back to Dashboard
  </a>
</div>
@endsection