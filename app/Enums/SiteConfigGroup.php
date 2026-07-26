<?php

namespace App\Enums;

enum SiteConfigGroup: string
{
    case Basic = 'basic';
    case Seo = 'seo';
    case Contact = 'contact';
    case Social = 'social';

    public function label(): string
    {
        return match ($this) {
            self::Basic => '基础设置',
            self::Seo => 'SEO设置',
            self::Contact => '联系方式',
            self::Social => '社交账号',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
