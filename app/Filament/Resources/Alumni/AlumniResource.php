<?php

namespace App\Filament\Resources\Alumni;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\Alumni\Pages\ListAlumni;
use App\Filament\Resources\Alumni\Tables\AlumniTable;
use App\Models\ClassRoomStudent;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Laporan alumni — read-only (tidak ada Create/Edit/Delete, cuma
 * halaman List). Berbasis model ClassRoomStudent yang SAMA dengan
 * yang dipakai RelationManager "Daftar Siswa", TAPI di-scope permanen
 * ke status Lulus lewat getEloquentQuery() — bukan resource baru
 * untuk model baru, cuma sudut pandang berbeda dari data yang sama.
 */
class AlumniResource extends Resource
{
    protected static ?string $model = ClassRoomStudent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Alumni';

    protected static ?string $pluralModelLabel = 'Alumni';

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    /**
     * withoutGlobalScopes(): laporan alumni WAJIB lintas Tahun
     * Akademik ("dari tahun manapun") — kalau ikut ActiveAcademicYear-
     * Scope, alumni dari tahun-tahun sebelumnya akan hilang begitu
     * tahun aktif berpindah.
     *
     * Kontrol akses berbasis role: Guru yang BUKAN kaprodi tidak
     * dapat baris sama sekali (whereRaw '1 = 0') — beda dari
     * ClassRoomStudentPolicy::viewAny() yang sengaja permisif untuk
     * semua Guru (supaya RelationManager "Daftar Siswa" tetap bisa
     * dibuka wali kelas biasa). Resource berdiri sendiri ini TIDAK
     * punya pre-scoping seperti RelationManager, jadi pembatasannya
     * harus eksplisit di sini.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->where('status', ClassRoomStudentStatusEnum::LULUS->value);

        $user = auth()->user();

        if ($user?->role === RoleEnum::TEACHER) {
            $headProgramKeahlianId = $user->teacher?->currentHeadOfProgramKeahlian()?->id;

            if (! $headProgramKeahlianId) {
                return $query->whereRaw('1 = 0');
            }

            $query->whereHas(
                'classRoom',
                fn ($q) => $q->withoutGlobalScopes()->where('program_keahlian_id', $headProgramKeahlianId),
            );
        }

        return $query;
    }

    /**
     * shouldRegisterNavigation() dicek TERPISAH dari Policy — supaya
     * Guru yang bukan kaprodi tidak melihat menu "Alumni" sama sekali
     * (bukan cuma tabel kosong), tanpa perlu mengetatkan
     * ClassRoomStudentPolicy::viewAny() yang memang sengaja dibiarkan
     * longgar untuk kebutuhan RelationManager lain.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if ($user?->role === RoleEnum::TEACHER) {
            return $user->teacher?->currentHeadOfProgramKeahlian() !== null;
        }

        return parent::shouldRegisterNavigation();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return AlumniTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlumni::route('/'),
        ];
    }
}
