<header class="bg-[#FBF6EE]/95 backdrop-blur border-b border-[#C89B3C]/25 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-display font-bold text-lg text-[#6B1220]">
            {{ config('app.name') }}
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#241512]/70">
            <a href="{{ route('home') }}"
                class="hover:text-[#6B1220] transition {{ request()->routeIs('home') ? 'text-[#6B1220] font-semibold' : '' }}">
                Beranda
            </a>
            <a href="{{ route('about') }}"
                class="hover:text-[#6B1220] transition {{ request()->routeIs('about') ? 'text-[#6B1220] font-semibold' : '' }}">
                Tentang Kami
            </a>
            <a href="{{ route('program.index') }}"
                class="hover:text-[#6B1220] transition {{ request()->routeIs('program.index') ? 'text-[#6B1220] font-semibold' : '' }}">
                Program
            </a>
            <a href="{{ route('blog.index') }}"
                class="hover:text-[#6B1220] transition {{ request()->routeIs('blog.*') ? 'text-[#6B1220] font-semibold' : '' }}">
                Blog
            </a>
            <x-app-links-dropdown />
            <a href="{{ route('contact') }}"
                class="hover:text-[#6B1220] transition {{ request()->routeIs('contact') ? 'text-[#6B1220] font-semibold' : '' }}">
                Kontak
            </a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ url('/admin') }}"
                    class="text-sm font-medium text-[#241512]/70 hover:text-[#6B1220] transition">
                    Dashboard
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-[#241512]/70 hover:text-[#6B1220] transition">
                        Masuk
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold bg-[#6B1220] text-[#FBF6EE] px-4 py-2 rounded-full hover:bg-[#8C1F2E] transition">
                        Daftar
                    </a>
                @endif
            @endauth
        </div>
    </div>
</header>
