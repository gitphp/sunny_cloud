<?php

namespace App\Enums;

enum WfCcReadStatus: int
{
    case Unread = 0;
    case Read = 1;

    public function label(): string
    {
        return match ($this) {
            self::Unread => '未读',
            self::Read => '已读',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
