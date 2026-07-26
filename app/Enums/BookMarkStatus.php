<?php

namespace App\Enums;

enum BookMarkStatus: int
{
    case Hidden = 0;
    case Normal = 1;
    case Invalid = 2;

    public function label(): string
    {
        return match ($this) {
            self::Hidden => '隐藏',
            self::Normal => '正常',
            self::Invalid => '失效',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
