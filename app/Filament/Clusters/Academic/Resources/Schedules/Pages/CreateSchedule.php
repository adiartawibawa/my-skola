<?php

namespace App\Filament\Clusters\Academic\Resources\Schedules\Pages;

use App\Filament\Clusters\Academic\Resources\Schedules\ScheduleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return static::getModel()::create($data);
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
