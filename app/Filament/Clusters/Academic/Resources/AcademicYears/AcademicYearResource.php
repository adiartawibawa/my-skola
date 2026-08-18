<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears;

use App\Filament\Clusters\Academic\AcademicCluster;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Pages\ManageAcademicYears;
use App\Filament\Widgets\AcademicCalendarWidget;
use App\Models\AcademicYear;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = AcademicCluster::class;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                Toggle::make('is_active')
                    ->required()
                    ->helperText('Hanya satu tahun ajaran yang dapat aktif')
                    ->afterStateUpdated(function ($state, $set, $get, $record) {
                        // Jika di-set aktif, nonaktifkan yang lain (akan ditangani oleh model)
                    }),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
            ])
            ->recordActions([
                Action::make('calendar')
                    ->label('Kalender')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->closeModalByClickingAway(false)
                    ->modalContent(function ($record) {
                        return new HtmlString(
                            Blade::render(
                                '@livewire($component, [
                    "academicYearId" => $academicYearId,
                ])',
                                [
                                    'component' => AcademicCalendarWidget::class,
                                    'academicYearId' => (string) $record->id,
                                ],
                            )
                        );
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAcademicYears::route('/'),
        ];
    }

    /**
     * Mendapatkan tahun ajaran aktif
     */
    public static function getActiveYear(): ?AcademicYear
    {
        return AcademicYear::active()->first();
    }

    /**
     * Mengaktifkan tahun ajaran tertentu dan menonaktifkan yang lain
     */
    public static function setActiveYear(AcademicYear $academicYear): void
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('id', '!=', $academicYear->id)
                ->update(['is_active' => false]);

            $academicYear->update(['is_active' => true]);
        });
    }
}
