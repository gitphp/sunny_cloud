<?php

namespace App\Enums;

enum OperationBizType: string
{
    case Auth = 'auth';
    case User = 'user';
    case ProductBrand = 'product_brand';
    case Product = 'product';
    case Category = 'category';
    case HrDepartment = 'hr_department';
    case HrPost = 'hr_post';
    case AuthMenu = 'auth_menu';
    case AuthRole = 'auth_role';
    case ArticleCategory = 'article_category';
    case Article = 'article';
    case BookMark = 'book_mark';

    public function label(): string
    {
        return match ($this) {
            self::Auth => '认证',
            self::User => '用户',
            self::ProductBrand => '品牌',
            self::Product => '商品',
            self::Category => '分类',
            self::HrDepartment => '部门',
            self::HrPost => '岗位',
            self::AuthMenu => '菜单',
            self::AuthRole => '角色',
            self::ArticleCategory => '文章分类',
            self::Article => '文章',
            self::BookMark => '书签',
        };
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
