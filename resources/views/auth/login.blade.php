<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - TDAC Accounts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md">
    <img class="mx-auto w-40 mb-6" src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" alt="TDAC Logo">

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      <input type="email" name="email" placeholder="Email"
             class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>

      <input type="password" name="password" placeholder="Password"
             class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>

      <button type="submit"
              class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
        Login
      </button>
    </form>
  </div>
</body>
</html>