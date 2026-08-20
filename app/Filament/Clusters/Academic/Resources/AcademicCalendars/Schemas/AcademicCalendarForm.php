<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicCalendars\Schemas;

use App\Enums\Enums\EventType;
use App\Models\AcademicYear;
use App\Support\AcademicYearContext;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AcademicCalendarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        Select::make('academic_year_id')
                            ->label('Tahun Akademik')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYearContext::get()?->id)
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload(),

                        TextInput::make('event_name')
                            ->label('Nama Event')
                            ->required()
                            ->maxLength(150),

                        DatePicker::make('event_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->required()
                            ->live()
                            ->minDate(fn (Get $get) => static::academicYearFromForm($get)?->start_date)
                            ->maxDate(fn (Get $get) => static::academicYearFromForm($get)?->end_date),

                        DatePicker::make('event_end_date')
                            ->label('Tanggal Selesai (opsional)')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->live()
                            ->minDate(fn (Get $get) => $get('event_date') ?? static::academicYearFromForm($get)?->start_date)
                            ->maxDate(fn (Get $get) => static::academicYearFromForm($get)?->end_date)
                            ->helperText('Kosongkan jika event berlangsung satu hari.'),

                        Placeholder::make('semester_preview')
                            ->label('Semester (otomatis)')
                            ->content(function (Get $get): string {
                                $academicYear = static::academicYearFromForm($get);
                                $eventDate = $get('event_date');

                                if (! $academicYear || ! $eventDate) {
                                    return '—';
                                }

                                $semester = $academicYear->getSemester($eventDate);

                                return $semester ? $semester->label() : '—';
                            }),

                        Select::make('event_type')
                            ->label('Tipe Event')
                            ->options(collect(EventType::cases())->mapWithKeys(
                                fn (EventType $type) => [$type->value => $type->label()]
                            ))
                            ->required()
                            ->live(),

                        ColorPicker::make('color')
                            ->label('Warna (opsional)')
                            ->helperText('Kosongkan untuk memakai warna default sesuai tipe event.'),
                    ]),

                Section::make('Detail Libur')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->visible(fn (Get $get) => in_array($get('event_type'), [
                        EventType::HOLIDAY->value,
                        EventType::NATIONALDAY->value,
                    ]))
                    ->components([
                        Toggle::make('is_national_holiday')
                            ->label('Libur Nasional'),

                        Toggle::make('is_school_holiday')
                            ->label('Libur Sekolah'),
                    ]),

                Section::make('Catatan')
                    ->collapsible()
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

            ]);
    }

    protected static function academicYearFromForm(Get $get): ?AcademicYear
    {
        $id = $get('academic_year_id');

        return $id ? AcademicYear::query()->find($id) : null;
    }
}
