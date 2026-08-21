<x-filament-panels::page>
    <div class="space-y-6">
        @livewire(\App\Filament\Clusters\Academic\Widgets\AcademicCalendarWidget::class)
    </div>
    <div class="space-y-6">
        @livewire(\App\Filament\Clusters\Academic\Widgets\AcademicCalendarEventsTable::class)
    </div>
</x-filament-panels::page>
