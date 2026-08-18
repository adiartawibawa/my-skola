<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

class AcademicCalendarWidget extends CalendarWidget
{
    public string $academicYearId;

    protected function getEvents(FetchInfo $info): Collection|array
    {
        $academicYear = AcademicYear::findOrFail(
            $this->academicYearId
        );

        return [
            CalendarEvent::make()
                ->title("Kalender - {$academicYear->name}")
                ->start(now())
                ->end(now()->addHours(2)),
        ];
    }
}
