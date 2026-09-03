<?php

namespace App\Policies;

/**
 * Penautan Orang Tua-Siswa: sama sifatnya dengan administrasi akun
 * (UserPolicy) — pekerjaan operasional harian, bukan keputusan
 * kebijakan. Tata Usaha bisa audit/cabut tautan yang keliru atau
 * disalahgunakan; penciptaan tautan normalnya lewat self-service
 * verifikasi NISN, bukan lewat panel ini.
 */
class GuardianStudentPolicy extends AdminStaffManagedPolicy
{
    //
}
