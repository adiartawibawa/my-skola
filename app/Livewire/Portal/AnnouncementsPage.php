<?php

namespace App\Livewire\Portal;

use App\Models\Announcement;
use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AnnouncementsPage extends Component
{
    public function render(): View
    {
        $student = PortalContext::currentStudent();

        $announcements = collect();

        if ($student?->user) {
            // scopeVisibleTo() dipanggil dengan User milik SISWA (bukan
            // Orang Tua yang sedang login) — supaya hasilnya identik
            // baik dilihat lewat akun siswa sendiri maupun lewat akun
            // orang tuanya.
            $announcements = Announcement::query()
                ->published()
                ->visibleTo($student->user)
                ->with('creator')
                ->orderByDesc('is_pinned')
                ->orderByDesc('publish_at')
                ->get();
        }

        return view('livewire.portal.announcements-page', [
            'student' => $student,
            'announcements' => $announcements,
        ])->layout('components.layouts.app', ['title' => 'Pengumuman']);
    }
}
