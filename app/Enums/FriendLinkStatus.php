<?php

namespace App\Enums;

enum FriendLinkStatus: int
{
    case Disabled = 0;
    case Enabled = 1;

    public function label(): string
    {
        return match ($this) {
            self::Disabled => '禁用',
            self::Enabled => '启用',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
