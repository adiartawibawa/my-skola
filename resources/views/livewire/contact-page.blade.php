<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="mb-12 max-w-xl">
        <p class="font-mono text-xs tracking-[0.2em] uppercase text-[var(--brand-primary-light)] mb-2">Kontak</p>
        <h1 class="font-display text-3xl font-bold text-[var(--brand-ink)]">Kami Siap Membantu</h1>
        <p class="text-[var(--brand-ink)]/60 mt-3 leading-relaxed">
            Ada pertanyaan seputar pendaftaran, kerja sama industri, atau hal lain? Hubungi kami lewat form di bawah,
            atau langsung lewat kontak resmi sekolah.
        </p>
    </div>

    <div class="grid lg:grid-cols-[1fr_1.2fr] gap-10">
        {{-- Info kontak --}}
        <div class="space-y-8">
            <div>
                <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-primary-light)] mb-2">Alamat
                </h3>
                <p class="text-[var(--brand-ink)]">{{ config('school.address') }}</p>
            </div>
            <div>
                <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-primary-light)] mb-2">Email</h3>
                <a href="mailto:{{ config('school.email') }}"
                    class="text-[var(--brand-primary)] hover:underline">{{ config('school.email') }}</a>
            </div>
            <div>
                <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-primary-light)] mb-2">Telepon
                </h3>
                <a href="tel:{{ config('school.phone') }}"
                    class="text-[var(--brand-primary)] hover:underline">{{ config('school.phone') }}</a>
            </div>
            <div>
                <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-primary-light)] mb-2">Jam
                    Layanan</h3>
                <ul class="text-[var(--brand-ink)]/70 space-y-1">
                    <li>Senin &ndash; Jumat: 07.00 &ndash; 15.00 WITA</li>
                    <li>Sabtu &ndash; Minggu: Tutup</li>
                </ul>
            </div>
            <div>
                <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-primary-light)] mb-2">Media
                    Sosial</h3>
                <div class="flex gap-4 text-sm">
                    <a href="#" class="text-[var(--brand-ink)]/70 hover:text-[var(--brand-primary)]">Instagram</a>
                    <a href="#" class="text-[var(--brand-ink)]/70 hover:text-[var(--brand-primary)]">YouTube</a>
                    <a href="#" class="text-[var(--brand-ink)]/70 hover:text-[var(--brand-primary)]">Facebook</a>
                </div>
            </div>

            {{-- Placeholder peta — ganti src dengan embed Google Maps lokasi asli --}}
            <div
                class="aspect-video rounded-xl border border-[var(--brand-accent)]/30 bg-[#F3EADA] flex items-center justify-center text-sm text-[var(--brand-ink)]/40">
                Peta lokasi sekolah
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/30 rounded-2xl p-6 sm:p-8">
            @if (session('contact_success'))
                <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg p-3 mb-5">
                    {{ session('contact_success') }}
                </div>
            @endif

            <form wire:submit="submit" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <input type="text" wire:model="name" placeholder="Nama Lengkap"
                            class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <input type="email" wire:model="email" placeholder="Email"
                            class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <input type="text" wire:model="subject" placeholder="Perihal (opsional)"
                        class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
                </div>

                <div>
                    <textarea wire:model="message" rows="5" placeholder="Tulis pesanmu..."
                        class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]"></textarea>
                    @error('message')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-3 rounded-full hover:bg-[var(--brand-primary-light)] transition">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
</div>
