<?php

namespace App\Livewire\Portal;

use App\Enums\DayOfWeekEnum;
use App\Enums\RoleEnum;
use App\Models\AcademicCalendar;
use App\Models\Announcement;
use App\Models\Schedule;
use App\Models\SchoolLink;
use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DashboardPage extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        // Baseline: SEMUA key yang dipakai dashboard-page.blade.php,
        // apa pun rolenya — supaya tidak ada satu pun cabang di bawah
        // yang bisa "lupa" mengisi salah satu key ini.
        $data = [
            'isStaff' => false,
            'isAlumni' => false,
            'appLinks' => collect(),
            'alumniProfile' => null,
            'student' => null,
            'classRoom' => null,
            'homeroomTeacher' => null,
            'childrenSummary' => collect(),
            'todaySchedules' => collect(),
            'upcomingEvents' => collect(),
            'latestAnnouncements' => collect(),
        ];

        if ($user->role->isStaff()) {
            $data['isStaff'] = true;
            $data['appLinks'] = SchoolLink::query()->active()->forRole($user->role)->get();

            return view('livewire.portal.dashboard-page', $data)
                ->layout('components.layouts.app', ['title' => 'Dashboard']);
        }

        if ($user->role === RoleEnum::ALUMNI) {
            $data['isAlumni'] = true;
            $data['alumniProfile'] = $user->alumniProfile;
            $data['latestAnnouncements'] = Announcement::query()
                ->published()
                ->visibleTo($user)
                ->latest('publish_at')
                ->limit(5)
                ->get();

            return view('livewire.portal.dashboard-page', $data)
                ->layout('components.layouts.app', ['title' => 'Dashboard']);
        }

        // Cabang Siswa/Orang Tua
        $student = PortalContext::currentStudent();
        $classRoom = $student?->currentClassRoom();

        $data['student'] = $student;
        $data['classRoom'] = $classRoom;
        $data['homeroomTeacher'] = $classRoom?->currentHomeroomTeacher();
        $data['childrenSummary'] = $user->role->value === 'parent' ? PortalContext::childrenSummary() : collect();

        if ($classRoom) {
            $today = $this->resolveToday();

            $data['todaySchedules'] = Schedule::query()
                ->where('class_room_id', $classRoom->id)
                ->where('day_of_week', $today->value)
                ->with(['subject', 'teacher.user'])
                ->orderBy('start_time')
                ->get();

            $data['upcomingEvents'] = AcademicCalendar::query()
                ->withoutGlobalScopes()
                ->where('academic_year_id', $classRoom->academic_year_id)
                ->upcoming()
                ->limit(3)
                ->get();
        }

        if ($student?->user) {
            $data['latestAnnouncements'] = Announcement::query()
                ->published()
                ->visibleTo($student->user)
                ->latest('publish_at')
                ->limit(3)
                ->get();
        }

        return view('livewire.portal.dashboard-page', $data)
            ->layout('components.layouts.app', ['title' => 'Dashboard']);
    }

    protected function resolveToday(): DayOfWeekEnum
    {
        $isoDay = now()->dayOfWeekIso;

        return collect(DayOfWeekEnum::cases())
            ->first(fn (DayOfWeekEnum $day) => $day->order() === $isoDay) ?? DayOfWeekEnum::SENIN;
    }
}
