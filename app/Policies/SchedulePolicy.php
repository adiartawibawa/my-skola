<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class SchedulePolicy extends AdminStaffManagedPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN_STAFF, RoleEnum::TEACHER], true);
    }

    public function view(User $user, $schedule): bool
    {
        if ($user->role === RoleEnum::ADMIN_STAFF) {
            return true;
        }

        if ($user->role === RoleEnum::TEACHER) {
            return $schedule->teacher?->user_id === $user->id
                || $user->teacher?->isHeadOfProgramKeahlian($schedule->classRoom?->program_keahlian_id);
        }

        return false;
    }
}
