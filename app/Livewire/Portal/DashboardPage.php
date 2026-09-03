<?php

namespace App\Livewire\Portal;

use App\Enums\DayOfWeekEnum;
use App\Models\AcademicCalendar;
use App\Models\Announcement;
use App\Models\Schedule;
use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DashboardPage extends Component
{
    public function render(): View
    {
        $student = PortalContext::currentStudent();
        $classRoom = $student?->currentClassRoom();

        $todaySchedules = collect();
        $upcomingEvents = collect();
        $latestAnnouncements = collect();

        if ($classRoom) {
            $today = $this->resolveToday();

            $todaySchedules = Schedule::query()
                ->where('class_room_id', $classRoom->id)
                ->where('day_of_week', $today->value)
                ->with(['subject', 'teacher.user'])
                ->orderBy('start_time')
                ->get();

            // withoutGlobalScopes(): kelas siswa bisa saja dari tahun
            // ajaran yang sedang tidak aktif (mis. siswa kelas akhir di
            // periode transisi) — ikuti tahun ajaran KELAS-nya, bukan
            // tahun ajaran yang sedang aktif secara global.
            $upcomingEvents = AcademicCalendar::query()
                ->withoutGlobalScopes()
                ->where('academic_year_id', $classRoom->academic_year_id)
                ->upcoming()
                ->limit(3)
                ->get();
        }

        if ($student?->user) {
            $latestAnnouncements = Announcement::query()
                ->published()
                ->visibleTo($student->user)
                ->latest('publish_at')
                ->limit(3)
                ->get();
        }

        return view('livewire.portal.dashboard-page', [
            'student' => $student,
            'classRoom' => $classRoom,
            'homeroomTeacher' => $classRoom?->currentHomeroomTeacher(),
            'todaySchedules' => $todaySchedules,
            'upcomingEvents' => $upcomingEvents,
            'latestAnnouncements' => $latestAnnouncements,
        ])->layout('components.layouts.app', ['title' => 'Dashboard']);
    }

    /**
     * ISO day-of-week (1=Senin..7=Minggu) — pola sama persis dengan
     * TodayScheduleWidget di panel Filament, supaya tidak bergantung
     * pada locale Carbon server.
     */
    protected function resolveToday(): DayOfWeekEnum
    {
        $isoDay = now()->dayOfWeekIso;

        return collect(DayOfWeekEnum::cases())
            ->first(fn (DayOfWeekEnum $day) => $day->order() === $isoDay) ?? DayOfWeekEnum::SENIN;
    }
}
