<?php

namespace App\Filament\Resources\GuardianStudents\Pages;

use App\Filament\Resources\GuardianStudents\GuardianStudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGuardianStudents extends ManageRecords
{
    protected static string $resource = GuardianStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tautkan Manual'),
        ];
    }
}
