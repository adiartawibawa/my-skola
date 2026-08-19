<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Widgets;

use App\Filament\Clusters\Academic\Resources\AcademicYears\Schemas\CalendarAcademicForm;
use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use Filament\Schemas\Schema;
use Guava\Calendar\Filament\Actions\CreateAction;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class AcademicCalendarWidget extends CalendarWidget
{
    public ?string $academicYearId = null;

    protected bool $dateClickEnabled = true;

    protected string|HtmlString|bool|null $heading = 'Academic Calendar';

    /**
     * Academic Year yang sedang ditampilkan.
     */
    public function getAcademicYear(): ?AcademicYear
    {
        return $this->academicYearId ? AcademicYear::find($this->academicYearId) : null;
    }

    /**
     * Batasi kalender hanya pada periode Academic Year.
     */
    public function getOptions(): array
    {
        $academicYear = $this->getAcademicYear();

        if (! $academicYear) {
            return [];
        }

        return [
            'validRange' => [
                'start' => $academicYear->start_date
                    ->toDateString(),

                'end' => $academicYear->end_date
                    ->copy()
                    ->addDay()
                    ->toDateString(),
            ],
        ];
    }

    /**
     * Event yang ditampilkan hanya berasal
     * dari Academic Year aktif pada halaman ini.
     */
    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $academicYear = $this->getAcademicYear();

        if (! $academicYear) {
            return [];
        }

        return AcademicCalendar::query()
            ->where('academic_year_id', $academicYear->getKey())
            ->whereDate('event_end_date', '>=', $info->start)
            ->whereDate('event_date', '<=', $info->end);
    }

    protected function getDateClickContextMenuActions(): array
    {
        return [
            $this->createEventFromDate(),
            $this->viewAction(),
            $this->deleteAction(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CalendarAcademicForm::configure($schema);
    }

    public function createEventFromDate(): CreateAction
    {
        return CreateAction::make('createEventFromDate')
            ->label('Tambah Event')
            ->icon('heroicon-o-plus')
            ->model(AcademicCalendar::class)
            ->schema(
                fn (Schema $schema): Schema => CalendarAcademicForm::configure($schema)
            )
            ->mountUsing(
                function (array $arguments, Schema $form): void {
                    $form->fill([
                        'academic_year_id' => $this->academicYearId,
                        'event_date' => data_get(
                            $arguments,
                            'dateStr',
                        ),
                    ]);
                },
            )
            ->mutateDataUsing(
                function (array $data): array {
                    $data['academic_year_id'] =
                        $this->academicYearId;

                    return $data;
                },
            );
    }

    // public function createEventFromDate(): CreateAction
    // {
    //     return CreateAction::make('createEventFromDate')
    //         ->label('Tambah Event')
    //         ->icon('heroicon-o-plus')
    //         ->model(AcademicCalendar::class)
    //         ->schema(
    //             fn (Schema $schema): Schema => CalendarAcademicForm::configure($schema)
    //         )
    //         ->mountUsing(
    //             function (array $arguments, Schema $form): void {
    //                 $form->fill([
    //                     'academic_year_id' => $this->academicYearId,
    //                     'event_date' => data_get(
    //                         $arguments,
    //                         'dateStr',
    //                     ),
    //                 ]);
    //             },
    //         )
    //         ->mutateDataUsing(
    //             function (array $data): array {
    //                 $data['academic_year_id'] =
    //                     $this->academicYearId;

    //                 return $data;
    //             },
    //         );
    // }
}
