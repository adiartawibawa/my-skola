<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Models\Announcement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_pinned')
                    ->label('Pin')
                    ->boolean()
                    ->trueIcon('heroicon-s-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('warning'),

                TextColumn::make('title')
                    ->label('Judul')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (Announcement $record) => $record->target_summary),

                TextColumn::make('publish_at')
                    ->label('Tayang')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Langsung tayang')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh'),
            ])
            ->filters([
                TernaryFilter::make('is_for_all')
                    ->label('Untuk Semua'),

                TernaryFilter::make('is_pinned')
                    ->label('Disematkan'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->orderByDesc('is_pinned')->orderByDesc('created_at'));
    }
}
