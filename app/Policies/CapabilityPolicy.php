<?php

namespace App\Policies;

/**
 * Capability adalah data privilese tingkat sistem (mis. blog.write,
 * blog.editor) — siapa saja yang boleh mendapat kemampuan tertentu
 * lintas role. Tata Usaha boleh melihat daftarnya (referensi saat
 * mengisi form User), tapi mendefinisikan/menghapus capability baru
 * dibatasi ke Super Admin/Admin Sekolah lewat Gate::before().
 */
class CapabilityPolicy extends AdminStaffViewOnlyPolicy
{
    //
}
