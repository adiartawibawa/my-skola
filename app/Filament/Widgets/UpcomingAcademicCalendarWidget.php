<?php

namespace App\Filament\Widgets;

use App\Models\AcademicCalendar;
use App\Support\AcademicYearContext;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class UpcomingAcademicCalendarWidget extends BaseWidget
{
    protected static ?string $heading = 'Agenda Terdekat';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $academicYearId = AcademicYearContext::get()?->id;

                // withoutGlobalScopes() + scopeUpcoming() yang sudah
                // ada di model (belum pernah dipakai di mana pun
                // sebelumnya) — dibatasi 8 event terdekat, bukan
                // rentang tanggal tetap, supaya selalu menampilkan
                // sesuatu meski sedang musim sepi event.
                return AcademicCalendar::query()
                    ->withoutGlobalScopes()
                    ->when($academicYearId, fn ($query) => $query->where('academic_year_id', $academicYearId))
                    ->upcoming()
                    ->limit(8);
            })
            ->columns([
                TextColumn::make('event_name')
                    ->label('Event')
                    ->weight('bold'),

                TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('event_end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('—'),

                TextColumn::make('event_type')
                    ->label('Tipe')
                    ->badge(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tidak ada agenda terdekat')
            ->emptyStateIcon('heroicon-o-calendar');
    }

    #[On('academic-year-context-changed')]
    public function onAcademicYearContextChanged(): void
    {
        //
    }
}
