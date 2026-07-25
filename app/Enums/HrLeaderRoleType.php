<?php

namespace App\Enums;

enum HrLeaderRoleType: int
{
    case Primary = 1;
    case Secondary = 2;

    public function label(): string
    {
        return match ($this) {
            self::Primary => '主要负责人',
            self::Secondary => '次要负责人',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
