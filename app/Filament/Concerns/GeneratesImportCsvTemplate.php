<?php

namespace App\Filament\Concerns;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Diekstrak dari ListUsers::generateCsvTemplate() — logika penulisan
 * CSV-nya generik (tidak tahu-menahu soal role/tipe data), jadi aman
 * dipakai ulang oleh importer mana pun yang butuh tombol "Download
 * Template" dengan format yang sama: header, baris contoh, 2 baris
 * kosong, lalu blok instruksi diawali '#'.
 */
trait GeneratesImportCsvTemplate
{
    protected function streamCsvTemplate(
        array $headers,
        array $example,
        string $filename,
        array $instructions = [],
    ): StreamedResponse {
        $callback = function () use ($headers, $example, $instructions) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 supaya Excel tidak salah baca karakter non-ASCII.
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $headers);
            fputcsv($file, $example);
            fputcsv($file, array_fill(0, count($headers), ''));
            fputcsv($file, array_fill(0, count($headers), ''));

            fputcsv($file, ['# ========== INSTRUKSI IMPORT ==========']);
            fputcsv($file, ['# 1. Jangan hapus atau ubah baris header (baris pertama)']);
            fputcsv($file, ['# 2. Isi data mulai baris ke-2 (hapus contoh)']);
            fputcsv($file, ['# 3. Password: Kosongkan untuk auto-generate, atau isi dengan password plain']);
            fputcsv($file, ['# 4. Format tanggal: YYYY-MM-DD (contoh: 2026-07-01)']);

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
}
