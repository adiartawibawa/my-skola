<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(
    'title',
    'body',
    'is_for_all',
    'is_pinned',
    'publish_at',
    'expires_at',
    'created_by',
)]
class Announcement extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_for_all' => 'boolean',
            'is_pinned' => 'boolean',
            'publish_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Relations
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(AnnouncementRole::class);
    }

    public function classRooms(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'announcement_class_room')->withTimestamps();
    }

    public function programKeahlians(): BelongsToMany
    {
        return $this->belongsToMany(ProgramKeahlian::class, 'announcement_program_keahlian')->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Pengumuman yang sedang tayang saat ini (sudah lewat publish_at,
     * belum lewat expires_at).
     */
    public function scopePublished($query)
    {
        return $query
            ->where(fn ($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    /**
     * Fondasi untuk konsumen di luar panel admin (portal siswa/guru,
     * API, dst) — belum dipakai di mana pun dalam panel ini sendiri.
     *
     * withoutGlobalScopes() pada classRooms/programKeahlians: target
     * yang di-set sekali tidak boleh "hilang" hanya karena Tahun
     * Akademik aktif sudah berpindah sejak pengumuman itu dibuat.
     */
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('is_for_all', true);

            if ($user->role) {
                $q->orWhereHas('roles', fn ($rq) => $rq->where('role', $user->role->value));
            }

            $currentClassRoom = $user->student?->currentClassRoom();

            if ($currentClassRoom) {
                $q->orWhereHas(
                    'classRooms',
                    fn ($rq) => $rq->withoutGlobalScopes()->where('class_rooms.id', $currentClassRoom->id),
                );

                if ($currentClassRoom->program_keahlian_id) {
                    $q->orWhereHas(
                        'programKeahlians',
                        fn ($rq) => $rq->withoutGlobalScopes()->where('program_keahlians.id', $currentClassRoom->program_keahlian_id),
                    );
                }
            }
        });
    }

    /**
     * Ringkasan target untuk ditampilkan di tabel admin — "Semua",
     * atau gabungan role/kelas/program yang dipilih.
     */
    public function getTargetSummaryAttribute(): string
    {
        if ($this->is_for_all) {
            return 'Semua';
        }

        $parts = [];

        if ($this->roles->isNotEmpty()) {
            $parts[] = $this->roles
                ->map(fn (AnnouncementRole $role) => RoleEnum::tryFrom($role->role)?->label() ?? $role->role)
                ->implode(', ');
        }

        if ($this->classRooms->isNotEmpty()) {
            $parts[] = $this->classRooms->pluck('full_name')->implode(', ');
        }

        if ($this->programKeahlians->isNotEmpty()) {
            $parts[] = $this->programKeahlians->pluck('name')->implode(', ');
        }

        return $parts === [] ? '—' : implode(' • ', $parts);
    }

    protected static function booted(): void
    {
        static::saving(function (Announcement $announcement): void {
            if (! $announcement->created_by && auth()->check()) {
                $announcement->created_by = auth()->id();
            }
        });
    }
}
