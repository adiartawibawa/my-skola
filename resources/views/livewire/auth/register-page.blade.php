<div>
    <h1 class="font-display text-xl font-bold text-[var(--brand-ink)] mb-2">Daftar Akun Orang Tua</h1>
    <p class="text-sm text-[var(--brand-ink)]/50 mb-6">
        Setelah mendaftar, kamu akan diminta menautkan data anak menggunakan NISN dan tanggal lahirnya.
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

        <button type="submit"
            class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-2.5 rounded-lg hover:bg-[var(--brand-primary-light)] transition">
            Daftar
        </button>
    </form>

    <p class="text-center text-sm text-[var(--brand-ink)]/50 mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-[var(--brand-primary)] hover:underline">Masuk</a>
    </p>
</div>
