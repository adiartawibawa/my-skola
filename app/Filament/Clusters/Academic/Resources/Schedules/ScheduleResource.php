<?php

namespace App\Filament\Clusters\Academic\Resources\Schedules;

use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Clusters\Academic\Resources\Schedules\Pages\EditSchedule;
use App\Filament\Clusters\Academic\Resources\Schedules\Pages\ListSchedules;
use App\Filament\Clusters\Academic\Resources\Schedules\Schemas\ScheduleForm;
use App\Filament\Clusters\Academic\Resources\Schedules\Tables\SchedulesTable;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'subject_id';

    protected static ?string $modelLabel = 'Jadwal Pelajaran';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
