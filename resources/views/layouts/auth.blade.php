<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'TDAC Portal' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center justify-center p-6">

  <div class="w-full max-w-xl">
    <div class="flex justify-center mb-8">
      <a href="https://thatdisabilityadventurecompany.com.au">
        <img src="https://thatdisabilityadventurecompany.com.au/icons/logo.webp" alt="TDAC Logo" class="h-16">
      </a>
    </div>

    @yield('content')
  </div>

</body>
</html>