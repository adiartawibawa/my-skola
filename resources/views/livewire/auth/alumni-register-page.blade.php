<div>
    <h1 class="font-display text-xl font-bold text-[var(--brand-ink)] mb-2">Daftar Akun Alumni</h1>
    <p class="text-sm text-[var(--brand-ink)]/50 mb-6">
        Akunmu langsung aktif setelah mendaftar. Data kelulusan akan ditinjau Tata Usaha — kamu tetap bisa
        mengakses info lowongan kerja & pengumuman alumni sambil menunggu.
    </p>

    <form wire:submit="register" class="space-y-4">
        <div>
            <input type="text" wire:model="name" placeholder="Nama Lengkap"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]"
                autofocus />
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

        <div class="grid grid-cols-2 gap-3">
            <div>
                <input type="password" wire:model="password" placeholder="Kata Sandi"
                    class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <input type="password" wire:model="password_confirmation" placeholder="Ulangi Kata Sandi"
                    class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <input type="number" wire:model="tahun_lulus" placeholder="Tahun Lulus"
                    class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
                @error('tahun_lulus')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <select wire:model="program_keahlian_id"
                    class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]">
                    <option value="">Program Keahlian (opsional)</option>
                    @foreach ($programKeahlians as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <input type="text" wire:model="nis_klaim" placeholder="NIS saat sekolah (opsional, jika masih ingat)"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
        </div>

        <button type="submit"
            class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-2.5 rounded-lg hover:bg-[var(--brand-primary-light)] transition">
            Daftar sebagai Alumni
        </button>
    </form>

    <p class="text-center text-sm text-[var(--brand-ink)]/50 mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-[var(--brand-primary)] hover:underline">Masuk</a>
    </p>
</div>
