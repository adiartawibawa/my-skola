<?php

namespace App\Filament\Imports;

use App\Enums\GolonganEnum;
use App\Enums\PendidikanEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusKepegawaianEnum;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    protected static ?string $defaultRole = RoleEnum::USER->value;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('username')
                ->requiredMapping()
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),

            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['nullable', 'min:6', 'max:255'])
                ->example('Kosongkan untuk auto-generate'),

            ImportColumn::make('role')
                ->requiredMapping()
                ->rules(['required', Rule::enum(RoleEnum::class)]),

            // ImportColumn::make('nis')
            //     ->rules(['nullable', 'required_if:role,student', 'string', 'max:50']),

            // ImportColumn::make('nisn')
            //     ->rules(['nullable', 'required_if:role,student', 'string', 'max:20']),

            // ImportColumn::make('tempat_lahir')
            //     ->rules(['nullable', 'string', 'max:100']),

            // ImportColumn::make('tanggal_lahir')
            //     ->rules(['nullable', 'required_if:role,student', 'date']),

            // ImportColumn::make('nama_ayah')
            //     ->rules(['nullable', 'string', 'max:150']),

            // ImportColumn::make('nama_ibu')
            //     ->rules(['nullable', 'string', 'max:150']),

            // ImportColumn::make('pekerjaan_orang_tua')
            //     ->rules(['nullable', 'string', 'max:100']),

            // ImportColumn::make('alamat_orang_tua')
            //     ->rules(['nullable', 'string', 'max:255']),

            // ImportColumn::make('no_telp_orang_tua')
            //     ->rules(['nullable', 'string', 'max:20']),

            // ImportColumn::make('is_active')
            //     ->boolean()
            //     ->rules(['nullable', 'boolean']),

            // --- Profil Guru (tabel teachers) ---

            // ImportColumn::make('nip')
            //     ->rules(['nullable', 'string', 'max:18']),

            // ImportColumn::make('nuptk')
            //     ->rules(['nullable', 'required_if:role,teacher', 'string', 'max:16']),

            // ImportColumn::make('nik')
            //     ->rules(['nullable', 'string', 'max:16']),

            // ImportColumn::make('status_kepegawaian')
            //     ->rules(['nullable', 'required_if:role,teacher', Rule::enum(StatusKepegawaianEnum::class)]),

            // ImportColumn::make('bidang_studi')
            //     ->rules(['nullable', 'required_if:role,teacher', 'string', 'max:100']),

            // ImportColumn::make('golongan')
            //     ->rules(['nullable', Rule::enum(GolonganEnum::class)]),

            // ImportColumn::make('tanggal_masuk')
            //     ->rules(['nullable', 'required_if:role,teacher', 'date']),

            // ImportColumn::make('pendidikan_terakhir')
            //     ->rules(['nullable', 'required_if:role,teacher', Rule::enum(PendidikanEnum::class)]),

        ];
    }

    public function resolveRecord(): User
    {
        $username = trim((string) ($this->data['username'] ?? ''));

        if ($username !== '') {
            return User::withTrashed()->firstOrNew(['username' => $username]);
        }

        $email = trim((string) ($this->data['email'] ?? ''));

        return User::withTrashed()->firstOrNew(['email' => $email]);
    }

    protected function beforeSave(): void
    {
        if (blank($this->record->password)) {
            $this->record->password = Str::random(12);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    protected function afterCreate(): void
    {
        $this->createUserProfile($this->record);
    }

    protected function afterUpdate(): void
    {
        $this->createUserProfile($this->record);
    }

    /**
     * Dispatcher upsert profil berdasarkan role user. Dipanggil setelah
     * User tersimpan (create/update), supaya $user->id sudah pasti ada
     * untuk foreign key Teacher/Student.
     */
    protected function createUserProfile(User $user): void
    {
        $profileData = $this->extractProfileData($this->data);

        match ($this->data['role']) {
            RoleEnum::TEACHER->value => $this->createOrUpdateGuru($user, $profileData),
            RoleEnum::STUDENT->value => $this->createOrUpdateSiswa($user, $profileData),
            default => null,
        };
    }

    /**
     * Ambil hanya kolom yang relevan dengan role baris ini dari data
     * mentah hasil import, lalu buang value kosong/null supaya update
     * tidak menimpa data lama dengan string kosong (mis. baris re-import
     * yang tidak mengisi ulang nama_ayah, jangan sampai menghapus nilai
     * yang sudah ada di database).
     */
    protected function extractProfileData(array $data): array
    {
        $keys = match ($data['role'] ?? null) {
            RoleEnum::STUDENT->value => [
                'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir',
                'nama_ayah', 'nama_ibu', 'pekerjaan_orang_tua',
                'alamat_orang_tua', 'no_telp_orang_tua', 'is_active',
            ],
            RoleEnum::TEACHER->value => [
                'nip', 'nuptk', 'nik', 'status_kepegawaian',
                'bidang_studi', 'golongan', 'tanggal_masuk',
                'pendidikan_terakhir',
            ],
            default => [],
        };

        return array_filter(
            Arr::only($data, $keys),
            fn ($value) => $value !== null && $value !== ''
        );
    }

    /**
     * Upsert profil guru. user_id kini benar-benar tersimpan karena
     * bug Fillable pada model Teacher sudah diperbaiki (lihat
     * Teacher.php) — sebelumnya baris ini selalu gagal secara diam-diam.
     */
    protected function createOrUpdateGuru(User $user, array $data): void
    {
        $teacher = Teacher::query()->firstOrNew(['user_id' => $user->id]);
        $teacher->fill($data);
        $teacher->user_id = $user->id;
        $teacher->save();
    }

    /**
     * Upsert profil siswa. is_active default true kalau tidak diisi di
     * file, HANYA saat membuat record baru — pada update, kalau kolom
     * dikosongkan di file, nilai is_active yang sudah ada di database
     * tidak akan ditimpa (sudah tersaring di extractProfileData()).
     */
    protected function createOrUpdateSiswa(User $user, array $data): void
    {
        $student = Student::query()->firstOrNew(['user_id' => $user->id]);

        if (! $student->exists && ! array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        $student->fill($data);
        $student->user_id = $user->id;
        $student->save();
    }
}
