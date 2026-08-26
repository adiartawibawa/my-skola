<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Enums\PostStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->square(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('author.name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tags.name')
                    ->badge()
                    ->separator(',')
                    ->limitList(2),

                BadgeColumn::make('status')
                    ->formatStateUsing(fn (PostStatus $state) => $state->label())
                    ->color(fn (PostStatus $state) => $state->color()),

                TextColumn::make('published_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(PostStatus::options()),
                SelectFilter::make('category')->relationship('category', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('submitForReview')
                    ->label('Submit for Review')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->can('submitForReview', $record))
                    ->action(function ($record) {
                        $record->submitForReview();
                        Notification::make()->title('Post dikirim untuk direview.')->success()->send();
                    }),

                Action::make('approve')
                    ->label('Approve & Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->can('approve', $record))
                    ->action(function ($record) {
                        $record->approve();
                        Notification::make()->title('Post dipublish.')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Textarea::make('note')
                            ->label('Catatan untuk penulis')
                            ->required(),
                    ])
                    ->visible(fn ($record) => auth()->user()->can('reject', $record))
                    ->action(function ($record, array $data) {
                        $record->reject($data['note']);
                        Notification::make()->title('Post dikembalikan ke draft.')->warning()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
