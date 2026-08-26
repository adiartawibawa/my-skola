<?php

namespace App\Filament\Imports;

use App\Models\ClassRoom;
use Filament\Actions\Imports\ImportColumn;

/**
 * Sama seperti ClassRoomStudentImporter — extends UserImporter untuk
 * logika akun, tambahan hanya membuat entri ClassRoomTeacher.
 *
 * Beda dari Student: file di sini SENGAJA mendukung banyak baris per
 * kelas, karena tabel Riwayat Wali Kelas memang histori berperiode
 * (started_at/ended_at) — berguna untuk migrasi data historis
 * sekaligus (mis. "Guru A menjabat 2023/2024, Guru B 2024/2025"),
 * bukan cuma menugaskan satu wali kelas aktif.
 *
 * ClassRoomTeacher::closeConflictingActiveAssignment() di model tetap
 * berjalan otomatis untuk tiap baris yang ended_at-nya kosong — kalau
 * file berisi beberapa baris tanpa ended_at, hanya baris TERAKHIR yang
 * diproses yang akan tersisa aktif, baris sebelumnya otomatis tertutup.
 * Ini sesuai desain, bukan bug — susun urutan baris di file dari yang
 * paling lama ke paling baru.
 */
class ClassRoomTeacherImporter extends UserImporter
{
    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    public static function getColumns(): array
    {
        return [
            ...parent::getColumns(),

            ImportColumn::make('nip')
                ->rules(['nullable', 'string', 'max:30']),

            ImportColumn::make('nuptk')
                ->rules(['nullable', 'string', 'max:30']),

            ImportColumn::make('nik')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:16']),

            ImportColumn::make('status_kepegawaian')
                ->rules(['nullable', 'string']),

            ImportColumn::make('bidang_studi')
                ->rules(['nullable', 'string', 'max:100']),

            ImportColumn::make('golongan')
                ->rules(['nullable', 'string']),

            ImportColumn::make('tanggal_masuk')
                ->rules(['nullable', 'date']),

            ImportColumn::make('pendidikan_terakhir')
                ->rules(['nullable', 'string']),

            ImportColumn::make('started_at')
                ->label('Mulai Menjabat')
                ->requiredMapping()
                ->rules(['required', 'date']),

            ImportColumn::make('ended_at')
                ->label('Selesai Menjabat')
                ->rules(['nullable', 'date']),

            ImportColumn::make('reason')
                ->label('Alasan')
                ->rules(['nullable', 'string', 'max:255']),
        ];
    }

    protected function afterCreate(): void
    {
        parent::afterCreate();

        $this->assignAsHomeroomTeacher();
    }

    protected function afterUpdate(): void
    {
        parent::afterUpdate();

        $this->assignAsHomeroomTeacher();
    }

    /**
     * Validasi periode (dalam rentang Tahun Akademik, ended_at tidak
     * boleh sebelum started_at) TIDAK ditulis ulang di sini — sudah
     * ditangani ClassRoomTeacher::booted() seperti biasa.
     */
    protected function assignAsHomeroomTeacher(): void
    {
        $classRoomId = $this->options['class_room_id'] ?? null;

        if (! $classRoomId) {
            return;
        }

        $classRoom = ClassRoom::query()->withoutGlobalScopes()->find($classRoomId);
        $teacher = $this->record->teacher;

        if (! $classRoom || ! $teacher) {
            return;
        }

        $classRoom->classRoomTeachers()->create([
            'teacher_id' => $teacher->id,
            'started_at' => $this->data['started_at'],
            'ended_at' => $this->data['ended_at'] ?? null,
            'reason' => $this->data['reason'] ?? null,
        ]);
    }
}
