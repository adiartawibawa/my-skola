<?php

namespace App\Filament\Widgets;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\ClassRoom;
use App\Models\ClassRoomStudent;
use App\Models\ProgramKeahlian;
use App\Support\AcademicYearContext;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AcademicOverviewStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $academicYear = AcademicYearContext::get();

        if (! $academicYear) {
            return [
                Stat::make('Tahun Akademik', 'Belum ada')
                    ->description('Buat Tahun Akademik terlebih dahulu')
                    ->color('danger'),
            ];
        }

        $academicYearId = $academicYear->id;

        // withoutGlobalScopes(): filter dashboard bisa memilih tahun
        // yang BUKAN tahun aktif (mis. melihat data tahun lalu) — query
        // di sini harus mengikuti PILIHAN FILTER, bukan ikut dibatasi
        // lagi oleh ActiveAcademicYearScope yang menyasar tahun aktif.
        $classRoomsQuery = ClassRoom::query()
            ->withoutGlobalScopes()
            ->where('academic_year_id', $academicYearId);

        $totalClassRooms = (clone $classRoomsQuery)->count();

        $totalActiveStudents = ClassRoomStudent::query()
            ->withoutGlobalScopes()
            ->where('academic_year_id', $academicYearId)
            ->where('status', ClassRoomStudentStatusEnum::AKTIF->value)
            ->count();

        $classRoomsWithoutHomeroom = (clone $classRoomsQuery)
            ->whereDoesntHave(
                'classRoomTeachers',
                fn ($query) => $query->whereNull('ended_at'),
            )
            ->count();

        $totalProgramKeahlian = ProgramKeahlian::query()->active()->count();

        return [
            Stat::make('Total Kelas', (string) $totalClassRooms)
                ->description('Rombel pada tahun ajaran terpilih')
                ->icon('heroicon-o-user-group'),

            Stat::make('Siswa Aktif', (string) $totalActiveStudents)
                ->description('Di seluruh kelas tahun ajaran terpilih')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Kelas Tanpa Wali Kelas', (string) $classRoomsWithoutHomeroom)
                ->description($classRoomsWithoutHomeroom > 0 ? 'Perlu penugasan wali kelas' : 'Semua kelas sudah punya wali kelas')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($classRoomsWithoutHomeroom > 0 ? 'danger' : 'success'),

            Stat::make('Program Keahlian Aktif', (string) $totalProgramKeahlian)
                ->icon('heroicon-o-briefcase'),
        ];

    }
}
