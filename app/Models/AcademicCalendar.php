<?php

namespace App\Filament\Widgets;

use App\Models\AcademicCalendar;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

class AcademicCalendarWidget extends CalendarWidget
{
    public string $academicYearId;

    protected function getEvents(FetchInfo $info): Collection|array
    {
        return AcademicCalendar::query()
            ->where('academic_year_id', $this->academicYearId)
            ->orderBy('event_date')
            ->get()
            ->map(
                fn (AcademicCalendar $event) => $this->toCalendarEvent($event)
            );
    }

    protected function toCalendarEvent(
        AcademicCalendar $event
    ): CalendarEvent {
        $start = $event->event_date->copy();

        /*
         * FullCalendar menggunakan `end` sebagai exclusive.
         *
         * Contoh:
         * event_date     = 2026-08-10
         * event_end_date = 2026-08-14
         *
         * Maka end harus:
         * 2026-08-15
         *
         * sehingga tanggal 10-14 ditampilkan sebagai event.
         */
        $end = $event->event_end_date
            ? $event->event_end_date->copy()->addDay()
            : $start->copy()->addDay();

        return CalendarEvent::make()
            ->key((string) $event->id)
            ->title($event->event_name)
            ->start($start)
            ->end($end)
            ->backgroundColor(
                $event->color ?: $event->default_color
            )
            ->textColor('#ffffff');
    }
}
