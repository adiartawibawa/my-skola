<?php

namespace App\Support;

use App\Enums\DayOfWeekEnum;
use App\Enums\RoleEnum;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

class PortalContext
{
    protected const SESSION_KEY = 'portal.active_child_id';

    /**
     * Siswa yang datanya sedang ditampilkan di portal — diri sendiri
     * untuk role Siswa, atau anak yang sedang dipilih untuk role
     * Orang Tua. Halaman Fase 3/4 (Jadwal, Kalender, Pengumuman, dst)
     * cukup panggil ini satu kali, tidak perlu tahu role user login.
     */
    public static function currentStudent(): ?Student
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        if ($user->role === RoleEnum::STUDENT) {
            return $user->student;
        }

        if ($user->role !== RoleEnum::PARENT) {
            return null;
        }

        $children = static::availableChildren();

        if ($children->isEmpty()) {
            return null;
        }

        $activeId = session(static::SESSION_KEY);
        $active = $activeId ? $children->firstWhere('id', $activeId) : null;

        if (! $active) {
            $active = $children->first();
            session([static::SESSION_KEY => $active->id]);
        }

        return $active;
    }

    public static function availableChildren(): Collection
    {
        $user = auth()->user();

        if (! $user || $user->role !== RoleEnum::PARENT) {
            return collect();
        }

        return $user->students()->with('user')->get();
    }

    public static function setActiveChild(string $studentId): void
    {
        if (! static::availableChildren()->contains('id', $studentId)) {
            abort(403);
        }

        session([static::SESSION_KEY => $studentId]);
    }

    /**
     * Ringkasan singkat SEMUA anak tertaut (bukan cuma yang aktif di
     * switcher) — dipakai khusus dashboard Orang Tua dengan anak lebih
     * dari satu. Sengaja dipisah dari currentStudent() karena tujuannya
     * beda: currentStudent() untuk "lihat detail satu anak", ini untuk
     * "bandingkan sekilas semua anak".
     */
    public static function childrenSummary(): Collection
    {
        return static::availableChildren()->map(function (Student $child) {
            $classRoom = $child->currentClassRoom();

            return [
                'student' => $child,
                'class_room' => $classRoom,
                'today_schedule_count' => $classRoom
                    ? Schedule::query()
                        ->where('class_room_id', $classRoom->id)
                        ->where('day_of_week', static::todayDayOfWeek()->value)
                        ->count()
                    : 0,
            ];
        });
    }

    protected static function todayDayOfWeek(): DayOfWeekEnum
    {
        $isoDay = now()->dayOfWeekIso;

        return collect(DayOfWeekEnum::cases())
            ->first(fn ($day) => $day->order() === $isoDay) ?? DayOfWeekEnum::SENIN;
    }

    /**
     * "Sudut pandang siapa" yang dipakai Announcement::scopeVisibleTo().
     * Siswa & Alumni: dirinya sendiri. Orang Tua: anak yang sedang aktif
     * di switcher. Staf: null (portal pengumuman bukan untuk mereka —
     * mereka sudah punya akses penuh lewat panel admin).
     */
    public static function targetUserForVisibility(): ?User
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return match ($user->role) {
            RoleEnum::STUDENT, RoleEnum::ALUMNI => $user,
            RoleEnum::PARENT => static::currentStudent()?->user,
            default => null,
        };
    }
}
