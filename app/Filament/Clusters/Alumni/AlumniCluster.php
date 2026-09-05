<?php

namespace App\Filament\Clusters\Alumni;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AlumniCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static ?string $navigationLabel = 'Data Alumni';

    protected static string|UnitEnum|null $navigationGroup = 'Alumni';
}
