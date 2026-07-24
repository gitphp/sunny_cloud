<?php

namespace App\Enums;

enum RoleType: int
{
    case System = 1;
    case Custom = 2;

    public function label(): string
    {
        return match ($this) {
            self::System => '系统内置',
            self::Custom => '用户自定义',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
