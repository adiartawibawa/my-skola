<?php

namespace App\Filament\Imports;

use App\Enums\Enums\RoleEnum;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    protected static ?string $defaultRole = RoleEnum::USER->value;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('username')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('password123 atau biarkan kosong untuk auto-generate'),
            ImportColumn::make('role')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): User
    {
        return User::withTrashed()->firstOrNew([
            'username' => $this->data['username'],
        ]);
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

    protected function createUserProfile(User $user): void
    {
        $this->data['_profile_data'] = $this->extractProfileData($this->data);
        $profileData = $this->data['_profile_data'] ?? [];

        switch ($this->data['role']) {
            case 'teacher':
                $this->createOrUpdateGuruProfile($user, $profileData);
                break;
            case 'student':
                $this->createOrUpdateGuruProfile($user, $profileData);
                break;
            default:
                // code...
                break;
        }
    }

    protected function extractProfileData(array $data): array
    {
        $dataKeys = [
            //
        ];

        $profileData = [];
        foreach ($dataKeys as $key) {
            if (isset($data[$key]) && ! empty($data[$key])) {
                $profileData[$key] = $data[$key];
            }
        }

        if (! isset($profileData['is_active'])) {
            $profileData['is_active'] = true;
        }

        return $profileData;
    }

    protected function createOrUpdateGuru(User $user, array $data): void
    {
        $guruData = [
            'user_id' => $user->id,
        ];

        $guruData = array_filter($guruData, fn ($value) => $value !== null);

        $guru = Teacher::where('user_id', $user->id)->first();

        if ($guru) {
            $guru->update($guruData);
        } else {
            Teacher::create($guruData);
        }

    }

    protected function createOrUpdateSiswa(User $user, array $data): void
    {
        $siswaData = [
            'user_id' => $user->id,
        ];

        $siswaData = array_filter($siswaData, fn ($value) => $value !== null);

        $siswa = Student::where('user_id', $user->id)->first();

        if ($siswa) {
            $siswa->update($siswaData);
        } else {
            Student::create($siswaData);
        }

    }
}
