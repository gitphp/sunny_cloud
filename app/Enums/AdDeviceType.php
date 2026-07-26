<?php

namespace App\Enums;

enum AdDeviceType: int
{
    case All = 1;
    case Ios = 2;
    case Android = 3;
    case Other = 4;

    public function label(): string
    {
        return match ($this) {
            self::All => '全部',
            self::Ios => 'iOS',
            self::Android => 'Android',
            self::Other => '其他',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
