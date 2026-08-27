<?php

namespace App\Filament\Clusters\Academic\Resources\Schedules\Pages;

use App\Filament\Clusters\Academic\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);

            return $record;
        } catch (ValidationException $e) {
            Notification::make()
                ->title('Jadwal tidak bisa disimpan')
                ->body(collect($e->errors())->flatten()->implode(' '))
                ->danger()
                ->persistent()
                ->send();

            throw $e;
        }
    }
}
