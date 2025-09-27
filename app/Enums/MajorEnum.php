<?php

namespace App\Enums;

enum MajorEnum: string
{
    case TKJ = 'TKJ';
    case TKR = 'TKR';
    case AK = 'AK';
    case AP = 'AP';

    public function label(): string
    {
        return match($this) {
            self::TKJ => 'Teknik Komputer dan Jaringan',
            self::TKR => 'Teknik Kendaraan Ringan',
            self::AK  => 'Akuntansi',
            self::AP  => 'Administrasi Perkantoran',
        };
    }
}
