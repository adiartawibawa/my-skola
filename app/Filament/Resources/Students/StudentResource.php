<?php

namespace App\Filament\Resources\Students;

use App\Enums\RoleEnum;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\RelationManagers\ClassRoomEnrollmentsRelationManager;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $recordTitleAttribute = 'nisn_name';

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    /**
     * Kontrol akses berbasis role. StudentResource sebelumnya tidak
     * butuh scoping sama sekali karena Guru diblokir total lewat
     * StudentPolicy — sekarang kaprodi butuh akses, dibatasi ke siswa
     * yang kelas AKTIF-nya berada di Program Keahlian yang dia pimpin.
     * Role lain (Admin/Kepala Sekolah/Tata Usaha) tidak terdampak.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user?->role === RoleEnum::TEACHER) {
            $headProgramKeahlianId = $user->teacher?->currentHeadOfProgramKeahlian()?->id;

            $query->whereHas(
                'classRoomEnrollments',
                fn ($q) => $q
                    ->whereHas(
                        'classRoom',
                        fn ($rq) => $rq->withoutGlobalScopes()->where('program_keahlian_id', $headProgramKeahlianId),
                    )
                    ->whereHas('academicYear', fn ($rq) => $rq->active()),
            );
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ClassRoomEnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
