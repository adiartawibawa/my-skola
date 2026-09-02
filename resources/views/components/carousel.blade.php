<div x-data="{ scrollBy(amount) { $refs.track.scrollBy({ left: amount, behavior: 'smooth' }); } }" class="relative">
    <div x-ref="track"
        class="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory scroll-px-4 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
        {{ $slot }}
    </div>

    <div class="flex justify-end gap-2 mt-2">
        <button type="button" @click="scrollBy(-320)" aria-label="Sebelumnya"
            class="w-9 h-9 rounded-full border border-[var(--brand-accent)]/40 flex items-center justify-center text-[var(--brand-primary)] hover:bg-[var(--brand-primary)] hover:text-[var(--brand-paper)] hover:border-[var(--brand-primary)] transition">
            &larr;
        </button>
        <button type="button" @click="scrollBy(320)" aria-label="Berikutnya"
            class="w-9 h-9 rounded-full border border-[var(--brand-accent)]/40 flex items-center justify-center text-[var(--brand-primary)] hover:bg-[var(--brand-primary)] hover:text-[var(--brand-paper)] hover:border-[var(--brand-primary)] transition">
            &rarr;
        </button>
    </div>
</div>
