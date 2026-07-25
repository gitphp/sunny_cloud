<?php

namespace App\Enums;

enum HrIsMain: int
{
    case PartTime = 0;
    case Main = 1;

    public function label(): string
    {
        return match ($this) {
            self::PartTime => '兼职',
            self::Main => '主岗',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
