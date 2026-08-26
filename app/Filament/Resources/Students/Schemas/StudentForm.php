<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Akun User')
                    ->relationship(
                        'user',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->whereDoesntHave('student'),
                    )
                    ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                    ->searchable()
                    ->preload()
                    ->required()
                    // Akun tidak boleh
                    // diganti lewat edit, supaya tidak konflik dengan
                    // whereDoesntHave di atas.
                    ->disabled(fn (string $operation): bool => $operation === Operation::Edit)
                    ->dehydrated(),

                Grid::make(2)
                    ->schema([
                        TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('Data Kelahiran')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('tempat_lahir')
                                    ->label('Tempat Lahir'),

                                DatePicker::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Data Orang Tua')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama_ayah')
                                    ->label('Nama Ayah'),

                                TextInput::make('nama_ibu')
                                    ->label('Nama Ibu'),

                                TextInput::make('pekerjaan_orang_tua')
                                    ->label('Pekerjaan Orang Tua'),

                                TextInput::make('no_telp_orang_tua')
                                    ->label('No. Telepon Orang Tua')
                                    ->tel(),
                            ]),

                        Textarea::make('alamat_orang_tua')
                            ->label('Alamat Orang Tua')
                            ->columnSpanFull(),
                    ]),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

            ]);
    }
}
