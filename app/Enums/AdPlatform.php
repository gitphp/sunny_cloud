<?php

namespace App\Enums;

enum AdPlatform: int
{
    case All = 1;
    case Pc = 2;
    case Mobile = 3;
    case MiniProgram = 4;

    public function label(): string
    {
        return match ($this) {
            self::All => '全部',
            self::Pc => 'PC端',
            self::Mobile => '移动端',
            self::MiniProgram => '小程序',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
