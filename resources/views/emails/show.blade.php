@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $template->getName() }}</h1>

  <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
    <p><strong>Template ID:</strong> {{ $template->getTemplateId() }}</p>
    <p><strong>Subject:</strong> {{ $template->getSubject() }}</p>
    <p><strong>Type:</strong> {{ $template->getTemplateType() }}</p>
    <p><strong>Alias:</strong> {{ $template->getAlias() ?? '—' }}</p>

    <div class="mt-6">
      <h2 class="text-lg font-semibold mb-2">Preview:</h2>
      <iframe src="https://postmarkapp.com/servers/YOUR_SERVER_ID/templates/{{ $template->getTemplateId() }}/preview"
              class="w-full h-[500px] border rounded-md"
              frameborder="0"></iframe>
    </div>

    <div class="mt-6 flex gap-4">
      <form method="POST" action="{{ route('emails.sendTest', $template->getTemplateId()) }}">
        @csrf
        <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
          Send Test Email
        </button>
      </form>

      <a href="{{ route('emails.sendForm', $template->getTemplateId()) }}"
         class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block">
        Send Bulk Email
      </a>
    </div>
  </div>
@endsection