<?php

namespace App\Filament\Widgets;

use App\Enums\RoleEnum;
use App\Filament\Clusters\Academic\Resources\Schedules\ScheduleResource;
use App\Models\ClassRoom;
use App\Support\AcademicYearContext;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class ClassRoomsWithoutScheduleWidget extends BaseWidget
{
    protected static ?string $heading = 'Kelas Belum Ada Jadwal';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role !== RoleEnum::TEACHER;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $academicYearId = AcademicYearContext::get()?->id;

                // withoutGlobalScopes(): widget ini mengikuti
                // AcademicYearContext (bisa tahun historis), bukan
                // ActiveAcademicYearScope.
                return ClassRoom::query()
                    ->withoutGlobalScopes()
                    ->when($academicYearId, fn ($query) => $query->where('academic_year_id', $academicYearId))
                    ->whereDoesntHave('schedules')
                    ->orderBy('grade_level');
            })
            ->columns([
                TextColumn::make('full_name')
                    ->label('Kelas')
                    ->weight('bold'),

                TextColumn::make('programKeahlian.name')
                    ->label('Program Keahlian'),

                TextColumn::make('student_count')
                    ->label('Jumlah Siswa'),
            ])
            ->recordActions([
                Action::make('addSchedule')
                    ->label('Tambah Jadwal')
                    ->icon('heroicon-o-calendar-days')
                    ->url(fn () => ScheduleResource::getUrl('create')),
            ])
            ->paginated(false)
            ->emptyStateHeading('Semua kelas sudah punya jadwal')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    #[On('academic-year-context-changed')]
    public function onAcademicYearContextChanged(): void
    {
        //
    }
}
