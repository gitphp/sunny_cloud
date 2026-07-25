<?php

namespace App\Enums;

enum WfConditionOperator: string
{
    case Gt = '>';
    case Gte = '>=';
    case Lt = '<';
    case Lte = '<=';
    case Eq = '=';
    case Neq = '!=';

    public function label(): string
    {
        return match ($this) {
            self::Gt => '大于',
            self::Gte => '大于等于',
            self::Lt => '小于',
            self::Lte => '小于等于',
            self::Eq => '等于',
            self::Neq => '不等于',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
