<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Pages;

use App\Filament\Clusters\Academic\Resources\AcademicYears\AcademicYearResource;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicYear extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Isi field bantu start_year dari start_date yang tersimpan,
     * supaya Select di form menampilkan tahun ajaran yang benar.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['start_date'])) {
            $data['start_year'] = Carbon::parse($data['start_date'])->year;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return AcademicYearResource::transformStartYear($data);
    }
}
