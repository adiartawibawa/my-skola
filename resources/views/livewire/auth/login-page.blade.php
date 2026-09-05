<div>
    <h1 class="font-display text-xl font-bold text-[var(--brand-ink)] mb-6">Masuk ke Akun</h1>

    <form wire:submit="login" class="space-y-4">
        <div>
            <input type="email" wire:model="email" placeholder="Email"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]"
                autofocus autocomplete="email" />
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="password" wire:model="password" placeholder="Kata Sandi"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]"
                autocomplete="current-password" />
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-[var(--brand-ink)]/60">
                <input type="checkbox" wire:model="remember" class="rounded border-[var(--brand-accent)]/40">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="text-[var(--brand-primary)] hover:underline">Lupa kata
                sandi?</a>
        </div>

        <button type="submit"
            class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-2.5 rounded-lg hover:bg-[var(--brand-primary-light)] transition">
            Masuk
        </button>
    </form>

    <p class="text-center text-sm text-[var(--brand-ink)]/50 mt-6">
        Orang tua siswa belum punya akun? <a href="{{ route('register') }}"
            class="text-[var(--brand-primary)] hover:underline">Daftar</a>
    </p>
    <p class="text-center text-sm text-[var(--brand-ink)]/50 mt-2">
        Alumni belum punya akun? <a href="{{ route('register.alumni') }}"
            class="text-[var(--brand-primary)] hover:underline">Daftar sebagai Alumni</a>
    </p>
</div>
