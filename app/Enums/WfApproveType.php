<?php

namespace App\Enums;

enum WfApproveType: int
{
    case DirectLeader = 1;
    case FixedUsers = 2;
    case Roles = 3;
    case DeptLeader = 4;
    case ApplicantSelect = 5;

    public function label(): string
    {
        return match ($this) {
            self::DirectLeader => '发起人直属上级',
            self::FixedUsers => '指定固定人员',
            self::Roles => '指定角色',
            self::DeptLeader => '部门负责人',
            self::ApplicantSelect => '发起人自选审批人',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
