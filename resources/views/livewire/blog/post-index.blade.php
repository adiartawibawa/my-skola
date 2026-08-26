<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- Konten utama --}}
        <div class="lg:col-span-3 space-y-8">

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Blog</h1>

                <div class="flex items-center gap-2">
                    <input type="search" wire:model.live.debounce.400ms="search" placeholder="Cari artikel..."
                        class="w-full sm:w-64 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" />

                    @if ($search || $category || $tag)
                        <button wire:click="resetFilters"
                            class="text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                            Reset
                        </button>
                    @endif
                </div>
            </div>

            @if ($category || $tag)
                <div class="flex flex-wrap gap-2 text-sm">
                    @if ($category)
                        <span
                            class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full">
                            Kategori: {{ $category }}
                            <button wire:click="setCategory(null)" class="font-bold">&times;</button>
                        </span>
                    @endif
                    @if ($tag)
                        <span
                            class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full">
                            Tag: {{ $tag }}
                            <button wire:click="setTag(null)" class="font-bold">&times;</button>
                        </span>
                    @endif
                </div>
            @endif

            <div wire:loading.class="opacity-50" class="grid sm:grid-cols-2 gap-6 transition-opacity">
                @forelse ($posts as $post)
                    <article class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                        @if ($post->featured_image)
                            <a href="{{ route('blog.show', $post) }}">
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                                    class="w-full h-40 object-cover" />
                            </a>
                        @endif

                        <div class="p-5 flex flex-col flex-1">
                            @if ($post->category)
                                <button wire:click="setCategory('{{ $post->category->slug }}')"
                                    class="text-xs font-medium text-indigo-600 mb-2 text-left">
                                    {{ $post->category->name }}
                                </button>
                            @endif

                            <h2 class="font-semibold text-gray-900 leading-snug mb-2">
                                <a href="{{ route('blog.show', $post) }}" class="hover:text-indigo-600">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            @if ($post->excerpt)
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $post->excerpt }}</p>
                            @endif

                            <div
                                class="mt-auto flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-50">
                                <span>{{ $post->author->name }}</span>
                                <div class="flex items-center gap-3">
                                    <span>{{ $post->read_time }} menit baca</span>
                                    <span>{{ $post->published_at?->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 text-center py-16 text-gray-400">
                        Belum ada artikel yang cocok dengan pencarianmu.
                    </div>
                @endforelse
            </div>

            <div>{{ $posts->links() }}</div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-8">
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Kategori</h3>
                <ul class="space-y-2 text-sm">
                    @foreach ($categories as $cat)
                        <li>
                            <button wire:click="setCategory('{{ $cat->slug }}')"
                                class="flex justify-between w-full {{ $category === $cat->slug ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-indigo-600' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-gray-400">{{ $cat->posts_count }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Tag Populer</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $t)
                        <button wire:click="setTag('{{ $t->slug }}')"
                            class="text-xs px-3 py-1 rounded-full {{ $tag === $t->slug ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            #{{ $t->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>
