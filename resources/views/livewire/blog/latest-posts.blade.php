<div class="grid sm:grid-cols-3 gap-6">
    @forelse ($posts as $post)
        <a href="{{ route('blog.show', $post) }}"
            class="block bg-[#FBF6EE] rounded-xl border border-[#C89B3C]/25 overflow-hidden hover:border-[#C89B3C]/60 hover:shadow-md transition">
            @if ($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                    class="w-full h-36 object-cover" />
            @endif
            <div class="p-4">
                @if ($post->category)
                    <span
                        class="font-mono text-[11px] tracking-wide uppercase text-[#8C1F2E]">{{ $post->category->name }}</span>
                @endif
                <h3 class="font-display font-semibold text-[#241512] text-base leading-snug mt-1 line-clamp-2">
                    {{ $post->title }}
                </h3>
                <p class="text-xs text-[#241512]/50 mt-2">
                    {{ $post->author->name }} &middot; {{ $post->published_at->translatedFormat('d M Y') }}
                </p>
            </div>
        </a>
    @empty
        <p class="text-sm text-[#241512]/50 sm:col-span-3">Belum ada artikel yang dipublikasikan.</p>
    @endforelse
</div>
