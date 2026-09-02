<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AcademicCalendarWidget;
use App\Filament\Widgets\AcademicOverviewStats;
use App\Filament\Widgets\ClassRoomsWithoutHomeroomWidget;
use App\Filament\Widgets\ClassRoomsWithoutScheduleWidget;
use App\Filament\Widgets\StudentsByProgramKeahlianChart;
use App\Filament\Widgets\TodayScheduleWidget;
use App\Filament\Widgets\UpcomingAcademicCalendarWidget;
use App\Models\AcademicYear;
use App\Support\AcademicYearContext;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    /**
     * AcademicYearContext (app/Support/AcademicYearContext.php) adalah
     * satu-satunya sumber kebenaran untuk "Tahun Akademik yang sedang
     * dilihat" di seluruh app — dipakai juga oleh AcademicCalendarWidget
     * di cluster Academic. Filter form Filament di sini murni UI yang
     * membaca/menulis ke context itu lewat afterStateUpdated() di
     * bawah, BUKAN state terpisah. Makanya persist-session bawaan
     * Filament dimatikan — AcademicYearContext sudah mengurus
     * persistensinya sendiri, dan kita tidak mau dua sumber kebenaran
     * yang bisa saling tidak sinkron.
     */
    public function persistsFiltersInSession(): bool
    {
        return false;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Akademik')
                            ->options(fn () => AcademicYear::query()
                                ->orderByDesc('start_date')
                                ->pluck('name', 'id'))
                            ->default(fn () => AcademicYearContext::get()?->id)
                            ->selectablePlaceholder(fn () => ! AcademicYear::query()->exists())
                            ->placeholder('Belum ada Tahun Akademik')
                            ->disabled(fn () => ! AcademicYear::query()->exists())
                            ->helperText(fn () => AcademicYear::query()->exists()
                                ? null
                                : 'Buat Tahun Akademik terlebih dahulu di menu Academic sebelum widget di bawah bisa menampilkan data.')
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?string $state): void {
                                if (! $state) {
                                    return;
                                }
                                AcademicYearContext::set($state);
                                $this->dispatch('academic-year-context-changed');
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    /**
     * Sembunyikan semua widget kalau belum ada Tahun Akademik sama
     * sekali — daripada masing-masing widget menampilkan state error/
     * kosong yang membingungkan, halaman cukup menampilkan filter
     * (dengan helper text di atas) tanpa widget di bawahnya.
     */
    public function getWidgets(): array
    {
        if (! AcademicYear::query()->exists()) {
            return [];
        }

        // return array_filter(
        //     parent::getWidgets(),
        //     fn ($widget) => $widget !== AcademicCalendarWidget::class
        // );

        return [
            AcademicCalendarWidget::class,
            AcademicOverviewStats::class,
            // ClassRoomsWithoutHomeroomWidget::class,
            // ClassRoomsWithoutScheduleWidget::class,
            StudentsByProgramKeahlianChart::class,
            // TodayScheduleWidget::class,
            // UpcomingAcademicCalendarWidget::class,
        ];
    }
}
