<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Enums\GolonganEnum;
use App\Enums\PendidikanEnum;
use App\Enums\StatusKepegawaianEnum;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class TeacherForm
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
                        modifyQueryUsing: fn ($query) => $query->whereDoesntHave('teacher'),
                    )
                    ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation === Operation::Edit)
                    ->dehydrated(),

                Grid::make(3)
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->required()
                            ->maxLength(16)
                            ->unique(ignoreRecord: true),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true),

                        TextInput::make('nuptk')
                            ->label('NUPTK')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options(StatusKepegawaianEnum::class)
                            ->native(false)
                            ->required(),

                        Select::make('golongan')
                            ->label('Golongan')
                            ->options(GolonganEnum::class)
                            ->native(false),

                        TextInput::make('bidang_studi')
                            ->label('Bidang Studi')
                            ->maxLength(100),

                        Select::make('pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options(PendidikanEnum::class)
                            ->native(false),

                        DatePicker::make('tanggal_masuk')
                            ->label('TMT (Tanggal Masuk)')
                            ->native(false),
                    ]),

            ]);
    }
}
