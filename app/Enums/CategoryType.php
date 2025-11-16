<?php

namespace App\Enums;

enum CategoryType: string
{
    case CATEGORY = 'category';
    case AUTHOR = 'author';
    case TAG = 'tag';
    case NAV_MENU = 'nav_menu';
    case OUR_TEAM = 'our_team';
    case PROJECT_TYPE = 'project_type';
    case PROJECT_STATUS = 'project_status';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function values(): array
    {
        return self::toArray();
    }

    public static function isValid(string $type): bool
    {
        return !empty(self::tryFrom($type));
    }
}
