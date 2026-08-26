<?php

namespace App\Filament\Imports;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\ClassRoom;

/**
 * Extends UserImporter — semua logika akun (resolveRecord, auto-
 * password, upsert profil Student lewat createOrUpdateSiswa) dipakai
 * apa adanya dari parent, TIDAK diduplikasi di sini.
 *
 * Tambahan satu-satunya: setelah User+Student ter-upsert, daftarkan
 * siswa itu ke ClassRoom yang sedang dibuka (class_room_id dikirim
 * lewat ImportAction::options() dari RelationManager, BUKAN dari
 * kolom file — kelasnya sudah pasti dari konteks halaman).
 */
class ClassRoomStudentImporter extends UserImporter
{
    /**
     * Tidak ada form opsi sama sekali — role SELALU Siswa dan
     * class_room_id SELALU dari kelas yang sedang dibuka, keduanya
     * dikunci lewat ImportAction::options(), bukan dipilih user.
     */
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

        $this->enrollToClassRoom();
    }

    protected function afterUpdate(): void
    {
        parent::afterUpdate();

        $this->enrollToClassRoom();
    }

    /**
     * Kapasitas kelas, unique constraint (student_id, academic_year_id),
     * dan sinkronisasi academic_year_id TIDAK ditulis ulang di sini —
     * semua sudah ditangani ClassRoomStudent::booted() seperti biasa.
     * Importer ini cuma memanggil create(), persis seperti input
     * manual lewat form RelationManager.
     */
    protected function enrollToClassRoom(): void
    {
        $classRoomId = $this->options['class_room_id'] ?? null;

        if (! $classRoomId) {
            return;
        }

        $classRoom = ClassRoom::query()->withoutGlobalScopes()->find($classRoomId);
        $student = $this->record->student;

        if (! $classRoom || ! $student) {
            return;
        }

        // Lewati kalau siswa ini sudah terdaftar di kelas MANAPUN pada
        // tahun ajaran yang sama — bukan error, cuma tidak perlu
        // didaftarkan ulang (mis. re-import file yang sama).
        $alreadyEnrolled = $student->classRoomEnrollments()
            ->withoutGlobalScopes()
            ->where('academic_year_id', $classRoom->academic_year_id)
            ->exists();

        if ($alreadyEnrolled) {
            return;
        }

        $classRoom->classRoomStudents()->create([
            'student_id' => $student->id,
            'status' => ClassRoomStudentStatusEnum::AKTIF->value,
        ]);
    }
}
