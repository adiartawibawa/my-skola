<?php

namespace App\Livewire\Portal;

use App\Models\AcademicCalendar;
use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CalendarPage extends Component
{
    public function render(): View
    {
        $student = PortalContext::currentStudent();
        $classRoom = $student?->currentClassRoom();

        $eventsByMonth = collect();

        if ($classRoom) {
            // withoutGlobalScopes(): ikuti tahun ajaran KELAS siswa, bukan
            // tahun ajaran aktif global (lihat catatan sama di Dashboard).
            $eventsByMonth = AcademicCalendar::query()
                ->withoutGlobalScopes()
                ->where('academic_year_id', $classRoom->academic_year_id)
                ->orderBy('event_date')
                ->get()
                ->groupBy(fn (AcademicCalendar $event) => $event->event_date->translatedFormat('F Y'));
        }

        return view('livewire.portal.calendar-page', [
            'student' => $student,
            'classRoom' => $classRoom,
            'eventsByMonth' => $eventsByMonth,
        ])->layout('components.layouts.app', ['title' => 'Kalender Akademik']);
    }
}
