<?php

namespace App\Enums;

enum CategoryStatus: int
{
    case Hidden = 0;
    case Visible = 1;

    public function label(): string
    {
        return match ($this) {
            self::Hidden => '隐藏',
            self::Visible => '显示',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
