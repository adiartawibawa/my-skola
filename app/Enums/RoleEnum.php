<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case SCHOOL_ADMIN = 'school_admin';
    case PRINCIPAL = 'principal';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';
    case ADMIN_STAFF = 'admin_staff';
    case ALUMNI = 'alumni';
    case USER = 'user';

    /**
     * Get human-readable label in Indonesian
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::SCHOOL_ADMIN => 'Admin Sekolah',
            self::PRINCIPAL => 'Kepala Sekolah',
            self::TEACHER => 'Guru',
            self::STUDENT => 'Siswa',
            self::PARENT => 'Orang Tua/Wali',
            self::ADMIN_STAFF => 'Tata Usaha',
            self::ALUMNI => 'Alumni',
            self::USER => 'Pengguna/Tamu',
        };
    }

    /**
     * Get options for dropdown/select
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get role level for access control
     */
    public function level(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'super_admin',
            self::SCHOOL_ADMIN, self::PRINCIPAL => 'admin',
            self::TEACHER, self::ADMIN_STAFF => 'staff',
            self::STUDENT, self::PARENT => 'user',
            self::USER => 'guest',
        };
    }

    /**
     * Check if role has administrative access
     */
    public function isAdmin(): bool
    {
        return in_array($this, [
            self::SUPER_ADMIN,
            self::SCHOOL_ADMIN,
            self::PRINCIPAL,
            self::ADMIN_STAFF,
        ]);
    }

    /**
     * Check if role is an educator
     */
    public function isEducator(): bool
    {
        return in_array($this, [
            self::TEACHER,
            self::PRINCIPAL,
        ]);
    }

    public static function importRoles(): array
    {
        return [
            self::USER->value => self::USER->label(),
            self::STUDENT->value => self::STUDENT->label(),
            self::TEACHER->value => self::TEACHER->label(),
        ];
    }

    public static function importTemplateTypeRoles(): array
    {
        return [
            self::USER->value => 'Template Umum (All Users)',
            self::STUDENT->value => 'Template untuk Murid (Siswa)',
            self::TEACHER->value => 'Template untuk Guru',
        ];
    }

    /**
     * Check if role is a student
     */
    public function isStudent(): bool
    {
        return $this === self::STUDENT;
    }

    /**
     * Check if role is a parent
     */
    public function isParent(): bool
    {
        return $this === self::PARENT;
    }

    /**
     * Get default role for new registration
     */
    public static function default(): self
    {
        return self::USER;
    }

    /**
     * Get roles allowed for self-registration
     */
    public static function registrableRoles(): array
    {
        return [
            self::STUDENT->value,
            self::PARENT->value,
        ];
    }

    /**
     * Get role hierarchy level (higher number = higher access)
     */
    public function getHierarchy(): int
    {
        return match ($this) {
            self::SUPER_ADMIN => 100,
            self::SCHOOL_ADMIN => 80,
            self::PRINCIPAL => 70,
            self::ADMIN_STAFF => 60,
            self::TEACHER => 50,
            self::STUDENT => 30,
            self::PARENT => 20,
            self::ALUMNI => 15,
            self::USER => 10,
        };
    }

    /**
     * Check if role has higher access than another role
     */
    public function hasHigherAccessThan(self $role): bool
    {
        return $this->getHierarchy() > $role->getHierarchy();
    }

    /**
     * Get all roles with their hierarchy
     */
    public static function withHierarchy(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => [
                    'label' => $case->label(),
                    'level' => $case->level(),
                    'hierarchy' => $case->getHierarchy(),
                ],
            ])
            ->toArray();
    }

    /**
     * Role yang bisa masuk panel Filament (lihat User::canAccessPanel())
     * DAN yang dapat kartu "aplikasi" di Dashboard Portal berbasis
     * SchoolLink::forRole(). Satu sumber kebenaran — jangan duplikasi
     * daftar ini di tempat lain.
     */
    public static function staffRoles(): array
    {
        return [
            self::SUPER_ADMIN,
            self::SCHOOL_ADMIN,
            self::PRINCIPAL,
            self::TEACHER,
            self::ADMIN_STAFF,
        ];
    }

    public function isStaff(): bool
    {
        return in_array($this, self::staffRoles(), true);
    }
}
