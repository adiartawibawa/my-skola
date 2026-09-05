<?php

namespace App\Filament\Clusters\Alumni\Resources\VerifikasiAlumni\Pages;

use App\Filament\Clusters\Alumni\Resources\VerifikasiAlumni\VerifikasiAlumniResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAlumniProfiles extends ManageRecords
{
    protected static string $resource = VerifikasiAlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
