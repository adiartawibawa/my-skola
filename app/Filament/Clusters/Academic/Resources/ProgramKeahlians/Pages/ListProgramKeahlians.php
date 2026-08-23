<?php

namespace App\Filament\Clusters\Academic\Resources\ProgramKeahlians\Pages;

use App\Filament\Clusters\Academic\Resources\ProgramKeahlians\ProgramKeahlianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramKeahlians extends ListRecords
{
    protected static string $resource = ProgramKeahlianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
