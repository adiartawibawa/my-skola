<?php

namespace App\Filament\Clusters\Academic\Widgets;

use App\Enums\EventType;
use App\Enums\SemesterEnum;
use App\Filament\Clusters\Academic\Resources\AcademicYears\Schemas\AcademicCalendarForm;
use App\Models\AcademicCalendar;
use App\Support\AcademicYearContext;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Livewire\Attributes\On;

/**
 * Tabel event untuk Tahun Akademik yang sedang dilihat, ditampilkan
 * di bawah grid kalender pada modal "Lihat Kalender" (AcademicYearResource).
 *
 * Dibuat sebagai TableWidget (bukan bagian dari sebuah Resource) karena
 * tidak ada lagi halaman List/Create/Edit tersendiri untuk event —
 * semua interaksi CRUD event terjadi di sini dan di kalender di atasnya,
 * dalam satu modal yang sama.
 */
class AcademicCalendarEventsTable extends TableWidget
{
    /**
     * Definisi tabel: query di-scope ke AcademicYearContext (tahun
     * ajaran yang sedang dilihat — di-set oleh action "Lihat Kalender"
     * pada AcademicYearResource sebelum modal ini dirender), sehingga
     * data yang tampil otomatis terfilter tanpa perlu filter manual
     * dari pengguna.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->scopedQuery())
            ->heading('Daftar Event')
            ->columns([
                TextColumn::make('event_name')
                    ->label('Nama Event')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (EventType $state) => $state->label())
                    ->color(fn (EventType $state) => $state->color()),

                TextColumn::make('event_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('event_end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->formatStateUsing(fn (?SemesterEnum $state) => $state ? $state->label() : '—'),

                IconColumn::make('is_national_holiday')
                    ->label('Nasional')
                    ->boolean(),

                IconColumn::make('is_school_holiday')
                    ->label('Sekolah')
                    ->boolean(),
            ])
            ->defaultSort('event_date')
            ->recordActions([
                // Reuse skema form yang sama persis dengan yang dipakai
                // widget kalender (AcademicCalendarForm) — supaya edit
                // lewat tabel dan lewat klik event di kalender selalu
                // konsisten.
                EditAction::make()
                    ->schema(fn (Schema $schema) => AcademicCalendarForm::configure($schema))
                    // Setelah tersimpan, kalender di atas tabel ini juga
                    // perlu tahu ada perubahan — lihat catatan listener
                    // di AcademicCalendarWidget.
                    ->after(fn () => $this->dispatch('academic-calendar-events-changed')),

                DeleteAction::make()
                    ->after(fn () => $this->dispatch('academic-calendar-events-changed')),
            ])
            ->paginated([5, 10, 25]);
    }

    /**
     * Query dasar tabel ini: seluruh AcademicCalendar milik Tahun
     * Akademik yang sedang dilihat (AcademicYearContext). Kalau
     * context kosong (seharusnya tidak terjadi karena modal ini selalu
     * dibuka dari baris AcademicYear tertentu), tabel akan kosong,
     * bukan menampilkan seluruh event lintas tahun ajaran.
     */
    protected function scopedQuery()
    {
        $academicYear = AcademicYearContext::get();

        return AcademicCalendar::query()
            ->when(
                $academicYear,
                fn ($query) => $query->where('academic_year_id', $academicYear->id),
                fn ($query) => $query->whereRaw('1 = 0')
            );
    }

    /**
     * Listener: dipicu saat widget kalender di atas tabel ini membuat,
     * mengubah, atau menghapus event (lihat ->after() di
     * AcademicCalendarWidget). Method ini sengaja kosong — cukup
     * dengan dipanggil lewat Livewire, komponen ini re-render dan
     * tabel query ulang dari scopedQuery(), sehingga datanya selalu
     * sinkron dengan kalender tanpa perlu tutup-buka modal.
     */
    #[On('academic-calendar-events-changed')]
    public function refreshEventsTable(): void
    {
        //
    }
}
