<div class="max-w-4xl mx-auto px-4 py-10">

    <nav class="text-sm text-[var(--brand-ink)]/40 mb-6">
        <a href="{{ route('blog.index') }}" class="hover:text-[var(--brand-primary)]">Blog</a>
        @if ($post->category)
            &raquo; {{ $post->category->name }}
        @endif
    </nav>

    <article>
        @if ($post->featured_image)
            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                class="w-full h-72 object-cover rounded-xl mb-6" />
        @endif

        <h1 class="text-3xl font-bold text-[var(--brand-ink)] mb-3">{{ $post->title }}</h1>

        <div class="flex items-center gap-4 text-sm text-[var(--brand-ink)]/50 mb-6">
            <span>Oleh {{ $post->author->name }}</span>
            <span>&middot;</span>
            <span>{{ $post->published_at?->translatedFormat('d F Y') }}</span>
            <span>&middot;</span>
            <span>{{ $post->read_time }} menit baca</span>
            <span>&middot;</span>
            <span>{{ $post->views_count }} views</span>
        </div>

        <div class="mb-6">
            <button wire:click="toggleLike"
                class="inline-flex items-center gap-2 text-sm px-4 py-2 rounded-full border transition
                    {{ $hasLiked ? 'bg-rose-50 border-rose-200 text-rose-600' : 'border-gray-200 text-gray-500 hover:border-rose-200 hover:text-rose-500' }}">
                <span>{{ $hasLiked ? '❤️' : '🤍' }}</span>
                <span>{{ $post->likes_count }} Suka</span>
            </button>
        </div>

        <div class="prose max-w-none mb-8">
            {!! $post->content !!}
        </div>

        @if ($post->tags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-10">
                @foreach ($post->tags as $t)
                    <a href="{{ route('blog.index', ['tag' => $t->slug]) }}"
                        class="text-xs px-3 py-1 rounded-full bg-[var(--brand-accent)]/10 text-[var(--brand-ink)]/60 hover:bg-[var(--brand-accent)]/20">
                        #{{ $t->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </article>

    {{-- Related posts --}}
    @if ($relatedPosts->isNotEmpty())
        <section class="mb-12">
            <h2 class="font-semibold text-[var(--brand-ink)] mb-4">Artikel Terkait</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach ($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related) }}"
                        class="block p-4 rounded-lg border border-[var(--brand-accent)]/20 hover:border-[var(--brand-primary)]/40 transition">
                        <h3 class="font-medium text-[var(--brand-ink)]/80 text-sm">{{ $related->title }}</h3>
                        <p class="text-xs text-[var(--brand-ink)]/40 mt-1">
                            {{ $related->published_at?->translatedFormat('d M Y') }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Komentar --}}
    <section>
        <h2 class="font-semibold text-[var(--brand-ink)] mb-4">
            Komentar ({{ $comments->count() }})
        </h2>

        @if (session('comment_success'))
            <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg p-3 mb-4">
                {{ session('comment_success') }}
            </div>
        @endif

        {{-- Form komentar --}}
        <form wire:submit="submitComment"
            class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/20 rounded-xl p-5 mb-8 space-y-3">
            @if ($parentId)
                <div class="text-xs text-[var(--brand-primary)] flex items-center justify-between">
                    <span>Membalas komentar</span>
                    <button type="button" wire:click="cancelReply" class="underline">Batal</button>
                </div>
            @endif

            @guest
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <input type="text" wire:model="guestName" placeholder="Nama"
                            class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm" />
                        @error('guestName')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <input type="email" wire:model="guestEmail" placeholder="Email"
                            class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm" />
                        @error('guestEmail')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endguest

            <div>
                <textarea wire:model="content" rows="3" placeholder="Tulis komentar..."
                    class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm"></textarea>
                @error('content')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm px-4 py-2 rounded-lg hover:bg-[var(--brand-primary-light)]">
                Kirim Komentar
            </button>
        </form>

        {{-- Daftar komentar (threaded) --}}
        <div class="space-y-6">
            @forelse ($comments as $comment)
                <div class="border-b border-[var(--brand-accent)]/15 pb-6">
                    <div class="flex items-center justify-between mb-1">
                        <span
                            class="font-medium text-sm text-[var(--brand-ink)]/80">{{ $comment->authorName() }}</span>
                        <span
                            class="text-xs text-[var(--brand-ink)]/40">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-[var(--brand-ink)]/60 mb-2">{{ $comment->content }}</p>
                    <button wire:click="replyTo({{ $comment->id }})"
                        class="text-xs text-[var(--brand-primary)] hover:underline">
                        Balas
                    </button>

                    {{-- Replies --}}
                    @if ($comment->replies->isNotEmpty())
                        <div class="mt-4 ml-6 space-y-4 border-l-2 border-[var(--brand-accent)]/20 pl-4">
                            @foreach ($comment->replies as $reply)
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span
                                            class="font-medium text-sm text-[var(--brand-ink)]/80">{{ $reply->authorName() }}</span>
                                        <span
                                            class="text-xs text-[var(--brand-ink)]/40">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-[var(--brand-ink)]/60">{{ $reply->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-[var(--brand-ink)]/40">Belum ada komentar. Jadilah yang pertama!</p>
            @endforelse
        </div>
    </section>
</div>
