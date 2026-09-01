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

    public function view(User $user, $record): bool
    {
        if ($user->role === RoleEnum::ADMIN_STAFF) {
            return true;
        }

        if ($user->role === RoleEnum::TEACHER) {
            return $record->teacher?->user_id === $user->id;
        }

        return false;
    }
}
