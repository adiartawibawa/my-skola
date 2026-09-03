@props(['title' => null])

<x-layouts.master :title="$title ?? 'Dashboard'">
    <div class="min-h-screen">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
                <a href="{{ route('home') }}" class="font-bold text-indigo-600">{{ config('app.name') }}</a>

                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>

                    @if (auth()->user()->role->value === 'parent')
                        <a href="{{ route('portal.link-child') }}" class="text-[var(--brand-primary)] hover:underline">
                            Tautkan Anak
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600">Keluar</button>
                    </form>
                </div>
            </div>

        </nav>

        <main class="max-w-6xl mx-auto px-4 py-8">
            {{ $slot }}
        </main>
    </div>
</x-layouts.master>
