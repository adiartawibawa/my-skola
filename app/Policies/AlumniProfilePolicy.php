<?php

namespace App\Policies;

/**
 * Sama sifatnya dengan GuardianStudentPolicy — operasional harian TU,
 * bukan kebijakan tingkat Super Admin/Admin Sekolah.
 */
class AlumniProfilePolicy extends AdminStaffManagedPolicy
{
    //
}
