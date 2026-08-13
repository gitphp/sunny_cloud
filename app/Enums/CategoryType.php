<?php

namespace App\Enums;

enum CategoryType: int
{
    /** 内容/资讯分类 */
    case Content = 1;
    /** 导航门户书签分类 */
    case Portal = 2;

    public function label(): string
    {
        return match ($this) {
            self::Content => '内容分类',
            self::Portal => '导航分类',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
