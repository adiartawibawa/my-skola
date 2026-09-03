<?php

namespace App\Livewire\Portal;

use App\Enums\DayOfWeekEnum;
use App\Models\Schedule;
use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SchedulePage extends Component
{
    public function render(): View
    {
        $student = PortalContext::currentStudent();
        $classRoom = $student?->currentClassRoom();

        $schedulesByDay = collect();

        if ($classRoom) {
            $schedulesByDay = Schedule::query()
                ->where('class_room_id', $classRoom->id)
                ->with(['subject', 'teacher.user'])
                ->orderBy('start_time')
                ->get()
                ->groupBy(fn (Schedule $schedule) => $schedule->day_of_week->value)
                ->sortBy(fn ($group, string $day) => DayOfWeekEnum::from($day)->order());
        }

        return view('livewire.portal.schedule-page', [
            'student' => $student,
            'classRoom' => $classRoom,
            'schedulesByDay' => $schedulesByDay,
        ])->layout('components.layouts.app', ['title' => 'Jadwal Pelajaran']);
    }
}
