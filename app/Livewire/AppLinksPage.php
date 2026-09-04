<?php

namespace App\Livewire;

use App\Enums\LinkCategory;
use App\Models\SchoolLink;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AppLinksPage extends Component
{
    public function render(): View
    {
        $grouped = SchoolLink::query()
            ->active()
            ->public()
            ->get()
            ->groupBy(fn (SchoolLink $link) => $link->category->value);

        return view('livewire.app-links-page', [
            'grouped' => $grouped,
            'categories' => LinkCategory::cases(),
        ])->layout('components.layouts.guest', [
            'title' => 'Aplikasi & Tautan Sekolah',
            'description' => 'Kumpulan aplikasi dan tautan resmi yang digunakan siswa, guru, dan orang tua di '.config('app.name').'.',
        ]);
    }
}
