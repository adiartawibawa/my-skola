<div>
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-2">
        Halo, {{ auth()->user()->name }} 👋
    </h1>

    @if ($isStaff)
        <p class="text-sm text-[var(--brand-ink)]/50 mb-6">Pilih aplikasi yang ingin kamu akses.</p>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($appLinks as $link)
                <a href="{{ $link->url }}" target="{{ str_starts_with($link->url, url('/')) ? '_self' : '_blank' }}"
                    rel="{{ str_starts_with($link->url, url('/')) ? '' : 'noopener' }}"
                    class="flex items-center gap-4 bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5 hover:border-[var(--brand-accent)] hover:shadow-md transition">
                    @if ($link->logoUrl())
                        <img src="{{ $link->logoUrl() }}" alt="{{ $link->name }}"
                            class="w-10 h-10 rounded object-contain shrink-0">
                    @else
                        <div
                            class="w-10 h-10 rounded-lg bg-[var(--brand-primary)] text-[var(--brand-paper)] flex items-center justify-center font-bold shrink-0">
                            {{ mb_substr($link->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-[var(--brand-ink)] text-sm">{{ $link->name }}</p>
                        @if ($link->description)
                            <p class="text-xs text-[var(--brand-ink)]/50 mt-0.5">{{ $link->description }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <p class="text-sm text-[var(--brand-ink)]/40 sm:col-span-2 lg:col-span-3">
                    Belum ada aplikasi yang diberikan akses untuk role kamu. Hubungi Super Admin.
                </p>
            @endforelse
        </div>
    @else
        @if ($childrenSummary->count() > 1)
            <div class="mb-8">
                <h2 class="font-semibold text-[var(--brand-ink)] mb-3 text-sm">Ringkasan Semua Anak</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($childrenSummary as $item)
                        <div
                            class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-4 {{ $student?->id === $item['student']->id ? 'border-l-4 border-l-[var(--brand-primary)]' : '' }}">
                            <p class="font-semibold text-[var(--brand-ink)] text-sm">{{ $item['student']->user->name }}
                            </p>
                            <p class="text-xs text-[var(--brand-ink)]/50 mt-0.5">
                                {{ $item['class_room']?->full_name ?? 'Belum ada kelas' }}
                            </p>
                            <p class="text-xs text-[var(--brand-primary-light)] font-mono mt-2">
                                {{ $item['today_schedule_count'] }} pelajaran hari ini
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!$student)
            <x-portal-empty-student :is-parent="auth()->user()->role->value === 'parent'" />
        @else
            <p class="text-sm text-[var(--brand-ink)]/50 mb-6">
                Menampilkan data untuk <span
                    class="font-semibold text-[var(--brand-ink)]">{{ $student->user->name }}</span>
            </p>

            <div class="grid sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5">
                    <p class="text-xs font-mono uppercase tracking-wide text-[var(--brand-primary-light)] mb-1">Kelas
                    </p>
                    <p class="font-display text-xl font-bold text-[var(--brand-ink)]">
                        {{ $classRoom?->full_name ?? '—' }}
                    </p>
                </div>
                <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5">
                    <p class="text-xs font-mono uppercase tracking-wide text-[var(--brand-primary-light)] mb-1">Wali
                        Kelas
                    </p>
                    <p class="font-display text-xl font-bold text-[var(--brand-ink)]">
                        {{ $homeroomTeacher?->name ?? '—' }}
                    </p>
                </div>
                <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5">
                    <p class="text-xs font-mono uppercase tracking-wide text-[var(--brand-primary-light)] mb-1">Program
                        Keahlian</p>
                    <p class="font-display text-xl font-bold text-[var(--brand-ink)]">
                        {{ $classRoom?->programKeahlian?->name ?? '—' }}</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Jadwal hari ini --}}
                <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-[var(--brand-ink)]">Jadwal Hari Ini</h2>
                        @if (Route::has('portal.schedule'))
                            <a href="{{ route('portal.schedule') }}"
                                class="text-xs text-[var(--brand-primary)] hover:underline">Lihat semua &rarr;</a>
                        @endif
                    </div>

                    @forelse ($todaySchedules as $schedule)
                        <div
                            class="flex items-center justify-between py-2 border-b border-[var(--brand-accent)]/10 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-[var(--brand-ink)]">{{ $schedule->subject->name }}
                                </p>
                                <p class="text-xs text-[var(--brand-ink)]/50">{{ $schedule->teacher->name }}</p>
                            </div>
                            <span class="text-xs font-mono text-[var(--brand-ink)]/60">
                                {{ $schedule->start_time->format('H:i') }}–{{ $schedule->end_time->format('H:i') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--brand-ink)]/40">Tidak ada jadwal hari ini.</p>
                    @endforelse
                </div>

                {{-- Agenda terdekat --}}
                <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-[var(--brand-ink)]">Agenda Terdekat</h2>
                        @if (Route::has('portal.calendar'))
                            <a href="{{ route('portal.calendar') }}"
                                class="text-xs text-[var(--brand-primary)] hover:underline">Lihat semua &rarr;</a>
                        @endif
                    </div>

                    @forelse ($upcomingEvents as $event)
                        <div
                            class="flex items-center justify-between py-2 border-b border-[var(--brand-accent)]/10 last:border-0">
                            <p class="text-sm font-medium text-[var(--brand-ink)]">{{ $event->event_name }}</p>
                            <span
                                class="text-xs font-mono text-[var(--brand-ink)]/60">{{ $event->event_date->translatedFormat('d M') }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--brand-ink)]/40">Tidak ada agenda terdekat.</p>
                    @endforelse
                </div>
            </div>

            {{-- Pengumuman terbaru --}}
            <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-[var(--brand-ink)]">Pengumuman Terbaru</h2>
                    @if (Route::has('portal.announcements'))
                        <a href="{{ route('portal.announcements') }}"
                            class="text-xs text-[var(--brand-primary)] hover:underline">Lihat semua &rarr;</a>
                    @endif
                </div>

                @forelse ($latestAnnouncements as $announcement)
                    <div class="py-2 border-b border-[var(--brand-accent)]/10 last:border-0">
                        <p class="text-sm font-medium text-[var(--brand-ink)]">{{ $announcement->title }}</p>
                        <p class="text-xs text-[var(--brand-ink)]/50">
                            {{ $announcement->publish_at?->translatedFormat('d M Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-[var(--brand-ink)]/40">Belum ada pengumuman.</p>
                @endforelse
            </div>
        @endif
    @endif
</div>
