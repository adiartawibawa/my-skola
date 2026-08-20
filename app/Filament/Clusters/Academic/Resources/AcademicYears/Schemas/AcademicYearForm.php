<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Periode Tahun Akademik')
                    ->description('Tahun Akademik selalu berlangsung 1 Juli – 30 Juni tahun berikutnya, mengikuti aturan akademik Indonesia.')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        Select::make('start_year')
                            ->label('Tahun Ajaran')
                            ->options(static::startYearOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, $set, ?string $state): void {
                                if (! $state) {
                                    return;
                                }

                                $label = static::yearLabel((int) $state);

                                // Hanya isi otomatis kalau admin belum mengetik manual,
                                // supaya tidak menimpa penamaan kustom saat edit.
                                if (blank($get('code'))) {
                                    $set('code', $label);
                                }

                                if (blank($get('name'))) {
                                    $set('name', "Tahun Akademik {$label}");
                                }
                            })
                            ->helperText('Tahun Akademik akan berlangsung 1 Juli – 30 Juni tahun berikutnya.'),

                        Placeholder::make('computed_end_date')
                            ->label('Berakhir Pada')
                            ->content(function (Get $get): string {
                                $startYear = $get('start_year');

                                if (! $startYear) {
                                    return '—';
                                }

                                return Carbon::create((int) $startYear + 1, 6, 30)->translatedFormat('d F Y');
                            }),

                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(50),

                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(150),

                        Toggle::make('is_active')
                            ->label('Jadikan Tahun Akademik Aktif')
                            ->helperText('Mengaktifkan tahun ini akan otomatis menonaktifkan Tahun Akademik lain.')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make('Pertengahan Semester')
                    ->description('Opsional — acuan tanggal UTS/pertengahan semester. Tidak menentukan batas semester (batas semester tetap: 1 Jul–31 Des = Ganjil, 1 Jan–30 Jun = Genap).')
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull()
                    ->components([
                        DatePicker::make('mid_semester_ganjil_date')
                            ->label('Pertengahan Semester Ganjil')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->minDate(fn (Get $get) => $get('start_year')
                                ? Carbon::create((int) $get('start_year'), 7, 1)
                                : null)
                            ->maxDate(fn (Get $get) => $get('start_year')
                                ? Carbon::create((int) $get('start_year'), 12, 31)
                                : null),

                        DatePicker::make('mid_semester_genap_date')
                            ->label('Pertengahan Semester Genap')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->minDate(fn (Get $get) => $get('start_year')
                                ? Carbon::create((int) $get('start_year') + 1, 1, 1)
                                : null)
                            ->maxDate(fn (Get $get) => $get('start_year')
                                ? Carbon::create((int) $get('start_year') + 1, 6, 30)
                                : null),
                    ]),

                Section::make('Catatan')
                    ->collapsed()
                    ->columnSpanFull()
                    ->components([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

            ]);
    }

    /**
     * Rentang tahun mulai yang ditawarkan di Select. Sesuaikan batas
     * bawah/atas sesuai kebutuhan histori & perencanaan ke depan.
     */
    protected static function startYearOptions(): array
    {
        $currentYear = now()->year;

        $options = [];

        for ($year = $currentYear - 5; $year <= $currentYear + 3; $year++) {
            $options[$year] = static::yearLabel($year);
        }

        return $options;
    }

    public static function yearLabel(int $startYear): string
    {
        return "{$startYear}/".($startYear + 1);
    }
}
