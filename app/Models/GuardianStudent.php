<?php

namespace App\Models;

use App\Enums\GuardianRelationshipType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'student_id', 'relationship_type', 'verified_at'])]
class GuardianStudent extends Model
{
    use HasUuids;

    protected $table = 'guardian_student';

    protected function casts(): array
    {
        return [
            'relationship_type' => GuardianRelationshipType::class,
            'verified_at' => 'datetime',
        ];
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
