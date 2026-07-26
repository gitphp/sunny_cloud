<?php

namespace App\Enums;

enum AdTargetUserType: int
{
    case All = 0;
    case New = 1;
    case Old = 2;
    case Vip = 3;
    case Group = 4;

    public function label(): string
    {
        return match ($this) {
            self::All => '全部用户',
            self::New => '新用户',
            self::Old => '老用户',
            self::Vip => 'VIP用户',
            self::Group => '指定用户组',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
