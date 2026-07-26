<?php

namespace App\Constants\Code;

class ArticleCategoryError
{
    public const NOT_FOUND = 1;

    public const NAME_DUPLICATED = 2;

    public const URL_DUPLICATED = 3;

    public const PARENT_INVALID = 4;

    public const DELETE_BLOCKED_HAS_CHILDREN = 5;

    public const DELETE_BLOCKED_HAS_ARTICLES = 6;
}
