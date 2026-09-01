<?php

namespace App\Filament\Widgets;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Enums\RoleEnum;
use App\Models\ClassRoomStudent;
use App\Models\ProgramKeahlian;
use App\Support\AcademicYearContext;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Attributes\On;

class StudentsByProgramKeahlianChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Siswa Aktif per Program Keahlian';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role !== RoleEnum::TEACHER;
    }

    protected function getData(): array
    {
        $academicYearId = AcademicYearContext::get()?->id;

        $programs = ProgramKeahlian::query()
            ->active()
            ->orderBy('code')
            ->get();

        $counts = $programs->map(
            fn (ProgramKeahlian $program) => ClassRoomStudent::query()
                ->withoutGlobalScopes()
                ->where('academic_year_id', $academicYearId)
                ->where('status', ClassRoomStudentStatusEnum::AKTIF->value)
                ->whereHas(
                    'classRoom',
                    fn ($query) => $query
                        ->withoutGlobalScopes()
                        ->where('program_keahlian_id', $program->id),
                )
                ->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Siswa Aktif',
                    'data' => $counts->toArray(),
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => $programs->pluck('code')->toArray(),
        ];

    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Sama seperti AcademicOverviewStats — lihat komentar di sana.
     */
    #[On('academic-year-context-changed')]
    public function onAcademicYearContextChanged(): void
    {
        //
    }
}
