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
    <div class="flex items-center">
      <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" alt="TDAC Logo" class="h-16">
    </div>
    <div class="flex items-center gap-4">
      <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="text-red-600 hover:underline text-sm" type="submit">Logout</button>
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