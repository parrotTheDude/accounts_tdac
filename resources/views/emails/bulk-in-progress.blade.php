@extends('layouts.dashboard')

@section('content')
  <h1 class="text-2xl font-bold text-gray-800 mb-4">Sending Emails...</h1>
  <p class="mb-4 text-gray-600">Sending to the <strong>{{ $list }}</strong> list.</p>

  <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
    <div id="progress-bar" class="bg-blue-600 h-full text-white text-center text-sm leading-6" style="width: 0%">
      0%
    </div>
  </div>

  <p class="mt-4 text-sm text-gray-500" id="status-text">Starting bulk email send...</p>

  <!-- Return Button (hidden by default) -->
  <div id="back-button" class="mt-6 hidden">
    <a href="{{ route('emails.index') }}"
       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium">
      ⬅️ Back to Email Templates
    </a>
  </div>

  <script>
    const bar = document.getElementById('progress-bar');
    const text = document.getElementById('status-text');
    const backBtn = document.getElementById('back-button');

    async function checkProgress() {
      try {
        const res = await fetch('/emails/progress');
        const data = await res.json();

        const percent = data.total > 0 ? Math.round((data.sent / data.total) * 100) : 0;
        bar.style.width = percent + '%';
        bar.innerText = percent + '%';

        text.innerText = `${data.sent} of ${data.total} emails sent...`;

        if (percent < 100) {
          setTimeout(checkProgress, 1000);
        } else {
          text.innerText = '✅ Bulk email complete!';
          backBtn.classList.remove('hidden');
        }
      } catch (err) {
        text.innerText = '⚠️ Failed to fetch progress. Try reloading.';
      }
    }

    checkProgress();
  </script>
@endsection