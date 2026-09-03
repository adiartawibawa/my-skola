@props(['title' => null])

<x-layouts.master :title="$title ?? 'Portal'">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">

        {{-- SIDEBAR DESKTOP — selalu bagian dari flex layout, tidak pernah `fixed`.
             Hanya tampil di lg+; tidak pernah bentrok dengan versi mobile di bawah. --}}
        <aside
            class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 bg-[var(--brand-primary-dark)] text-[var(--brand-paper)]">
            <div class="px-6 py-5 border-b border-[var(--brand-paper)]/10">
                <a href="{{ route('portal.dashboard') }}" class="font-display font-bold text-lg block">
                    {{ config('app.name') }}
                </a>
                <p class="text-xs text-[var(--brand-paper)]/50 mt-1 font-mono uppercase tracking-wide">
                    Portal {{ auth()->user()->role->label() }}
                </p>
            </div>

            <x-portal-nav />

            <div class="px-3 py-4 border-t border-[var(--brand-paper)]/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--brand-paper)]/60 hover:bg-[var(--brand-paper)]/5 hover:text-[var(--brand-paper)] w-full text-sm transition">
                        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 shrink-0" />
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- SIDEBAR MOBILE — overlay `fixed` terpisah total, cuma untuk layar < lg.
             Tidak pernah dirender/berlaku di desktop sama sekali (lg:hidden pada wrapper). --}}
        <div x-show="sidebarOpen" x-cloak class="lg:hidden">
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-black/30 z-40"></div>

            <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-[var(--brand-primary-dark)] text-[var(--brand-paper)]">
                <div class="px-6 py-5 border-b border-[var(--brand-paper)]/10 flex items-start justify-between">
                    <div>
                        <a href="{{ route('portal.dashboard') }}" class="font-display font-bold text-lg block">
                            {{ config('app.name') }}
                        </a>
                        <p class="text-xs text-[var(--brand-paper)]/50 mt-1 font-mono uppercase tracking-wide">
                            Portal {{ auth()->user()->role->label() }}
                        </p>
                    </div>
                    <button @click="sidebarOpen = false" aria-label="Tutup menu" class="text-[var(--brand-paper)]/60">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <x-portal-nav />

                <div class="px-3 py-4 border-t border-[var(--brand-paper)]/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[var(--brand-paper)]/60 hover:bg-[var(--brand-paper)]/5 hover:text-[var(--brand-paper)] w-full text-sm transition">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 shrink-0" />
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        {{-- KONTEN UTAMA --}}
        <div class="flex-1 min-w-0 bg-[var(--brand-paper)]">
            <header class="bg-[var(--brand-paper)] border-b border-[var(--brand-accent)]/20 sticky top-0 z-20">
                <div class="px-4 sm:px-6 py-4 flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-[var(--brand-ink)]"
                        aria-label="Buka menu">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>

                    <div class="flex-1"></div>

                    @if (auth()->user()->role->value === 'parent')
                        <livewire:portal.child-switcher />
                    @endif

                    <span class="text-sm text-[var(--brand-ink)]/70 hidden sm:inline whitespace-nowrap">
                        {{ auth()->user()->name }}
                    </span>
                </div>
            </header>

            <main class="p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.master>
