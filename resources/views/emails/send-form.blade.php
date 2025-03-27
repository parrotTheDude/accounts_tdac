@extends('layouts.dashboard')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Send Bulk Email: {{ $template->getName() }}</h1>

<form action="{{ route('emails.sendBulk', $template->getTemplateId()) }}" method="POST" class="space-y-6 max-w-xl">
  @csrf

  <div>
    <label class="block font-medium text-gray-700 mb-1">Subscription List</label>
    <select name="list" required class="w-full border rounded-md px-3 py-2">
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
               class="w-full border rounded-md px-3 py-2 mb-2">
      @endforeach
    </div>
  @endif

  <div>
    <label class="block font-medium text-gray-700 mb-1">Confirm Your Password</label>
    <input type="password" name="password" required class="w-full border rounded-md px-3 py-2">
  </div>

  <button type="submit"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium">
    🚨 Send Bulk Email
  </button>
</form>
@endsection