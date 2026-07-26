<?php

namespace App\Enums;

enum OperationAction: string
{
    case Insert = 'INSERT';
    case Update = 'UPDATE';
    case Delete = 'DELETE';
    case Login = 'LOGIN';

    public function label(): string
    {
        return match ($this) {
            self::Insert => '新增',
            self::Update => '修改',
            self::Delete => '删除',
            self::Login => '登录',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
