<?php

namespace App\Enums;

enum WfApplyStatus: int
{
    case Draft = 0;
    case Pending = 1;
    case Approved = 2;
    case Rejected = 3;
    case Withdrawn = 4;
    case Voided = 5;

    public function label(): string
    {
        return match ($this) {
            self::Draft => '草稿',
            self::Pending => '审批中',
            self::Approved => '已通过',
            self::Rejected => '已驳回',
            self::Withdrawn => '已撤回',
            self::Voided => '已作废',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
