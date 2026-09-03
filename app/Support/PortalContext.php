<?php

namespace App\Support;

use App\Enums\RoleEnum;
use App\Models\Student;
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
}
