<?php

namespace App\Enums;

enum BossJobStatus: int
{
    case Pending = 1;
    case Published = 2;
    case Closed = 3;

    public function label(): string
    {
        return match ($this) {
            self::Pending => '待发布',
            self::Published => '发布中',
            self::Closed => '已关闭',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
