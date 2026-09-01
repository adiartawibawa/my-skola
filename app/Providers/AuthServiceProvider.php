<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Kontrol akses berbasis role — bagian global.
 *
 * Gate::before() dipanggil SEBELUM Policy per-model manapun, dan
 * kalau closure ini mengembalikan non-null, hasilnya dipakai
 * langsung (Policy tidak jadi dicek lagi). Dipakai untuk 2 aturan
 * yang berlaku SAMA di semua model, supaya tidak perlu ditulis
 * ulang di setiap Policy:
 *
 * - Super Admin & Admin Sekolah: akses penuh ke semua ability, di
 *   semua model.
 * - Kepala Sekolah: read-only global — boleh viewAny/view di semua
 *   model, ditolak untuk ability lain apa pun (create/update/delete/
 *   deleteAny/restore/forceDelete/dst).
 *
 * Role lain (Guru, Tata Usaha) SENGAJA tidak disentuh di sini
 * (return null) — mereka lanjut ke Policy per-model masing-masing,
 * karena kebutuhan aksesnya beda-beda tiap model (lihat app/Policies).
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            if (in_array($user->role, [RoleEnum::SUPER_ADMIN, RoleEnum::SCHOOL_ADMIN], true)) {
                return true;
            }

            if ($user->role === RoleEnum::PRINCIPAL) {
                // Blog dikecualikan dari read-only global: biarkan PostPolicy
                // + capability (blog.write/blog.editor) yang memutuskan.
                $subject = $arguments[0] ?? null;
                $isPost = $subject instanceof Post || $subject === Post::class;

                if ($isPost) {
                    return null;
                }

                return in_array($ability, ['viewAny', 'view'], true) ? true : false;
            }

            return null;
        });
    }
}
