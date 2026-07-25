<?php

namespace App\Enums;

enum CategoryShowType: int
{
    case All = 0;
    case IncludeCustomers = 1;
    case ExcludeCustomers = 2;

    public function label(): string
    {
        return match ($this) {
            self::All => '全部可见',
            self::IncludeCustomers => '指定客户可见',
            self::ExcludeCustomers => '指定客户不可见',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
