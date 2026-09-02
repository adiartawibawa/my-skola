<footer class="bg-[var(--brand-primary-dark)] text-[var(--brand-paper)]/80 mt-20">
    <div class="max-w-6xl mx-auto px-4 py-14 grid sm:grid-cols-4 gap-10 text-sm">
        <div class="sm:col-span-2">
            <h3 class="font-display text-[var(--brand-paper)] font-bold text-xl mb-3">{{ config('app.name') }}</h3>
            <p class="text-[var(--brand-paper)]/60 max-w-sm leading-relaxed">
                Sistem informasi akademik sekaligus media resmi sekolah untuk berbagi kabar, wawasan, dan pengumuman.
            </p>
        </div>

        <div>
            <h4 class="text-[var(--brand-accent-light)] font-semibold mb-3 font-mono text-xs tracking-widest uppercase">
                Tautan</h4>
            <ul class="space-y-2 text-[var(--brand-paper)]/60">
                <li><a href="{{ route('home') }}" class="hover:text-[var(--brand-paper)] transition">Beranda</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-[var(--brand-paper)] transition">Tentang Kami</a>
                </li>
                <li><a href="{{ route('program.index') }}"
                        class="hover:text-[var(--brand-paper)] transition">Program</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-[var(--brand-paper)] transition">Blog</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-[var(--brand-paper)] transition">Kontak</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-[var(--brand-accent-light)] font-semibold mb-3 font-mono text-xs tracking-widest uppercase">
                Kontak</h4>
            <ul class="space-y-2 text-[var(--brand-paper)]/60">
                <li>{{ config('school.address') }}</li>
                <li>{{ config('school.email') }}</li>
                <li>{{ config('school.phone') }}</li>
            </ul>
        </div>
    </div>

    <div
        class="border-t border-[var(--brand-paper)]/10 py-4 text-center text-xs text-[var(--brand-paper)]/40 font-mono">
        &copy; {{ now()->year }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
    </div>
</footer>
