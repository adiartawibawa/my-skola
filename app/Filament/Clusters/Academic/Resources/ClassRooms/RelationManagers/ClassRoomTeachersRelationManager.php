<?php

namespace App\Filament\Clusters\Academic\Resources\ClassRooms\RelationManagers;

use App\Enums\RoleEnum;
use App\Filament\Concerns\GeneratesImportCsvTemplate;
use App\Filament\Imports\ClassRoomTeacherImporter;
use App\Models\Teacher;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClassRoomTeachersRelationManager extends RelationManager
{
    use GeneratesImportCsvTemplate;

    protected static string $relationship = 'classRoomTeachers';

    protected static ?string $title = 'Riwayat Wali Kelas';

    protected static ?string $modelLabel = 'Penugasan Wali Kelas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')
                    ->label('Guru')
                    ->options(
                        fn () => Teacher::query()
                            ->with('user')
                            ->get()
                            ->mapWithKeys(fn (Teacher $teacher) => [
                                $teacher->id => "{$teacher->user?->name} ({$teacher->nip})",
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('started_at')
                    ->label('Mulai Menjabat')
                    ->native(false)
                    ->required(),

                DatePicker::make('ended_at')
                    ->label('Selesai Menjabat')
                    ->native(false)
                    ->helperText('Kosongkan jika masih menjabat sebagai wali kelas.'),

                Textarea::make('reason')
                    ->label('Alasan Pergantian')
                    ->columnSpanFull(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('teacher_id')
            ->columns([
                TextColumn::make('teacher.user.name')
                    ->label('Guru')
                    ->description(fn ($record) => $record->teacher?->nip)
                    ->searchable(),

                TextColumn::make('started_at')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('ended_at')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('Masih menjabat')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('started_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),

                ImportAction::make()
                    ->label('Import Riwayat Wali Kelas')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->maxRows(100)
                    ->chunkSize(50)
                    ->modalHeading('Import Riwayat Wali Kelas')
                    ->modalDescription('Setiap baris membuat akun User + profil Guru (jika belum ada) dan satu entri penugasan wali kelas. Urutkan baris dari yang paling lama ke paling baru kalau mengisi beberapa periode sekaligus.')
                    ->importer(ClassRoomTeacherImporter::class)
                    ->options(fn () => [
                        'role' => RoleEnum::TEACHER,
                        'class_room_id' => $this->getOwnerRecord()->getKey(),
                    ]),

                Action::make('downloadTeacherImportTemplate')
                    ->label('Template Import')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(fn () => $this->downloadTeacherTemplate()),

            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Urutan kolom harus sama persis dengan
     * ClassRoomTeacherImporter::getColumns(). started_at/ended_at/
     * reason bukan kolom Teacher — itu kolom periode penugasan
     * ClassRoomTeacher yang dibuat per baris.
     */
    protected function downloadTeacherTemplate(): StreamedResponse
    {
        $headers = [
            'username', 'name', 'email', 'password',
            'nip', 'nuptk', 'nik', 'status_kepegawaian',
            'bidang_studi', 'golongan', 'tanggal_masuk', 'pendidikan_terakhir',
            'started_at', 'ended_at', 'reason',
        ];

        $example = [
            '',
            'I Nyoman Putra Prandana, S.E.',
            'prandana.guru@example.com',
            '',
            '',                              // nip (opsional)
            '1234567890123456',              // nuptk
            '3201234567890001',              // nik
            'PNS',
            'Ekonomi Akuntansi',
            'III/a',
            '2023-07-01',
            'S1',
            '2026-07-01',                    // started_at
            '',                              // ended_at (kosong = masih menjabat)
            '',                              // reason
        ];

        $instructions = [
            'Username boleh dikosongkan — sistem akan mencocokkan berdasarkan email sebagai gantinya',
            'NIK wajib diisi dan unik',
            'Status kepegawaian yang valid: PNS, PPPK, Honorer, Kontrak',
            'started_at wajib diisi; ended_at kosongkan jika baris ini adalah wali kelas yang masih menjabat',
            'Kalau ada beberapa baris tanpa ended_at, hanya baris TERAKHIR yang tersisa aktif — baris sebelumnya otomatis ditutup sistem',
        ];

        $classRoomName = $this->getOwnerRecord()->full_name ?? 'kelas';
        $timestamp = now()->format('Ymd_His');

        return $this->streamCsvTemplate(
            $headers,
            $example,
            "template_import_wali_kelas_{$classRoomName}_{$timestamp}.csv",
            $instructions,
        );
    }
}
