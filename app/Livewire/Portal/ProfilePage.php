<?php

namespace App\Livewire\Portal;

use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProfilePage extends Component
{
    public function render(): View
    {
        $student = PortalContext::currentStudent();
        $classRoom = $student?->currentClassRoom();

        return view('livewire.portal.profile-page', [
            'student' => $student,
            'classRoom' => $classRoom,
            'homeroomTeacher' => $classRoom?->currentHomeroomTeacher(),
        ])->layout('components.layouts.app', ['title' => 'Profil']);
    }
}
