<x-layouts.master title="Pemeliharaan">
    <x-slot:head>
        <meta name="robots" content="noindex, nofollow">
    </x-slot:head>

    <div class="min-h-screen flex items-center justify-center px-4 bg-[#FBF6EE]">
        <div class="text-center max-w-md">
            <x-school-seal class="w-24 h-24 mx-auto mb-6" />
            <h1 class="font-display text-2xl font-bold text-[#241512] mb-3">Sedang Dalam Pemeliharaan</h1>
            <p class="text-[#241512]/60">{{ $message }}</p>
        </div>
    </div>
</x-layouts.master>
