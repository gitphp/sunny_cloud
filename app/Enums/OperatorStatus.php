<?php

namespace App\Enums;

enum OperatorStatus: int
{
    case Failed = 0;
    case Success = 1;

    public function label(): string
    {
        return match ($this) {
            self::Failed => '失败',
            self::Success => '成功',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
