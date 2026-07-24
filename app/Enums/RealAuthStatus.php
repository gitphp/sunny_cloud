<?php

namespace App\Enums;

enum RealAuthStatus: int
{
    case None = 0;
    case Pending = 1;
    case Verified = 2;
    case Rejected = 3;

    public function label(): string
    {
        return match ($this) {
            self::None => '未实名',
            self::Pending => '待审核',
            self::Verified => '已实名',
            self::Rejected => '实名驳回',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
