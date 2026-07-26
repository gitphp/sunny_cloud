<?php

namespace App\Enums;

enum FeedbackStatus: int
{
    case Pending = 0;
    case Handled = 1;

    public function label(): string
    {
        return match ($this) {
            self::Pending => '未处理',
            self::Handled => '已处理',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
