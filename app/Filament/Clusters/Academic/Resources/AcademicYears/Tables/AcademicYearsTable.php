<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Tables;

use App\Models\AcademicYear;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('name')
                    ->label('Tahun Akademik')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('academic_calendars_count')
                    ->label('Jumlah Event')
                    ->counts('academicCalendars')
                    ->sortable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->recordActions([
                Action::make('viewCalendar')
                    ->label('Lihat Kalender')
                    ->icon('heroicon-o-calendar-days')
                    ->iconButton()
                    ->url(fn (AcademicYear $record) => route('filament.admin.academic.resources.academic-years.calendar', ['record' => $record])),

                EditAction::make()
                    ->iconButton(),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
