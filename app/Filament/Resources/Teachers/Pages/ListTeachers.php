<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Imports\TeacherImporter;
use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(TeacherImporter::class)
                ->label('Import Teacher'),
        ];
    }
}
