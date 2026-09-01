<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RoleEnum;
use App\Models\Capability;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                    ->options(RoleEnum::options())
                    ->default(RoleEnum::default())
                    ->required(),

                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->nullable(),

                Section::make('Capability')
                    ->columnSpanFull()
                    ->components([
                        CheckboxList::make('capabilities')
                            ->relationship('capabilities', 'name')
                            ->descriptions(fn () => Capability::query()->pluck('description', 'id')->toArray())
                            ->columns(2)
                            ->bulkToggleable()
                            ->disabled(fn (?User $record) => ! auth()->user()->can('assignCapabilities', $record ?? User::class))
                            ->dehydrated(fn (?User $record) => auth()->user()->can('assignCapabilities', $record ?? User::class))
                            ->helperText(fn (?User $record) => auth()->user()->can('assignCapabilities', $record ?? User::class)
                                ? 'Kemampuan tambahan lintas role — tidak terikat pada role akademis di atas.'
                                : 'Hanya Super Admin/Admin Sekolah yang dapat mengubah capability. Tata Usaha dapat melihat, tidak dapat mengubah.'),
                    ]),
            ]);
    }
}
