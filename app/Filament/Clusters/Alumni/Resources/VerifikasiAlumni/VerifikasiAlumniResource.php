<?php

namespace App\Filament\Clusters\Alumni\Resources\VerifikasiAlumni;

use App\Filament\Clusters\Alumni\AlumniCluster;
use App\Filament\Clusters\Alumni\Resources\VerifikasiAlumni\Pages\ManageAlumniProfiles;
use App\Models\AlumniProfile;
use App\Models\ProgramKeahlian;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VerifikasiAlumniResource extends Resource
{
    protected static ?string $model = AlumniProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?string $cluster = AlumniCluster::class;

    protected static ?string $navigationLabel = 'Verifikasi Alumni';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Verifikasi Alumni';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_verified', false);
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
                TextColumn::make('user.name')->label('Nama')->searchable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('tahun_lulus')->label('Tahun Lulus')->sortable(),
                TextColumn::make('programKeahlian.name')->label('Program Keahlian')->placeholder('—'),
                TextColumn::make('nis_klaim')->label('NIS (Klaim)')->placeholder('—'),
                IconColumn::make('is_verified')->label('Terverifikasi')->boolean(),
                TextColumn::make('created_at')->label('Daftar Pada')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_verified')->label('Status Verifikasi'),
                SelectFilter::make('program_keahlian_id')
                    ->label('Program Keahlian')
                    ->options(fn () => ProgramKeahlian::pluck('name', 'id')),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (AlumniProfile $record) => ! $record->is_verified)
                    ->requiresConfirmation()
                    ->action(function (AlumniProfile $record) {
                        $record->update([
                            'is_verified' => true,
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Alumni terverifikasi.')->success()->send();
                    }),
                DeleteAction::make()->label('Tolak/Hapus'),
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
            'index' => ManageAlumniProfiles::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return AlumniProfile::where('is_verified', false)->count() ?: null;
    }
}
