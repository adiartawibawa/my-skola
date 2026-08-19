<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Schemas;

use App\Enums\Enums\EventType;
use App\Enums\Enums\SemesterEnum;
use App\Models\AcademicCalendar;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Guava\Calendar\Attributes\CalendarSchema;

class CalendarAcademicForm
{
    #[CalendarSchema(AcademicCalendar::class)]
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('event_name')
                    ->label('Nama Event')
                    ->required()
                    ->maxLength(255),

                Select::make('event_type')
                    ->label('Tipe Event')
                    ->options(
                        collect(EventType::cases())
                            ->mapWithKeys(
                                fn (EventType $type) => [
                                    $type->value => $type->name,
                                ],
                            )
                            ->toArray(),
                    )
                    ->required(),

                DatePicker::make('event_date')
                    ->label('Tanggal Mulai')
                    ->required(),

                DatePicker::make('event_end_date')
                    ->label('Tanggal Selesai')
                    ->afterOrEqual('event_date'),

                Select::make('semester')
                    ->label('Semester')
                    ->options(
                        collect(SemesterEnum::cases())
                            ->mapWithKeys(
                                fn (SemesterEnum $semester) => [
                                    $semester->value => $semester->value,
                                ],
                            )
                            ->toArray(),
                    ),

                ColorPicker::make('color')
                    ->label('Warna'),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),

                Checkbox::make(
                    'is_national_holiday',
                )
                    ->label('Hari Libur Nasional'),

                Checkbox::make(
                    'is_school_holiday',
                )
                    ->label('Libur Sekolah'),

            ]);
    }
}
