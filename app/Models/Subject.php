<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'code',
    'name',
    'program_keahlian_id',
    'description',
    'is_active',
)]
class Subject extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relations
     */
    public function programKeahlian(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Mapel umum (program_keahlian_id null) berlaku untuk semua kelas;
     * mapel kejuruan hanya untuk kelas di program keahlian tersebut.
     */
    public function isGeneral(): bool
    {
        return $this->program_keahlian_id === null;
    }
}
