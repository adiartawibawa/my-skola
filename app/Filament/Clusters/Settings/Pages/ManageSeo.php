<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\SeoSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSeo extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string $settings = SeoSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'SEO';

    protected static string|UnitEnum|null $navigationGroup = 'Lanjutan & Integrasi';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Metadata Default')
                    ->description('Dipakai untuk halaman yang tidak menyetel judul/deskripsi sendiri (mis. halaman yang belum kita bangun).')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('default_meta_title')
                            ->label('Meta Title Default')
                            ->maxLength(60)
                            ->required(),

                        Textarea::make('default_meta_description')
                            ->label('Meta Description Default')
                            ->rows(2)
                            ->maxLength(160),

                        FileUpload::make('default_og_image')
                            ->label('OG Image Default')
                            ->image()
                            ->directory('settings')
                            ->helperText('Muncul saat halaman dibagikan ke sosial media, jika halaman tidak punya gambar sendiri. Rasio disarankan 1200x630px.'),
                    ]),

                Section::make('Verifikasi & Analitik')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('google_search_console_verification')
                            ->label('Kode Verifikasi Google Search Console')
                            ->helperText('Hanya isi kode content= dari tag meta yang diberikan Google, tanpa tag HTML-nya.'),

                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics Measurement ID')
                            ->placeholder('G-XXXXXXXXXX'),

                        TextInput::make('twitter_username')
                            ->label('Username Twitter/X')
                            ->prefix('@'),
                    ]),

                Section::make('Kontrol Mesin Pencari')
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('indexable')
                            ->label('Izinkan situs diindeks mesin pencari')
                            ->helperText('Matikan sementara kalau situs masih dalam tahap pengembangan/staging — mencegah Google mengindeks konten yang belum final.'),
                    ]),
            ]);
    }
}
