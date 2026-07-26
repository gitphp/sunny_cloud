<?php

namespace App\Enums;

enum AdStatus: int
{
    case Draft = 1;
    case Pending = 2;
    case Approved = 3;
    case Running = 4;
    case Finished = 5;
    case Paused = 6;
    case Rejected = 7;
    case Offline = 8;

    public function label(): string
    {
        return match ($this) {
            self::Draft => '草稿',
            self::Pending => '待审核',
            self::Approved => '审核通过',
            self::Running => '投放中',
            self::Finished => '已结束',
            self::Paused => '已暂停',
            self::Rejected => '审核驳回',
            self::Offline => '已下线',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
