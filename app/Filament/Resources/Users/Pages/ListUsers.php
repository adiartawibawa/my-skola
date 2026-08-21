<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleEnum;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->label('Import')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('danger')
                ->maxRows(1000)
                ->chunkSize(100)
                ->csvDelimiter(',')
                ->modalHeading('Import Data Users')
                ->modalDescription('Upload file CSV dengan data users. Download template untuk format yang benar.')
                ->importer(UserImporter::class),

            Action::make('download_template')
                ->label('Template')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->schema([
                    Select::make('template_type')
                        ->label('Pilih template')
                        ->options([
                            // PERBAIKAN: sebelumnya value di sini adalah
                            // 'all', tapi seluruh match($type) di bawah
                            // hanya mengenali 'user_only' — memilih "All
                            // Users" akan selalu UnhandledMatchError.
                            'student' => 'Template untuk Murid (Siswa)',
                            'teacher' => 'Template untuk Guru',
                            'user_only' => 'Template Umum (All Users)',
                        ])
                        ->default('user_only')
                        ->required()
                        ->native(false)
                        ->helperText('Pilih template sesuai dengan role user yang akan diimport'),
                ])
                ->action(function (array $data) {
                    return $this->downloadTemplate($data['template_type']);
                })
                ->modalHeading('Download Template Import')
                ->modalSubmitActionLabel('Download')
                ->modalCancelActionLabel('Batal'),
        ];

    }

    /**
     * Download template berdasarkan tipe.
     */
    protected function downloadTemplate(string $type): StreamedResponse
    {
        $headers = $this->getTemplateHeaders($type);
        $example = $this->getExampleData($type);
        $filename = $this->getTemplateFilename($type);
        $instructions = $this->getTemplateInstructions($type);

        return $this->generateCsvTemplate($headers, $example, $filename, $instructions);
    }

    /**
     * Generate file CSV template beserta baris contoh dan instruksi.
     * Tidak berubah dari versi sebelumnya — bug-nya ada di data yang
     * di-generate (headers/example/instructions), bukan di sini.
     */
    protected function generateCsvTemplate(
        array $headers,
        array $example,
        string $filename,
        array $instructions = []
    ): StreamedResponse {
        $callback = function () use ($headers, $example, $instructions) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $headers);
            fputcsv($file, $example);
            fputcsv($file, array_fill(0, count($headers), ''));
            fputcsv($file, array_fill(0, count($headers), ''));

            fputcsv($file, ['# ========== INSTRUKSI IMPORT ==========']);
            fputcsv($file, ['# 1. Jangan hapus atau ubah baris header (baris pertama)']);
            fputcsv($file, ['# 2. Isi data mulai baris ke-3 (setelah contoh)']);
            fputcsv($file, ['# 3. Kolom dengan tanda * (asterisk) wajib diisi']);
            fputcsv($file, ['# 4. Password: Kosongkan untuk auto-generate, atau isi dengan password plain']);
            fputcsv($file, ['# 5. Format tanggal: YYYY-MM-DD (contoh: 2024-01-15)']);

            foreach ($instructions as $instruction) {
                fputcsv($file, ['# '.$instruction]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Header kolom per tipe template — disamakan dengan kolom yang
     * benar-benar dibaca UserImporter::getColumns() sekarang (termasuk
     * 'nis*' yang sebelumnya hilang padahal wajib di database, dan
     * 'role' singular, bukan lagi 'roles').
     */
    protected function getTemplateHeaders(string $type): array
    {
        $baseHeaders = [
            'username',
            'name*',
            'email*',
            'password',
            'role*',
        ];

        return match ($type) {
            'student' => array_merge($baseHeaders, [
                'nis*',
                'nisn*',
                'tempat_lahir',
                'tanggal_lahir*',
                'nama_ayah',
                'nama_ibu',
                'pekerjaan_orang_tua',
                'alamat_orang_tua',
                'no_telp_orang_tua',
                'is_active',
            ]),

            'teacher' => array_merge($baseHeaders, [
                'nip',
                'nuptk*',
                'nik',
                'status_kepegawaian*',
                'bidang_studi*',
                'golongan',
                'tanggal_masuk*',
                'pendidikan_terakhir*',
            ]),

            'user_only' => $baseHeaders,
        };
    }

    /**
     * Baris contoh per tipe template — urutan HARUS sama persis dengan
     * getTemplateHeaders(). Nilai status_kepegawaian/golongan/
     * pendidikan_terakhir memakai value enum yang valid, supaya baris
     * contoh sendiri tidak gagal kalau di-import apa adanya.
     */
    protected function getExampleData(string $type): array
    {
        return match ($type) {
            'student' => [
                '',                                    // username (opsional, fallback ke email)
                'Budi Santoso',
                'budi.siswa@example.com',
                '',                                     // password (kosong = auto-generate)
                RoleEnum::STUDENT->value,
                '2026001',                               // nis
                '1234567890',                            // nisn
                'Jakarta',
                '2010-05-15',
                'Ahmad Santoso',
                'Siti Aminah',
                'Wiraswasta',
                'Jl. Merdeka No. 123, Jakarta',
                '081234567890',
                '1',
            ],

            'teacher' => [
                '',
                'Siti Rahayu',
                'siti.guru@example.com',
                '',
                RoleEnum::TEACHER->value,
                '',                                       // nip (opsional)
                '1234567890123456',                        // nuptk
                '',                                         // nik (opsional)
                'PNS',
                'Matematika',
                'IV/a',
                '2015-07-01',
                'S2',
            ],

            'user_only' => [
                'admin1',
                'Admin User',
                'admin@example.com',
                '',
                RoleEnum::ADMIN->value,
            ],
        };
    }

    /**
     * Instruksi per tipe template. Diperbaiki: instruksi "multiple
     * roles pisahkan koma" sebelumnya kontradiktif dengan RoleEnum yang
     * memang single-value — dihapus, diganti daftar role valid yang
     * diambil langsung dari RoleEnum (supaya tidak bisa lagi tidak
     * sinkron dengan enum di masa depan).
     */
    protected function getTemplateInstructions(string $type): array
    {
        $validRoles = implode(', ', array_column(RoleEnum::cases(), 'value'));

        $commonInstructions = [
            "# 6. Role yang valid (isi salah satu): {$validRoles}",
            '# 7. Username boleh dikosongkan — sistem akan mencocokkan berdasarkan email sebagai gantinya',
        ];

        return match ($type) {
            'student' => array_merge($commonInstructions, [
                '# 8. NIS dan NISN harus unik dan wajib diisi',
                '# 9. Format tanggal: YYYY-MM-DD',
                '# 10. is_active: 1 untuk aktif, 0 untuk tidak aktif (kosongkan = aktif)',
            ]),

            'teacher' => array_merge($commonInstructions, [
                '# 8. NUPTK wajib diisi dan unik (16 digit)',
                '# 9. Status kepegawaian yang valid: PNS, PPPK, Honorer, Kontrak',
                '# 10. Golongan yang valid: I/a - IV/d (kosongkan jika tidak berlaku, mis. Honorer)',
                '# 11. Pendidikan terakhir yang valid: SMA, D3, S1, S2, S3',
            ]),

            'user_only' => array_merge($commonInstructions, [
                '# 8. Template ini untuk role admin/user yang tidak punya profil siswa/guru',
            ]),
        };
    }

    /**
     * Nama file template per tipe.
     */
    protected function getTemplateFilename(string $type): string
    {
        $timestamp = now()->format('Ymd_His');

        return match ($type) {
            'student' => "template_import_siswa_{$timestamp}.csv",
            'teacher' => "template_import_guru_{$timestamp}.csv",
            'user_only' => "template_import_user_{$timestamp}.csv",
        };
    }
}
