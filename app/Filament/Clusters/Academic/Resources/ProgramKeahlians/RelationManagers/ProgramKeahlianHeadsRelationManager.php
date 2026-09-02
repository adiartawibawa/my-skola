<?php

namespace App\Filament\Clusters\Academic\Resources\ProgramKeahlians\RelationManagers;

use App\Models\Teacher;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramKeahlianHeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'heads';

    protected static ?string $title = 'Riwayat Kepala Program Keahlian';

    protected static ?string $modelLabel = 'Penugasan Kaprodi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')
                    ->label('Guru')
                    ->options(
                        fn () => Teacher::query()
                            ->with('user')
                            ->get()
                            ->mapWithKeys(fn (Teacher $teacher) => [
                                $teacher->id => "{$teacher->user?->name} ({$teacher->nip})",
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('started_at')
                    ->label('Mulai Menjabat')
                    ->native(false)
                    ->required(),

                DatePicker::make('ended_at')
                    ->label('Selesai Menjabat')
                    ->native(false)
                    ->helperText('Kosongkan jika masih menjabat sebagai kaprodi.'),

                Textarea::make('reason')
                    ->label('Alasan Pergantian')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('teacher_id')
            ->columns([
                TextColumn::make('teacher.user.name')
                    ->label('Guru')
                    ->description(fn ($record) => $record->teacher?->nip)
                    ->searchable(),

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
                    ->boolean(fn ($state) => $state === null),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('started_at', 'desc')
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DissociateBulkAction::make(),
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
