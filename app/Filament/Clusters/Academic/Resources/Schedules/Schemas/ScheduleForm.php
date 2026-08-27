<?php

namespace App\Filament\Clusters\Academic\Resources\Schedules\Schemas;

use App\Enums\DayOfWeekEnum;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('class_room_id')
                            ->label('Kelas')
                            ->options(
                                fn () => ClassRoom::query()
                                    ->with('programKeahlian')
                                    ->get()
                                    ->mapWithKeys(fn (ClassRoom $classRoom) => [
                                        $classRoom->id => $classRoom->full_name,
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            // Reset mapel yang sudah dipilih kalau kelas
                            // diganti — supaya tidak ada mapel kejuruan
                            // "nyangkut" dari program keahlian lain.
                            ->afterStateUpdated(fn ($set) => $set('subject_id', null)),

                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->options(function (Get $get): array {
                                $classRoom = ClassRoom::query()
                                    ->withoutGlobalScopes()
                                    ->find($get('class_room_id'));

                                return Subject::query()
                                    ->active()
                                    ->where(function ($query) use ($classRoom) {
                                        $query->whereNull('program_keahlian_id');

                                        if ($classRoom) {
                                            $query->orWhere('program_keahlian_id', $classRoom->program_keahlian_id);
                                        }
                                    })
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Hanya menampilkan mapel umum + mapel kejuruan sesuai program keahlian kelas yang dipilih.'),

                        Select::make('teacher_id')
                            ->label('Guru Pengajar')
                            ->options(
                                fn () => Teacher::query()
                                    ->with('user')
                                    ->get()
                                    ->mapWithKeys(fn ($teacher) => [
                                        $teacher->id => "{$teacher->user?->name} ({$teacher->nip})",
                                    ])
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('day_of_week')
                            ->label('Hari')
                            ->options(DayOfWeekEnum::options())
                            ->native(false)
                            ->required(),

                        TimePicker::make('start_time')
                            ->label('Jam Mulai')
                            ->seconds(false)
                            ->native(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Jam Selesai')
                            ->seconds(false)
                            ->native(false)
                            ->required(),
                    ]),

            ]);
    }
}
