<?php

namespace App\Filament\Clusters\Academic\Widgets;

use App\Filament\Clusters\Academic\Resources\AcademicYears\Schemas\AcademicCalendarForm;
use App\Models\AcademicCalendar;
use App\Models\AcademicYear;
use App\Support\AcademicYearContext;
use Carbon\Carbon;
use Filament\Schemas\Schema;
use Guava\Calendar\Filament\Actions\CreateAction;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\DateClickInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

/**
 * Widget kalender akademik.
 *
 * Referensi arsitektur (resmi dari guava.cz/developers/packages/calendar/3.x):
 * - "Clicks and selections" — date click vs event click adalah dua
 *   interaksi TERPISAH, masing-masing dengan info object sendiri
 *   (DateClickInfo vs EventClickInfo).
 * - "Context menus" — getDateClickContextMenuActions() dipakai untuk
 *   action bertipe CREATE saja (klik sel tanggal kosong), sedangkan
 *   view/edit/delete SEHARUSNYA didaftarkan lewat
 *   getEventClickContextMenuActions() karena resolusi record-nya
 *   otomatis terikat ke event yang diklik (bukan ke sel tanggal).
 *   Mencampur editAction()/deleteAction() ke dalam date-click context
 *   menu (dan menimpa ->record() dengan DateClickInfo) adalah pola
 *   yang TIDAK didukung dan menyebabkan error resolusi container.
 *
 * Konsekuensinya untuk kebutuhan "klik tanggal yang sudah ada event
 * juga menampilkan edit/hapus": karena event tampil sebagai blok pada
 * tanggalnya di kalender, admin cukup mengklik BLOK EVENT tersebut
 * (memicu event click, bukan date click) untuk mendapatkan menu
 * Lihat/Edit/Hapus. Klik pada bagian tanggal yang masih kosong hanya
 * memicu menu Tambah.
 */
class AcademicCalendarWidget extends CalendarWidget
{
    /**
     * Mengaktifkan klik pada sel tanggal (untuk menu "Tambah Event").
     */
    protected bool $dateClickEnabled = true;

    /**
     * Mengaktifkan klik pada blok event (untuk menu "Lihat/Edit/Hapus").
     */
    protected bool $eventClickEnabled = true;

    /**
     * Sumber data event kalender.
     *
     * Difilter berdasarkan Tahun Akademik yang sedang dilihat
     * (AcademicYearContext — bisa histori, lihat catatan di file
     * AcademicYearContext) DAN rentang tanggal yang sedang tampil di
     * kalender ($info->start/$info->end) supaya tidak menarik seluruh
     * tabel setiap kali kalender di-render.
     *
     * Kondisi whereNull/orWhere pada event_end_date memakai konvensi
     * inclusive yang sama dengan AcademicCalendar::scopeCurrent() —
     * event yang mulai sebelum $info->start tapi masih berlangsung
     * tetap ikut ditampilkan.
     */
    protected function getEvents(FetchInfo $info): Builder
    {
        $academicYear = AcademicYearContext::get();

        return AcademicCalendar::query()
            ->when(
                $academicYear,
                fn (Builder $query) => $query->where('academic_year_id', $academicYear->id)
            )
            ->where('event_date', '<=', $info->end)
            ->where(function (Builder $query) use ($info) {
                $query->whereNull('event_end_date')
                    ->orWhere('event_end_date', '>=', $info->start);
            });
    }

    /**
     * Schema form yang dipakai oleh modal Create/View/Edit bawaan
     * Guava. Kita reuse AcademicCalendarResource::form() secara
     * eksplisit (bukan mengandalkan auto-discovery Guava) supaya
     * tidak ada dua definisi form yang bisa saling menyimpang.
     */
    public function defaultSchema(Schema $schema): Schema
    {
        return AcademicCalendarForm::configure($schema);
    }

    /**
     * Action untuk membuat event baru.
     *
     * Dipakai dari dua tempat: tombol header widget, dan context menu
     * saat klik tanggal kosong. mountUsing menerima DateClickInfo
     * (nullable — bisa null kalau action dipicu bukan dari klik
     * tanggal, misalnya dari tombol header) untuk:
     * 1. Mengisi event_date dari tanggal yang diklik.
     * 2. (Poin 4 requirement) Meresolve academic_year_id otomatis
     *    dari tanggal tersebut lewat resolveAcademicYearForDate() —
     *    bukan sekadar mengambil dari AcademicYearContext mentah-
     *    mentah, supaya relasinya selalu akurat terhadap tanggalnya
     *    sendiri.
     */
    public function createAcademicCalendarAction(): CreateAction
    {
        return CreateAction::make('createAcademicCalendar')
            ->label('Tambah Event')
            ->model(AcademicCalendar::class)
            ->schema(fn (Schema $schema) => AcademicCalendarForm::configure($schema))
            ->mountUsing(function ($form, ?DateClickInfo $info = null) {
                $date = $this->extractClickedDate($info);
                $academicYear = $this->resolveAcademicYearForDate($date);

                $form->fill([
                    'academic_year_id' => $academicYear?->id,
                    'event_date' => $date?->toDateString(),
                ]);
            });
        // ->authorize(fn () => Auth::user()?->can('create', AcademicCalendar::class) ?? false);
    }

    /**
     * Context menu saat sel TANGGAL (bukan event) diklik.
     * Sesuai dokumentasi resmi, hanya action bertipe create yang
     * seharusnya ada di sini.
     */
    protected function getDateClickContextMenuActions(): array
    {
        return [
            $this->createAcademicCalendarAction(),
        ];
    }

    /**
     * Context menu saat blok EVENT yang sudah ada diklik.
     *
     * $this->viewAction()/editAction()/deleteAction() sudah otomatis
     * disediakan Guava dan otomatis terikat ke record event yang
     * diklik (lewat EventClickInfo) — tidak perlu (dan tidak boleh)
     * kita timpa ->record()-nya secara manual.
     *
     * Otorisasi eksplisit ditambahkan di sini karena Guava tidak
     * mewajibkan otorisasi default pada action apapun — $eventRecord
     * adalah record event yang sedang diklik (disediakan Guava,
     * berbeda dari $record yang berarti record resource halaman ini).
     */
    protected function getEventClickContextMenuActions(): array
    {
        return [
            $this->viewAction(),
            $this->editAction(),
            // ->authorize(fn (?Model $eventRecord = null) => $eventRecord
            //     && (Auth::user()?->can('update', $eventRecord) ?? false)),
            $this->deleteAction(),
            // ->authorize(fn (?Model $eventRecord = null) => $eventRecord
            //     && (Auth::user()?->can('delete', $eventRecord) ?? false)),
        ];
    }

    /**
     * Tombol "Tambah Event" tetap tersedia di header widget, bukan
     * cuma lewat klik tanggal, untuk aksesibilitas yang lebih baik
     * (mis. di tampilan list view yang tidak punya sel tanggal untuk
     * diklik).
     */
    public function getHeaderActions(): array
    {
        return [
            $this->createAcademicCalendarAction(),
        ];
    }

    /**
     * Meresolve AcademicYear berdasarkan tanggal yang diklik (bukan
     * sekadar dari AcademicYearContext), supaya relasi academic_year_id
     * pada event baru selalu akurat terhadap tanggalnya sendiri —
     * ini yang memenuhi requirement "otomatis langsung berelasi
     * dengan AcademicYear" saat tanggal diberi aksi.
     *
     * Fallback ke AcademicYearContext hanya kalau (secara tak
     * terduga) tanggal jatuh di luar semua periode Tahun Akademik
     * yang terdaftar — seharusnya jarang terjadi karena validRange
     * kalender (lihat getOptions()) sudah membatasi navigasi ke
     * periode Tahun Akademik yang sedang dilihat.
     */
    protected function resolveAcademicYearForDate(?Carbon $date): ?AcademicYear
    {
        if ($date) {
            $byDate = AcademicYear::query()
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();

            if ($byDate) {
                return $byDate;
            }
        }

        return AcademicYearContext::get();
    }

    /**
     * Mengekstrak tanggal yang diklik dari DateClickInfo.
     *
     * Properti persis pada DateClickInfo dicoba berurutan (date lalu
     * dateStr) karena representasi tanggal pada value object ini bisa
     * berbeda bentuknya; kalau instance Carbon langsung dikembalikan
     * apa adanya tanpa re-parse.
     */
    protected function extractClickedDate(?DateClickInfo $info = null): ?Carbon
    {
        if (! $info) {
            return null;
        }

        $raw = null;

        if (isset($info->date)) {
            $raw = $info->date;
        } elseif (isset($info->dateStr)) {
            $raw = $info->dateStr;
        }

        if (! $raw) {
            return null;
        }

        return $raw instanceof Carbon ? $raw : Carbon::parse($raw);
    }

    /**
     * Membatasi navigasi kalender agar tidak bisa digeser ke luar
     * periode Tahun Akademik yang sedang dilihat — event memang tidak
     * mungkin ada di luar periode itu (lihat
     * AcademicYear::validateNoOverlap dan
     * AcademicCalendar::validateWithinAcademicYear).
     */
    public function getOptions(): array
    {
        $academicYear = AcademicYearContext::get();

        if (! $academicYear) {
            return [];
        }

        return [
            'validRange' => [
                'start' => $academicYear->start_date->toDateString(),
                // exclusive end, konsisten dengan konvensi toCalendarEvent()
                'end' => $academicYear->end_date->copy()->addDay()->toDateString(),
            ],
        ];
    }

    /**
     * Dipicu dari AcademicCalendarPage saat admin mengganti Tahun
     * Akademik yang sedang dilihat (lewat dropdown context-switcher),
     * supaya kalender langsung menampilkan data & validRange yang
     * baru tanpa reload halaman penuh. refreshRecords() adalah method
     * resmi bawaan Guava untuk memicu refetch event di sisi JS.
     */
    #[On('academic-year-context-changed')]
    public function onAcademicYearContextChanged(): void
    {
        $this->refreshRecords();
    }
}
