<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'TDAC Portal' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

  <!-- Top nav -->
  <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
    <!-- Logo (clickable) -->
    <a href="{{ route('dashboard') }}" class="flex items-center">
      <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" alt="TDAC Logo" class="h-16">
    </a>

    <div class="flex items-center gap-4">
      <!-- User Settings Button -->
      <a href="/settings"
         class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium px-3 py-2 rounded-md shadow-sm">
        <img src="{{ asset('icons/user.svg') }}" alt="User Settings" class="w-4 h-4">
        {{ Auth::user()->name }}
      </a>

      <!-- Logout Button -->
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium px-3 py-2 rounded-md shadow-sm">
          <img src="{{ asset('icons/logout.svg') }}" alt="Logout" class="w-4 h-4">
          Logout
        </button>
      </form>
    </div>
  </nav>

  <!-- Main content -->
  <main class="flex-1 p-8">
      @yield('content')
  </main>

</body>
</html>