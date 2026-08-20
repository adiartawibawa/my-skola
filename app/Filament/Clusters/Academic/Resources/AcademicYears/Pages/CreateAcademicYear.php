<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Pages;

use App\Filament\Clusters\Academic\Resources\AcademicYears\AcademicYearResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi bisnis sesungguhnya (1 Juli, no-overlap, dst) tetap
        // ditegakkan di AcademicYear::booted() — ini hanya transformasi
        // field bantu start_year → start_date sebelum sampai ke model.
        return AcademicYearResource::transformStartYear($data);
    }
}
