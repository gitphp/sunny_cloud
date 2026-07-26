<?php

namespace App\Enums;

enum BossJobHot: int
{
    case No = 0;
    case Yes = 1;

    public function label(): string
    {
        return match ($this) {
            self::No => '否',
            self::Yes => '急聘',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
