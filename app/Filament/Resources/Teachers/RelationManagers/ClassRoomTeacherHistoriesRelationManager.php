<?php

namespace App\Filament\Resources\Teachers\RelationManagers;

use App\Models\ClassRoom;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassRoomTeacherHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'classRoomTeacherHistories';

    protected static ?string $title = 'Riwayat Wali Kelas';

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

                TextColumn::make('started_at')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('ended_at')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('Masih menjabat')
                    ->sortable(),

                IconColumn::make('ended_at')
                    ->label('Aktif')
                    ->getStateUsing(fn ($record) => $record->ended_at === null)
                    ->boolean(),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('started_at', 'desc')
            ->headerActions([])
            ->recordActions([]);

    }

    /**
     * Query langsung tanpa scope, terpisah dari mekanisme eager-load
     * Filament sepenuhnya. Sedikit N+1 (1 query tambahan per baris),
     * tapi tabel riwayat ini kecil (per guru) sehingga dampaknya
     * dapat diabaikan — kebenaran data lebih diprioritaskan di sini.
     */
    protected static function resolveClassRoom($record): ?ClassRoom
    {
        return ClassRoom::query()
            ->withoutGlobalScopes()
            ->with('academicYear')
            ->find($record->class_room_id);
    }
}
