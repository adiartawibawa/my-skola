<?php

namespace App\Filament\Widgets;

use App\Enums\DayOfWeekEnum;
use App\Models\Schedule;
use App\Support\AcademicYearContext;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class TodayScheduleWidget extends BaseWidget
{
    protected static ?string $heading = 'Jadwal Hari Ini';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $academicYearId = AcademicYearContext::get()?->id;
                $today = $this->resolveToday();

                return Schedule::query()
                    ->withoutGlobalScopes()
                    ->where('day_of_week', $today->value)
                    ->when(
                        $academicYearId,
                        fn ($query) => $query->whereHas(
                            'classRoom',
                            fn ($q) => $q->withoutGlobalScopes()->where('academic_year_id', $academicYearId),
                        ),
                    )
                    ->orderBy('start_time');
            })
            ->columns([
                TextColumn::make('start_time')
                    ->label('Jam')
                    ->formatStateUsing(fn ($record) => $record->start_time->format('H:i').' - '.$record->end_time->format('H:i')),

                TextColumn::make('classRoom.full_name')
                    ->label('Kelas')
                    ->weight('bold'),

                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran'),

                TextColumn::make('teacher.user.name')
                    ->label('Guru'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tidak ada jadwal hari ini')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    /**
     * ISO day-of-week (1=Senin..7=Minggu) dipakai supaya tidak
     * bergantung pada locale Carbon (nama hari bisa tidak konsisten
     * tergantung konfigurasi server).
     */
    protected function resolveToday(): DayOfWeekEnum
    {
        $isoDay = now()->dayOfWeekIso;

        return collect(DayOfWeekEnum::cases())
            ->first(fn (DayOfWeekEnum $day) => $day->order() === $isoDay) ?? DayOfWeekEnum::SENIN;
    }

    #[On('academic-year-context-changed')]
    public function onAcademicYearContextChanged(): void
    {
        //
    }
}
