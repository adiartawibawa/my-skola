<?php

namespace App\Models;

use App\Enums\GolonganEnum;
use App\Enums\PendidikanEnum;
use App\Enums\StatusKepegawaianEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(
    'user_id',
    'nip',
    'nuptk',
    'nik',
    'status_kepegawaian',
    'bidang_studi',
    'golongan',
    'tanggal_masuk',
    'pendidikan_terakhir',
)]
class Teacher extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'status_kepegawaian' => StatusKepegawaianEnum::class,
            'golongan' => GolonganEnum::class,
            'tanggal_masuk' => 'date',
            'pendidikan_terakhir' => PendidikanEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
