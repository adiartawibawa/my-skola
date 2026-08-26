<?php

namespace App\Filament\Imports;

use App\Models\ClassRoom;

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
