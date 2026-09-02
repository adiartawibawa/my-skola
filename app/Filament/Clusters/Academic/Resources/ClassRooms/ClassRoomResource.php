<?php

namespace App\Filament\Clusters\Academic\Resources\ClassRooms;

use App\Enums\RoleEnum;
use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Resources\ClassRooms\Pages\CreateClassRoom;
use App\Filament\Clusters\Academic\Resources\ClassRooms\Pages\EditClassRoom;
use App\Filament\Clusters\Academic\Resources\ClassRooms\Pages\ListClassRooms;
use App\Filament\Clusters\Academic\Resources\ClassRooms\RelationManagers\ClassRoomStudentsRelationManager;
use App\Filament\Clusters\Academic\Resources\ClassRooms\RelationManagers\ClassRoomTeachersRelationManager;
use App\Filament\Clusters\Academic\Resources\ClassRooms\Schemas\ClassRoomForm;
use App\Filament\Clusters\Academic\Resources\ClassRooms\Tables\ClassRoomsTable;
use App\Models\ClassRoom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Kelas / Rombel';

    protected static ?int $navigationSort = 3;

    /**
     * Kontrol akses berbasis role — bagian pembatas BARIS, bukan
     * boleh/tidaknya (itu tugas ClassRoomPolicy). Guru hanya boleh
     * melihat kelas yang JADI wali kelasnya sendiri saat ini; role
     * lain (Admin/Kepala Sekolah/Tata Usaha, sudah difilter lolos
     * lewat Gate::before()/Policy) melihat semua seperti biasa.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user?->role === RoleEnum::TEACHER) {
            $teacherId = $user->teacher?->id;
            $headProgramKeahlianId = $user->teacher?->currentHeadOfProgramKeahlian()?->id;

            $query->where(function (Builder $q) use ($teacherId, $headProgramKeahlianId) {
                $q->whereHas(
                    'classRoomTeachers',
                    fn ($rq) => $rq->withoutGlobalScopes()
                        ->where('teacher_id', $teacherId)
                        ->whereNull('ended_at'),
                );

                if ($headProgramKeahlianId) {
                    $q->orWhere('program_keahlian_id', $headProgramKeahlianId);
                }
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return ClassRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassRoomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ClassRoomTeachersRelationManager::class,
            ClassRoomStudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassRooms::route('/'),
            'create' => CreateClassRoom::route('/create'),
            'edit' => EditClassRoom::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
