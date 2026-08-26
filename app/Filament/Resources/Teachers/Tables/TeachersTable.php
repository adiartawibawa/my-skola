<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Enums\GolonganEnum;
use App\Enums\StatusKepegawaianEnum;
use App\Models\Teacher;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Nama Guru')
                    ->description(fn (Teacher $record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('nuptk')
                    ->label('NUPTK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->badge()
                    ->sortable(),

                TextColumn::make('golongan')
                    ->label('Golongan')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('bidang_studi')
                    ->label('Bidang Studi')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('homeroom_class')
                    ->label('Wali Kelas Saat Ini')
                    ->getStateUsing(fn (Teacher $record) => $record->currentHomeroomClass()?->full_name ?? '—'),

                TextColumn::make('tanggal_masuk')
                    ->label('TMT')
                    ->date('d-m-Y')
                    ->sortable(),

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
                SelectFilter::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->options(StatusKepegawaianEnum::class),

                SelectFilter::make('golongan')
                    ->label('Golongan')
                    ->options(GolonganEnum::class),
            ])
            ->defaultSort('nik')
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
