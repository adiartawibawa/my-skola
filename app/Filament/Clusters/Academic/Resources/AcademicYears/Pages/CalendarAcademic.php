<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Pages;

use App\Filament\Clusters\Academic\Resources\AcademicYears\AcademicYearResource;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Widgets\AcademicCalendarWidget;
use App\Models\AcademicYear;
use Filament\Resources\Pages\Page;

class CalendarAcademic extends Page
{
    protected static string $resource = AcademicYearResource::class;

    public AcademicYear $academicYear;

    // protected string $view = 'filament.clusters.academic.resources.academic-years.pages.calendar-academic';

    public function mount(string $record): void
    {
        $this->academicYear = AcademicYear::query()
            ->findOrFail($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AcademicCalendarWidget::make([
                'academicYearId' => $this->academicYear->getKey(),
            ]),
        ];
    }
}
