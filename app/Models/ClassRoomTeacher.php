<?php

namespace App\Models;

use App\Models\Concerns\ScopedToActiveAcademicYearViaClassRoom;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[Fillable(
    'class_room_id',
    'teacher_id',
    'started_at',
    'ended_at',
    'reason',
)]
class ClassRoomTeacher extends Model
{
    use HasFactory;
    use HasUuids;
    use ScopedToActiveAcademicYearViaClassRoom;

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    /**
     * Relations
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Boot method untuk validasi periode dan auto-close assignment lama
     */
    protected static function booted(): void
    {
        static::saving(function (ClassRoomTeacher $assignment): void {
            static::validateDateOrder($assignment);

            $classRoom = static::resolveClassRoom($assignment);

            static::validateWithinAcademicYear($assignment, $classRoom);
            static::closeConflictingActiveAssignment($assignment);
        });
    }

    protected static function validateDateOrder(ClassRoomTeacher $assignment): void
    {
        if (! $assignment->started_at) {
            throw ValidationException::withMessages([
                'started_at' => 'Tanggal mulai menjabat wajib diisi.',
            ]);
        }

        if ($assignment->ended_at && $assignment->ended_at->lt($assignment->started_at)) {
            throw ValidationException::withMessages([
                'ended_at' => 'Tanggal selesai menjabat tidak boleh sebelum tanggal mulai.',
            ]);
        }
    }

    /**
     * Resolusi internal ini TIDAK boleh terikat scope tahun aktif, atau
     * penugasan wali kelas untuk kelas di tahun yang sedang tidak aktif
     * (mis. saat mengisi data historis) tidak akan pernah ketemu
     * kelasnya sendiri.
     */
    protected static function resolveClassRoom(ClassRoomTeacher $assignment): ClassRoom
    {
        $classRoom = $assignment->relationLoaded('classRoom')
            ? $assignment->getRelation('classRoom')
            : null;

        if (! $classRoom && $assignment->class_room_id) {
            $classRoom = ClassRoom::query()
                ->withoutGlobalScopes()
                ->find($assignment->class_room_id);
        }

        if (! $classRoom) {
            throw ValidationException::withMessages([
                'class_room_id' => 'Penugasan wali kelas harus terikat pada Kelas.',
            ]);
        }

        return $classRoom;
    }

    protected static function validateWithinAcademicYear(ClassRoomTeacher $assignment, ClassRoom $classRoom): void
    {
        $academicYear = $classRoom->academicYear;

        if (! $academicYear) {
            return;
        }

        $end = $assignment->ended_at ?? $assignment->started_at;

        if ($assignment->started_at->lt($academicYear->start_date) || $end->gt($academicYear->end_date)) {
            throw ValidationException::withMessages([
                'started_at' => "Periode penugasan harus berada dalam Tahun Akademik ({$academicYear->start_date->format('d M Y')} – {$academicYear->end_date->format('d M Y')}).",
            ]);
        }
    }

    /**
     * Kalau assignment baru ini aktif (ended_at null), otomatis tutup
     * assignment aktif sebelumnya di kelas yang sama — supaya tidak ada
     * 2 wali kelas aktif sekaligus di 1 kelas, dan admin tidak perlu
     * ingat menutup histori lama secara manual. Pola sama seperti
     * AcademicYear::enforceSingleActive().
     */
    protected static function closeConflictingActiveAssignment(ClassRoomTeacher $assignment): void
    {
        if ($assignment->ended_at !== null) {
            return;
        }

        $query = static::query()
            ->withoutGlobalScopes()
            ->where('class_room_id', $assignment->class_room_id)
            ->whereNull('ended_at');

        if ($assignment->exists) {
            $query->whereKeyNot($assignment->getKey());
        }

        $query->update([
            'ended_at' => $assignment->started_at->copy()->subDay(),
        ]);

    }
}
