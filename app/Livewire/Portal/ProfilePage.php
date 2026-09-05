<?php

namespace App\Livewire\Portal;

use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProfilePage extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        if ($user->role->value === 'alumni') {
            return view('livewire.portal.profile-page', [
                'isAlumni' => true,
                'alumniProfile' => $user->alumniProfile,
            ])->layout('components.layouts.app', ['title' => 'Profil']);
        }

        $student = PortalContext::currentStudent();
        $classRoom = $student?->currentClassRoom();

        return view('livewire.portal.profile-page', [
            'isAlumni' => false,
            'student' => $student,
            'classRoom' => $classRoom,
            'homeroomTeacher' => $classRoom?->currentHomeroomTeacher(),
        ])->layout('components.layouts.app', ['title' => 'Profil']);
    }
}
