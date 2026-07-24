<?php

namespace App\Enums;

enum DataScope: int
{
    case All = 1;
    case DeptAndChildren = 2;
    case DeptOnly = 3;
    case SelfOnly = 4;
    case CustomDepts = 5;

    public function label(): string
    {
        return match ($this) {
            self::All => '全部数据',
            self::DeptAndChildren => '本部门及下级',
            self::DeptOnly => '本部门',
            self::SelfOnly => '仅本人数据',
            self::CustomDepts => '自定义指定部门',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
