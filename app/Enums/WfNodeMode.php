<?php

namespace App\Enums;

enum WfNodeMode: int
{
    case OrSign = 1;
    case AndSign = 2;

    public function label(): string
    {
        return match ($this) {
            self::OrSign => '或签（一人通过即过）',
            self::AndSign => '会签（全部必须通过）',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
