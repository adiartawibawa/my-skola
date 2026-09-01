<?php

namespace App\Filament\Widgets;

use App\Enums\RoleEnum;
use App\Filament\Clusters\Academic\Resources\ClassRooms\ClassRoomResource;
use App\Models\ClassRoom;
use App\Support\AcademicYearContext;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class ClassRoomsWithoutHomeroomWidget extends BaseWidget
{
    protected static ?string $heading = 'Kelas Tanpa Wali Kelas';

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
                    ->whereDoesntHave(
                        'classRoomTeachers',
                        fn ($query) => $query->whereNull('ended_at'),
                    )
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
                Action::make('assign')
                    ->label('Tugaskan Wali Kelas')
                    ->icon('heroicon-o-user-plus')
                    ->url(fn (ClassRoom $record) => ClassRoomResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Semua kelas sudah punya wali kelas')
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    /**
     * Sama seperti widget dashboard lain — dengar sinyal ganti Tahun
     * Akademik dari Dashboard::filtersForm().
     */
    #[On('academic-year-context-changed')]
    public function onAcademicYearContextChanged(): void
    {
        //
    }
}
