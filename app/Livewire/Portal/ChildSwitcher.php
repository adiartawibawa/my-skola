<?php

namespace App\Livewire\Portal;

use App\Support\PortalContext;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ChildSwitcher extends Component
{
    public string $childId = '';

    public function mount(): void
    {
        $this->childId = PortalContext::currentStudent()?->id ?? '';
    }

    /**
     * Ganti context lalu redirect ke halaman yang sama — halaman portal
     * adalah komponen full-page terpisah (bukan sibling di satu
     * halaman), jadi cara paling sederhana & pasti benar untuk membuat
     * semuanya membaca ulang PortalContext adalah reload penuh,
     * bukan event broadcast antar-komponen.
     */
    public function updatedChildId(string $value): void
    {
        PortalContext::setActiveChild($value);

        $this->redirect(url()->current(), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.portal.child-switcher', [
            'children' => PortalContext::availableChildren(),
        ]);
    }
}
