@extends('layouts.auth')

@section('content')
  <div class="bg-white p-8 rounded-lg shadow text-center max-w-md mx-auto">
    @if ($status === 'success')
      <h1 class="text-2xl font-bold text-green-600 mb-4">🎉 Email Verified!</h1>
      <p class="text-gray-700 mb-6">
        Thanks for verifying your email. You're all set to continue.
      </p>
    @elseif ($status === 'already_verified')
      <h1 class="text-2xl font-bold text-yellow-600 mb-4">✅ Already Verified</h1>
      <p class="text-gray-700 mb-6">
        This email has already been verified.
      </p>
    @elseif ($status === 'invalid')
      <h1 class="text-2xl font-bold text-red-600 mb-4">❌ Invalid or Expired Link</h1>
      <p class="text-gray-700 mb-6">
        This verification link is no longer valid. Please request a new one.
      </p>
    @elseif ($status === 'not_found')
      <h1 class="text-2xl font-bold text-red-600 mb-4">❌ User Not Found</h1>
      <p class="text-gray-700 mb-6">
        We couldn't find a user with that email address.
      </p>
    @endif

    <a href="https://thatdisabilityadventurecompany.com.au"
       class="mt-6 inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
      Go to Website
    </a>
  </div>
@endsection