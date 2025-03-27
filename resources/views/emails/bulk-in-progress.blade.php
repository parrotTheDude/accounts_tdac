@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-4">Sending in progress...</h1>
  <p class="text-gray-600 mb-4">We’re sending your email to the <strong>{{ $list }}</strong> list.</p>

  <div id="log-output" class="bg-black text-green-300 font-mono p-4 rounded-md text-sm h-[400px] overflow-y-auto shadow-inner"></div>

  <script>
    const logEl = document.getElementById('log-output');

    async function fetchLogs() {
      try {
        const res = await fetch('/emails/live-log');
        const text = await res.text();
        logEl.innerText = text;
        logEl.scrollTop = logEl.scrollHeight;
      } catch (err) {
        logEl.innerText += '\n⚠️ Error fetching logs.';
      }
    }

    setInterval(fetchLogs, 2000);
    fetchLogs();
  </script>
@endsection