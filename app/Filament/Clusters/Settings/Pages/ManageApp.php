<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\AppSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageApp extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAsiaAustralia;

    protected static string $settings = AppSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Aplikasi';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional & Komunikasi';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Regional')
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull()
                    ->components([
                        Select::make('timezone')
                            ->label('Zona Waktu')
                            ->options([
                                'Asia/Jakarta' => 'WIB — Jakarta',
                                'Asia/Makassar' => 'WITA — Makassar',
                                'Asia/Jayapura' => 'WIT — Jayapura',
                            ])
                            ->required()
                            ->helperText('Mengubah ini akan langsung memengaruhi seluruh jam & tanggal di aplikasi.'),

                        Select::make('locale')
                            ->label('Bahasa')
                            ->options(['id' => 'Indonesia', 'en' => 'English'])
                            ->required(),

                        Select::make('date_format')
                            ->label('Format Tanggal')
                            ->options([
                                'd M Y' => now()->format('d M Y').' (d M Y)',
                                'd/m/Y' => now()->format('d/m/Y').' (d/m/Y)',
                                'Y-m-d' => now()->format('Y-m-d').' (Y-m-d)',
                            ])
                            ->required(),
                    ]),

                Section::make('Mode Pemeliharaan')
                    ->columns(1)
                    ->collapsible()
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('maintenance_mode')
                            ->label('Aktifkan Mode Pemeliharaan')
                            ->helperText('Saat aktif, pengunjung publik akan melihat halaman pemeliharaan. Admin tetap bisa mengakses panel & situs untuk pratinjau.')
                            ->live(),

                        Textarea::make('maintenance_message')
                            ->label('Pesan untuk Pengunjung')
                            ->rows(2)
                            ->visible(fn ($get) => $get('maintenance_mode')),
                    ]),
            ]);
    }
}
