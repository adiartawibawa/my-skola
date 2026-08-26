<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state)))
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatis dari judul, bisa diedit manual.'),

                        FileUpload::make('featured_image')
                            ->image()
                            ->directory('posts/featured')
                            ->imageEditor(),

                        Textarea::make('excerpt')
                            ->rows(2)
                            ->maxLength(300)
                            ->helperText('Ringkasan singkat, muncul di listing & meta description default.')
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->required()
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->columnSpanFull(),
                    ]),

                Section::make('Kategori & Tag')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required()->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')->required(),
                            ]),

                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required()->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')->required(),
                            ]),
                    ]),

                Section::make('Publikasi')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        Select::make('status')
                            ->options(PostStatus::options())
                            ->required()
                            ->visible(fn () => auth()->user()?->canEditBlog())
                            ->helperText('Author cukup simpan sebagai draft, lalu gunakan tombol "Submit for Review".'),

                        TextInput::make('read_time')
                            ->numeric()
                            ->suffix('menit')
                            ->helperText('Kosongkan untuk dihitung otomatis dari isi konten.'),

                        DateTimePicker::make('published_at')
                            ->visible(fn () => auth()->user()?->canEditBlog()),

                        DateTimePicker::make('scheduled_at')
                            ->visible(fn () => auth()->user()?->canEditBlog()),

                        Textarea::make('review_note')
                            ->label('Catatan Editor')
                            ->disabled()
                            ->visible(fn (?string $state) => filled($state))
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->components([
                        TextInput::make('meta_title')->maxLength(60),
                        TextInput::make('canonical_url')->url(),
                        Textarea::make('meta_description')->maxLength(160)->columnSpanFull(),
                        FileUpload::make('og_image')->image()->directory('posts/og'),
                    ]),
            ]);
    }
}
