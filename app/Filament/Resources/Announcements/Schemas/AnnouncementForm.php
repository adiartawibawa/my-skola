<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Enums\RoleEnum;
use App\Models\ClassRoom;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengumuman')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('body')
                            ->label('Isi Pengumuman')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Target')
                    ->collapsible()
                    ->schema([
                        Toggle::make('is_for_all')
                            ->label('Untuk Semua Pengguna')
                            ->live()
                            ->default(false),

                        // target_roles adalah field VIRTUAL — bukan
                        // kolom/relasi langsung di model Announcement.
                        // Disimpan lewat AnnouncementRole oleh
                        // CreateAnnouncement/EditAnnouncement, bukan
                        // otomatis oleh Filament (beda dari classRooms/
                        // programKeahlians di bawah yang memang
                        // BelongsToMany asli).
                        CheckboxList::make('target_roles')
                            ->label('Role')
                            ->options(RoleEnum::options())
                            ->columns(2)
                            ->visible(fn (Get $get) => ! $get('is_for_all'))
                            ->dehydrated(fn (Get $get) => ! $get('is_for_all')),

                        Grid::make(2)
                            ->schema([
                                // relationship() query ClassRoom otomatis
                                // ter-scope tahun aktif (ActiveAcademicYearScope)
                                // — cukup untuk kasus normal (target kelas
                                // tahun berjalan). Kalau nanti perlu target
                                // kelas historis, opsi ini butuh disesuaikan.
                                Select::make('classRooms')
                                    ->label('Kelas')
                                    ->relationship('classRooms', 'rombel_label')
                                    ->getOptionLabelFromRecordUsing(fn (ClassRoom $record) => $record->full_name)
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),

                                Select::make('programKeahlians')
                                    ->label('Program Keahlian')
                                    ->relationship('programKeahlians', 'name', fn ($query) => $query->active())
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->visible(fn (Get $get) => ! $get('is_for_all')),
                    ]),

                Section::make('Option')
                    ->collapsible()
                    ->schema([
                        Toggle::make('is_pinned')
                            ->label('Sematkan (Pin)')
                            ->default(false),

                        DateTimePicker::make('publish_at')
                            ->label('Jadwal Tayang')
                            ->native(false)
                            ->helperText('Kosongkan untuk langsung tayang.'),

                        DateTimePicker::make('expires_at')
                            ->label('Kedaluwarsa')
                            ->native(false)
                            ->helperText('Kosongkan supaya tidak pernah kedaluwarsa.')
                            ->after('publish_at'),
                    ]),

            ]);
    }
}
