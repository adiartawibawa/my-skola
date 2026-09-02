<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- Konten utama --}}
        <div class="lg:col-span-3 space-y-8">

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                <h1 class="text-2xl font-bold text-[var(--brand-ink)]">Blog</h1>

                <div class="flex items-center gap-2">
                    <input type="search" wire:model.live.debounce.400ms="search" placeholder="Cari artikel..."
                        class="w-full sm:w-64 rounded-lg border-[var(--brand-accent)]/30 shadow-sm focus:ring-[var(--brand-primary)] focus:border-[var(--brand-primary)] text-sm" />

                    @if ($search || $category || $tag)
                        <button wire:click="resetFilters"
                            class="text-sm text-[var(--brand-ink)]/50 hover:text-[var(--brand-ink)]/70 whitespace-nowrap">
                            Reset
                        </button>
                    @endif
                </div>
            </div>

            @if ($category || $tag)
                <div class="flex flex-wrap gap-2 text-sm">
                    @if ($category)
                        <span
                            class="inline-flex items-center gap-1 bg-[var(--brand-primary)]/10 text-[var(--brand-primary)] px-3 py-1 rounded-full">
                            Kategori: {{ $category }}
                            <button wire:click="setCategory(null)" class="font-bold">&times;</button>
                        </span>
                    @endif
                    @if ($tag)
                        <span
                            class="inline-flex items-center gap-1 bg-[var(--brand-accent)]/15 text-[var(--brand-ink)] px-3 py-1 rounded-full">
                            Tag: {{ $tag }}
                            <button wire:click="setTag(null)" class="font-bold">&times;</button>
                        </span>
                    @endif
                </div>
            @endif

            <div wire:loading.class="opacity-50" class="grid sm:grid-cols-2 gap-6 transition-opacity">
                @forelse ($posts as $post)
                    <article
                        class="bg-[var(--brand-paper)] rounded-xl border border-[var(--brand-accent)]/20 shadow-sm overflow-hidden flex flex-col">
                        @if ($post->featured_image)
                            <a href="{{ route('blog.show', $post) }}">
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                                    class="w-full h-40 object-cover" />
                            </a>
                        @endif

                        <div class="p-5 flex flex-col flex-1">
                            @if ($post->category)
                                <button wire:click="setCategory('{{ $post->category->slug }}')"
                                    class="text-xs font-medium text-[var(--brand-primary)] mb-2 text-left">
                                    {{ $post->category->name }}
                                </button>
                            @endif

                            <h2 class="font-semibold text-[var(--brand-ink)] leading-snug mb-2">
                                <a href="{{ route('blog.show', $post) }}" class="hover:text-[var(--brand-primary)]">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            @if ($post->excerpt)
                                <p class="text-sm text-[var(--brand-ink)]/50 line-clamp-2 mb-4">{{ $post->excerpt }}</p>
                            @endif

                            <div
                                class="mt-auto flex items-center justify-between text-xs text-[var(--brand-ink)]/40 pt-3 border-t border-[var(--brand-accent)]/15">
                                <span>{{ $post->author->name }}</span>
                                <div class="flex items-center gap-3">
                                    <span>{{ $post->read_time }} menit baca</span>
                                    <span>{{ $post->published_at?->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 text-center py-16 text-[var(--brand-ink)]/40">
                        Belum ada artikel yang cocok dengan pencarianmu.
                    </div>
                @endforelse
            </div>

            <div>{{ $posts->links() }}</div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-8">
            <div class="bg-[var(--brand-paper)] rounded-xl border border-[var(--brand-accent)]/20 p-5">
                <h3 class="font-semibold text-[var(--brand-ink)] mb-3">Kategori</h3>
                <ul class="space-y-2 text-sm">
                    @foreach ($categories as $cat)
                        <li>
                            <button wire:click="setCategory('{{ $cat->slug }}')"
                                class="flex justify-between w-full {{ $category === $cat->slug ? 'text-[var(--brand-primary)] font-medium' : 'text-[var(--brand-ink)]/60 hover:text-[var(--brand-primary)]' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-[var(--brand-ink)]/40">{{ $cat->posts_count }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-[var(--brand-paper)] rounded-xl border border-[var(--brand-accent)]/20 p-5">
                <h3 class="font-semibold text-[var(--brand-ink)] mb-3">Tag Populer</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $t)
                        <button wire:click="setTag('{{ $t->slug }}')"
                            class="text-xs px-3 py-1 rounded-full {{ $tag === $t->slug ? 'bg-[var(--brand-accent)] text-[var(--brand-primary-dark)]' : 'bg-[var(--brand-accent)]/10 text-[var(--brand-ink)]/60 hover:bg-[var(--brand-accent)]/20' }}">
                            #{{ $t->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
