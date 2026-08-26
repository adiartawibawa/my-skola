<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\ClassRoom;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassRoomEnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'classRoomEnrollments';

    protected static ?string $title = 'Riwayat Kelas';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('class_room_id')
            ->modifyQueryUsing(fn ($query) => $query->withoutGlobalScopes())
            ->columns([
                TextColumn::make('classRoom')
                    ->label('Kelas')
                    ->getStateUsing(
                        fn ($record) => self::resolveClassRoom($record)?->full_name ?? '—'
                    ),

                TextColumn::make('academicYear')
                    ->label('Tahun Akademik')
                    ->getStateUsing(
                        fn ($record) => self::resolveClassRoom($record)?->academicYear?->name ?? '—'
                    ),

                TextColumn::make('joined_at')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('left_at')
                    ->label('Keluar')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (ClassRoomStudentStatusEnum $state) => $state->label())
                    ->color(fn (ClassRoomStudentStatusEnum $state) => $state->color()),
            ])
            ->defaultSort('joined_at', 'desc')
            ->headerActions([])
            ->recordActions([]);

    }

    protected static function resolveClassRoom($record): ?ClassRoom
    {
        return ClassRoom::query()
            ->withoutGlobalScopes()
            ->with('academicYear')
            ->find($record->class_room_id);
    }
}
