<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AcademicCalendarWidget;
use App\Filament\Widgets\AcademicOverviewStats;
use App\Filament\Widgets\ClassRoomsWithoutHomeroomWidget;
use App\Filament\Widgets\ClassRoomsWithoutScheduleWidget;
use App\Filament\Widgets\StudentsByProgramKeahlianChart;
use App\Filament\Widgets\TodayScheduleWidget;
use App\Filament\Widgets\UpcomingAcademicCalendarWidget;
use App\Settings\AppearanceSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => $this->resolvePrimaryColor(),
            ])
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AcademicOverviewStats::class,
                ClassRoomsWithoutHomeroomWidget::class,
                ClassRoomsWithoutScheduleWidget::class,
                TodayScheduleWidget::class,
                AcademicCalendarWidget::class,
                UpcomingAcademicCalendarWidget::class,
                StudentsByProgramKeahlianChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function resolvePrimaryColor(): array
    {
        if (! Schema::hasTable('settings')) {
            return Color::hex('#6B1220'); // fallback saat instalasi awal / migrate pertama
        }

        try {
            return Color::hex(app(AppearanceSettings::class)->primary);
        } catch (\Throwable $e) {
            return Color::hex('#6B1220');
        }
    }
}
