@props(['title' => null])

<x-layouts.master :title="$title ?? 'Masuk'">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2">
            <span class="font-bold text-lg text-gray-900">{{ config('app.name') }}</span>
        </a>

        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            {{ $slot }}
        </div>

        <p class="mt-8 text-sm text-gray-400">
            &copy; {{ now()->year }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
        </p>
    </div>
</x-layouts.master>
