<?php

namespace App\Filament\Resources\Announcements\Widgets;

use App\Models\Announcement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnnouncementStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Announcement::query()->count();

        $published = Announcement::query()->published()->count();

        $scheduled = Announcement::query()
            ->whereNotNull('publish_at')
            ->where('publish_at', '>', now())
            ->count();

        $expired = Announcement::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->count();

        return [
            Stat::make('Total Pengumuman', (string) $total)
                ->icon('heroicon-o-megaphone'),

            Stat::make('Tayang Sekarang', (string) $published)
                ->icon('heroicon-o-eye')
                ->color('success'),

            Stat::make('Terjadwal', (string) $scheduled)
                ->description('Belum tayang')
                ->icon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Kedaluwarsa', (string) $expired)
                ->icon('heroicon-o-archive-box-x-mark')
                ->color($expired > 0 ? 'danger' : 'gray'),
        ];
    }
}
