<div>
    <h1 class="font-display text-xl font-bold text-[var(--brand-ink)] mb-6">Atur Ulang Kata Sandi</h1>

    <form wire:submit="resetPassword" class="space-y-4">
        <div>
            <input type="email" wire:model="email" placeholder="Email"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="password" wire:model="password" placeholder="Kata Sandi Baru"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
            @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="password" wire:model="password_confirmation" placeholder="Ulangi Kata Sandi Baru"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
        </div>

        <button type="submit"
            class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-2.5 rounded-lg hover:bg-[var(--brand-primary-light)] transition">
            Ubah Kata Sandi
        </button>
    </form>
</div>
