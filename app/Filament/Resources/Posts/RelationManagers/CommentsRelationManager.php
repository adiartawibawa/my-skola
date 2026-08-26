<?php

namespace App\Filament\Resources\Posts\RelationManagers;

use App\Enums\CommentStatus;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                TextColumn::make('authorName')
                    ->label('Dari')
                    ->getStateUsing(fn ($record) => $record->authorName()),

                TextColumn::make('content')
                    ->limit(60)
                    ->wrap(),

                BadgeColumn::make('status')
                    ->formatStateUsing(fn (CommentStatus $state) => $state->label())
                    ->color(fn (CommentStatus $state) => $state->color()),

                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== CommentStatus::APPROVED)
                    ->action(function ($record) {
                        $record->update(['status' => CommentStatus::APPROVED]);
                        Notification::make()->title('Komentar disetujui.')->success()->send();
                    }),

                Action::make('reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== CommentStatus::REJECTED)
                    ->action(function ($record) {
                        $record->update(['status' => CommentStatus::REJECTED]);
                        Notification::make()->title('Komentar ditolak.')->warning()->send();
                    }),

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
