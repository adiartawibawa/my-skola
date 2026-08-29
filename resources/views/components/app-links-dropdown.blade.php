@php
    $links = \App\Models\SchoolLink::query()->active()->featured()->limit(6)->get();
@endphp

@if ($links->isNotEmpty())
    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
        <button @click="open = !open"
            class="flex items-center gap-1 text-sm font-medium text-[#241512]/70 hover:text-[#6B1220] transition">
            Aplikasi
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" :class="open && 'rotate-180'"
                style="transition: transform .15s" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open" x-transition x-cloak
            class="absolute left-1/2 -translate-x-1/2 mt-3 w-72 bg-[#FBF6EE] border border-[#C89B3C]/30 rounded-xl shadow-lg p-2 z-40">
            @foreach ($links as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener"
                    class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-[#F3EADA] transition">
                    @if ($link->logoUrl())
                        <img src="{{ $link->logoUrl() }}" alt="{{ $link->name }}"
                            class="w-8 h-8 rounded object-contain">
                    @else
                        <div
                            class="w-8 h-8 rounded bg-[#6B1220] text-[#FBF6EE] flex items-center justify-center text-xs font-bold">
                            {{ mb_substr($link->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-sm font-medium text-[#241512]">{{ $link->name }}</span>
                </a>
            @endforeach


            <a href="{{ route('links.index') }}"
                class="block text-center text-xs font-semibold text-[#6B1220] hover:underline mt-1 pt-2 border-t border-[#C89B3C]/20">
                Lihat semua aplikasi &rarr;
            </a>
        </div>
    </div>
@endif
