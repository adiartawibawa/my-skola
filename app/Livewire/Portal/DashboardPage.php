<?php

namespace App\Livewire\Portal;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DashboardPage extends Component
{
    public function render(): View
    {
        return view('livewire.portal.dashboard-page')->layout('components.layouts.app', [
            'title' => 'Dashboard',
        ]);
    }
}
