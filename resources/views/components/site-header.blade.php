<header class="bg-[var(--brand-paper)]/95 backdrop-blur border-b border-[var(--brand-accent)]/25 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-display font-bold text-lg text-[var(--brand-primary)]">
            {{ config('app.name') }}
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[var(--brand-ink)]/70">
            <a href="{{ route('home') }}"
                class="hover:text-[var(--brand-primary)] transition {{ request()->routeIs('home') ? 'text-[var(--brand-primary)] font-semibold' : '' }}">
                Beranda
            </a>
            <a href="{{ route('about') }}"
                class="hover:text-[var(--brand-primary)] transition {{ request()->routeIs('about') ? 'text-[var(--brand-primary)] font-semibold' : '' }}">
                Tentang Kami
            </a>
            <a href="{{ route('program.index') }}"
                class="hover:text-[var(--brand-primary)] transition {{ request()->routeIs('program.index') ? 'text-[var(--brand-primary)] font-semibold' : '' }}">
                Program
            </a>
            <a href="{{ route('blog.index') }}"
                class="hover:text-[var(--brand-primary)] transition {{ request()->routeIs('blog.*') ? 'text-[var(--brand-primary)] font-semibold' : '' }}">
                Blog
            </a>
            <x-app-links-dropdown />
            <a href="{{ route('contact') }}"
                class="hover:text-[var(--brand-primary)] transition {{ request()->routeIs('contact') ? 'text-[var(--brand-primary)] font-semibold' : '' }}">
                Kontak
            </a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ url('/portal') }}"
                    class="text-sm font-medium text-[var(--brand-ink)]/70 hover:text-[var(--brand-primary)] transition">
                    Dashboard
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-[var(--brand-ink)]/70 hover:text-[var(--brand-primary)] transition">
                        Masuk
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold bg-[var(--brand-primary)] text-[var(--brand-paper)] px-4 py-2 rounded-full hover:bg-[var(--brand-primary-light)] transition">
                        Daftar
                    </a>
                @endif
            @endauth
        </div>
    </div>
</header>
