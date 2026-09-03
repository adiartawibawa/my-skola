<div>
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-2">Jadwal Pelajaran</h1>

    @if (!$student)
        <x-portal-empty-student :is-parent="auth()->user()->role->value === 'parent'" />
    @elseif (!$classRoom)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl p-4">
            {{ $student->user->name }} belum terdaftar di kelas manapun pada tahun ajaran ini.
        </div>
    @else
        <p class="text-sm text-[var(--brand-ink)]/50 mb-6">
            Kelas {{ $classRoom->full_name }}
        </p>

        @forelse ($schedulesByDay as $day => $schedules)
            <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl overflow-hidden mb-4">
                <div class="bg-[var(--brand-accent)]/10 px-5 py-2.5">
                    <h2 class="font-semibold text-sm text-[var(--brand-ink)] uppercase tracking-wide">
                        {{ \App\Enums\DayOfWeekEnum::from($day)->label() }}
                    </h2>
                </div>
                <div class="divide-y divide-[var(--brand-accent)]/10">
                    @foreach ($schedules as $schedule)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div>
                                <p class="text-sm font-medium text-[var(--brand-ink)]">{{ $schedule->subject->name }}</p>
                                <p class="text-xs text-[var(--brand-ink)]/50">{{ $schedule->teacher->name }}</p>
                            </div>
                            <span class="text-xs font-mono text-[var(--brand-ink)]/60 whitespace-nowrap">
                                {{ $schedule->start_time->format('H:i') }}–{{ $schedule->end_time->format('H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-sm text-[var(--brand-ink)]/40">Jadwal belum tersedia untuk kelas ini.</p>
        @endforelse
    @endif
</div>
