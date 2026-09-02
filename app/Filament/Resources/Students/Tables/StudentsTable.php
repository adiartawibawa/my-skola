<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\ProgramKeahlian;
use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),

                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->description(fn (Student $record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('current_class_room')
                    ->label('Kelas Saat Ini')
                    ->getStateUsing(fn (Student $record) => $record->currentClassRoom()?->full_name ?? '—'),

                TextColumn::make('program_keahlian')
                    ->label('Program Keahlian')
                    ->getStateUsing(fn (Student $record) => $record->programKeahlian()?->name ?? '—'),

                TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('tanggal_lahir')
                    ->date('d-m-Y')
                    ->label('Tanggal Lahir')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('nama_ayah')
                    ->label('Ayah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nama_ibu')
                    ->label('Ibu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
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
            ->defaultSort('nisn')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                // whereHas ke classRoomEnrollments (bukan kolom
                // tersimpan) — filter ini cuma menyaring siswa yang
                // SEDANG aktif di kelas program tersebut pada Tahun
                // Akademik aktif, konsisten dengan currentClassRoom().
                SelectFilter::make('program_keahlian_id')
                    ->label('Program Keahlian')
                    ->options(fn () => ProgramKeahlian::query()->active()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(
                            'classRoomEnrollments',
                            fn ($q) => $q
                                ->whereHas(
                                    'classRoom',
                                    fn ($rq) => $rq->withoutGlobalScopes()->where('program_keahlian_id', $data['value']),
                                )
                                ->whereHas('academicYear', fn ($rq) => $rq->active()),
                        );
                    }),

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
