<?php

namespace App\Filament\Widgets;

use App\Models\AcademicCalendar;
use App\Support\AcademicYearContext;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;

class AcademicCalendarWidget extends CalendarWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getEvents(FetchInfo $info): Builder
    {
        $academicYear = AcademicYearContext::get();

        return AcademicCalendar::query()
            ->when(
                $academicYear,
                fn (Builder $query) => $query->where('academic_year_id', $academicYear->id)
            )
            ->where('event_date', '<=', $info->end)
            ->where(function (Builder $query) use ($info) {
                $query->whereNull('event_end_date')
                    ->orWhere('event_end_date', '>=', $info->start);
            });
    }
}
