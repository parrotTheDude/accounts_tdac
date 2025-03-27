@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Send Bulk Email: {{ $template->getName() }}</h1>

  @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      <strong>Error:</strong> {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('emails.sendBulk', $template->getTemplateId()) }}" method="POST" class="space-y-6 max-w-xl bg-white p-6 rounded-lg shadow-md">
    @csrf

    <div>
      <label class="block font-medium text-gray-700 mb-1">Subscription List</label>
      <select name="list" required class="w-full border border-gray-300 rounded-md px-3 py-2">
        <option value="">Select a list</option>
        @foreach ($lists as $list)
          <option value="{{ $list }}">{{ ucfirst($list) }}</option>
        @endforeach
      </select>
    </div>

    @if(count($variables))
      <div>
        <label class="block font-medium text-gray-700 mb-1">Template Variables</label>
        @foreach ($variables as $var)
          <input type="text" name="variables[{{ $var }}]" placeholder="{{ $var }}"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 mb-2">
        @endforeach
      </div>
    @endif

    <div>
      <label class="block font-medium text-gray-700 mb-1">Confirm Your Password</label>
      <p class="text-sm text-gray-500 mt-1">
        You must confirm your password to send a bulk email.
      </p>
      <input type="password" name="password" required class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1">
    </div>

    <div class="flex justify-between items-center pt-2">
      <form action="{{ route('emails.index') }}" method="GET">
        <button type="submit"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium px-4 py-2 rounded-md shadow">
          ⬅️ Back to Email Templates
        </button>
      </form>

      <button type="submit"
              class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow">
        🚨 Send Bulk Email
      </button>
    </div>
  </form>

  <script>
    const form = document.querySelector('form');
    form.addEventListener('submit', () => {
      const btn = form.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.innerText = 'Sending...';
      }
    });
  </script>
@endsection