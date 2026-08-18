<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Enums\RoleEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->autocomplete('username'),

                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->autocomplete('email'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->autocomplete('new-password'),

                Select::make('role')
                    ->label('Role')
                    ->options(RoleEnum::class)
                    ->default(RoleEnum::USER)
                    ->required(),

                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->nullable(),
            ]);
    }
}
