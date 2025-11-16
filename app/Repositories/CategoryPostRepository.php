<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Post;

class CategoryPostRepository
{
    public function assignCategory($payload, $ids)
    {
        if (!$payload instanceof Post) {
            throw new \InvalidArgumentException('$payload must be an instance of Post');
        }

        $existingCategoryIds = Category::whereIn('id', $ids)->pluck('id')->toArray();

        if ($payload->exists()) {

            $payload->categories()->sync($existingCategoryIds);
        } else {

            $payload->categories()->attach($existingCategoryIds);
        }

        return true;
    }
}
