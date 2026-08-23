<?php

namespace App\Filament\Clusters\Academic\Resources\ClassRooms\Tables;

use App\Actions\Academic\GraduateClassRoomAction;
use App\Jobs\PromoteClassRoomsJob;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ClassRoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Kelas')
                    ->weight('bold')
                    ->description(fn (ClassRoom $record) => $record->academicYear?->name),

                TextColumn::make('programKeahlian.name')
                    ->label('Program Keahlian')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('homeroom_teacher')
                    ->label('Wali Kelas')
                    ->getStateUsing(
                        fn (ClassRoom $record) => $record->currentHomeroomTeacher()?->user?->name ?? '—'
                    ),

                TextColumn::make('student_count')
                    ->label('Siswa')
                    ->formatStateUsing(
                        fn (ClassRoom $record) => $record->capacity
                            ? "{$record->student_count} / {$record->capacity}"
                            : (string) $record->student_count
                    ),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->relationship('academicYear', 'name'),

                SelectFilter::make('program_keahlian_id')
                    ->label('Program Keahlian')
                    ->relationship('programKeahlian', 'name'),

                SelectFilter::make('grade_level')
                    ->label('Tingkat')
                    ->options([
                        10 => 'X',
                        11 => 'XI',
                        12 => 'XII',
                        13 => 'XIII',
                    ]),
            ])
            ->defaultSort('grade_level')
            ->recordActions([
                EditAction::make(),

                Action::make('graduate')
                    ->label('Luluskan')
                    ->icon(Heroicon::OutlinedAcademicCap)
                    ->color('success')
                    ->visible(fn (ClassRoom $record) => $record->isTerminalGrade())
                    ->requiresConfirmation()
                    ->modalHeading('Luluskan Siswa di Kelas Ini?')
                    ->modalDescription('Semua siswa Aktif di kelas ini akan ditandai Lulus. Aksi ini tidak membuat baris kelas baru — hanya menutup periode keanggotaan siswa di kelas ini.')
                    ->action(function (ClassRoom $record, GraduateClassRoomAction $graduate) {
                        $graduated = $graduate->execute($record);

                        Notification::make()
                            ->title('Siswa berhasil diluluskan')
                            ->body("{$graduated} siswa di {$record->full_name} ditandai Lulus.")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),

                BulkAction::make('promoteClassRooms')
                    ->label('Proses Kenaikan Kelas')
                    ->icon(Heroicon::OutlinedArrowUpCircle)
                    ->color('warning')
                    ->schema([
                        Select::make('target_academic_year_id')
                            ->label('Ke Tahun Akademik')
                            ->helperText('Kelas tingkat akhir (lihat status Aktif program keahliannya) akan dilewati — gunakan aksi "Luluskan" untuk itu.')
                            ->options(fn () => AcademicYear::query()
                                ->orderByDesc('start_date')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Proses Kenaikan Kelas?')
                    ->modalDescription('Siswa Aktif di kelas yang dipilih akan dipindahkan ke kelas tingkat berikutnya (dibuat otomatis bila belum ada) pada Tahun Akademik tujuan. Proses berjalan di background.')
                    ->action(function (Collection $records, array $data) {
                        PromoteClassRoomsJob::dispatch(
                            $records->pluck('id')->all(),
                            $data['target_academic_year_id'],
                            auth()->id(),
                        );

                        Notification::make()
                            ->title('Proses kenaikan kelas dijadwalkan')
                            ->body(count($records).' kelas akan diproses di background. Anda akan mendapat notifikasi setelah selesai.')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

            ]);
    }
}
