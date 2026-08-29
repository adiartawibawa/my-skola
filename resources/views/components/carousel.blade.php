<div x-data="{ scrollBy(amount) { $refs.track.scrollBy({ left: amount, behavior: 'smooth' }); } }" class="relative">
    <div x-ref="track"
        class="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory scroll-px-4 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
        {{ $slot }}
    </div>

    <div class="flex justify-end gap-2 mt-2">
        <button type="button" @click="scrollBy(-320)" aria-label="Sebelumnya"
            class="w-9 h-9 rounded-full border border-[#C89B3C]/40 flex items-center justify-center text-[#6B1220] hover:bg-[#6B1220] hover:text-[#FBF6EE] hover:border-[#6B1220] transition">
            &larr;
        </button>
        <button type="button" @click="scrollBy(320)" aria-label="Berikutnya"
            class="w-9 h-9 rounded-full border border-[#C89B3C]/40 flex items-center justify-center text-[#6B1220] hover:bg-[#6B1220] hover:text-[#FBF6EE] hover:border-[#6B1220] transition">
            &rarr;
        </button>
    </div>
</div>
