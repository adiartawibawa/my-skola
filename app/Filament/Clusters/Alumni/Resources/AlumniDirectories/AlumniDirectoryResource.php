<?php

namespace App\Filament\Clusters\Alumni\Resources\AlumniDirectories;

use App\Filament\Clusters\Alumni\AlumniCluster;
use App\Filament\Clusters\Alumni\Resources\AlumniDirectories\Pages\ManageAlumniDirectories;
use App\Models\AlumniProfile;
use App\Models\ProgramKeahlian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlumniDirectoryResource extends Resource
{
    protected static ?string $model = AlumniProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $cluster = AlumniCluster::class;

    protected static ?string $navigationLabel = 'Direktori Alumni';

    protected static ?string $modelLabel = 'Direktori Alumni';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_verified', true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('tahun_lulus')
                    ->label('Tahun Lulus')
                    ->getStateUsing(fn (AlumniProfile $record) => $record->resolvedTahunLulus() ?? '—')
                    ->sortable(),

                TextColumn::make('program_keahlian')
                    ->label('Program Keahlian')
                    ->getStateUsing(fn (AlumniProfile $record) => $record->resolvedProgramKeahlianName() ?? '—'),

                IconColumn::make('source')
                    ->label('Digital')
                    ->boolean()
                    ->getStateUsing(fn (AlumniProfile $record) => $record->isFromDigitalTrack())
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-user-plus')
                    ->tooltip(fn (AlumniProfile $record) => $record->isFromDigitalTrack()
                        ? 'Tercatat sistem sejak siswa aktif'
                        : 'Daftar mandiri, diverifikasi manual'),

                TextColumn::make('verified_at')
                    ->label('Diverifikasi')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('program_keahlian')
                    ->label('Program Keahlian')
                    ->options(fn () => ProgramKeahlian::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        // Cek dua kemungkinan sumber: kolom langsung
                        // (jalur legacy) ATAU lewat kelas terakhir siswa
                        // (jalur digital) — konsisten dengan bagaimana
                        // resolvedProgramKeahlianName() membaca datanya.
                        return $query->where(function (Builder $q) use ($data) {
                            $q->where('program_keahlian_id', $data['value'])
                                ->orWhereHas(
                                    'student.classRoomEnrollments',
                                    fn ($eq) => $eq->withoutGlobalScopes()
                                        ->where('status', 'lulus')
                                        ->whereHas(
                                            'classRoom',
                                            fn ($cq) => $cq->withoutGlobalScopes()
                                                ->where('program_keahlian_id', $data['value']),
                                        ),
                                );
                        });
                    }),
            ])
            ->defaultSort('verified_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAlumniDirectories::route('/'),
        ];
    }
}
