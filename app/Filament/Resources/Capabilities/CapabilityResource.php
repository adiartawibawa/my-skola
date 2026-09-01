<?php

namespace App\Filament\Resources\Capabilities;

use App\Filament\Resources\Capabilities\Pages\CreateCapability;
use App\Filament\Resources\Capabilities\Pages\EditCapability;
use App\Filament\Resources\Capabilities\Pages\ListCapabilities;
use App\Filament\Resources\Capabilities\Schemas\CapabilityForm;
use App\Filament\Resources\Capabilities\Tables\CapabilitiesTable;
use App\Models\Capability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CapabilityResource extends Resource
{
    protected static ?string $model = Capability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return CapabilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapabilitiesTable::configure($table);
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
            'index' => ListCapabilities::route('/'),
            'create' => CreateCapability::route('/create'),
            'edit' => EditCapability::route('/{record}/edit'),
        ];
    }
}
