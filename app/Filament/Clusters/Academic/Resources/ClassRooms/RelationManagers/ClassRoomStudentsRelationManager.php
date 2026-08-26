<?php

namespace App\Filament\Clusters\Academic\Resources\ClassRooms\RelationManagers;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Concerns\GeneratesImportCsvTemplate;
use App\Filament\Imports\ClassRoomStudentImporter;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClassRoomStudentsRelationManager extends RelationManager
{
    use GeneratesImportCsvTemplate;

    protected static string $relationship = 'classRoomStudents';

    protected static ?string $title = 'Daftar Siswa';

    protected static ?string $modelLabel = 'Keanggotaan Siswa';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Siswa')
                    ->options(function (RelationManager $livewire, ?Model $record): array {
                        $academicYearId = $livewire->getOwnerRecord()->academic_year_id;

                        // Hanya tampilkan siswa yang belum punya kelas di
                        // Tahun Akademik ini (aturan: 1 siswa 1 kelas per
                        // tahun ajaran).
                        return Student::query()
                            ->with('user')
                            ->where(function ($query) use ($academicYearId, $record) {
                                $query->whereDoesntHave(
                                    'classRoomEnrollments',
                                    fn ($q) => $q->where('academic_year_id', $academicYearId),
                                );

                                // Re-include the current student when editing an existing record
                                if ($record?->student_id) {
                                    $query->orWhere('id', $record->student_id);
                                }
                            })
                            ->get()
                            ->mapWithKeys(fn (Student $student) => [$student->id => "{$student->user?->name} ({$student->nis})"])
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    // Siswa tidak boleh diganti lewat form edit — kalau
                    // salah kelas, hapus baris ini dan buat baru, supaya
                    // periode joined_at/left_at tetap konsisten.
                    ->disabled(fn (string $operation): bool => $operation === Operation::Edit->value)
                    ->dehydrated(),

                DatePicker::make('joined_at')
                    ->label('Tanggal Bergabung')
                    ->native(false)
                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->academicYear?->start_date)
                    ->required(),

                DatePicker::make('left_at')
                    ->label('Tanggal Keluar')
                    ->native(false)
                    ->helperText('Wajib diisi jika status selain Aktif.'),

                Select::make('status')
                    ->label('Status')
                    ->options(collect(ClassRoomStudentStatusEnum::cases())
                        ->mapWithKeys(fn (ClassRoomStudentStatusEnum $status) => [
                            $status->value => $status->label(),
                        ]))
                    ->default(ClassRoomStudentStatusEnum::AKTIF->value)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student_id')
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Nama Siswa')
                    ->description(fn ($record) => $record->student?->nis)
                    ->searchable(),

                TextColumn::make('joined_at')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('left_at')
                    ->label('Keluar')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (ClassRoomStudentStatusEnum $state) => $state->label())
                    ->color(fn (ClassRoomStudentStatusEnum $state) => $state->color()),

            ])
            ->defaultSort('joined_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Tambah Siswa ke Kelas'),

                ImportAction::make()
                    ->label('Import Siswa')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->maxRows(500)
                    ->chunkSize(100)
                    ->modalHeading('Import Siswa ke Kelas Ini')
                    ->modalDescription('Setiap baris akan membuat akun User + profil Siswa (jika belum ada) dan langsung mendaftarkannya ke kelas ini.')
                    ->importer(ClassRoomStudentImporter::class)
                    ->options(fn () => [
                        'role' => RoleEnum::STUDENT,
                        'class_room_id' => $this->getOwnerRecord()->getKey(),
                    ]),

                Action::make('downloadStudentImportTemplate')
                    ->label('Template Import')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(fn () => $this->downloadStudentTemplate()),

            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(fn ($record) => "Edit Keanggotaan: {$record->student?->user?->name}"),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Format kolom SENGAJA tidak punya 'role' atau 'kelas' — keduanya
     * sudah pasti dari konteks halaman (RelationManager ini cuma
     * dipasang untuk Siswa, di kelas yang sedang dibuka), bukan dari
     * isi file. Urutan kolom harus sama persis dengan
     * ClassRoomStudentImporter::getColumns().
     */
    protected function downloadStudentTemplate(): StreamedResponse
    {
        $headers = [
            'username', 'name', 'email', 'password',
            'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir',
            'nama_ayah', 'nama_ibu', 'pekerjaan_orang_tua',
            'alamat_orang_tua', 'no_telp_orang_tua',
        ];

        $example = [
            '',                              // username (opsional, fallback ke email)
            'I Putu Gede Arnold Saputra',
            'arnold.siswa@mail.com',
            '',                              // password (kosong = auto-generate)
            '2026001',                       // nis
            '1234567890',                    // nisn
            'Jakarta',
            '2010-05-15',
            'I Wayan Sucipta',
            'Ni Luh Astuti',
            'Wiraswasta',
            'Jl. Merdeka No. 123, Singasana',
            '081234567890',
        ];

        $instructions = [
            'Username boleh dikosongkan — sistem akan mencocokkan berdasarkan email sebagai gantinya',
            'NIS dan NISN harus unik dan wajib diisi',
            'Siswa yang sudah terdaftar di kelas manapun pada Tahun Akademik ini akan dilewati otomatis, bukan error',
        ];

        $classRoomName = $this->getOwnerRecord()->full_name ?? 'kelas';
        $timestamp = now()->format('Ymd_His');

        return $this->streamCsvTemplate(
            $headers,
            $example,
            "template_import_siswa_{$classRoomName}_{$timestamp}.csv",
            $instructions,
        );
    }
}
