<?php

namespace App\Enums;

enum AdShowTimeType: int
{
    case AllDay = 0;
    case Custom = 1;

    public function label(): string
    {
        return match ($this) {
            self::AllDay => '全天',
            self::Custom => '自定义时间段',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
