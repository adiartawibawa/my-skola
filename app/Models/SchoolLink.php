<?php

namespace App\Models;

use App\Enums\LinkCategory;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'description', 'url', 'logo', 'category', 'roles', 'is_featured', 'is_active', 'order'])]
class SchoolLink extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'category' => LinkCategory::class,
            'roles' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn (Builder $query) => $query->orderBy('order'));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Tautan publik — TIDAK dibatasi role apa pun. Dipakai untuk menu
     * publik (dropdown header & halaman /aplikasi).
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('roles')->orWhereJsonLength('roles', 0);
        });
    }

    /**
     * Tautan privat yang diberikan akses ke role tertentu — dipakai
     * untuk kartu aplikasi di Dashboard Portal. TIDAK menyertakan
     * tautan publik (lihat catatan di scopePublic) — keduanya memang
     * sengaja saling eksklusif.
     */
    public function scopeForRole(Builder $query, RoleEnum $role): Builder
    {
        return $query->whereJsonContains('roles', $role->value);
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }
}
