<?php

namespace App\Support;

use App\Models\AcademicYear;

/**
 * Memisahkan dua konsep yang sering tertukar:
 *
 * - "Tahun aktif" (AcademicYear::resolveDefault()) — state bisnis,
 *   satu-satunya, dipakai sebagai default untuk entry data baru.
 * - "Tahun sedang dilihat" (context ini) — state UI per-sesi, admin
 *   bisa switch ke Tahun Akademik lama untuk melihat data historis
 *   tanpa mengubah status aktif yang sesungguhnya.
 */
class AcademicYearContext
{
    protected const SESSION_KEY = 'viewing_academic_year_id';

    /**
     * Tahun Akademik yang sedang dilihat admin di UI. Fallback ke
     * resolveDefault() (aktif → current-by-date) kalau belum ada
     * pilihan eksplisit di sesi, atau kalau pilihan tersimpan sudah
     * tidak valid/terhapus.
     */
    public static function get(): ?AcademicYear
    {
        $id = session(self::SESSION_KEY);

        if ($id) {
            $academicYear = AcademicYear::query()->find($id);

            if ($academicYear) {
                return $academicYear;
            }
        }

        return AcademicYear::resolveDefault();
    }

    public static function set(string $academicYearId): void
    {
        session([self::SESSION_KEY => $academicYearId]);
    }

    /**
     * Kembali ke default (aktif), menghapus pilihan histori dari sesi.
     */
    public static function reset(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function isViewingHistorical(): bool
    {
        $viewing = static::get();
        $default = AcademicYear::resolveDefault();

        if (! $viewing || ! $default) {
            return false;
        }

        return ! $viewing->is($default);
    }
}
