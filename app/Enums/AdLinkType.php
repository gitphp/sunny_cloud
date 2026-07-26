<?php

namespace App\Enums;

enum AdLinkType: int
{
    case Internal = 1;
    case External = 2;
    case MiniProgram = 3;
    case None = 4;

    public function label(): string
    {
        return match ($this) {
            self::Internal => '站内链接',
            self::External => '站外链接',
            self::MiniProgram => '小程序',
            self::None => '无跳转',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
