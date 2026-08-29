@props([
    'title' => null,
    'description' => null,
    'ogImage' => null,
    'canonicalUrl' => null,
    'type' => 'website',
])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}@if ($title && $title !== config('app.name'))
            — {{ config('app.name') }}
        @endif
    </title>
    <meta name="description" content="{{ $description ?? config('app.name') . ' — Sistem Informasi Akademik' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? '' }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ Storage::url($ogImage) }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $description ?? '' }}">

    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} — Blog"
        href="{{ route('blog.feed') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;0,9..144,900;1,9..144,500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        :root {
            --font-display: 'Fraunces', serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-body);
        }

        .font-display {
            font-family: var(--font-display);
        }

        .font-mono {
            font-family: var(--font-mono);
            font-variant-numeric: tabular-nums;
        }

        ::selection {
            background: #C89B3C;
            color: #4A0D17;
        }

        :focus-visible {
            outline: 2px solid #C89B3C;
            outline-offset: 3px;
            border-radius: 2px;
        }

        @media (prefers-reduced-motion: no-preference) {
            .seal-stamp {
                animation: stampIn .9s cubic-bezier(.16, 1, .3, 1) both;
            }

            .fade-up {
                animation: fadeUp .7s ease both;
            }
        }

        @keyframes stampIn {
            from {
                opacity: 0;
                transform: scale(1.35) rotate(-10deg);
            }

            to {
                opacity: 1;
                transform: scale(1) rotate(-6deg);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            animation: marquee linear infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-marquee {
                animation: none;
            }

            .seal-stamp,
            .fade-up {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        @keyframes heroSlide {
            0% {
                opacity: 0;
                transform: scale(1.06);
            }

            6% {
                opacity: 1;
            }

            25% {
                opacity: 1;
                transform: scale(1.14);
            }

            31% {
                opacity: 0;
            }

            100% {
                opacity: 0;
            }
        }

        .hero-slide {
            animation: heroSlide 16s infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-slide {
                animation: none;
                opacity: 0;
                transform: none;
            }

            .hero-slide:first-child {
                opacity: 1;
            }
        }
    </style>

    {{ $head ?? '' }}
</head>

<body class="bg-gray-50 text-gray-800 antialiased">
    {{ $slot }}

    @livewireScripts
    {{ $scripts ?? '' }}
</body>

</html>
