<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\NotificationSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageNotification extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string $settings = NotificationSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Notifikasi';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional & Komunikasi';

    protected static ?int $navigationSort = 2;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pesan Formulir Kontak')
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('notify_on_contact_message')
                            ->label('Kirim email saat ada pesan kontak baru')
                            ->live(),

                        TextInput::make('notify_email')
                            ->label('Kirim ke Email')
                            ->email()
                            ->required(fn ($get) => $get('notify_on_contact_message'))
                            ->visible(fn ($get) => $get('notify_on_contact_message'))
                            ->helperText('Bisa email pribadi staf TU, atau alias/grup email sekolah.'),
                    ]),
            ]);
    }
}
