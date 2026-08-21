<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears;

use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Pages\AcademicYearCalendar;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Schemas\AcademicYearForm;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Tables\AcademicYearsTable;
use App\Models\AcademicYear;
use BackedEnum;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Tahun Akademik';

    protected static ?string $pluralModelLabel = 'Tahun Akademik';

    public static function form(Schema $schema): Schema
    {
        return AcademicYearForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicYearsTable::configure($table);
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
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'edit' => EditAcademicYear::route('/{record}/edit'),
            'calendar' => AcademicYearCalendar::route('/{record}/calendar'),
        ];
    }

    /**
     * Ubah start_year (field bantu, bukan kolom model) menjadi start_date
     * yang sesungguhnya. end_date sengaja TIDAK dikirim — AcademicYear
     * model men-derive-nya sendiri di saving hook.
     */
    public static function transformStartYear(array $data): array
    {
        if (isset($data['start_year'])) {
            $data['start_date'] = Carbon::create((int) $data['start_year'], 7, 1)->toDateString();
            unset($data['start_year']);
        }

        return $data;
    }
}
