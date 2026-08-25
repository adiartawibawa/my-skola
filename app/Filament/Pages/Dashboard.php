<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Support\AcademicYearContext;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    /**
     * AcademicYearContext (app/Support/AcademicYearContext.php) adalah
     * satu-satunya sumber kebenaran untuk "Tahun Akademik yang sedang
     * dilihat" di seluruh app — dipakai juga oleh AcademicCalendarWidget
     * di cluster Academic. Filter form Filament di sini murni UI yang
     * membaca/menulis ke context itu lewat afterStateUpdated() di
     * bawah, BUKAN state terpisah. Makanya persist-session bawaan
     * Filament dimatikan — AcademicYearContext sudah mengurus
     * persistensinya sendiri, dan kita tidak mau dua sumber kebenaran
     * yang bisa saling tidak sinkron.
     */
    public function persistsFiltersInSession(): bool
    {
        return false;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Akademik')
                            ->options(fn () => AcademicYear::query()
                                ->orderByDesc('start_date')
                                ->pluck('name', 'id'))
                            ->default(fn () => AcademicYearContext::get()?->id)
                            ->selectablePlaceholder(false)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?string $state): void {
                                if (! $state) {
                                    return;
                                }
                                AcademicYearContext::set($state);
                                $this->dispatch('academic-year-context-changed');
                            }),
                    ])
                    ->columns(1),
            ]);
    }
}
