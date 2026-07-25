<?php

namespace App\Enums;

enum ProductMediaType: int
{
    case MainImage = 1;
    case DetailImage = 2;
    case Video = 3;
    case Qualification = 4;
    case Other = 5;

    public function label(): string
    {
        return match ($this) {
            self::MainImage => '主图',
            self::DetailImage => '详情图',
            self::Video => '视频',
            self::Qualification => '资质文件',
            self::Other => '其他附件',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
