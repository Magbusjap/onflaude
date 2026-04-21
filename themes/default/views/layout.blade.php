<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', option('site_name', 'OnFlaude'))</title>
    <meta name="description" content="@yield('description', '')">
    @vite(['themes/default/css/app.css', 'themes/default/js/app.js'])
</head>
<body class="bg-white text-gray-900 min-h-screen flex flex-col">

    <header class="bg-[#003893] text-white py-4">
        <div class="max-w-5xl mx-auto px-6 flex items-center justify-between">
            <a href="/" class="text-xl font-bold tracking-tight">
                {{ option('site_name', 'OnFlaude') }}
            </a>
            <nav class="flex gap-6 text-sm">
                <a href="/" class="text-[#0097D7] hover:underline">Home</a>
                <a href="/blog" class="text-[#0097D7] hover:underline">Blog</a>
            </nav>
        </div>
    </header>

    <main class="flex-1 py-12">
        <div class="max-w-5xl mx-auto px-6">
            @yield('content')
        </div>
    </main>

    <footer class="bg-[#003893] text-white text-center py-6 text-sm">
        &copy; {{ date('Y') }} {{ option('site_name', 'OnFlaude') }}
    </footer>

</body>

</html>