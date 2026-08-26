<?php

namespace App\Enums;

enum CommentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SPAM = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Moderasi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::SPAM => 'Spam',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED, self::SPAM => 'danger',
        };
    }
}
