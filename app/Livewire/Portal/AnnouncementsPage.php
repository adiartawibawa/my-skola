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
        $targetUser = PortalContext::targetUserForVisibility();

        $announcements = $targetUser
            ? Announcement::query()
                ->published()
                ->visibleTo($targetUser)
                ->with('creator')
                ->orderByDesc('is_pinned')
                ->orderByDesc('publish_at')
                ->get()
            : collect();

        return view('livewire.portal.announcements-page', [
            'targetUser' => $targetUser,
            'announcements' => $announcements,
        ])->layout('components.layouts.app', ['title' => 'Pengumuman']);
    }
}
