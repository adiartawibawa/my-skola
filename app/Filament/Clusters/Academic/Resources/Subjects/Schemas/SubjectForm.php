<?php

namespace App\Filament\Clusters\Academic\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Nama Mata Pelajaran')
                            ->required()
                            ->maxLength(150),

                        Select::make('program_keahlian_id')
                            ->label('Program Keahlian')
                            ->relationship(
                                'programKeahlian',
                                'name',
                                fn ($query) => $query->active(),
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Mapel Umum (semua program keahlian)')
                            ->helperText('Kosongkan kalau mapel ini berlaku untuk semua program keahlian (mis. Matematika, Bahasa Indonesia). Isi kalau mapel kejuruan khusus satu program (mis. Pemrograman Web hanya untuk RPL).'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Deskripsi'),
                    ])->columnSpanFull(),
            ]);
    }
}
