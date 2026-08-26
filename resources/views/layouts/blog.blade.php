<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Blog' }}</title>
    <meta name="description" content="{{ $description ?? '' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $title ?? 'Blog' }}">
    <meta property="og:description" content="{{ $description ?? '' }}">
    @if (!empty($ogImage))
        <meta property="og:image" content="{{ Storage::url($ogImage) }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Blog' }}">
    <meta name="twitter:description" content="{{ $description ?? '' }}">

    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} — Blog"
        href="{{ route('blog.feed') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 text-gray-800">
    {{ $slot }}

    @livewireScripts
</body>

</html>
