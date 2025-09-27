<?php

namespace App\Enums;

enum LevelEnum: string
{
    case X = 'X';
    case XI = 'XI';
    case XII = 'XII';

    public function label(): string
    {
        return $this->value;
    }
}
