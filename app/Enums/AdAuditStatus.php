<?php

namespace App\Enums;

enum AdAuditStatus: int
{
    case None = 0;
    case Pending = 1;
    case Approved = 2;
    case Rejected = 3;

    public function label(): string
    {
        return match ($this) {
            self::None => '未提交',
            self::Pending => '待审核',
            self::Approved => '审核通过',
            self::Rejected => '审核驳回',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
