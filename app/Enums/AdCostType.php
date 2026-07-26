<?php

namespace App\Enums;

enum AdCostType: int
{
    case Cpm = 1;
    case Cpc = 2;
    case Cpt = 3;
    case Cpa = 4;

    public function label(): string
    {
        return match ($this) {
            self::Cpm => 'CPM',
            self::Cpc => 'CPC',
            self::Cpt => 'CPT',
            self::Cpa => 'CPA',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
