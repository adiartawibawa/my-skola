<?php

namespace App\Filament\Resources\Alumni\Pages;

use App\Filament\Resources\Alumni\AlumniResource;
use Filament\Resources\Pages\ListRecords;

class ListAlumni extends ListRecords
{
    protected static string $resource = AlumniResource::class;

    // Sengaja tidak ada getHeaderActions() — laporan alumni read-only,
    // tidak ada "Tambah Alumni" karena status Lulus muncul otomatis
    // lewat aksi "Luluskan" di ClassRoomResource, bukan input manual
    // di sini.
}
