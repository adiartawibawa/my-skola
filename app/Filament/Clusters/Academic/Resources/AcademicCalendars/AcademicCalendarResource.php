<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicCalendars;

use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Resources\AcademicCalendars\Pages\CreateAcademicCalendar;
use App\Filament\Clusters\Academic\Resources\AcademicCalendars\Pages\EditAcademicCalendar;
use App\Filament\Clusters\Academic\Resources\AcademicCalendars\Pages\ListAcademicCalendars;
use App\Filament\Clusters\Academic\Resources\AcademicCalendars\Schemas\AcademicCalendarForm;
use App\Filament\Clusters\Academic\Resources\AcademicCalendars\Tables\AcademicCalendarsTable;
use App\Models\AcademicCalendar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AcademicCalendarResource extends Resource
{
    protected static ?string $model = AcademicCalendar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'event_name';

    protected static ?string $navigationLabel = 'Kalender Akademik';

    protected static ?string $modelLabel = 'Event Kalender';

    protected static ?string $pluralModelLabel = 'Event Kalender';

    public static function form(Schema $schema): Schema
    {
        return AcademicCalendarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicCalendarsTable::configure($table);
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
            'index' => ListAcademicCalendars::route('/'),
            'create' => CreateAcademicCalendar::route('/create'),
            'edit' => EditAcademicCalendar::route('/{record}/edit'),
        ];
    }
}
