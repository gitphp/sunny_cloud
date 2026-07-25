<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class SeqCode
{
    /**
     * 生成业务编码，如 BR000001 / FL000001
     */
    public static function next(string $modelClass, string $column, string $prefix, int $pad = 6): string
    {
        /** @var class-string<Model> $modelClass */
        $query = $modelClass::query();
        if (method_exists($modelClass, 'withTrashed')) {
            $query = $modelClass::withTrashed();
        }

        $latest = $query
            ->where($column, 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value($column);

        $n = 1;
        if (is_string($latest) && preg_match('/(\d+)$/', $latest, $m)) {
            $n = ((int) $m[1]) + 1;
        }

        return $prefix.str_pad((string) $n, $pad, '0', STR_PAD_LEFT);
    }
}
