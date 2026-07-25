<?php

namespace App\Enums;

enum ProductIsSystem: int
{
    case Custom = 0;
    case System = 1;

    public function label(): string
    {
        return match ($this) {
            self::Custom => '自定义',
            self::System => '系统预设',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
