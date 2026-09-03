<div>
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-6">Kalender Akademik</h1>

    @if (!$student)
        <x-portal-empty-student :is-parent="auth()->user()->role->value === 'parent'" />
    @elseif (!$classRoom)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl p-4">
            {{ $student->user->name }} belum terdaftar di kelas manapun pada tahun ajaran ini.
        </div>
    @else
        @forelse ($eventsByMonth as $month => $events)
            <div class="mb-6">
                <h2 class="font-mono text-xs uppercase tracking-widest text-[var(--brand-primary-light)] mb-3">
                    {{ $month }}</h2>

                <div
                    class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl divide-y divide-[var(--brand-accent)]/10">
                    @foreach ($events as $event)
                        <div class="flex items-center gap-4 px-5 py-3">
                            <div class="w-14 shrink-0 text-center">
                                <p class="font-display text-lg font-bold text-[var(--brand-primary)] leading-none">
                                    {{ $event->event_date->format('d') }}
                                </p>
                                @if ($event->event_end_date && !$event->event_end_date->isSameDay($event->event_date))
                                    <p class="text-[10px] text-[var(--brand-ink)]/40">s.d.
                                        {{ $event->event_end_date->format('d') }}</p>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-[var(--brand-ink)]">{{ $event->event_name }}</p>
                                @if ($event->description)
                                    <p class="text-xs text-[var(--brand-ink)]/50 mt-0.5">{{ $event->description }}</p>
                                @endif
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap"
                                style="background-color: {{ $event->default_color }}20; color: {{ $event->default_color }};">
                                {{ $event->event_type->label() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-sm text-[var(--brand-ink)]/40">Belum ada agenda pada tahun ajaran ini.</p>
        @endforelse
    @endif
</div>
