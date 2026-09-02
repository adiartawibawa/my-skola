<?php

namespace App\Filament\Clusters\Academic\Resources\ProgramKeahlians;

use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Pages\CreateProgramKeahlian;
use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Pages\EditProgramKeahlian;
use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Pages\ListProgramKeahlians;
use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\RelationManagers\ProgramKeahlianHeadsRelationManager;
use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Schemas\ProgramKeahlianForm;
use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Tables\ProgramKeahliansTable;
use App\Models\ProgramKeahlian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProgramKeahlianResource extends Resource
{
    protected static ?string $model = ProgramKeahlian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Program Keahlian';

    protected static ?string $pluralModelLabel = 'Program Keahlian';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ProgramKeahlianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramKeahliansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProgramKeahlianHeadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramKeahlians::route('/'),
            'create' => CreateProgramKeahlian::route('/create'),
            'edit' => EditProgramKeahlian::route('/{record}/edit'),
        ];
    }
}
