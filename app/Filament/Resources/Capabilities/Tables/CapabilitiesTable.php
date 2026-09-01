<?php

namespace App\Filament\Resources\Capabilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CapabilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->badge()->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('description')->limit(50)->toggleable(),
                TextColumn::make('users_count')
                    ->label('Dipakai Oleh')
                    ->counts('users')
                    ->suffix(' user')
                    ->sortable(),
            ])
            ->filters([
                //
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
