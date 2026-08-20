<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicCalendars\Tables;

use App\Enums\Enums\EventType;
use App\Enums\Enums\SemesterEnum;
use App\Support\AcademicYearContext;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AcademicCalendarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_name')
                    ->label('Nama Event')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (EventType $state) => $state->label())
                    ->color(fn (EventType $state) => $state->color()),

                TextColumn::make('event_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('event_end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn (?SemesterEnum $state) => $state ? $state->label() : '—'),

                TextColumn::make('academicYear.name')
                    ->label('Tahun Akademik')
                    ->sortable(),

                IconColumn::make('is_national_holiday')
                    ->label('Nasional')
                    ->boolean(),

                IconColumn::make('is_school_holiday')
                    ->label('Sekolah')
                    ->boolean(),
            ])
            ->defaultSort('event_date')
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => AcademicYearContext::get()?->id)
                    ->searchable(),

                SelectFilter::make('event_type')
                    ->label('Tipe Event')
                    ->options(collect(EventType::cases())->mapWithKeys(
                        fn (EventType $type) => [$type->value => $type->label()]
                    )),

                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options(collect(SemesterEnum::cases())->mapWithKeys(
                        fn (SemesterEnum $semester) => [$semester->value => $semester->label()]
                    )),

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
