@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Email Templates</h1>

  <div class="bg-white shadow-md rounded-lg p-6">
    @if(count($templates))
      <ul class="divide-y divide-gray-200">
        @foreach ($templates as $template)
          <li class="py-4 flex justify-between items-center">
            <div>
              <h2 class="font-semibold text-gray-800">{{ $template->getName() }}</h2>
              <p class="text-gray-500 text-sm">Template ID: {{ $template->getTemplateId() }}</p>
            </div>
            <form method="POST" action="/emails/send/{{ $template->getTemplateId() }}">
              @csrf
              <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Send
              </button>
            </form>
          </li>
        @endforeach
      </ul>
    @else
      <p class="text-gray-500">No templates found in Postmark.</p>
    @endif
  </div>
@endsection