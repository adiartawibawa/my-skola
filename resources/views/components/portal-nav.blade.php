@php
    $isStaff = auth()->user()->role->isStaff();

    $isAlumni = auth()->user()->role->value === 'alumni';

    $navItems = match (true) {
        $isStaff => [['route' => 'portal.dashboard', 'label' => 'Dashboard', 'icon' => 'heroicon-o-home']],
        $isAlumni => [
            ['route' => 'portal.dashboard', 'label' => 'Dashboard', 'icon' => 'heroicon-o-home'],
            ['route' => 'portal.announcements', 'label' => 'Info & Lowongan', 'icon' => 'heroicon-o-megaphone'],
            ['route' => 'portal.profile', 'label' => 'Profil', 'icon' => 'heroicon-o-user-circle'],
        ],
        default => [
            ['route' => 'portal.dashboard', 'label' => 'Dashboard', 'icon' => 'heroicon-o-home'],
            ['route' => 'portal.schedule', 'label' => 'Jadwal Pelajaran', 'icon' => 'heroicon-o-calendar-days'],
            ['route' => 'portal.calendar', 'label' => 'Kalender Akademik', 'icon' => 'heroicon-o-calendar'],
            ['route' => 'portal.announcements', 'label' => 'Pengumuman', 'icon' => 'heroicon-o-megaphone'],
            ['route' => 'portal.profile', 'label' => 'Profil', 'icon' => 'heroicon-o-user-circle'],
        ],
    };
@endphp

<nav class="flex-1 px-3 py-6 space-y-1 text-sm overflow-y-auto">
    @foreach ($navItems as $item)
        <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                {{ request()->routeIs($item['route']) ? 'bg-[var(--brand-paper)]/10 text-[var(--brand-paper)] font-semibold' : 'text-[var(--brand-paper)]/60 hover:bg-[var(--brand-paper)]/5 hover:text-[var(--brand-paper)]' }}">
            <x-dynamic-component :component="$item['icon']" class="w-5 h-5 shrink-0" />
            {{ $item['label'] }}
        </a>
    @endforeach

    @if (auth()->user()->role->value === 'parent')
        <a href="{{ route('portal.link-child') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                {{ request()->routeIs('portal.link-child') ? 'bg-[var(--brand-paper)]/10 text-[var(--brand-paper)] font-semibold' : 'text-[var(--brand-paper)]/60 hover:bg-[var(--brand-paper)]/5 hover:text-[var(--brand-paper)]' }}">
            <x-heroicon-o-link class="w-5 h-5 shrink-0" />
            Tautkan Anak
        </a>
    @endif
</nav>
