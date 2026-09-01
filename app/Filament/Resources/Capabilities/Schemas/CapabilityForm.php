<?php

namespace App\Filament\Resources\Capabilities\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CapabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rule('regex:/^[a-z]+(\.[a-z_]+)+$/')
                    ->placeholder('blog.write')
                    ->helperText('Format: domain.aksi — contoh: blog.write, blog.editor.'),

                TextInput::make('name')
                    ->required()
                    ->placeholder('Menulis Blog'),

                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Ditampilkan ke admin saat memilih capability ini di form User.'),
            ]);
    }
}
