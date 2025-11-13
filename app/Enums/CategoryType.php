<?php

namespace App\Enums;

class CategoryType
{
    const CATEGORY = 'category';
    const AUTHOR = 'author';
    const TAG = 'tag';
    const DEVELOPMENT = 'development';

    public static function toArray()
    {
        return [
            self::CATEGORY,
            self::AUTHOR,
            self::TAG,
            self::DEVELOPMENT,
        ];
    }
}
