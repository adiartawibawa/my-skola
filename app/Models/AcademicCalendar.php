<?php

namespace App\Models;

use App\Enums\Enums\EventType;
use App\Enums\Enums\SemesterEnum;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[Fillable(
    'academic_year_id',
    'event_name',
    'event_date',
    'event_end_date',
    'event_type',
    'semester',
    'is_national_holiday',
    'is_school_holiday',
    'description',
    'color',
)]
class AcademicCalendar extends Model implements Eventable
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_end_date' => 'date',
            'is_national_holiday' => 'boolean',
            'is_school_holiday' => 'boolean',
            'event_type' => EventType::class,
            'semester' => SemesterEnum::class,
        ];
    }

    /**
     * Relasi ke Academic Year
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope untuk event berdasarkan tipe
     */
    public function scopeOfType($query, EventType|string $type)
    {
        $type = $type instanceof EventType ? $type->value : $type;

        return $query->where('event_type', $type);
    }

    /**
     * Scope untuk semester tertentu
     */
    public function scopeSemester($query, SemesterEnum|string $semester)
    {
        $value = $semester instanceof SemesterEnum ? $semester->value : $semester;

        return $query->where('semester', $value);
    }

    /**
     * Scope untuk event yang sedang berlangsung
     */
    public function scopeCurrent($query)
    {
        return $query
            ->where('event_date', '<=', today())
            ->where(function ($query) {
                $query
                    ->whereNull('event_end_date')
                    ->orWhere('event_end_date', '>=', today());
            });
    }

    /**
     * Scope untuk event yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query
            ->where('event_date', '>=', today())
            ->orderBy('event_date');
    }

    /**
     * Cek apakah event sedang berlangsung
     */
    public function isOngoing(): bool
    {
        if ($this->event_date->isFuture()) {
            return false;
        }

        return $this->event_end_date ? $this->event_end_date->gte(today()) : $this->event_date->isToday();
    }

    /**
     * Mendapatkan durasi event (dalam hari)
     */
    public function getDurationAttribute(): int
    {
        return $this->event_end_date ? $this->event_date->diffInDays($this->event_end_date) + 1 : 1;
    }

    /**
     * Mendapatkan label warna berdasarkan tipe event
     */
    public function getDefaultColorAttribute(): string
    {
        return $this->event_type?->color() ?? EventType::OTHER->color();
    }

    /**
     * Convert Eloquent model → Guava CalendarEvent.
     *
     * event_end_date adalah inclusive di database.
     * Guava menggunakan end sebagai exclusive boundary.
     */
    public function toCalendarEvent(): CalendarEvent
    {
        /*
         * Database:
         *
         * event_date      = 2026-08-19
         * event_end_date  = 2026-08-21
         *
         * Calendar:
         *
         * start = 2026-08-19
         * end   = 2026-08-22
         *
         * karena end pada calendar bersifat exclusive.
         */
        $end = $this->event_end_date
            ? $this->event_end_date->copy()->addDay()
            : $this->event_date->copy()->addDay();

        return CalendarEvent::make($this)
            ->title($this->event_name)
            ->start($this->event_date)
            ->end($end)
            ->allDay()
            ->backgroundColor(
                $this->color ?: $this->default_color,
            )
            ->textColor('#FFFFFF')
            ->action('view');
    }

    /**
     * Boot method untuk validasi periode, auto-set semester, dan auto-set color
     */
    protected static function booted()
    {
        static::saving(function (AcademicCalendar $event): void {
            static::validateEventDateOrder($event);

            $academicYear = static::resolveAcademicYear($event);

            static::validateWithinAcademicYear($event, $academicYear);
            static::assignSemester($event, $academicYear);
            static::normalizeHolidayFlags($event);

            if (empty($event->color)) {
                $event->color = $event->default_color;
            }
        });
    }

    /**
     * Jaga konsistensi antara event_type dan flag is_national_holiday /
     * is_school_holiday.
     *
     * - Untuk event_type yang tidak mungkin berupa hari libur (mis.
     *   EXAMINATION, REPORT, CEREMONY, dst), kedua flag dipaksa false
     *   secara otomatis — mencegah data usang tersisa saat admin
     *   mengganti tipe event tapi lupa mematikan flag lama.
     * - Untuk event_type HOLIDAY, setidaknya salah satu flag (nasional
     *   atau sekolah) wajib diisi — event "Libur" tanpa keterangan jenis
     *   libur apapun tidak bermakna.
     * - NATIONALDAY sengaja tidak diwajibkan mengisi flag, karena tidak
     *   semua Hari Nasional adalah hari libur.
     */
    protected static function normalizeHolidayFlags(AcademicCalendar $event): void
    {
        if (! $event->event_type?->isHolidayEligible()) {
            $event->is_national_holiday = false;
            $event->is_school_holiday = false;

            return;
        }

        if (
            $event->event_type === EventType::HOLIDAY
            && ! $event->is_national_holiday
            && ! $event->is_school_holiday
        ) {
            throw ValidationException::withMessages([
                'is_national_holiday' => 'Event bertipe Libur harus ditandai sebagai libur nasional dan/atau libur sekolah.',
            ]);
        }
    }

    /**
     * event_end_date (jika diisi) tidak boleh sebelum event_date.
     * Tanpa ini, getDurationAttribute() bisa menghasilkan angka negatif
     * dan toCalendarEvent() bisa membuat rentang kalender yang terbalik.
     */
    protected static function validateEventDateOrder(AcademicCalendar $event): void
    {
        if ($event->event_date && $event->event_end_date && $event->event_end_date->lt($event->event_date)) {
            throw ValidationException::withMessages([
                'event_end_date' => 'Tanggal selesai event tidak boleh sebelum tanggal mulai event.',
            ]);
        }
    }

    /**
     * Ambil AcademicYear terkait, dari relasi yang sudah di-load
     * atau query manual jika belum (mis. saat set academic_year_id
     * langsung tanpa memuat relasinya).
     */
    protected static function resolveAcademicYear(AcademicCalendar $event): AcademicYear
    {
        $academicYear = $event->academicYear;

        if (! $academicYear && $event->academic_year_id) {
            $academicYear = AcademicYear::query()->find($event->academic_year_id);
        }

        if (! $academicYear) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'Event harus terikat pada Tahun Akademik.',
            ]);
        }

        return $academicYear;
    }

    /**
     * Event tidak boleh berada di luar periode Tahun Akademik yang terikat.
     */
    protected static function validateWithinAcademicYear(AcademicCalendar $event, AcademicYear $academicYear): void
    {
        if (! $event->event_date) {
            return;
        }

        $end = $event->event_end_date ?? $event->event_date;

        if ($event->event_date->lt($academicYear->start_date) || $end->gt($academicYear->end_date)) {
            throw ValidationException::withMessages([
                'event_date' => "Tanggal event harus berada dalam periode Tahun Akademik ({$academicYear->start_date->format('d M Y')} – {$academicYear->end_date->format('d M Y')}).",
            ]);
        }
    }

    /**
     * Semester selalu diturunkan otomatis dari tanggal event dan
     * Tahun Akademik terkait — tidak bisa diisi manual oleh admin,
     * sesuai aturan bisnis.
     */
    protected static function assignSemester(AcademicCalendar $event, AcademicYear $academicYear): void
    {
        if (! $event->event_date) {
            return;
        }

        $semester = $academicYear->getSemester($event->event_date);

        if ($semester) {
            $event->semester = $semester;
        }
    }
}
