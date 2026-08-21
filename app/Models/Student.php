<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(
    'user_id',
    'nis',
    'nisn',
    'tempat_lahir',
    'tanggal_lahir',
    'nama_ayah',
    'nama_ibu',
    'pekerjaan_orang_tua',
    'alamat_orang_tua',
    'no_telp_orang_tua',
    'is_active',
)]
class Student extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
