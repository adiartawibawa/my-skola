@props(['isParent' => false])

<div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-8 text-center">
    <p class="text-[var(--brand-ink)]/60 text-sm">
        @if ($isParent)
            Kamu belum menautkan data anak.
            <a href="{{ route('portal.link-child') }}" class="text-[var(--brand-primary)] font-semibold underline">Tautkan
                sekarang</a>.
        @else
            Data siswa tidak ditemukan. Hubungi Tata Usaha untuk bantuan.
        @endif
    </p>
</div>
