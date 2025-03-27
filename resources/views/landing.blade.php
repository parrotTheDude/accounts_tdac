<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to TDAC Accounts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen m-0">
  <div class="bg-white p-10 rounded-2xl shadow-md text-center max-w-md w-full">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Welcome to TDAC Accounts</h1>

    @auth
      <p class="text-green-600 font-semibold mb-4">
        You are logged in as <strong>{{ Auth::user()->name }} {{ Auth::user()->last_name }}</strong><br>
        ({{ Auth::user()->email }} – {{ Auth::user()->user_type }})
      </p>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
          class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
          Logout
        </button>
      </form>
    @else
      <a href="{{ route('login') }}"
         class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
        Login
      </a>
    @endauth
  </div>
</body>
</html>