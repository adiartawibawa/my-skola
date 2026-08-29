<?php

namespace App\Filament\Clusters\Settings\Resources\SchoolLinks;

use App\Enums\LinkCategory;
use App\Filament\Clusters\Settings\Resources\SchoolLinks\Pages\ManageSchoolLinks;
use App\Filament\Clusters\Settings\SettingsCluster;
use App\Models\SchoolLink;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchoolLinkResource extends Resource
{
    protected static ?string $model = SchoolLink::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $navigationLabel = 'Aplikasi & Tautan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),

                TextInput::make('url')
                    ->required()
                    ->url()
                    ->helperText('Sertakan https:// — ini link ke aplikasi/situs eksternal.'),

                Textarea::make('description')->rows(2),

                FileUpload::make('logo')
                    ->image()
                    ->directory('school-links')
                    ->helperText('Opsional. Idealnya logo persegi dengan latar transparan.'),

                Select::make('category')
                    ->options(LinkCategory::options())
                    ->required(),

                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Angka lebih kecil tampil lebih dulu.'),

                Toggle::make('is_featured')
                    ->label('Tampilkan di dropdown header')
                    ->helperText('Hanya beberapa item terpilih yang sebaiknya diaktifkan di sini.'),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                ImageColumn::make('logo')->circular(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('category')->formatStateUsing(fn (LinkCategory $state) => $state->label())->badge(),
                TextColumn::make('url')->limit(30)->url(fn ($record) => $record->url, true),
                IconColumn::make('is_featured')->boolean()->label('Header'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')->options(LinkCategory::options()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManageSchoolLinks::route('/'),
        ];
    }

    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->role->isAdmin() ?? false;
    // }
}
