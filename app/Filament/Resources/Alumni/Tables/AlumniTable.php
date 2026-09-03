<?php

namespace App\Filament\Resources\Alumni\Tables;

use App\Models\ClassRoom;
use App\Models\ProgramKeahlian;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlumniTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Nama')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable(),

                // Notasi titik sengaja dihindari — lihat catatan yang
                // sudah berulang kali di RelationManager lain: eager-
                // load Filament untuk classRoom.* masih kena
                // ActiveAcademicYearScope milik ClassRoom sendiri,
                // walau query utama sudah withoutGlobalScopes().
                TextColumn::make('classRoom')
                    ->label('Kelas Terakhir')
                    ->getStateUsing(fn ($record) => self::resolveClassRoom($record)?->full_name ?? '—'),

                TextColumn::make('program_keahlian')
                    ->label('Program Keahlian')
                    ->getStateUsing(fn ($record) => self::resolveClassRoom($record)?->programKeahlian?->name ?? '—'),

                TextColumn::make('academic_year')
                    ->label('Tahun Kelulusan')
                    ->getStateUsing(fn ($record) => self::resolveClassRoom($record)?->academicYear?->name ?? '—'),

                TextColumn::make('left_at')
                    ->label('Tanggal Lulus')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('program_keahlian_id')
                    ->label('Program Keahlian')
                    ->options(fn () => ProgramKeahlian::query()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas(
                            'classRoom',
                            fn ($q) => $q->withoutGlobalScopes()->where('program_keahlian_id', $data['value']),
                        );
                    }),

                SelectFilter::make('academic_year_id')
                    ->label('Tahun Kelulusan')
                    ->relationship('academicYear', 'name'),
            ])
            ->defaultSort('left_at', 'desc');
    }

    protected static function resolveClassRoom($record): ?ClassRoom
    {
        return ClassRoom::query()
            ->withoutGlobalScopes()
            ->with('programKeahlian', 'academicYear')
            ->find($record->class_room_id);
    }
}
