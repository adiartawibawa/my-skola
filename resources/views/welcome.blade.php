<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} — Blog"
        href="{{ route('blog.feed') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <header class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-bold text-lg text-indigo-600">
                {{ config('app.name') }}
            </a>
            <nav class="flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('blog.index') }}" class="hover:text-indigo-600">Blog</a>
                @auth
                    <a href="{{ url('/admin') }}" class="hover:text-indigo-600">Dashboard</a>
                @else
                    {{-- <a href="{{ route('login') }}" class="hover:text-indigo-600">Masuk</a> --}}
                @endauth
            </nav>
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Selamat Datang di {{ config('app.name') }}</h1>
        <p class="text-gray-500 max-w-xl mx-auto">
            Sistem informasi akademik sekaligus ruang berbagi cerita, wawasan, dan pengumuman dari sekolah kami.
        </p>
    </section>

    <section class="max-w-6xl mx-auto px-4 pb-20">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Artikel Terbaru</h2>
            <a href="{{ route('blog.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua &rarr;</a>
        </div>

        <livewire:blog.latest-posts />
    </section>

    @livewireScripts
</body>

</html>
