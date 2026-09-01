<?php

namespace App\Policies;

/**
 * Asumsi: Tata Usaha ikut boleh membuat pengumuman operasional
 * (bukan cuma Admin/Kepala Sekolah). Kalau ternyata pengumuman resmi
 * harus dibatasi ke Admin/Kepala Sekolah saja, ganti extends ke
 * AdminStaffViewOnlyPolicy.
 */
class AnnouncementPolicy extends AdminStaffManagedPolicy
{
    //
}
