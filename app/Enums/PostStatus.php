<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Menunggu Review',
            self::SCHEDULED => 'Terjadwal',
            self::PUBLISHED => 'Terbit',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_REVIEW => 'info',
            self::SCHEDULED => 'warning',
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'danger',
        };
    }

    /**
     * Status yang masih boleh diedit isinya oleh penulis
     */
    public function isEditableByAuthor(): bool
    {
        return in_array($this, [self::DRAFT, self::PENDING_REVIEW]);
    }
}
