<?php

namespace App\Enums;

enum BookMarkBold: int
{
    /** 加粗显示 */
    case Bold = 0;

    /** 正常显示 */
    case Normal = 1;

    public function label(): string
    {
        return match ($this) {
            self::Bold => '加粗',
            self::Normal => '正常',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
