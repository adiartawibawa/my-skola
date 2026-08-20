<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicCalendars\Pages;

use App\Filament\Clusters\Academic\Resources\AcademicCalendars\AcademicCalendarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicCalendar extends EditRecord
{
    protected static string $resource = AcademicCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
