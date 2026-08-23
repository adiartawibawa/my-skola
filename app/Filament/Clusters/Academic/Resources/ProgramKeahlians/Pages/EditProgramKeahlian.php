<?php

namespace App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Pages;

use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\ProgramKeahlianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramKeahlian extends EditRecord
{
    protected static string $resource = ProgramKeahlianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
