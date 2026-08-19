<?php

namespace App\Enums\Enums;

enum EventType: string
{
    case HOLIDAY = 'Holiday';
    case EXAMINATION = 'Examination';
    case REPORT = 'Report';
    case REGISTRATION = 'Registration';
    case ORIENTATION = 'Orientation';
    case CEREMONY = 'Ceremony';
    case PARENTMEETING = 'Parent_Meeting';
    case EXTRACURRICULAR = 'Extracurricular';
    case NATIONALDAY = 'National_Day';
    case OTHER = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::HOLIDAY => 'Libur',
            self::EXAMINATION => 'Ujian',
            self::REPORT => 'Pembagian Rapor',
            self::REGISTRATION => 'Pendaftaran',
            self::ORIENTATION => 'MPLS / MOS',
            self::CEREMONY => 'Upacara',
            self::PARENTMEETING => 'Pertemuan Orang Tua',
            self::EXTRACURRICULAR => 'Ekstrakurikuler',
            self::NATIONALDAY => 'Hari Nasional',
            self::OTHER => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HOLIDAY => '#FF6B6B',
            self::EXAMINATION => '#4ECDC4',
            self::REPORT => '#45B7D1',
            self::REGISTRATION => '#96CEB4',
            self::ORIENTATION => '#FFEAA7',
            self::CEREMONY => '#DDA0DD',
            self::PARENTMEETING => '#FF8A5C',
            self::EXTRACURRICULAR => '#A29BFE',
            self::NATIONALDAY => '#FF4757',
            self::OTHER => '#DFE6E9',
        };
    }

    /**
     * Tipe event yang SECARA KONSEP boleh ditandai sebagai hari libur
     * (is_national_holiday / is_school_holiday).
     *
     * Sengaja tidak dipakai untuk auto-derive penuh nilai boolean-nya,
     * karena NATIONALDAY tidak selalu berarti hari libur (mis. Hari
     * Pendidikan Nasional biasanya bukan hari libur, sementara Hari
     * Kemerdekaan libur) — keputusan itu tetap perlu input admin.
     * Method ini hanya dipakai untuk MENOLAK kombinasi yang jelas tidak
     * masuk akal, misalnya EXAMINATION ditandai is_national_holiday.
     */
    public function isHolidayEligible(): bool
    {
        return match ($this) {
            self::HOLIDAY, self::NATIONALDAY => true,
            default => false,
        };
    }
}
