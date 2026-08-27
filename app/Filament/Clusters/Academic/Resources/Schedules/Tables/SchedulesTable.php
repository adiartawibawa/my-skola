<?php

namespace App\Filament\Clusters\Academic\Resources\Schedules\Tables;

use App\Enums\DayOfWeekEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->badge()
                    ->formatStateUsing(fn (DayOfWeekEnum $state) => $state->label())
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Jam')
                    ->formatStateUsing(fn ($record) => $record->start_time->format('H:i').' - '.$record->end_time->format('H:i')),

                TextColumn::make('classRoom.full_name')
                    ->label('Kelas')
                    ->searchable(),

                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),

                TextColumn::make('teacher.user.name')
                    ->label('Guru')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label('Hari')
                    ->options(DayOfWeekEnum::options()),

                SelectFilter::make('class_room_id')
                    ->label('Kelas')
                    ->relationship('classRoom', 'rombel_label'),

                SelectFilter::make('teacher_id')
                    ->label('Guru')
                    ->relationship('teacher', 'nip'),
            ])
            ->modifyQueryUsing(fn ($query) => $query
                ->orderByRaw("FIELD(day_of_week, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
                ->orderBy('start_time'))
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
