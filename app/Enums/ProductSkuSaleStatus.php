<?php

namespace App\Enums;

enum ProductSkuSaleStatus: int
{
    case OffShelf = 0;
    case OnShelf = 1;

    public function label(): string
    {
        return match ($this) {
            self::OffShelf => '下架',
            self::OnShelf => '上架',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
