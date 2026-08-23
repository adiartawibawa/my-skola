<?php

namespace App\Filament\Clusters\Academic\Resources\ClassRooms\Schemas;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ClassRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Akademik')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::resolveDefault()?->id)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('program_keahlian_id')
                            ->label('Program Keahlian')
                            ->relationship(
                                'programKeahlian',
                                'name',
                                fn ($query) => $query->active(),
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('grade_level')
                            ->label('Tingkat')
                            ->options([
                                10 => 'X',
                                11 => 'XI',
                                12 => 'XII',
                                13 => 'XIII',
                            ])
                            ->required(),

                        TextInput::make('rombel_label')
                            ->label('Label Rombel')
                            ->helperText('Contoh: A, B, 1, Pagi — bebas, akan digabung jadi "X TKJ A".')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Deskripsi'),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
