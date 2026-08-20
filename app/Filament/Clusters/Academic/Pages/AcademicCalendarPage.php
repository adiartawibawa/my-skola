<?php

namespace App\Filament\Clusters\Academic\Pages;

use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Widgets\AcademicCalendarWidget;
use App\Models\AcademicYear;
use App\Support\AcademicYearContext;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AcademicCalendarPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $cluster = AcademicCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Kalender';

    protected static ?string $title = 'Kalender Akademik';

    protected string $view = 'filament.clusters.academic.pages.academic-calendar-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'viewing_academic_year_id' => AcademicYearContext::get()?->id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('viewing_academic_year_id')
                    ->label('Menampilkan Tahun Akademik')
                    ->options(fn () => AcademicYear::query()
                        ->orderByDesc('start_date')
                        ->pluck('name', 'id'))
                    ->selectablePlaceholder(false)
                    ->live()
                    ->afterStateUpdated(function (?string $state): void {
                        if ($state) {
                            AcademicYearContext::set($state);
                        } else {
                            AcademicYearContext::reset();
                        }

                        // Widget kalender ada di Livewire component
                        // terpisah, jadi dikoordinasikan lewat event
                        // — bukan property langsung.
                        $this->dispatch('academic-year-context-changed');
                    })
                    ->helperText(
                        fn () => AcademicYearContext::isViewingHistorical()
                            ? 'Anda sedang melihat data historis, bukan Tahun Akademik yang aktif saat ini.'
                            : null
                    ),
            ])
            ->statePath('data');
    }

    protected function getFooterWidgets(): array
    {
        return [
            AcademicCalendarWidget::class,
        ];
    }
}
