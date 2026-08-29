<?php

namespace App\Filament\Clusters\Settings\Resources\SchoolLinks\Pages;

use App\Filament\Clusters\Settings\Resources\SchoolLinks\SchoolLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSchoolLinks extends ManageRecords
{
    protected static string $resource = SchoolLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
