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
     * Boot method untuk auto-set semester dan color
     */
    protected static function booted()
    {
        static::saving(function (AcademicCalendar $event): void {
            $academicYear = $event->academicYear;

            if (! $academicYear && $event->academic_year_id) {
                $academicYear = AcademicYear::query()->find($event->academic_year_id);
            }

            if (! $academicYear) {
                throw ValidationException::withMessages([
                    'academic_year_id' => 'Event harus terikat pada Tahun Akademik.',
                ]);
            }

            if ($event->event_date) {
                $end = $event->event_end_date ?? $event->event_date;

                if (
                    $event->event_date->lt($academicYear->start_date)
                    || $end->gt($academicYear->end_date)
                ) {
                    throw ValidationException::withMessages([
                        'event_date' => "Tanggal event harus berada dalam periode Tahun Akademik ({$academicYear->start_date->format('d M Y')} – {$academicYear->end_date->format('d M Y')}).",
                    ]);
                }

                $semester = $academicYear->getSemester($event->event_date);

                if ($semester) {
                    $event->semester = SemesterEnum::from(
                        $semester,
                    );
                }
            }

            if (empty($event->color)) {
                $event->color = $event->default_color;
            }
        });
    }
}
