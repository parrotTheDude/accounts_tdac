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
    <div class="text-xl font-bold text-blue-600">TDAC</div>
    <div class="flex items-center gap-4">
      <span class="text-sm text-gray-600">{{ Auth::user()->name }} ({{ Auth::user()->user_type }})</span>
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
        <li><a href="/dashboard" class="text-blue-600 font-medium hover:underline">Dashboard</a></li>
        <li><a href="#" class="text-gray-700 hover:text-blue-500">Users</a></li>
        <li><a href="#" class="text-gray-700 hover:text-blue-500">Settings</a></li>
      </ul>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-8">
      @yield('content')
    </main>
  </div>

</body>
</html>