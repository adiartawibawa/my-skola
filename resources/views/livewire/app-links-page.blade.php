<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="mb-12 max-w-xl">
        <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-2">Portal Digital</p>
        <h1 class="font-display text-3xl font-bold text-[#241512]">Aplikasi &amp; Tautan Sekolah</h1>
        <p class="text-[#241512]/60 mt-3 leading-relaxed">
            Kumpulan aplikasi dan tautan resmi yang digunakan siswa, guru, dan orang tua sehari-hari.
        </p>
    </div>

    @forelse ($categories as $category)
        @continue(($grouped[$category->value] ?? collect())->isEmpty())

        <section class="mb-12">
            <h2 class="font-display text-xl font-semibold text-[#6B1220] mb-4">{{ $category->label() }}</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                @foreach ($grouped[$category->value] as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener"
                        class="flex items-start gap-4 p-5 bg-[#FBF6EE] border border-[#C89B3C]/25 rounded-xl hover:border-[#C89B3C] hover:shadow-md transition">
                        @if ($link->logoUrl())
                            <img src="{{ $link->logoUrl() }}" alt="{{ $link->name }}"
                                class="w-10 h-10 rounded object-contain shrink-0">
                        @else
                            <div
                                class="w-10 h-10 rounded bg-[#6B1220] text-[#FBF6EE] flex items-center justify-center text-sm font-bold shrink-0">
                                {{ mb_substr($link->name, 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <h3 class="font-semibold text-[#241512]">{{ $link->name }}</h3>
                            @if ($link->description)
                                <p class="text-sm text-[#241512]/60 mt-1">{{ $link->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <p class="text-[#241512]/50">Belum ada aplikasi yang terdaftar.</p>
    @endforelse
</div>
