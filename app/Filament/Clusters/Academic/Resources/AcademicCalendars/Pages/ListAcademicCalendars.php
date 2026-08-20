<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicCalendars\Pages;

use App\Filament\Clusters\Academic\Resources\AcademicCalendars\AcademicCalendarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcademicCalendars extends ListRecords
{
    protected static string $resource = AcademicCalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
