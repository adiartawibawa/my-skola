<?php

namespace App\Filament\Clusters\Academic\Resources\AcademicYears\Pages;

use App\Filament\Clusters\Academic\Resources\AcademicYears\AcademicYearResource;
use App\Support\AcademicYearContext;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

/**
 * Halaman kalender untuk satu Tahun Akademik tertentu.
 *
 * Ini BUKAN halaman yang muncul di sidebar navigasi — dia hanya
 * dijangkau lewat ikon "Lihat Kalender" pada baris tabel
 * AcademicYearResource (lihat action `viewCalendar` di sana, yang
 * mengarah ke rute 'calendar' halaman ini). Tujuannya: sidebar tetap
 * cuma satu menu ("Tahun Akademik"), tapi setiap tahun ajaran tetap
 * punya "halaman kerja" sendiri untuk kelola kalendernya.
 *
 * Memakai trait InteractsWithRecord (pola resmi Filament untuk
 * halaman kustom yang terikat ke satu record, serupa halaman
 * Edit/View bawaan) supaya breadcrumb, judul, dan resolusi record
 * dari parameter URL berjalan otomatis tanpa kita tulis ulang.
 */
class AcademicYearCalendar extends Page
{
    use InteractsWithRecord;

    protected static string $resource = AcademicYearResource::class;

    protected string $view = 'filament.clusters.academic.resources.academic-years.pages.academic-year-calendar';

    /**
     * Resolusi record dari parameter {record} di URL (didaftarkan di
     * AcademicYearResource::getPages()), lalu langsung men-set
     * AcademicYearContext ke tahun ajaran ini — SATU-SATUNYA tempat
     * context di-set untuk alur ini (dibanding sebelumnya yang di-set
     * dari action tabel), supaya context selalu konsisten dengan URL
     * yang sedang dibuka (mis. kalau halaman ini di-refresh langsung
     * atau link-nya dibagikan/dibuka ulang).
     */
    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        AcademicYearContext::set($this->record->id);
    }

    /**
     * Judul halaman menampilkan nama Tahun Akademik yang sedang
     * dilihat, supaya jelas konteksnya saat admin membuka lebih dari
     * satu tab/halaman kalender untuk tahun ajaran berbeda.
     */
    public function getTitle(): string
    {
        return "Kalender — {$this->record->name}";
    }

    /**
     * Tombol kembali ke daftar Tahun Akademik. Breadcrumb bawaan
     * Filament sebenarnya sudah menyediakan jalan kembali, tombol ini
     * hanya mempertegas karena halaman ini tidak ada di sidebar.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToList')
                ->label('Kembali ke Daftar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => AcademicYearResource::getUrl('index')),
        ];
    }
}
