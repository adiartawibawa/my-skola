<div class="grid sm:grid-cols-3 gap-6">
    @forelse ($posts as $post)
        <a href="{{ route('blog.show', $post) }}"
            class="block bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
            @if ($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                    class="w-full h-36 object-cover" />
            @endif
            <div class="p-4">
                @if ($post->category)
                    <span class="text-xs font-medium text-indigo-600">{{ $post->category->name }}</span>
                @endif
                <h3 class="font-semibold text-gray-900 text-sm leading-snug mt-1 line-clamp-2">
                    {{ $post->title }}
                </h3>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $post->author->name }} &middot; {{ $post->published_at->translatedFormat('d M Y') }}
                </p>
            </div>
        </a>
    @empty
        <p class="text-sm text-gray-400 sm:col-span-3">Belum ada artikel yang dipublikasikan.</p>
    @endforelse
</div>
