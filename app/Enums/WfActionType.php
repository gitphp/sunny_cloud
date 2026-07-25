<?php

namespace App\Enums;

enum WfActionType: int
{
    case Agree = 1;
    case Reject = 2;
    case Transfer = 3;
    case AddSign = 4;
    case Withdraw = 5;
    case CcRead = 6;

    public function label(): string
    {
        return match ($this) {
            self::Agree => '同意',
            self::Reject => '驳回',
            self::Transfer => '转审',
            self::AddSign => '加签',
            self::Withdraw => '撤回',
            self::CcRead => '抄送已读',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
