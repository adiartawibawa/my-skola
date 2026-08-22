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
                ->chunkSize(250)
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
                        ->options(RoleEnum::importTemplateTypeRoles())
                        ->default(RoleEnum::USER->value)
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

    /*
    |--------------------------------------------------------------------
    | 1. Entry Point
    |--------------------------------------------------------------------
    | Titik masuk yang dipanggil dari controller/action. Bertugas
    | mengumpulkan seluruh potongan data template (headers, contoh,
    | nama file, instruksi) lalu menyerahkannya ke mesin generate CSV.
    */

    /**
     * Download template berdasarkan tipe ('student', 'teacher',
     * atau 'user_only').
     */
    protected function downloadTemplate(string $type): StreamedResponse
    {
        $headers = $this->getTemplateHeaders($type);
        $example = $this->getExampleData($type);
        $filename = $this->getTemplateFilename($type);
        $instructions = $this->getTemplateInstructions($type);

        return $this->generateCsvTemplate($headers, $example, $filename, $instructions);
    }

    /*
    |--------------------------------------------------------------------
    | 2. Mesin Generate CSV
    |--------------------------------------------------------------------
    | Bagian generik yang menyusun file CSV secara streaming. Tidak
    | tahu-menahu soal tipe (student/teacher/user_only) — murni
    | menerima data jadi dan menulisnya ke output CSV.
    */

    /**
     * Generate file CSV template beserta baris contoh dan instruksi.
     *
     * Struktur baris yang dihasilkan:
     * 1. Header kolom
     * 2. Baris contoh (siap dihapus user)
     * 3-4. Dua baris kosong (tempat user mulai mengisi data)
     * 5+. Blok instruksi (diawali '#' agar tidak ikut terbaca sebagai
     *     data saat file diimport ulang)
     */
    protected function generateCsvTemplate(
        array $headers,
        array $example,
        string $filename,
        array $instructions = []
    ): StreamedResponse {
        $callback = function () use ($headers, $example, $instructions) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 supaya Excel tidak salah baca karakter non-ASCII
            // (misal nama dengan huruf ber-diakritik).
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $headers);
            fputcsv($file, $example);
            fputcsv($file, array_fill(0, count($headers), ''));
            fputcsv($file, array_fill(0, count($headers), ''));

            fputcsv($file, ['# ========== INSTRUKSI IMPORT ==========']);
            fputcsv($file, ['# 1. Jangan hapus atau ubah baris header (baris pertama)']);
            fputcsv($file, ['# 2. Isi data mulai baris ke-2 (hapus contoh)']);
            fputcsv($file, ['# 3. Password: Kosongkan untuk auto-generate, atau isi dengan password plain']);
            fputcsv($file, ['# 4. Format tanggal: YYYY-MM-DD (contoh: 2024-01-15)']);

            // Instruksi tambahan spesifik per tipe (lihat getTemplateInstructions()),
            // nomornya melanjutkan dari daftar umum di atas.
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

    /*
    |--------------------------------------------------------------------
    | 3. Data Template Per Tipe
    |--------------------------------------------------------------------
    | Tiga method di bawah ini SALING BERPASANGAN dan urutan elemennya
    | harus selalu sinkron satu sama lain:
    | getTemplateHeaders($type) <-> getExampleData($type)
    | Kolom ke-N di headers harus berisi contoh ke-N di example.
    | Role TIDAK didaftarkan sebagai kolom di sini karena diisi lewat
    | opsi form saat import (lihat UserImporter::beforeSave()), bukan
    | dari file.
    */

    /**
     * Header kolom per tipe template — disamakan dengan kolom yang
     * benar-benar dibaca UserImporter::getColumns() sekarang (termasuk
     * 'nis' yang sebelumnya hilang padahal wajib di database).
     */
    protected function getTemplateHeaders(string $type): array
    {
        $baseHeaders = [
            'username',
            'name',
            'email',
            'password',
        ];

        return match ($type) {
            'student' => array_merge($baseHeaders, [
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
            ]),

            'teacher' => array_merge($baseHeaders, [
                'nip',
                'nuptk',
                'nik',
                'status_kepegawaian',
                'bidang_studi',
                'golongan',
                'tanggal_masuk',
                'pendidikan_terakhir',
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
                '',                             // username (opsional, fallback ke email)
                'Budi Santoso',
                'budi.siswa@example.com',
                '',                             // password (kosong = auto-generate)
                '2026001',                      // nis
                '1234567890',                   // nisn
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
                '',                              // nip (opsional)
                '1234567890123456',              // nuptk
                '',                              // nik (opsional)
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
            ],
        };
    }

    /**
     * Instruksi tambahan per tipe, nomornya melanjutkan 4 instruksi
     * umum di generateCsvTemplate(). Instruksi soal role dihapus dari
     * sini karena role tidak lagi jadi kolom file (lihat catatan di
     * atas grup method ini).
     */
    protected function getTemplateInstructions(string $type): array
    {
        $commonInstructions = [
            '# 5. Username boleh dikosongkan — sistem akan mencocokkan berdasarkan email sebagai gantinya',
        ];

        return match ($type) {
            'student' => array_merge($commonInstructions, [
                '# 6. NIS dan NISN harus unik dan wajib diisi',
                '# 7. Format tanggal: YYYY-MM-DD',
                '# 8. is_active: 1 untuk aktif, 0 untuk tidak aktif (kosongkan = aktif)',
            ]),

            'teacher' => array_merge($commonInstructions, [
                '# 6. NUPTK wajib diisi dan unik (16 digit)',
                '# 7. Status kepegawaian yang valid: PNS, PPPK, Honorer, Kontrak',
                '# 8. Golongan yang valid: I/a - IV/d (kosongkan jika tidak berlaku, mis. Honorer)',
                '# 9. Pendidikan terakhir yang valid: SMA, D3, S1, S2, S3',
            ]),

            'user_only' => $commonInstructions,
        };
    }

    /*
    |--------------------------------------------------------------------
    | 4. Metadata File
    |--------------------------------------------------------------------
    */

    /**
     * Nama file template per tipe, disisipi timestamp supaya setiap
     * download punya nama unik (tidak numpuk/ketimpa di folder Download).
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
