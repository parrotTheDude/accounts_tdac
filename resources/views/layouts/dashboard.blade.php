<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'TDAC Dashboard' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

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

  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white h-screen p-6 shadow-md">
      <ul class="space-y-4">
        <li>
          <a href="/dashboard"
             class="flex items-center px-2 py-1 rounded-lg 
             {{ request()->is('dashboard') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <img src="{{ asset('icons/home.svg') }}" alt="Dashboard" class="w-5 h-5 mr-2">
            Dashboard
          </a>
        </li>
        <li>
          <a href="/users"
             class="flex items-center px-2 py-1 rounded-lg 
             {{ request()->is('users') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <img src="{{ asset('icons/team.svg') }}" alt="Users" class="w-5 h-5 mr-2">
            Users
          </a>
        </li>
        <li>
          <a href="/subscriptions"
             class="flex items-center px-2 py-1 rounded-lg 
             {{ request()->is('subscriptions') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <img src="{{ asset('icons/ticket.svg') }}" alt="Subscriptions" class="w-5 h-5 mr-2">
            Subscriptions
          </a>
        </li>
        <li>
          <a href="/emails"
             class="flex items-center px-2 py-1 rounded-lg 
             {{ request()->is('emails') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">
            <img src="{{ asset('icons/email.svg') }}" alt="Emails" class="w-5 h-5 mr-2">
            Emails
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8">
      @yield('content')
    </main>
  </div>

</body>
</html>