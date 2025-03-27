@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $template->getName() }}</h1>

  @if (session('success'))
    <div class="mb-4 text-green-600 font-medium">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="mb-4 text-red-600 font-medium">
        {{ $errors->first() }}
    </div>
    @endif

  <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
    <p><strong>Template ID:</strong> {{ $template->getTemplateId() }}</p>
    <p><strong>Subject:</strong> {{ $template->getSubject() }}</p>

    @if (count($variables))
    <div class="mt-8">
        <h3 class="text-lg font-semibold mb-2">Template Variables</h3>
        <ul class="list-disc pl-6 text-gray-700">
        @foreach ($variables as $var)
            <li>{{ $var }}</li>
        @endforeach
        </ul>
    </div>
    @endif

    <div class="mt-10">
    <h3 class="text-lg font-semibold mb-2">Send Test Email</h3>

    <form action="{{ route('emails.sendTest', $template->getTemplateId()) }}" method="POST" class="space-y-4">
        @csrf

        <div>
        <label class="block text-sm font-medium text-gray-700">Send to email</label>
        <input type="email" name="to" required
                class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2">
        </div>

        @if(count($variables))
        <div>
            <label class="block text-sm font-medium text-gray-700">Template Variables</label>
            @foreach ($variables as $var)
            <input type="text" name="variables[{{ $var }}]" placeholder="{{ $var }}"
                    class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 mb-2">
            @endforeach
        </div>
        @endif

        <a href="{{ route('emails.index') }}"
        class="inline-block mb-6 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
        ⬅️ Back to Email Templates
        </a>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        Send Test Email
        </button>
    </form>
    </div>
    
  </div>
@endsection