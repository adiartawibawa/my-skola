<?php

namespace App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProgramKeahlianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->helperText('Kode singkat, mis. TKJ, OTO, RPL — dipakai untuk penamaan rombel (contoh: "X TKJ A").')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                    ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null),

                TextInput::make('name')
                    ->label('Nama Program Keahlian')
                    ->required()
                    ->maxLength(150),

                Select::make('duration_years')
                    ->label('Lama Pendidikan')
                    ->helperText('Menentukan tingkat akhir: 3 tahun = XII, 4 tahun = XIII. Dipakai saat proses kenaikan/kelulusan kelas.')
                    ->options([
                        3 => '3 Tahun (X–XII)',
                        4 => '4 Tahun (X–XIII)',
                    ])
                    ->default(3)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
