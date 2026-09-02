<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'code',
    'name',
    'duration_years',
    'is_active',
    'description',
)]
class ProgramKeahlian extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'duration_years' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relations
     */
    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function heads(): HasMany
    {
        return $this->hasMany(ProgramKeahlianHead::class);
    }

    public function currentHead(): ?Teacher
    {
        return $this->heads()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first()
            ?->teacher;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
