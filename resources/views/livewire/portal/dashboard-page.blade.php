<div>
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-2">
        Halo, {{ auth()->user()->name }} 👋
    </h1>
    <p class="text-sm text-[var(--brand-ink)]/50">
        Dashboard lengkap (jadwal, kalender akademik, pengumuman) sedang disiapkan.
    </p>

    @if (auth()->user()->role->value === 'parent' && auth()->user()->students()->doesntExist())
        <div class="mt-6 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl p-4">
            Kamu belum menautkan data anak.
            <a href="{{ route('portal.link-child') }}" class="font-semibold underline">Tautkan sekarang</a>.
        </div>
    @endif
</div>
