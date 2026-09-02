<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\AppearanceSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageAppearance extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string $settings = AppearanceSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'Tampilan';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem & Tampilan';

    protected static ?int $navigationSort = 2;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Warna Utama')
                ->description('Dipakai untuk tombol, aksen navigasi, dan warna primer panel Filament ini juga.')
                ->columns(3)
                ->columnSpanFull()
                ->components([
                    ColorPicker::make('primary')->label('Primer'),
                    ColorPicker::make('primary_dark')->label('Primer (Gelap)')->helperText('Footer, gradient, latar kontras.'),
                    ColorPicker::make('primary_light')->label('Primer (Terang)')->helperText('Hover state.'),
                ]),

            Section::make('Warna Aksen')
                ->columns(2)
                ->columnSpanFull()
                ->components([
                    ColorPicker::make('accent')->label('Aksen'),
                    ColorPicker::make('accent_light')->label('Aksen (Terang)'),
                ]),

            Section::make('Warna Dasar')
                ->columns(2)
                ->columnSpanFull()
                ->components([
                    ColorPicker::make('paper')->label('Latar (Krem/Kertas)'),
                    ColorPicker::make('ink')->label('Teks Utama'),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetDefaults')
                ->label('Kembalikan ke Default')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $this->form->fill([
                        'primary' => '#6B1220',
                        'primary_dark' => '#4A0D17',
                        'primary_light' => '#8C1F2E',
                        'accent' => '#C89B3C',
                        'accent_light' => '#E4C878',
                        'paper' => '#FBF6EE',
                        'ink' => '#241512',
                    ]);
                }),
        ];
    }
}
