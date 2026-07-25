<?php

namespace App\Enums;

enum CategoryLevel: int
{
    case Level1 = 1;
    case Level2 = 2;
    case Level3 = 3;

    public function label(): string
    {
        return match ($this) {
            self::Level1 => '一级',
            self::Level2 => '二级',
            self::Level3 => '三级',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }

    public static function max(): int
    {
        return self::Level3->value;
    }
}
