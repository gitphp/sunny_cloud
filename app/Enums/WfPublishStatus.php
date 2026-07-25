<?php

namespace App\Enums;

enum WfPublishStatus: int
{
    case Draft = 0;
    case Published = 1;

    public function label(): string
    {
        return match ($this) {
            self::Draft => '草稿',
            self::Published => '已发布',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
