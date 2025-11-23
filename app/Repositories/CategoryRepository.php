<?php

namespace App\Repositories;

use App\Models\Category;
use App\Enums\CategoryType;
use App\Models\Position;
use App\Traits\SlugGenerateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CategoryMeta;

class CategoryRepository
{
    use SlugGenerateTrait;

    // check post type exists or not
    public function checkCategoryTypeExists($type)
    {
        // Ensure type is a string and clean encoding
        $type = (string) $type;
        $type = mb_convert_encoding($type, 'UTF-8', 'UTF-8');

        // Use the new values method or handle the array differently
        $validTypes = CategoryType::values();

        // Clean the valid types array
        $validTypes = array_map(function ($item) {
            return mb_convert_encoding((string) $item, 'UTF-8', 'UTF-8');
        }, $validTypes);

        if (!in_array($type, $validTypes, true)) {
            \Log::error('Invalid category type provided', [
                'provided_type' => $type,
                'valid_types' => $validTypes,
                'type_encoding' => mb_detect_encoding($type),
                'type_length' => strlen($type)
            ]);

            abort(403, "Post Type '{$type}' Not Found. Valid types: " . implode(', ', $validTypes));
        }

        return true;
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }

public function deleteCategory($id)
{
    try {
        DB::transaction(function () use ($id) {
            $category = Category::find($id);

            if (!$category) {
                throw new \Exception('Category not found');
            }

            // Delete from positions table
            Position::where('positionable_id', $id)
                ->where('positionable_type', 'App\Models\Category')
                ->delete();

            // Delete from category_metas table
            CategoryMeta::where('category_id', $id)->delete();

            // Delete the category
            $category->delete();
        });

        return true;
    } catch (\Exception $e) {
        throw new \Exception('Failed to delete category: ' . $e->getMessage());
    }
}

    public function getCategoryByType($type)
    {
        return Category::where('type', $type)->get();
    }

    public function getCategoriesOrderedByPosition($type)
    {
        return Category::where('type', $type)
            ->orderBy('position', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createCategoryWithMeta($request, $type)
    {
        return DB::transaction(function () use ($request, $type) {
            // Create category
            $category = Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'parent' => $request->parent ?? 0,
                'type' => $type,
                'description' => $request->description ?? null,
                'position' => Category::where('type', $type)->max('position') + 1,
            ]);

            return $category;
        });
    }


    public function updateCategory($request, $payload, $type)
    {
        $category = $payload;

        $status = $category->update([
            'name' => $request->name,
            'slug' => $this->getSlug($category, $request->name, $request->slug),
            'type' => $type,
            'description' => isset($request->description) ?  $request->description : NULL,
            'parent' => isset($request->parent) ?  $request->parent : 0,
            'menu_order' => isset($request->menu_order) ?  $request->menu_order : 0,
        ]);

        return ['status' => $status, 'category' => $category];
    }

    public function getMetaDatas($category)
    {
        return CategoryMeta::where('category_id', $category->id)
            ->get()
            ->pluck('meta_value', 'meta_key')
            ->toArray();
    }

    public function storeMetaData($payload, $request)
    {
        $metaDatas = [];
        $metaDatas['seo_title'] = $request->seo_title ?? null;
        $metaDatas['seo_description'] = $request->seo_description ?? null;

        // insert or update meta data
        foreach ($metaDatas as $key => $value) {
            $this->updateOrCreateMeta($payload, $key, $value);
        }
    }

    // update or create category meta
    public function updateOrCreateMeta($category, $key, $value)
    {
        if ($value === null) {
            // Delete if value is null
            CategoryMeta::where('category_id', $category->id)
                ->where('meta_key', $key)
                ->delete();
            return;
        }

        CategoryMeta::updateOrCreate(
            [
                'category_id' => $category->id,
                'meta_key' => $key
            ],
            [
                'meta_value' => $value
            ]
        );
    }


    public function getCategoriesWithMeta($type)
    {
        return Category::with(['categoryMeta', 'position']) // Use your actual relationship names
            ->where('type', $type)
            ->orderBy('position', 'asc') // This uses the position column in categories table
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($category) {
                // Get parent name
                $parentName = null;
                if ($category->parent) {
                    $parentCategory = Category::find($category->parent);
                    $parentName = $parentCategory ? $parentCategory->name : null;
                }

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'parent' => $category->parent,
                    'parent_name' => $parentName,
                    'type' => $category->type,
                    'position' => $category->position, // From categories table
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                    'meta' => $category->categoryMeta->pluck('meta_value', 'meta_key')->toArray()
                ];
            });
    }

    // restore posts
    public function restoreCategory($id)
    {
        $cat = Category::withTrashed()->findOrFail($id);

        if (!empty($cat)) {
            $cat->restore();
            Category::where('parent_id_backup', $cat->id)->update([
                'parent' => $cat->id,
                'parent_id_backup' => null
            ]);
        }

        return true;
    }

    public function permanentDeleteCategory($id)
    {
        $cat = Category::withTrashed()->findOrFail($id);

        if (!empty($cat)) {

            Category::where('parent_id_backup', $cat->id)->update([
                'parent_id_backup' => null,
            ]);

            $cat->forceDelete();
        }

        return true;
    }
}
