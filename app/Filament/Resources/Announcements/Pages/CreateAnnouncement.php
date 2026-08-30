<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    /**
     * target_roles adalah field virtual di form (bukan kolom
     * Announcement) — harus dikeluarkan dari $data sebelum
     * Announcement::create() dipanggil, atau akan error "Unknown
     * column" persis seperti kasus ImportColumn dulu. Nilainya
     * distash ke property biasa untuk dipakai di afterCreate().
     */
    protected array $pendingRoles = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingRoles = $data['target_roles'] ?? [];
        unset($data['target_roles']);

        return $data;
    }

    /**
     * classRooms/programKeahlians TIDAK perlu ditangani manual di
     * sini — keduanya relationship() BelongsToMany asli, Filament
     * otomatis sync pivot-nya sendiri setelah record dibuat.
     */
    protected function afterCreate(): void
    {
        if ($this->pendingRoles === []) {
            return;
        }

        $this->record->roles()->createMany(
            collect($this->pendingRoles)
                ->map(fn (string $role) => ['role' => $role])
                ->all()
        );
    }
}
