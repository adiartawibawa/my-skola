<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['username', 'name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasUuids;
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleEnum::class,
        ];
    }

    /**
     * Kontrol akses panel Filament (kontrol akses berbasis role).
     *
     * Panel admin ini HANYA untuk staf sekolah — Admin, Kepala
     * Sekolah, Guru, Tata Usaha. Siswa dan Orang Tua diblokir total
     * di sini karena mereka akan punya tempat tersendiri (portal
     * read-only + sedikit CRUD terbatas).
     *
     * Pembatasan LEBIH LANJUT (siapa boleh apa DI DALAM panel) ada di
     * Gate::before() (lihat AuthorizationServiceProvider) dan
     * Policy per-model di app/Policies — method ini cuma gerbang
     * "boleh masuk panel atau tidak", bukan "boleh apa saja".
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [
            RoleEnum::SUPER_ADMIN,
            RoleEnum::SCHOOL_ADMIN,
            RoleEnum::PRINCIPAL,
            RoleEnum::TEACHER,
            RoleEnum::ADMIN_STAFF,
        ], true);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Relations
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Blogging System
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function capabilities(): BelongsToMany
    {
        return $this->belongsToMany(Capability::class)->withTimestamps();
    }

    public function studentLinks(): HasMany
    {
        return $this->hasMany(GuardianStudent::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
            ->withPivot(['relationship_type', 'verified_at'])
            ->withTimestamps();
    }

    public function isParentOf(Student $student): bool
    {
        return $this->students()->whereKey($student->id)->exists();
    }

    public function hasCapability(string $key): bool
    {
        return $this->relationLoaded('capabilities')
            ? $this->capabilities->contains('key', $key)
            : $this->capabilities()->where('key', $key)->exists();
    }

    public function canWriteBlog(): bool
    {
        return $this->hasCapability('blog.write') || $this->hasCapability('blog.editor');
    }

    public function canEditBlog(): bool
    {
        return $this->hasCapability('blog.editor');
    }
}
