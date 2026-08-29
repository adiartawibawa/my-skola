<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'subject', 'message', 'is_read'])]
class ContactMessage extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
