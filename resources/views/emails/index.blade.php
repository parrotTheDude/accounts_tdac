@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Email Templates</h1>

  <a href="{{ route('emails.history') }}"
   class="inline-block mb-6 mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow">
   📜 View Bulk Email History
</a>

  <div class="bg-white shadow-md rounded-lg p-6">
    @if(count($templates))
      <div class="space-y-4">
      @foreach ($templates as $template)
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition flex items-center justify-between">
            <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $template->getName() }}</h2>
            <p class="text-sm text-gray-500">Template ID: {{ $template->getTemplateId() }}</p>
            </div>
            <div class="flex gap-3">
            <a href="{{ route('emails.show', $template->getTemplateId()) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow">
                View Template
            </a>
            <a href="{{ route('emails.sendForm', $template->getTemplateId()) }}"
                class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow">
                Send Bulk
            </a>
            </div>
        </div>
        @endforeach
      </div>
    @else
      <p class="text-gray-500">No templates found in Postmark.</p>
    @endif
  </div>
@endsection