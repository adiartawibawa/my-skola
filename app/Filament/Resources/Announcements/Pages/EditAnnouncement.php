<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            // ForceDeleteAction::make(),
            // RestoreAction::make(),
        ];
    }

    /**
     * Isi ulang target_roles (field virtual) dari baris
     * AnnouncementRole yang sudah ada, supaya CheckboxList-nya
     * ter-pre-select saat form edit dibuka.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['target_roles'] = $this->record->roles()->pluck('role')->all();

        return $data;
    }

    /**
     * Sama seperti CreateAnnouncement::mutateFormDataBeforeCreate() —
     * target_roles bukan kolom Announcement, harus dikeluarkan
     * sebelum update() dipanggil.
     */
    protected array $pendingRoles = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingRoles = $data['target_roles'] ?? [];
        unset($data['target_roles']);

        return $data;
    }

    /**
     * Ganti seluruh baris role lama dengan yang baru — lebih
     * sederhana dan aman daripada diff manual, dan volumenya kecil
     * (paling banyak sejumlah RoleEnum::cases()) jadi tidak masalah
     * dari sisi performa.
     */
    protected function afterSave(): void
    {
        $this->record->roles()->delete();

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
