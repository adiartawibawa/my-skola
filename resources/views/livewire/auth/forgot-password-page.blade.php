<div>
    <h1 class="font-display text-xl font-bold text-[var(--brand-ink)] mb-2">Lupa Kata Sandi</h1>
    <p class="text-sm text-[var(--brand-ink)]/50 mb-6">
        Masukkan email akunmu, kami akan kirimkan tautan untuk mengatur ulang kata sandi.
    </p>

    @if ($status)
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg p-3 mb-5">{{ $status }}</div>
    @endif

    <form wire:submit="sendResetLink" class="space-y-4">
        <div>
            <input type="email" wire:model="email" placeholder="Email"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]"
                autofocus />
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-2.5 rounded-lg hover:bg-[var(--brand-primary-light)] transition">
            Kirim Tautan Reset
        </button>
    </form>

    <p class="text-center text-sm text-[var(--brand-ink)]/50 mt-6">
        <a href="{{ route('login') }}" class="text-[var(--brand-primary)] hover:underline">&larr; Kembali ke halaman
            masuk</a>
    </p>
</div>
