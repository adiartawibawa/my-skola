<?php

namespace App\Filament\Imports;

use App\Enums\RoleEnum;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Select;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;

/**
 * Importer untuk mengimpor data User dari file CSV/XLSX melalui Filament.
 *
 * Selain membuat/memperbarui record User, importer ini juga otomatis
 * meng-upsert profil terkait (Teacher atau Student) sesuai role yang
 * dipilih pada form opsi import.
 *
 * Alur eksekusi Filament untuk setiap baris data kurang lebih:
 * 1. getOptionsFormComponents()  -> render form opsi sebelum import jalan
 * 2. getColumns()                -> definisi kolom & validasi per baris
 * 3. resolveRecord()             -> cari/siapkan record User untuk baris ini
 * 4. beforeSave()                -> hook sebelum User disimpan
 * 5. afterCreate() / afterUpdate() -> hook setelah User disimpan (upsert profil)
 */
class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    /*
    |--------------------------------------------------------------------
    | 1. Konfigurasi Form & Kolom
    |--------------------------------------------------------------------
    | Bagian ini mendefinisikan tampilan form opsi import dan kolom
    | apa saja yang dibaca dari file, beserta aturan validasinya.
    */

    /**
     * Form opsi yang tampil di modal sebelum proses import dimulai.
     * User memilih role (Pengguna Umum/Siswa/Guru) untuk menentukan
     * template kolom dan tabel profil mana yang akan diisi.
     */
    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('role')
                ->label('Peran')
                ->options(RoleEnum::importRoles())
                ->default(RoleEnum::default())
                ->required()
                ->live()
                ->helperText(
                    'Gunakan template CSV sesuai jenis data yang dipilih.'
                ),
        ];
    }

    /**
     * Kolom dasar akun User yang wajib ada di setiap file import,
     * terlepas dari role yang dipilih. Kolom khusus profil (nis, nip,
     * dll) tidak didaftarkan di sini karena ditangani secara dinamis
     * lewat extractProfileData(), bukan lewat validasi Importer bawaan.
     */
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

        ];
    }

    /*
    |--------------------------------------------------------------------
    | 2. Resolusi Record & Lifecycle Hooks User
    |--------------------------------------------------------------------
    | Bagian ini menentukan record User mana yang dipakai untuk baris
    | yang sedang diproses, dan apa yang terjadi sebelum/sesudah User
    | tersebut disimpan.
    */

    /**
     * Menentukan record User yang akan dibuat/diperbarui untuk baris
     * saat ini. Prioritas pencarian: username dulu (jika diisi), baru
     * fallback ke email. withTrashed() dipakai supaya re-import tidak
     * membuat duplikat pada user yang sudah di-soft-delete.
     */
    public function resolveRecord(): User
    {
        $username = trim((string) ($this->data['username'] ?? ''));

        if ($username !== '') {
            return User::withTrashed()->firstOrNew([
                'username' => $username,
                'role' => $this->options['role'],
            ]);
        }

        $email = trim((string) ($this->data['email'] ?? ''));

        return User::withTrashed()->firstOrNew([
            'email' => $email,
            'role' => $this->options['role'],
        ]);
    }

    /**
     * Dipanggil tepat sebelum User disimpan. Jika kolom password
     * kosong di file (baik saat create maupun update), password
     * di-generate otomatis dari username agar akun tetap punya
     * kredensial login yang valid.
     */
    protected function beforeSave(): void
    {
        if (blank($this->record->password)) {
            $this->record->password = Hash::make($this->record->username);
        }
    }

    /**
     * Dipanggil setelah User baru berhasil dibuat. Meneruskan ke
     * createUserProfile() supaya profil Teacher/Student ikut dibuat
     * pada baris yang sama.
     */
    protected function afterCreate(): void
    {
        $this->createUserProfile($this->record);
    }

    /**
     * Dipanggil setelah User yang sudah ada berhasil diperbarui.
     * Sama seperti afterCreate(), profil terkait ikut di-upsert
     * supaya data profil tetap sinkron saat re-import.
     */
    protected function afterUpdate(): void
    {
        $this->createUserProfile($this->record);
    }

    /*
    |--------------------------------------------------------------------
    | 3. Upsert Profil (Teacher / Student)
    |--------------------------------------------------------------------
    | Bagian ini menangani pembuatan/pembaruan tabel profil tambahan
    | (teachers/students) berdasarkan role yang dipilih di form opsi.
    */

    /**
     * Dispatcher upsert profil berdasarkan role user. Dipanggil setelah
     * User tersimpan (create/update), supaya $user->id sudah pasti ada
     * untuk foreign key Teacher/Student.
     */
    protected function createUserProfile(User $user): void
    {
        $profileData = $this->extractProfileData($this->data);

        match ($this->options['role']) {
            RoleEnum::TEACHER => $this->createOrUpdateGuru($user, $profileData),
            RoleEnum::STUDENT => $this->createOrUpdateSiswa($user, $profileData),
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
        $keys = match ($this->options['role'] ?? null) {
            RoleEnum::STUDENT => [
                'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir',
                'nama_ayah', 'nama_ibu', 'pekerjaan_orang_tua',
                'alamat_orang_tua', 'no_telp_orang_tua', 'is_active',
            ],
            RoleEnum::TEACHER => [
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

    /*
    |--------------------------------------------------------------------
    | 4. Notifikasi
    |--------------------------------------------------------------------
    */

    /**
     * Menyusun teks notifikasi yang tampil ke user setelah proses
     * import selesai, termasuk jumlah baris sukses dan gagal (jika ada).
     */
    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
