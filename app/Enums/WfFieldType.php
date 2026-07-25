<?php

namespace App\Enums;

enum WfFieldType: string
{
    case Input = 'input';
    case Number = 'number';
    case Textarea = 'textarea';
    case Radio = 'radio';
    case Select = 'select';
    case Upload = 'upload';
    case Date = 'date';

    public function label(): string
    {
        return match ($this) {
            self::Input => '单行文本',
            self::Number => '数字',
            self::Textarea => '多行文本',
            self::Radio => '单选',
            self::Select => '下拉',
            self::Upload => '附件上传',
            self::Date => '日期',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
