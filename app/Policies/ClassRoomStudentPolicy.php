<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class ClassRoomStudentPolicy extends AdminStaffManagedPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN_STAFF, RoleEnum::TEACHER], true);
    }

    public function view(User $user, $classRoomStudent): bool
    {
        if ($user->role === RoleEnum::ADMIN_STAFF) {
            return true;
        }

        if ($user->role === RoleEnum::TEACHER) {
            return $classRoomStudent->classRoom?->currentHomeroomTeacher()?->user_id === $user->id
                || $user->teacher?->isHeadOfProgramKeahlian($classRoomStudent->classRoom?->program_keahlian_id);
        }

        return false;
    }
}
