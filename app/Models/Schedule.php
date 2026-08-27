<?php

namespace App\Models;

use App\Enums\DayOfWeekEnum;
use App\Models\Concerns\ScopedToActiveAcademicYearViaClassRoom;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[Fillable(
    'class_room_id',
    'subject_id',
    'teacher_id',
    'day_of_week',
    'start_time',
    'end_time',
)]
class Schedule extends Model
{
    use HasFactory;
    use HasUuids;

    // Terikat Tahun Akademik lewat classRoom-nya — pola sama seperti
    // ClassRoomTeacher, karena Schedule tidak punya academic_year_id
    // langsung.
    use ScopedToActiveAcademicYearViaClassRoom;

    protected function casts(): array
    {
        return [
            'day_of_week' => DayOfWeekEnum::class,
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    /**
     * Relations
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Boot method — validasi urutan jam, kecocokan mapel-program
     * keahlian, dan dua jenis konflik (kelas & guru).
     */
    protected static function booted(): void
    {
        static::saving(function (Schedule $schedule): void {
            static::validateTimeOrder($schedule);

            $classRoom = static::resolveClassRoom($schedule);

            static::validateSubjectMatchesProgram($schedule, $classRoom);
            static::validateNoClassConflict($schedule);
            static::validateNoTeacherConflict($schedule, $classRoom);
        });
    }

    protected static function validateTimeOrder(Schedule $schedule): void
    {
        if (! $schedule->start_time || ! $schedule->end_time) {
            throw ValidationException::withMessages([
                'start_time' => 'Jam mulai dan jam selesai wajib diisi.',
            ]);
        }

        if ($schedule->end_time->lte($schedule->start_time)) {
            throw ValidationException::withMessages([
                'end_time' => 'Jam selesai harus setelah jam mulai.',
            ]);
        }
    }

    /**
     * Resolusi ClassRoom TIDAK boleh terikat scope tahun aktif — sama
     * alasannya seperti ClassRoomStudent/ClassRoomTeacher.
     */
    protected static function resolveClassRoom(Schedule $schedule): ClassRoom
    {
        $classRoom = $schedule->relationLoaded('classRoom')
            ? $schedule->getRelation('classRoom')
            : null;

        if (! $classRoom && $schedule->class_room_id) {
            $classRoom = ClassRoom::query()
                ->withoutGlobalScopes()
                ->find($schedule->class_room_id);
        }

        if (! $classRoom) {
            throw ValidationException::withMessages([
                'class_room_id' => 'Jadwal harus terikat pada Kelas.',
            ]);
        }

        return $classRoom;
    }

    /**
     * Mapel kejuruan (program_keahlian_id terisi) hanya boleh
     * dijadwalkan di kelas dengan program keahlian yang sama. Mapel
     * umum (null) boleh di kelas manapun.
     */
    protected static function validateSubjectMatchesProgram(Schedule $schedule, ClassRoom $classRoom): void
    {
        $subject = Subject::query()->find($schedule->subject_id);

        if (! $subject || ! $subject->program_keahlian_id) {
            return;
        }

        if ($subject->program_keahlian_id !== $classRoom->program_keahlian_id) {
            throw ValidationException::withMessages([
                'subject_id' => 'Mata pelajaran ini khusus program keahlian lain, tidak bisa dijadwalkan di kelas ini.',
            ]);
        }
    }

    /**
     * Satu kelas tidak boleh punya 2 mapel di jam yang tumpang tindih,
     * pada hari yang sama, di Tahun Akademik yang sama.
     */
    protected static function validateNoClassConflict(Schedule $schedule): void
    {
        $query = static::overlapQuery($schedule)
            ->where('class_room_id', $schedule->class_room_id);

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'start_time' => 'Kelas ini sudah punya jadwal lain yang bentrok pada waktu tersebut.',
            ]);
        }
    }

    /**
     * Satu guru tidak boleh mengajar 2 kelas di jam yang tumpang
     * tindih, pada hari yang sama, di Tahun Akademik yang sama —
     * dibatasi ke Tahun Akademik yang sama dengan $classRoom karena
     * jadwal hari/jam yang sama di tahun ajaran BERBEDA tidak benar-
     * benar bentrok (minggu kalender yang berbeda).
     */
    protected static function validateNoTeacherConflict(Schedule $schedule, ClassRoom $classRoom): void
    {
        $query = static::overlapQuery($schedule)
            ->where('teacher_id', $schedule->teacher_id)
            ->whereHas(
                'classRoom',
                fn ($q) => $q->withoutGlobalScopes()->where('academic_year_id', $classRoom->academic_year_id),
            );

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Guru ini sudah mengajar kelas lain pada waktu yang bentrok.',
            ]);
        }
    }

    /**
     * Query dasar untuk deteksi tumpang-tindih jam pada hari yang sama,
     * dipakai bersama oleh validateNoClassConflict() dan
     * validateNoTeacherConflict(). withoutGlobalScopes() wajib —
     * pengecekan konflik harus melihat SEMUA jadwal terlepas tahun
     * mana yang sedang aktif.
     */
    protected static function overlapQuery(Schedule $schedule)
    {
        $query = static::query()
            ->withoutGlobalScopes()
            ->where('day_of_week', $schedule->day_of_week instanceof DayOfWeekEnum
                ? $schedule->day_of_week->value
                : $schedule->day_of_week)
            ->where('start_time', '<', $schedule->end_time)
            ->where('end_time', '>', $schedule->start_time);

        if ($schedule->exists) {
            $query->whereKeyNot($schedule->getKey());
        }

        return $query;
    }
}
