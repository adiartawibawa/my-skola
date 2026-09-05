<div>
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-6">Pengumuman</h1>

    @if (!$targetUser)
        <x-portal-empty-student :is-parent="auth()->user()->role->value === 'parent'" />
    @else
        <div class="space-y-4">
            @forelse ($announcements as $announcement)
                <div
                    class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5 {{ $announcement->is_pinned ? 'border-l-4 border-l-[var(--brand-primary)]' : '' }}">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h2 class="font-semibold text-[var(--brand-ink)]">{{ $announcement->title }}</h2>
                        @if ($announcement->is_pinned)
                            <span
                                class="text-xs px-2 py-0.5 rounded-full bg-[var(--brand-accent)]/15 text-[var(--brand-primary-light)] font-medium whitespace-nowrap">
                                Disematkan
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-[var(--brand-ink)]/70 leading-relaxed mb-3">{{ $announcement->body }}</p>
                    <p class="text-xs text-[var(--brand-ink)]/40">
                        {{ $announcement->creator?->name ?? 'Sekolah' }} &middot;
                        {{ $announcement->publish_at?->translatedFormat('d M Y') }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-[var(--brand-ink)]/40">Belum ada pengumuman untuk kamu.</p>
            @endforelse
        </div>
    @endif
</div>
