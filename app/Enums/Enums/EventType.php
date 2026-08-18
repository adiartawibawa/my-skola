<?php

namespace App\Enums\Enums;

enum EventType: string
{
    case HOLIDAY = 'Holiday';        // Libur
    case EXAMINATION = 'Examination';    // Ujian
    case REPORT = 'Report';         // Pembagian Rapor
    case REGISTRATION = 'Registration';   // Pendaftaran
    case ORIENTATION = 'Orientation';    // MPLS/MOS
    case CEREMONY = 'Ceremony';       // Upacara
    case PARENTMEETING = 'Parent_Meeting'; // Pertemuan Orang Tua
    case EXTRACURRICULAR = 'Extracurricular'; // Ekstrakurikuler
    case NATIONALDAY = 'National_Day';   // Hari Nasional
    case OTHER = 'Other';           // Lainnya
}
