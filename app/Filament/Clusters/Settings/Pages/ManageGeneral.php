<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageGeneral extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string $settings = GeneralSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Umum';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem & Tampilan';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Sekolah')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->components([
                        TextInput::make('school_name')->label('Nama Sekolah')->required(),
                        TextInput::make('tagline')->label('Tagline'),
                        FileUpload::make('logo')->image()->directory('settings'),
                        FileUpload::make('favicon')->image()->directory('settings'),
                    ]),

                Section::make('Kontak')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->components([
                        TextInput::make('address')->label('Alamat')->columnSpanFull(),
                        TextInput::make('email')->email(),
                        TextInput::make('phone')->label('Telepon'),
                        TextInput::make('founded_year')->label('Tahun Berdiri'),
                        TextInput::make('service_hours_weekday')->label('Jam Layanan (Senin–Jumat)'),
                        TextInput::make('service_hours_weekend')->label('Jam Layanan (Sabtu–Minggu)'),
                    ]),

                Section::make('Media Sosial')
                    ->columns(3)
                    ->columnSpanFull()
                    ->collapsible()
                    ->components([
                        TextInput::make('instagram_url')->label('Instagram')->url(),
                        TextInput::make('youtube_url')->label('YouTube')->url(),
                        TextInput::make('facebook_url')->label('Facebook')->url(),
                    ]),
            ]);
    }
}
