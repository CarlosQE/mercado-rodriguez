<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Sitio Web')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class = "bg-gray-100 text-gray-800 min-h-screen flex flex-col">
    <header class="bg-gradient-to-r from-teal-900 via-teal-700 to-emerald-500 shadow">
        <div class="max-w-7xl mx-auto p-4 flex items-center justify-between">
            <h1 class= "text-xl font-bold text-white">@yield('header', 'Encabezado del Sitio')</h1>
        </div>
    </header>
    <main class="grow container mx-auto p-4">
        @yield('content')
    </main>
    <footer class="bg-gradient-to-r from-teal-900 to-emerald-800 text-white text-center p-4">
        <p>&copy; {{date('Y')}} Mi sitio Web. Todos los derechos resevados</p>
    </footer>
    @yield('script')
</body>
</html>