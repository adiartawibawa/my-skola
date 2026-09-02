<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[Fillable(
    'program_keahlian_id',
    'teacher_id',
    'started_at',
    'ended_at',
    'reason',
)]
class ProgramKeahlianHead extends Model
{
    use HasFactory;
    use HasUuids;

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
    public function programKeahlian(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class);
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
     * Boot method — validasi urutan tanggal, auto-tutup penugasan lama.
     * Sengaja TIDAK ada validasi "dalam periode Tahun Akademik" seperti
     * ClassRoomTeacher — jabatan kaprodi bersifat lintas tahun ajaran
     * (bisa menjabat beberapa tahun), tidak terikat satu AcademicYear.
     */
    protected static function booted(): void
    {
        static::saving(function (ProgramKeahlianHead $head): void {
            static::validateDateOrder($head);
            static::closeConflictingActiveAssignment($head);
        });
    }

    protected static function validateDateOrder(ProgramKeahlianHead $head): void
    {
        if (! $head->started_at) {
            throw ValidationException::withMessages([
                'started_at' => 'Tanggal mulai menjabat wajib diisi.',
            ]);
        }

        if ($head->ended_at && $head->ended_at->lt($head->started_at)) {
            throw ValidationException::withMessages([
                'ended_at' => 'Tanggal selesai menjabat tidak boleh sebelum tanggal mulai.',
            ]);
        }
    }

    /**
     * Sama persis pola ClassRoomTeacher::closeConflictingActiveAssignment()
     * — kalau penugasan baru ini aktif (ended_at null), otomatis tutup
     * penugasan aktif sebelumnya di program keahlian yang sama.
     */
    protected static function closeConflictingActiveAssignment(ProgramKeahlianHead $head): void
    {
        if ($head->ended_at !== null) {
            return;
        }

        $query = static::query()
            ->where('program_keahlian_id', $head->program_keahlian_id)
            ->whereNull('ended_at');

        if ($head->exists) {
            $query->whereKeyNot($head->getKey());
        }

        $query->update([
            'ended_at' => $head->started_at->copy()->subDay(),
        ]);
    }
}
