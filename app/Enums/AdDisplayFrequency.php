<?php

namespace App\Enums;

enum AdDisplayFrequency: int
{
    case DailyOnce = 1;
    case HourlyOnce = 2;
    case Unlimited = 3;

    public function label(): string
    {
        return match ($this) {
            self::DailyOnce => '每人每天1次',
            self::HourlyOnce => '每人每小时1次',
            self::Unlimited => '无限次',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
