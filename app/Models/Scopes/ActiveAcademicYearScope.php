<?php

namespace App\Models\Scopes;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope yang menjadikan Tahun Akademik AKTIF (AcademicYear::is_active)
 * sebagai ruang lingkup data default di SELURUH aplikasi — bukan sekadar
 * filter UI yang bisa diabaikan di satu halaman.
 *
 * Kebenaran ada di SATU tempat: kolom `is_active` pada AcademicYear
 * (dijaga selalu tunggal oleh AcademicYear::enforceSingleActive()).
 * Model-model yang datanya terikat langsung ke satu Tahun Akademik
 * (kolom academic_year_id) memakai scope ini lewat trait
 * ScopedToActiveAcademicYear — sehingga query di Resource table,
 * RelationManager, Action, maupun Job otomatis hanya melihat data tahun
 * aktif, tanpa perlu diatur ulang berulang-ulang di tiap tempat.
 *
 * Operasi yang MEMANG harus lintas Tahun Akademik (mis. proses kenaikan
 * kelas yang menyentuh tahun aktif DAN tahun berikutnya sekaligus) wajib
 * membuka scope ini secara eksplisit lewat ::withoutAcademicYearScope() —
 * default yang diam-diam membatasi tidak boleh mendiamkan operasi yang
 * justru butuh melihat lintas tahun.
 */
class ActiveAcademicYearScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $activeAcademicYearId = AcademicYear::resolveDefault()?->id;

        if ($activeAcademicYearId) {
            $builder->where($model->qualifyColumn('academic_year_id'), $activeAcademicYearId);
        }
    }
}
